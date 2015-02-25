<?php

use Codacy\Coverage\Config;
use Codacy\Coverage\Report\JsonProducer;
use Codacy\Coverage\Parser\CloverParser;

class JsonProducerTest extends PHPUnit_Framework_TestCase
{
    public function testCanProduceCorrectJson()
    {
        Config::loadConfig();
        $p = new CloverParser(Config::$projectRoot . "/tests/res/clover/clover.xml");
        $j = new JsonProducer();
        $j->setParser($p);
        $json = $j->makeJson();
        $jsonFile = file_get_contents(Config::$projectRoot . "/tests/res/clover/clover.json");
        $this->assertEquals($json, $jsonFile);
    }
}