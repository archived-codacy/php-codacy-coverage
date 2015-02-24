<?php

use Codacy\Coverage\Parser\CloverParser;
use Codacy\Coverage\Config;
use Codacy\Coverage\Parser\PhpUnitXmlParser;

class ParserTest extends PHPUnit_Framework_TestCase {

    public function testCanParsePhpUnitXmlReport()
    {
        Config::loadConfig(); //TODO: How can this be run automatically?
        $p = new PhpUnitXmlParser(Config::$projectRoot . '/tests/res/phpunitxml/index.xml');
        $report = $p->makeReport();
        $this->assertEquals(50, $report->getTotal());
    }
	
	public function testCanParseCloverXmlWithoutProject()
    {
		$this->_canParseClover(Config::$projectRoot . '/tests/res/clover/clover.xml');
	}
	
	public function testCanParseCloverXmlWithProject()
    {
		$this->_canParseClover(Config::$projectRoot . '/tests/res/clover/clover_without_packages.xml');
	}

    private function _canParseClover($path)
    {
		$p = new CloverParser($path);
		$report = $p->makeReport();
		$this->assertEquals(38, $report->getTotal());
		$this->assertEquals(5, sizeof($report->getFileReports()));
		
		$parserFileReport = $report->getFileReports()[0];
		$coverageReportFileReport = $report->getFileReports()[1];
		
		$this->assertEquals(33, $parserFileReport->getTotal());
		$this->assertEquals(33, $coverageReportFileReport->getTotal());
		
		$parserFileName = $parserFileReport->getFileName();	
		
		$reportFileName = $coverageReportFileReport->getFileName();
				
		$this->assertEquals("src/Codacy/Coverage/Parser/Parser.php", $parserFileName);
		$this->assertEquals("src/Codacy/Coverage/Report/CoverageReport.php", $reportFileName);
	}
}