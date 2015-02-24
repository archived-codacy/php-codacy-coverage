<?php

namespace Codacy\Coverage\Report;

class CoverageReport
{

    private $_total;
    private $_fileReports; // array of type FileReport

    public function __construct($total, $fileReports)
    {
        $this->_total = $total;
        $this->_fileReports = $fileReports;
    }

    public function getTotal() 
    {
        return $this->_total;
    }
    public function getFileReports() 
    {
        return $this->_fileReports;
    }
}