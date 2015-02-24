<?php

use Codacy\Coverage\Parser\CloverParser;
use Codacy\Coverage\Config;

class ParserTest extends PHPUnit_Framework_TestCase {
	
	public function testCanParseCloverXmlWithoutProject() {
		Config::loadConfig();
		$this->canParse(Config::$projectRoot . '/tests/res/clover/clover.xml');
	}
	
	public function testCanParseCloverXmlWithProject() {
		$this->canParse(Config::$projectRoot . '/tests/res/clover/clover_without_packages.xml');
	}
	
	private function canParse($path){
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