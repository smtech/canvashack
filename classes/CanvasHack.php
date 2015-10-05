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
	
	private $table = 'canvashacks';
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
			$this->parseManifest($manifest);
		} else {
			if (isset($manifest)) {
				throw new CanvasHack_Exception(
					"Manifest file missing, expected $manifest",
					CanvasHack_Exception::MANIFEST
				);
			} else {
				loadManifestEntry($path);
			}
		}
	}
	
	private function loadManifestEntry($id) {
		$response = $this->sql->query("
			SELECT * FROM `{$this->table}` WHERE `id` = '" . $this->sql->real_escape_string($path) . "'
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
				"Manifest database entry missing for $path",
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
		$this->parseManigestComponents($xml->components);
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
		$result = $this->sql->query("
			SELECT *
				FROM `{$this->table}`
				WHERE
					`id` = '{$this->id}'
				LIMIT 1
		");
		if($result->num_rows > 0) {
			if (!$this->sql->query("
				UPDATE `{$this->table}`
					SET
						`name` = '$_name',
						`abstract` = '$_abstract',
						`description` = '$_description'
					WHERE
						`id` = '{$this->id}'
			")) {
				throw new CanvasHack_Exception(
					"Could not update CanvasHack manifest database for {$this->name}.",
					CanvasHack_Exception::SQL
				);
			}
		} else {
			if (!$this->sql->query("
				INSERT INTO `{$this->table}`
				(
					`id`, `name`, `abstract`, `description`
				) VALUES (
					'{$this->id}', '$_name', '$_abstract', '$_description'
				)
			")) {
				throw new CanvasHack_Exception(
					"Could not create CanvasHack manifest database entry for {$this->name}. " . $this->sql->error,
					CanvasHack_Exception::SQL
				);
			}
		}
	}
	
	private function paraseManifestComponents($components) {
		
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
}
	
?>