<?php

use Codacy\Coverage\Util\Config;
use Codacy\Coverage\Util\JsonProducer;
use Codacy\Coverage\Parser\CloverParser;

class JsonProducerTest extends PHPUnit_Framework_TestCase
{
    public function testCanProduceCorrectJson()
    {
        $parser = new CloverParser("tests/res/clover/clover.xml");
        $jsonProducer = new JsonProducer();
        $jsonProducer->setParser($parser);
        $json = $jsonProducer->makeJson();
        $jsonFile = file_get_contents("tests/res/clover/clover.json");
        $this->assertJsonStringEqualsJsonString($json, $jsonFile);
    }
}