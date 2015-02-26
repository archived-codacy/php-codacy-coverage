<?php

use Codacy\Coverage\Util\Config;
use Codacy\Coverage\Util\JsonProducer;
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
        $this->assertJsonStringEqualsJsonString($json, $jsonFile);
    }
}