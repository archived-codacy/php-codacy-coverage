<?php

namespace Codacy\Coverage\Parser;

use Codacy\Coverage\Report\FileReport;
use Codacy\Coverage\Report\CoverageReport;
use Codacy\Coverage\Parser\IParser;
use Codacy\Coverage\Config;

/**
 * Parses Clover XML file and produces a CoverageReport object.
 * @package Codacy\Coverage\Parser
 * @author Jakob Pupke <jakob.pupke@gmail.com>
 */
class CloverParser implements IParser
{
    
    private $_element;
    
    /**
     * Construct CloverParser and set the XML object as member field
     * @param string $path Path to XML file
     */
    public function __construct($path) 
    {
        $this->_element = simplexml_load_file($path) or 
        die("Error: Cannot create object from XML file. Check file path!");
    }
    
    /**
     * Extracts basic information about coverage report and delegates
     * more detailed extraction work to _makeFileReports() method.
     * @see \Codacy\Coverage\Parser\IParser::makeReport()
     * @return CoverageReport $report The CoverageReport object
     */
    public function makeReport() 
    {
        $project = $this->_element->project;
        $projectMetrics = $project->metrics;
        $coveredStatements = intval($projectMetrics['coveredstatements']);
        $statementsTotal = intval($projectMetrics['statements']);
        $reportTotal = round(($coveredStatements / $statementsTotal) * 100);
        $fileReports = $this->_makeFileReports($project);
        $report = new CoverageReport($reportTotal, $fileReports);
        return $report;
    }
    
    /**
     * Takes the root \SimpleXMLElement object of the parsed file
     * and decides on how to iterate it to extract information of all 
     * <file...>..</file> nodes.
     * @param \SimpleXMLElement $node the root XML node.
     * @return array holding FileReport objects
     */
    private function _makeFileReports(\SimpleXMLElement $node) 
    {
        $fileReports = array();
        /*
        * Most clover reports will have project/package/file/line xPath.
        * But there could be files that are not part of any package, i.e files that 
        * that do not declare namespace.
        */
        if ($node->file->count() > 0) {
            // so there is a file without package
            $fileReports = $this->_makeFileReportsFromFiles($node->file, $fileReports);
        }
        if ($node->package->count() > 0) {
            $fileReports = $this->_makeFileReportsFromPackages($node->package, $fileReports);
        }
        return $fileReports;
    }
    
    /**
     * Iterates all over all <file...>..</file> nodes.
     * @param \SimpleXMLElement $node The XML node holding the file nodes.
     * @param array $fileReports array of FileReport objects
     * @return array holding FileReport objects
     */
    private function _makeFileReportsFromFiles(\SimpleXMLElement $node, $fileReports) 
    {
        foreach ($node as $file) {
            // iterate files in the package
            $countStatement = intval($file->metrics['statements']);
            $countCoveredStatements = intval($file->metrics['coveredstatements']);
            if ($countStatement == 0) {
                $fileTotal = 0;
            } else {
                $fileTotal = round(($countCoveredStatements / $countStatement) * 100);
            }
            $fileName = $this->_getRelativePath($file['name']);
            $lineCoverage = $this->_getLineCoverage($file);
            $fileReport = new FileReport($fileTotal, $fileName, $lineCoverage);
            array_push($fileReports, $fileReport);
        }
        return $fileReports;
    }
    
    /**
     * Iterates over all <package..>...</package> nodes and calls _makeFileReportsFromFiles on them
     * @param \SimpleXMLElement $node        The XML node holding all <package..>...</package> nodes
     * @param array             $fileReports array of FileReport objects
     * @return array holding FileReport objects
     */
    private function _makeFileReportsFromPackages(\SimpleXMLElement $node, $fileReports) 
    {
        // iterate all packages
        foreach ($node as $package) {
            $fileReports = $this->_makeFileReportsFromFiles($package, $fileReports);
        }
        return $fileReports;
    }
    
    /**
     * Iterates all <line></line> nodes and produces an array holding line coverage information.
     * Only adds lines of type "stmt" and with count greater than 0.
     * @param \SimpleXMLElement $node The XML node holding the <line></line> nodes
     * @return array: (lineNumber -> hits)
     */
    private function _getLineCoverage(\SimpleXMLElement $node)
    {
        $lineCoverage = array();
        foreach ($node as $line) {
            // iterate all lines in that file
            if ($line['type'] == 'stmt' && intval($line['count']) > 0 ) {
                // https://github.com/satooshi/php-coveralls#cloverxml
                $lineNr = (string) $line['num'];
                $hit = (string) $line['count'];
                $lineCoverage[$lineNr] = $hit;
            }
        }
        return $lineCoverage;
    }
    
    /**
     * Cuts the file name so we have relative path to projectRoot.
     * In a clover file file names are saved from / on.
     * We are only interested in relative filename
     * @param \SimpleXMLElement $fileName The filename attribute
     * @return string The relative path of that file
     */
    private function _getRelativePath(\SimpleXMLElement $fileName) 
    {
        $len = strlen(Config::$projectRoot);
        return substr((string) $fileName, $len + 1);
    }
}