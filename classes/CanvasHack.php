<?php

/** CanvasHack and related classes */

namespace smtech\CanvasHack;

/**
 * CanvasHack
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class CanvasHack {
	
	private $sql;
	
	private $path;
	
	private $table = 'canvashacks';
	private $css = 'css';
	private $javascript = 'javascript';
	private $pages = 'pages';
	private $dom = 'dom';
	
	private $id = null;
	private $name = null;
	private $abstract = null;
	private $description = null;
	
	public function __construct($sql, $path) {

		if ($sql instanceof \mysqli) {
			$this->sql = $sql;
		} else {
			throw new CanvasHack_Exception(
				'Expected mysqli object, received ' . get_class($sql),
				CanvasHack_Exception::MYSQL
			);
		}

		if (file_exists($path) && file_exists($manifest = realpath("$path/manifest.xml"))) {
			$this->path = dirname($manifest);
			$this->parseManifest($manifest);
			$pluginMetadata = new \Battis\AppMetadata($this->sql, $this->id);
			$pluginMetadata['PLUGIN_PATH'] = $path;
			$pluginMetadata['PLUGIN_URL'] = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'] . preg_replace("|^{$_SERVER['DOCUMENT_ROOT']}(.*)$|", '$1', $pluginMetadata['PLUGIN_PATH']);
		} else {
			if (isset($manifest)) {
				throw new CanvasHack_Exception(
					"Manifest file missing, expected <code>$path/manifest.xml</code>",
					CanvasHack_Exception::MANIFEST
				);
			} else {
				$this->loadManifestEntry($path);
			}
		}
	}
	
	public static function getCanvasHackById($sql, $id) {
		if (!($sql instanceof \mysqli)) {
			throw new CanvasHack_Exception(
				'Expected mysqli object, received ' . get_class($sql),
				CanvasHack_Exception::MYSQL
			);
		}
		
		$_id = $sql->real_escape_string($id);
		$result = $sql->query("SELECT * FROM `canvashacks` WHERE `id` = '$_id'");
		if ($result->num_rows === 0) {
			throw new CanvasHack_Exception(
				"No existing CanvasHacks matching ID `$id`",
				CanvasHack_Exception::ID
			);
		} else {
			$row = $result->fetch_assoc();
			return new CanvasHack($sql, $row['path']);
		}
	}
	
	private function loadManifestEntry($id) {
		$response = $this->sql->query("
			SELECT * FROM `{$this->table}` WHERE `id` = '" . $this->sql->real_escape_string($id) . "'
		");
		$row = $response->fetch_assoc();
		if ($row) {
			$this->id = $row['id'];
			$this->name = $row['name'];
			if (!empty($row['abstract'])) {
				$this->abstract = $row['abstract'];
			}
			if (!empty($row['description'])) {
				$this->description = $row['description'];
			}
		} else {
			throw new CanvasHack_Exception(
				"Manifest database entry missing for $id",
				CanvasHack_Exception::MANIFEST
			);
		}
	}
	
	private function parseManifest($manifest) {
		$xml = simplexml_load_string(file_get_contents($manifest));
		if ($xml === false) {
			throw new CanvasHack_Exception(
				"$manifest could not be parsed as a valid XML document",
				CanvasHack_Exception::MANIFEST
			);
		}
		
		$this->parseManifestMetadata($xml);
		$this->parseManifestComponents($xml->components);
	}
	
	private function parseManifestMetadata($xml) {
		$this->required('id', $this->sql->real_escape_string($xml->id));
		$this->required('name', $xml->name);
		$this->optional('abstract', $xml->abstract);
		$this->optional('description', $xml->description);
		if (!isset($this->abstract) && !isset($this->description)) {
			throw new CanvasHack_Exception(
				'Either an abstract or a description must be provided in the manifest',
				CanvasHack_Exception::REQUIRED
			);
		}
		
		// TODO deal with authors
		
		$_name = $this->sql->real_escape_string($this->name);
		$_abstract = (
			isset($this->abstract) ?
				$this->sql->real_escape_string($this->abstract) :
				$this->sql->real_escape_string($this->description)
		);
		$_description = (
			isset($this->description) ?
				$this->sql->real_escape_string($this->description) :
				$this->sql->real_escape_string($this->abstract)
		);
		$_path = $this->sql->real_escape_string($this->path);
		
		$this->updateDb($this->table, $this->id, array('name' => $_name, 'path' => $_path, 'abstract' => $_abstract, 'description' => $_description), 'id');
	}
	
	private function parseManifestCSS($css) {
		if (!empty($css)) {
			$this->updateDb($this->css, $this->id, array('path' => realpath($this->path . '/' . $css)));
		} else {
			$this->clearDb($this->css, $this->id);
		}
	}
	
	private function parseManifestJavascript($javascript) {
		if (!empty($javascript)) {
			$this->updateDb($this->javascript, $this->id, array('path' => realpath($this->path) . '/' . $javascript));
		} else {
			$this->clearDb($this->javascript, $this->id);
		}
	}
	
	private function parseManifestCanvasPages($pages) {
		$this->clearDb($this->pages, $this->id);
		if (!empty($pages->include)) {
			foreach($pages->include->children() as $page) {
				if (!$this->sql->query("
					INSERT INTO `{$this->pages}`
					(
						`canvashack`,
						`url`,
						`pattern`,
						`include`
					) VALUES (
						'{$this->id}',
						" . ($page->type == 'url' ? "'{$page->url}'" : 'NULL') . ",
						" . ($page->type == 'regex' ? "'" . addslashes($page->pattern) . "'" : 'NULL') . ",
						TRUE
					)
				")) {
					throw new CanvasHack_Exception(
						"Could not insert included page entry for {$this->id}: " . $page->asXml() . PHP_EOL . $this->sql->error,
						CanvasHack_Exception::SQL
					);
				}
			}
		}
		if (!empty($pages->exclude)) {
			foreach($pages->exclude->children() as $page) {
				if (!$this->sql->query("
					INSERT INTO `{$this->pages}`
					(
						`canvashack`,
						`url`,
						`pattern`,
						`include`
					) VALUES (
						'{$this->id}',
						" . ($page->type == 'url' ? "'" . $this->sql->real_escape_string($page->url) . "'" : 'NULL') . ",
						" . ($page->type == 'regex' ? "'" . $this->sql->real_escape_string($page->pattern) . "'" : 'NULL') . ",
						FALSE
					)
				")) {
					// TODO wording could be improved
					throw new CanvasHack_Exception(
						"Could not insert included page entry for {$this->id}: " . $page->asXml() . PHP_EOL . $this->sql->error,
						CanvasHack_Exception::SQL
					);
				}
			}
		}
	}
	
	private function parseManifestCanvasDOM($dom) {
		$this->clearDb($this->dom, $this->id);
		if (!empty($dom)) {
			foreach($dom->children() as $bundle) {
				if (!$this->sql->query("
					INSERT INTO `{$this->dom}`
					(
						`canvashack`,
						`selector`,
						`event`,
						`action`
					) VALUES (
						'{$this->id}',
						'" . $this->sql->real_escape_string($bundle->selector) . "',
						'" . $this->sql->real_escape_string($bundle->event) . "',
						'" . $this->sql->real_escape_string($bundle->action) . "'
					)
				")) {
					// TODO wording could be improved
					throw new CanvasHack_Exception(
						"Could not insert DOM entry for {$this->id}: " . $dom->asXml() . PHP_EOL . $this->sql->error,
						CanvasHack_Exception::SQL
					);
				}
			}
		}
	}
	
	private function parseManifestCanvas($canvas) {
		$this->parseManifestCanvasPages($canvas->pages);
		$this->parseManifestCanvasDOM($canvas->dom);
	}
	
	private function parseManifestComponents($components) {
		$this->parseManifestCSS($components->css);
		$this->parseManifestJavascript($components->javascript);
		$this->parseManifestCanvas($components->canvas);
	}
	
	private function required($field, $value) {
		if (!empty($value)) {
			$this->$field = (string) $value;
		} else {
			throw new CanvasHack_Exception(
				"`$field` is required and was not found in the manifest",
				CanvasHack_Exception::REQUIRED
			);
		}
	}
	
	private function optional($field, $value) {
		if (isset($value)) {
			$this->$field = (string) $value;
		} else {
			$this->$field = null;
		}
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getAbstract() {
		if (empty($this->abstract)) {
			return $this->description;
		} else {
			return $this->abstract;
		}
	}
	
	public function getDescription() {
		if (empty($this->description)) {
			return $this->abstract;
		} else {
			return $this->description;
		}
	}
	
	public function isEnabled() {
		$result = $this->sql->query("
			SELECT * FROM `{$this->table}` WHERE `id` = '{$this->id}'
		");
		$row = $result->fetch_assoc();
		return (isset($row['enabled']) && $row['enabled']);
	}
	
	public function enable() {
		$this->sql->query("
			UPDATE `{$this->table}` SET `enabled` = '1' WHERE `id` = '{$this->id}'
		");
	}
	
	public function disable() {
		$this->sql->query("
			UPDATE `{$this->table}` SET `enabled` = '0' WHERE `id` = '{$this->id}'
		");
	}
	
	/**
	 * Helper function to insert/update into a SQL table
	 * 
	 * @param string $table
	 * @param string $id CanvasHack identifier
	 * @param array $fields
	 **/
	private function updateDb($table, $id, $fields, $idKey = 'canvashack') {
		$response = $this->sql->query("
			SELECT *
				FROM `$table`
				WHERE
					`$idKey` = '$id'
				LIMIT 1
		");
		
		$params = array();
		foreach($fields as $field => $value) {
			$params[] = "`$field` = '$value'";
		}
		
		if ($response->num_rows > 0) {
			if (!$this->sql->query("
				UPDATE `$table`
					SET " .
					implode(', ', $params) .
				"WHERE
					`$idKey` = '$id'
			")) {
				throw new CanvasHack_Exception(
					"Could not update `$table` with `$idKey` = '$id' and fields `$params'. " . $this->sql->error,
					CanvasHack_Exception::SQL
				);
			}
		} else {
			$fields[$idKey] = $this->id;
			if (!$this->sql->query("
				INSERT INTO `$table`
				(`" . implode('`, `', array_keys($fields)) . "`)
				VALUES
				('" . implode("', '", $fields) . "')
			")) {
				throw new CanvasHack_Exception(
					"Could not insert a new row into `$table` with `$idkey` = '$id' and fields `$params'. " . $this->sql->error,
					CanvasHack_Exception::SQL
				);
			}
		}
	}
	
	private function clearDb($table, $id, $idKey = 'canvashack') {
		if (!$this->sql->query("
			DELETE FROM `$table`
				WHERE
					`$idKey` = '$id'
		")) {
			throw new CanvasHack_Exception(
				"Could not clear `$table` of `$idKey` = '$id' entries. " . $this->sql->error,
				CanvasHack_Exception::SQL
			);
		}
	}
}

/**
 * All exceptions thrown by CanvasHack
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class CanvasHack_Exception extends \Exception {
	const MANIFEST = 1;
	const SQL = 2;
	const REQUIRED = 3;
	const ID = 4;
}
	
?>