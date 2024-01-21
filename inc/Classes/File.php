<?php

namespace LanSuite;

use Symfony\Component\Filesystem\Filesystem;

class File {


    private string $_fileName='';
    private string $_filePath='';
    private Filesystem $_fileSystem;

    /**
     * 
     * 
     * 
     */
    public function __construct($filePath)
    {
        $this->_filePath = $filePath;
        $this->_fileSystem = new Filesystem;
    }

    /**
     * outputs file content when access OK
     * 
     * @param string $filePath The relative path to the file to be output
     * @throws Exception if path is not below allowed path
     */
    public function outputFileContent(): void
    {
            readfile($this->_filePath);
    }

    public function rename($newFileName){

        //make sure we just change the filename

    }

    public function delete() :void
    {

    }

    public function exists() :bool 
    {

    }

}