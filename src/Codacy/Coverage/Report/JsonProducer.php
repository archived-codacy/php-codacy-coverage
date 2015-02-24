<?php

namespace Codacy\Coverage\Report;

use Codacy\Coverage\Parser\IParser;

class JsonProducer
{
    
    private $_parser;
    
    public function setParser(IParser $parser) 
    {
        $this->_parser = $parser;
    }
    
    public function makeReport() 
    {
        return $this->_parser->makeReport();
    }
    
    /**
     * Takes a CoverageReport object and outputs JSON
     * @return string the JSON string
     */
    public function makeJson() 
    {
        $report = $this->makeReport();
        $array = array();
        $array['total'] = $report->getTotal();
    
        $fileReportsArray = array();
        $fileReports = $report->getFileReports();
    
        foreach ($fileReports as $fr) {
            $fileArray = array();
            $fileArray['filename'] = $fr->getFileName();
            $fileArray['total']    = $fr->getTotal();
            $fileArray['coverage'] = $fr->getLineCoverage();
    
            array_push($fileReportsArray, $fileArray);
        }
        $array['fileReports'] = $fileReportsArray;
        return json_encode($array, JSON_PRETTY_PRINT);
    }
}