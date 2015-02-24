<?php

namespace Codacy\Coverage;

class Config
{
    
    static $projectRoot;
    static $coverageFormat;
    static $phpUnitXmlDir;

    /**
     * Get setting from conf.ini and store them so they
     * are available throughout the project.
     */
    public static function loadConfig()
    {
        $conf = parse_ini_file("/home/jacke/Desktop/codacy-php/conf.ini");
        self::$projectRoot = $conf["projectRoot"];
        self::$coverageFormat = $conf["coverageFormat"];
        self::$phpUnitXmlDir = $conf["phpunitXmlDir"];
    }
    
    private function __construct()
    {
        // There shall only be one!
    }
}