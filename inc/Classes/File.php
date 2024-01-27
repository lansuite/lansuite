<?php
namespace LanSuite;

class File {


    private string $_filePath='';

    /**
     * 
     * 
     * 
     */
    public function __construct(string $filePath)
    {
        $this->_filePath = $filePath;
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
        unlink($this->_filePath);
    }

    public function exists() :bool
    {
        return is_file($this->_filePath);
    }

}