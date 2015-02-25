<?php

use Codacy\Coverage\Config;
use Codacy\Coverage\Git\GitClient;

class GitClientTest extends PHPUnit_Framework_TestCase
{
    public function testGetHashOfLastCommit()
    {
        Config::loadConfig();
        $g = new GitClient(Config::$projectRoot);
        $hash = $g->getHashOfLatestCommit();
        $this->assertEquals(40, strlen($hash));
    }
}