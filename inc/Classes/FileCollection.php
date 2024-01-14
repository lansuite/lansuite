<?php

namespace LanSuite;

use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
/**
 * Class File
 * Generic class for file accessor functions
 */

class FileCollection
{
    private static string $_basePath='';
    private static bool $_basePathInitialized=false;
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
        //ensure basepath is initialized before use, default to LS ROOT_DIRECTORY
        if (!self::$_basePathInitialized) {
            self::setBasePath(ROOT_DIRECTORY);
        }
    }

    /**
     * Function to be called once during LS initalisation to set base path for the installation
     * 
     * @param string $basePath 
     * @return void
     */
    public static function setBasePath($basePath) :void
    {
        if (!self::$_basePathInitialized) {
            self::$_basePath = PAth::canonicalize($basePath);
            //ensure string ends with directory separator
            if (!str_ends_with(self::$_basePath, '/')) {
                self::$_basePath .='/';
            }
            self::$_basePathInitialized = true;
        }
    }

    /**
     * Builds a file path with the relative path and checks for path traversals
     * 
     * @param  string $path the relative path to be accessed
     * @throws Exception if path is below allowed path
     * @return string|bool The full path or false if attempted directory traversal
     */
    public function getFullPath($path) :string 
    {

        $path = Path::canonicalize(self::$_basePath . $this->_relativePath . $path);
        //ensure that resulting path is still below the base path, return false if not
        if (str_starts_with($path, self::$_basePath . $this->_relativePath)) {
            return $path;
        } else {
            throw new Exception(t('Auf den Pfad kann nicht zugegriffen werden'));
        }
    }

    /**
     * Sets a relative path on top of the basepath for any following operation
     * 
     * @param  string $relativePath a path to be added on top of basepath
     * @return bool true if set, false if not secure
     */
    public function setRelativePath($relativePath) :bool
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
     * @throws Exception if path is not below allowed path
     */
    public function outputFileContent(string $filePath): void
    {
        $fullPath = $this->getFullPath($filePath);
        if ($fullPath && $this->_fileSystem->exists($fullPath)) {
            readfile($fullPath);
        } else {
            throw new Exception(t('Auf die Datei kann nicht zugegriffen werden'));
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