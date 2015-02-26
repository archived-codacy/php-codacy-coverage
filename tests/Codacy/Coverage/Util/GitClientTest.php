<?php

use Codacy\Coverage\Util\GitClient;

class GitClientTest extends PHPUnit_Framework_TestCase
{
    public function testGetHashOfLastCommit()
    {
        $g = new GitClient(getcwd());
        $hash = $g->getHashOfLatestCommit();
        $this->assertEquals(40, strlen($hash));
    }
}