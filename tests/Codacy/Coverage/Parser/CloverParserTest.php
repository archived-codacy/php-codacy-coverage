<?php

use Codacy\Coverage\Parser\CloverParser;
use Codacy\Coverage\Config;

class CloverParserTest extends PHPUnit_Framework_TestCase
{


    public function testThrowsExceptionOnWrongPath()
    {
        $this->setExpectedException('InvalidArgumentException');
        $p = new CloverParser("/home/foo/bar/baz/m.xml");
    }

    /**
     * Testing against the clover coverage report 'tests/res/clover/clover.xml'
     */
    public function testCanParseCloverXmlWithoutProject()
    {
        Config::loadConfig(); //TODO: How can this be run automatically prior to every test?
		$this->_canParseClover(Config::$projectRoot . '/tests/res/clover/clover.xml');
	}

    /**
     * Testing against the clover coverage report 'tests/res/clover/clover.xml'
     */
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

        $lineCoverage = $report->getFileReports()[1]->getLineCoverage();
        $expLineCoverage = array(11 => 1, 12 => 1, 13 => 1, 16 => 1);
        $this->assertEquals($lineCoverage, $expLineCoverage);
				
		$this->assertEquals("src/Codacy/Coverage/Parser/Parser.php", $parserFileName);
		$this->assertEquals("src/Codacy/Coverage/Report/CoverageReport.php", $reportFileName);
	}
}