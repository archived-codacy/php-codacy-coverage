<?php

namespace Codacy\Coverage\Command;

use Codacy\Coverage\Parser\PhpUnitXmlParser;

/**
 * Class Phpunit
 *
 */
class Phpunit extends Clover {

    protected function configure()
    {
        parent::configure();

        $this
            ->setName("phpunit")
            ->setDescription("Send coverage results in phpunit format");
    }

    protected function getParser($path = null)
    {
        $path = is_null($path) ?
            "build" . DIRECTORY_SEPARATOR . "coverage-xml" :
            $path;
        list($xmlFile, $dir) = is_file($path) ?
            [$path, dirname($path)] :
            [$path . DIRECTORY_SEPARATOR . "index.xml", $path];
        $parser->setDirOfFileXmls($dir);
        return new PhpUnitXmlParser($xmlFile);
    }
}
