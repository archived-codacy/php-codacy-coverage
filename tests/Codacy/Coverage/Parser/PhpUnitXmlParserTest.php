<?php

use Codacy\Coverage\Parser\PhpUnitXmlParser;

class PhpUnitXmlParserTest extends PHPUnit_Framework_TestCase
{


    public function testThrowsExceptionOnWrongPath()
    {
        $this->setExpectedException('InvalidArgumentException');
        $p = new PhpUnitXmlParser("/home/foo/bar/baz/fake.xml");
    }

    /**
     * Testing against the coverage report in 'tests/res/phpunitxml'
     */
    public function testCanParsePhpUnitXmlReport()
    {

        $p = new PhpUnitXmlParser('tests/res/phpunitxml/index.xml');
        $report = $p->makeReport();

        $this->assertEquals(69, $report->getTotal());
        $this->assertEquals(10, sizeof($report->getFileReports()));

        $ConfigFileReport = $report->getFileReports()[2];
        $cloverParserFileReport = $report->getFileReports()[4];

        $this->assertEquals(86, $ConfigFileReport->getTotal());
        $this->assertEquals(95, $cloverParserFileReport->getTotal());

        $lineCoverage = $ConfigFileReport->getLineCoverage();
        $expLineCoverage = array(24 => 4, 25 => 4, 26 => 4, 27 => 4, 28 => 4, 29 => 4);
        $this->assertEquals($lineCoverage, $expLineCoverage);

        $configFileName = $ConfigFileReport->getFileName();

        $cloverParserFileName = $cloverParserFileReport->getFileName();

        $this->assertEquals("src/Codacy/Coverage/Config.php", $configFileName);
        $this->assertEquals("src/Codacy/Coverage/Parser/CloverParser.php", $cloverParserFileName);
    }

}