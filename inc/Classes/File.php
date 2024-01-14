<?php

namespace LanSuite;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
/**
 * Class File
 * Generic class for file accessor functions
 */

class File
{
    private static string $_basePath='';
    private string $_relativePath='';
    private Filesystem $_fileSystem;

    /**
     * Constructor sets base path and initializes FS object
     * 
     * @param Filesystem $fs Filesystem-object for dependency injection
     */
    public function __construct(Filesystem $fs = new Filesystem)
    {
        $this->_fileSystem = $fs;
        self::$_basePath = Path::canonicalize(ROOT_DIRECTORY);
        if (!str_ends_with(self::$_basePath, '/')) {
            self::$_basePath .='/';
        }
    }

    /**
     * Builds a file path with the relative path and checks for path traversals
     * 
     * @param  string $path the relative path to be accessed
     * @return string|bool The full path or false if attempted directory traversal
     */
    public function getFullPath($path) :string|bool 
    {

        $path = Path::canonicalize(self::$_basePath . $this->_relativePath . $path);
        //ensure that resulting path is still below the base path, return false if not
        if (str_starts_with($path, self::$_basePath . $this->_relativePath)) {
            return $path;
        } else {
            return false;
        }
    }

    /**
     * Sets a relative path on top of the basepath for any following operation
     * 
     * @param  string $relativePath a path to be added on top of basepath
     * @return bool true if set, false if not secure
     */
    public function setRelativePath($relativePath) 
    {
        //ensure new path is not below basepath
        $path = Path::join(self::$_basePath, $relativePath);
        if (str_starts_with($path, self::$_basePath)) {
            $this->_relativePath = $relativePath;
            return true;
        } else {
            return false;
        }
    }

    /**
     * outputs file content when access OK
     * 
     * @param string $filePath The relative path to the file to be output
     */
    public function outputFileContent(string $filePath): void
    {
        $fullPath = $this->getFullPath($filePath);
        if ($fullPath && $this->_fileSystem->exists($fullPath)) {
            readfile($fullPath);
        }
        return;
    }

    /**
     * Checks if a file exists and is accessible based on the path
     * 
     * @param  string $filePath The path of the file relative to path set in class
     * @return bool true if accessible, false if not
     */

    public function exists(string $filePath): bool
    {
        $fullPath = $this->getFullPath($filePath);
        return $this->_fileSystem->exists($fullPath);
    }

    /**
     * Returns relative path 
     * 
     * @return string the combination of base + relative path
     */
    public function getCurrentPath() 
    {
        return self::$_basePath . $this->_relativePath;
    }    

    /**
     * Returns value of relative path
     * 
     * @return string the relative path set on top of the base path
     */
    public function getRelativePath() 
    {
        return $this->_relativePath;
    }

    /**
     * Returns value of base path
     * 
     * @return string the base path set on top of the base path
     */
    public function getBasePath() 
    {
        return self::$_basePath;
    }


}