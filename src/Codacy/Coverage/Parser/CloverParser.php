<?php

namespace Codacy\Coverage\Parser;

use Codacy\Coverage\Report\FileReport;
use Codacy\Coverage\Report\CoverageReport;
use Codacy\Coverage\Parser\IParser;
use Codacy\Coverage\Config;

/**
 * Takes clover.xml as source element
 * Like here https://github.com/sebastianbergmann/php-code-coverage
 */
class CloverParser implements IParser
{
    
    private $_element;
    
    public function __construct($path) 
    {
        $this->_element = simplexml_load_file($path) or 
        die("Error: Cannot create object from XML file. Check file path!");
    }
    
    public function makeReport() 
    {
        $project = $this->_element->project;
        $projectMetrics = $project->metrics;
        $coveredStatements = intval($projectMetrics['coveredstatements']);
        $statementsTotal = intval($projectMetrics['statements']);
        $reportTotal = round(($coveredStatements / $statementsTotal) * 100);
        $fileReports = $this->_makeFileReports($project, array());
        $report = new CoverageReport($reportTotal, $fileReports);
        return $report;
    }
    
    private function _makeFileReports(\SimpleXMLElement $project, $fileReports) 
    {
        /*
        * Most clover reports will have project/package/file/line xPath.
        * But there could be files that are not part of any package, i.e files that 
        * that do not declare namespace.
        */
        if($project->file->count() > 0) {
            // so there is a file without project
            $fileReports = $this->_makeFileReportsFromFiles($project->file, $fileReports);
        }
        if($project->package->count() > 0) {
            $fileReports = $this->_makeFileReportsFromPackages($project->package, $fileReports);
        }
        return $fileReports;
    }
    
    private function _makeFileReportsFromFiles(\SimpleXMLElement $files, $fileReports) 
    {
        foreach ($files as $file) {
            // iterate files in the package
            $countStatement = intval($file->metrics['statements']);
            $countCoveredStatements = intval($file->metrics['coveredstatements']);
            if($countStatement == 0) {
                $fileTotal = 0;
            } else {
                $fileTotal = round(($countCoveredStatements / $countStatement) * 100);
            }
            $fileName = $this->_cutFileName($file['name']);
            $lineCoverage = array();
            foreach ($file as $f) { 
                // iterate all lines in that file
                if($f['type'] == 'stmt' && intval($f['count']) > 0 ) { 
                    // https://github.com/satooshi/php-coveralls#cloverxml
                    $lineNr = (string) $f['num'];
                    $hit = (string) $f['count'];
                    $lineCoverage[$lineNr] = $hit;
                }
            }
            $fileReport = new FileReport($fileTotal, $fileName, $lineCoverage);
            array_push($fileReports, $fileReport);
        }
        return $fileReports;
    }
    
    /**
     * Aggregates data for each file from packages
     */
    private function _makeFileReportsFromPackages(\SimpleXMLElement $packages, $fileReports) 
    {
        foreach ($packages as $package) { // iterate all packages
            $fileReports = $this->_makeFileReportsFromFiles($package, $fileReports);
        }
        return $fileReports;
    }    
    
    /**
     * Cut the file name so we have relative path to projectRoot.
     */
    private function _cutFileName(\SimpleXMLElement $fileName) 
    {
        $proj_root = Config::$projectRoot;
        $length = strlen($proj_root);
        return substr((string) $fileName, $length + 1);
    }
}