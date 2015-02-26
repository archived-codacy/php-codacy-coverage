<?php

namespace Codacy\Coverage;

require '../../../vendor/autoload.php';

use Codacy\Coverage\Parser\CloverParser;
use Codacy\Coverage\Parser\PhpUnitXmlParser;
use Codacy\Coverage\Util\CodacyApiClient;
use Codacy\Coverage\Util\GitClient;
use Codacy\Coverage\Util\JsonProducer;
use Codacy\Coverage\Util\Config;

class CodacyPhpCoverage
{

    static function doIt() 
    {


        Config::loadConfig();

        $jsonProducer = new JsonProducer();
        switch (Config::$coverageFormat){
            
        case "clover":
            $parser = new CloverParser(
                Config::$projectRoot . "/build/logs/clover.xml"
            );
            break;
            
        case "phpunit-xml":
            $parser = new PhpUnitXmlParser(
                Config::$projectRoot . "/" . Config::$phpUnitXmlDir . "/index.xml"
            );
            break;
            
        default:
            throw new \InvalidArgumentException(
                'No valid coverage format configuration found. 
                		Please set it in conf.ini.'
            );
        }
            
        $jsonProducer->setParser($parser);
        print_r($jsonProducer->makeJson());

        /*

        // JUST A TEST


        $gClient = new GitClient(Config::$projectRoot);

        $data = file_get_contents("/home/jacke/Desktop/phpCoverageTest/test.json");

        //print_r($gClient->getHashOfLatestCommit() . "\n");
        //print_r(Config::$projectToken  . "\n");
        $token = Config::$projectToken;
        $commit = $gClient->getHashOfLatestCommit();
        $baseUrl = "https://www.codacy.com";

        $url = $baseUrl . "/api/coverage/" . $token . "/" . $commit;

        ApiClient::postData("http://requestb.in/p8zjyhp9", "fizz=buzz");
        */

    }
}

CodacyPhpCoverage::doIt();