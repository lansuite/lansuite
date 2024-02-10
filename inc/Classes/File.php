<?php
namespace LanSuite;

class File {


    private string $filePath='';

    /**
     * Base constructor
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Outputs file content when access OK
     *
     * @throws Exception if path is not below allowed path
     * @return void
     */
    public function outputFileContent(): void
    {
            readfile($this->filePath);
    }

    /**
     * Outputs file content when access OK
     *
     * @param string $filePath The relative path to the file to be output
     * @throws Exception if path is not below allowed path
     * @return void
     */
    public function includeCode(): void
    {
            include_once $this->filePath;
    }

    /**
     * Removes the file represented by the object
     *
     * @return void
     */
    public function delete() :void
    {
        unlink($this->filePath);
    }

    /**
     * Checks if target is existing and a file
     *
     * @return bool true if existing and file, false otherwise
     */
    public function exists() :bool
    {
        return is_file($this->filePath);
    }

}