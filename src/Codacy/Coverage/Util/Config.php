<?php

namespace Codacy\Coverage\Util;

/**
 * Class Config
 * @package Codacy\Coverage
 * @author Jakob Pupke <jakob.pupke@gmail.com>
 */
class Config
{
    
    static $projectRoot;
    static $coverageFormat;
    static $phpUnitXmlDir;
    static $projectToken;

    /**
     * Gets settings from conf.ini and stores them so they
     * are available throughout the project.
     */
    public static function loadConfig()
    {
        //TODO: Get rid of hardcoded path
        $conf = parse_ini_file("/home/jacke/Desktop/codacy-php/conf.ini");
        self::$projectRoot = $conf["projectRoot"];
        self::$coverageFormat = $conf["coverageFormat"];
        self::$phpUnitXmlDir = $conf["phpunitXmlDir"];
        self::$projectToken = $conf["projectToken"];
    }
    
    private function __construct()
    {
        // There shall only be one!
    }
}