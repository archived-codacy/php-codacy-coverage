<?php

namespace Codacy\Coverage\Parser;



use Codacy\Coverage\Util\JsonProducer;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Note: This test can only be executed once the tests have run once.
     */
    public function testParsersProduceSameResult() {

        if (file_exists('build/logs/clover.xml') && file_exists('build/coverage-xml/index.xml')) {

            $cloverParser = new CloverParser('build/logs/clover.xml');
            $xunitParser = new PhpUnitXmlParser('build/coverage-xml/index.xml');

            $jsonProducer = new JsonProducer();

            $jsonProducer->setParser($cloverParser);

            $cloverJson = $jsonProducer->makeJson();

            $jsonProducer->setParser($xunitParser);

            $xunitJson = $jsonProducer->makeJson();

            $this->assertJsonStringEqualsJsonString($cloverJson, $xunitJson);
        }

    }
}