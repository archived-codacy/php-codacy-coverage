<?php

namespace Codacy\Coverage\Parser;

use Codacy\Coverage\Report\FileReport;
use Codacy\Coverage\Report\CoverageReport;
use Codacy\Coverage\Config;

/**
 * Parses XML file, result of phpunit --coverage-xml, and produces
 * a CoverageReport object. The challenging problem here is that
 * the report is scattered over different files. Basic information
 * can be parsed from the index.xml file. But the relevant information
 * for each file is stored in individual files.
 * @package Codacy\Coverage\Parser
 * @author Jakob Pupke <jakob.pupke@gmail.com>
 */
class PhpUnitXmlParser extends XMLParser implements IParser
{

    /**
     * Extracts basic information about coverage report
     * from the root xml file (index.xml).
     * For line coverage information about the files it has
     * to parse each individual file. This is handled by
     * _getLineCoverage() private method.
     * @see \Codacy\Coverage\Parser\IParser::makeReport()
     * @return CoverageReport $report The CoverageReport object
     */
    public function makeReport()
    {
        //we can get the report total from the first directory summary.
        $reportTotal = $this->_getTotalFromPercent($this->element->project->directory->totals->lines["percent"]);
        $fileReports = array();
        foreach ($this->element->project->directory->file as $file) {
            $fileName = $this->_getRelativePath($file["href"]);
            
            $xmlFileHref = (string) $file["href"];
            $base = Config::$projectRoot . "/" . Config::$phpUnitXmlDir . "/";

            // get the corresponding xml file to get lineCoverage information.
            if (file_exists($base . $xmlFileHref)) {
                $fileXml = simplexml_load_file($base . $xmlFileHref);
            } else {
                throw new \InvalidArgumentException(
                    "Error: Cannot read XML file. Please check config.ini.
                    Is phpUnitXmlDir properly set? Using: " . Config::$phpUnitXmlDir
                );
            }

            $fileTotal = $this->_getTotalFromPercent($fileXml->file->totals->lines["percent"]);
            $lineCoverage = $this->_getLineCoverage($fileXml);
            $fileReport = new FileReport($fileTotal, $fileName, $lineCoverage);
            array_push($fileReports, $fileReport);
        }
        $report = new CoverageReport($reportTotal, $fileReports);
        return $report;
    }
    
    /**
     * Iterates all <line></line> nodes and produces an array holding line coverage information.
     * @param \SimpleXMLElement $node The XML node holding the <line></line> nodes
     * @return array: (lineNumber -> hits)
     */
    private function _getLineCoverage(\SimpleXMLElement $node)
    {
        $lineCoverage = array();
        foreach ($node->file->coverage->line as $line) {
            //TODO: Is this the correct way to get nr of hits?
            $count = $line->covered->count();
            if ($count > 0) {
                $nr = (string) $line["nr"];
                $lineCoverage[$nr] = $count;
            }
        }
        return $lineCoverage;
    }
    
    /**
     * Gets Integer from percent. Example: 95.00% -> 95
     * @param \SimpleXMLElement $percent The percent attribute of the node
     * @return int number The according integer
     */
    private function _getTotalFromPercent(\SimpleXMLElement $percent)
    {
        $percent = (string) $percent;
        $percent = explode("%", $percent)[0];
        return round($percent);
    }
    
    /**
     * The phpUnit Xml Coverage format only saves the filename without
     * path. We can get the filename from the href attribute though.
     * @param \SimpleXMLElement $fileName The href attribute of the <file></file> node.
     * @return string The relative path of the file, that is, relative to project root.
     */
    private function _getRelativePath(\SimpleXMLElement $fileName) 
    {
        $proj_root = Config::$projectRoot;
        $length = strlen($proj_root);
        // remove .xml and convert to string
        $absoluteFileName = substr((string) $fileName, 0, -4);
        return substr($absoluteFileName, $length);
    }
}