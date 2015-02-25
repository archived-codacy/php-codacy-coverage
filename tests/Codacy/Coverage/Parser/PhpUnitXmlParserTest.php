<?php

use Codacy\Coverage\Config;
use Codacy\Coverage\Parser\PhpUnitXmlParser;

class PhpUnitXmlParserTest extends PHPUnit_Framework_TestCase
{


    public function testThrowsExceptionOnWrongPath()
    {
        $this->setExpectedException('InvalidArgumentException');
        $p = new PhpUnitXmlParser("/home/foo/bar/baz/m.xml");
    }

    public function testCanParsePhpUnitXmlReport()
    {
        Config::loadConfig(); //TODO: How can this be run automatically prior to every test?

        $p = new PhpUnitXmlParser(Config::$projectRoot . '/tests/res/phpunitxml/index.xml');
        $report = $p->makeReport();

        $this->assertEquals(50, $report->getTotal());
        $this->assertEquals(5, sizeof($report->getFileReports()));

        $parserFileReport = $report->getFileReports()[0];
        $coverageReportFileReport = $report->getFileReports()[1];

        $this->assertEquals(100, $parserFileReport->getTotal());
        $this->assertEquals(75, $coverageReportFileReport->getTotal());

        $lineCoverage = $report->getFileReports()[1]->getLineCoverage();
        $expLineCoverage = array(11 => 2, 12 => 2, 13 => 2, 16 => 2, 19 => 2, 30 => 2, 31 => 2, 32 => 2, 33 => 2);
        $this->assertEquals($lineCoverage, $expLineCoverage);

        $parserFileName = $parserFileReport->getFileName();

        $reportFileName = $coverageReportFileReport->getFileName();

        $this->assertEquals("src/Codacy/Coverage/Parser/Parser.php", $parserFileName);
        $this->assertEquals("src/Codacy/Coverage/Report/CoverageReport.php", $reportFileName);
    }

}