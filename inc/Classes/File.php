<?php
namespace LanSuite;

class File {


    private string $_filePath='';

    /**
     * Base constructor
     */
    public function __construct(string $filePath)
    {
        $this->_filePath = $filePath;
    }

    /**
     * Outputs file content when access OK
     * 
     * @param string $filePath The relative path to the file to be output
     * @throws Exception if path is not below allowed path
     */
    public function outputFileContent(): void
    {
            readfile($this->_filePath);
    }

    public function rename($newFileName)
    {

        //make sure we just change the filename
    }

    /**
     * Removes the file represented by the object
     */
    public function delete() :void
    {
        unlink($this->_filePath);
    }

    /**
     * Checks if target is existing and a file
     * 
     * @return bool true if existing and file, false otherwise
     */
    public function exists() :bool
    {
        return is_file($this->_filePath);
    }

}