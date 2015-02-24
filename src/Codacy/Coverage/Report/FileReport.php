<?php

namespace Codacy\Coverage\Report;

class FileReport
{

    private $_total;
    private $_fileName;
    private $_lineCoverage; // array (line -> hits) of type [int -> int]

    public function __construct($total, $fileName, $lineCoverage)
    {
        $this->_total  = $total;
        $this->_fileName = $fileName;
        $this->_lineCoverage = $lineCoverage;
    }

    public function getTotal()
    {
        return $this->_total;
    }
    public function getFileName()
    {
        return $this->_fileName;
    }
    public function getLineCoverage()
    {
        return $this->_lineCoverage;
    }
}