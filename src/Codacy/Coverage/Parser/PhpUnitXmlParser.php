<?php

namespace Codacy\Coverage\Parser;

use Codacy\Coverage\Parser\IParser;
use Codacy\Coverage\Report\FileReport;
use Codacy\Coverage\Report\CoverageReport;
use Codacy\Coverage\Config;

class PhpUnitXmlParser implements IParser
{
    
    public function __construct($path) 
    {
        $this->element = simplexml_load_file($path) or die("Error: Cannot create object from XML file. Check file path! Path: ". $path);
    }
    
    public function makeReport()
    {
        //we can get the report total from the first directory summary.
        $reportTotal = $this->_getTotalFromPercent($this->element->project->directory->totals->lines["percent"]);
        $fileReports = array();
        foreach ($this->element->project->directory->file as $file) {
            $fileName = $this->_cutFileName($file["href"]);
            
            $xmlFileHref = (string) $file["href"];
            $base = "/home/jacke/Desktop/codacy-php/phpunit-xml/";
            // get the corresponding xml file.
            $fileXml = simplexml_load_file($base . $xmlFileHref);
            $fileTotal = $this->_getTotalFromPercent($fileXml->file->totals->lines["percent"]);
            $lineCoverage = array();
            foreach($fileXml->file->coverage->line as $line) {
                $count = $line->covered->count();
                if($count > 0) {
                    $nr = (string) $line["nr"];
                    $lineCoverage[$nr] = $count;
                }
            }
            $fileReport = new FileReport($fileTotal, $fileName, $lineCoverage);
            array_push($fileReports, $fileReport);
        }
        $report = new CoverageReport($reportTotal, $fileReports);
        return $report;
    }
    
    private function _getTotalFromPercent(\SimpleXMLElement $percent)
    {
        $percent = (string) $percent;
        $percent = explode("%", $percent)[0];
        return round($percent);
    }
    
    /**
     * Cut the file name so we have relative path to projectRoot
     */
    private function _cutFileName(\SimpleXMLElement $fileName) 
    {
        $proj_root = Config::$projectRoot;
        $length = strlen($proj_root);
        $fileName = substr((string) $fileName, 0, -4); // remove .xml
        return substr($fileName, $length);
    }
}