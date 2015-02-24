<?php

namespace Codacy\Coverage;

class Config
{
    
    static $projectRoot;
    static $coverageFormat;
    static $phpUnitXmlDir;
    
    public function loadConfig()
    {
        $conf = parse_ini_file("/home/jacke/Desktop/codacy-php/conf.ini");
        self::$projectRoot = $conf["projectRoot"];
        self::$coverageFormat = $conf["coverageFormat"];
        self::$phpUnitXmlDir = $conf["phpunitXmlDir"];
    }
    
    private function __construct()
    {

    }
}