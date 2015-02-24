<?php

namespace Codacy\Coverage\Git;

use Gitonomy\Git\Repository;

class GitClient
{
    
    private $_repository;
    
    public function __construct() 
    {
        $this->_repository = new Repository('/home/jacke/Desktop/codacy-php');
    }
    
    public function getHash() 
    {
        $head = $this->_repository->getHeadCommit(); // Commit
        
        echo $head->getHash();
        return $head->getHash();
    
    }
}

