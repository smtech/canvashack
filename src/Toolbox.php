<?php

namespace smtech\CanvasHack;

use mysqli;
use Battis\ConfigXML;
use smtech\LTI\Configuration\Option;

class Toolbox extends \smtech\StMarksReflexiveCanvasLTI\Toolbox
{
    /**
     * Custom Preferences database
     * @var mysqli
     */
    protected $customPrefs;

    /**
     * Configure account navigation placement
     *
     * @return Generator
     */
    public function getGenerator()
    {
        parent::getGenerator();

        $this->generator->setOptionProperty(
            Option::ACCOUNT_NAVIGATION(),
            'visibility',
            'admins'
        );

        return $this->generator;
    }

    /**
     * Update a Toolbox instance from a configuration file
     *
     * @see Toolbox::fromConfiguration() Use `Toolbox::fromConfiguration()`
     *
     * @param  string $configFilePath
     * @param  boolean $forceRecache
     * @return void
     */
    protected function loadConfiguration($configFilePath, $forceRecache = false)
    {
        parent::loadConfiguration($configFilePath, $forceRecache);

        $config = new ConfigXML($configFilePath);

        /* configure database connections */
        $this->setCustomPrefs($config->newInstanceOf(mysqli::class, '/config/customprefs'));
    }

    /**
     * Set MySQL connection object
     *
     * @param mysqli $mysql
     */
    public function setCustomPrefs(mysqli $mysql)
    {
        $this->customPrefs = $mysql;
    }

    /**
     * Get MySQL connection object
     *
     * @return mysqli
     */
    public function getCustomPrefs()
    {
        return $this->customPrefs;
    }

    /**
     * Make a MySQL query
     *
     * @link http://php.net/manual/en/mysqli.query.php Pass-through to `mysqli::query()`
     * @param string $query
     * @param int $resultMode (Optional, defaults to `MYSQLI_STORE_RESULT`)
     * @return mixed
     */
    public function customPrefs_query($query, $resultMode = MYSQLI_STORE_RESULT)
    {
        return $this->getCustomPrefs()->query($query, $resultMode);
    }
}
