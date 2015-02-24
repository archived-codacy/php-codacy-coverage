<?php

namespace Codacy\Coverage\Parser;

/**
 * 
 * @author Jakob Pupke
 *
 */
interface IParser
{
    /**
     * Takes the SimpleXmlElement member object and produces
     * a CoverageReport object
     */
    public function makeReport();

}