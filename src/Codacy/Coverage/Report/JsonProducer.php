<?php

namespace Codacy\Coverage\Report;

use Codacy\Coverage\Parser\IParser;

/**
 * Class JsonProducer
 * Can be composed of parsers that implement the IParser interface.
 * @package Codacy\Coverage\Report
 * @author Jakob Pupke <jakob.pupke@gmail.com>
 */
class JsonProducer
{
    /**
     * @var parser object that implements IParser interface
     */
    private $_parser;

    /**
     * Sets the JsonParser's member field
     * @param $parser IParser Any parser class that implements the IParser interface
     */
    public function setParser(IParser $parser)
    {
        $this->_parser = $parser;
    }

    /**
     * Delegates the job to the parser's makeReport() method
     * @return CoverageReport object
     */
    public function makeReport() 
    {
        return $this->_parser->makeReport();
    }
    
    /**
     * Takes a CoverageReport object, the result of makeReport(), and outputs JSON
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