<?php

namespace Codacy\Coverage;

require '../../../vendor/autoload.php';

use Codacy\Coverage\Parser\CloverParser;
use Codacy\Coverage\Parser\PhpUnitXmlParser;
use Codacy\Coverage\Api\Api;
use Codacy\Coverage\Git\GitClient;
use Codacy\Coverage\Report\JsonProducer;
use Codacy\Coverage\Config;

class CodacyPhpCoverage
{

    static function doIt() 
    {
        Config::loadConfig();
        
        $jsonProducer = new JsonProducer();
        switch (Config::$coverageFormat){
            
        case "clover":
            $parser = new CloverParser(
                Config::$projectRoot . "/tests/res/clover/closver.xml"
            );
            break;
            
        case "phpunit-xml":
            $parser = new PhpUnitXmlParser(
                Config::$projectRoot . "/" . Config::$phpUnitXmlDir . "/inex.xml"
            );
            break;
            
        default:
            throw new \InvalidArgumentException(
                'No valid coverage format configuration found. 
                		Please set it in conf.ini.'
            );
        }
            
        $jsonProducer->setParser($parser);
        print_R($jsonProducer->makeJson());
        
    }
}

CodacyPhpCoverage::doIt();