<?php

namespace Codacy\Coverage\Parser;



use Codacy\Coverage\Util\JsonProducer;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Running against tests/res/phpunit-clover
     */
    public function testParsersProduceSameResult()
    {
        $cloverParser = new CloverParser('tests/res/phpunit-clover/clover.xml');
        $xunitParser = new PhpUnitXmlParser('tests/res/phpunit-clover/index.xml');
        $xunitParser->setDirOfFileXmls('tests/res/phpunit-clover');

        $jsonProducer = new JsonProducer();

        $jsonProducer->setParser($cloverParser);

        $cloverJson = $jsonProducer->makeJson();

        $jsonProducer->setParser($xunitParser);

        $xunitJson = $jsonProducer->makeJson();

        $this->assertJsonStringEqualsJsonString($cloverJson, $xunitJson);
    }
}