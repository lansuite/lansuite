<?php

namespace LanSuite;

/**
 * Class File
 * Generic class for file accessor functions
 */

class File
{
    private static bool $_BASEPATH_INITIALISED = false;
    private static string $_LS_BASEPATH;
    private string $_intermediatePath;

    public function __construct()
    {
        //Ensure basepath is set on first instantiation
        if (!self::$_BASEPATH_INITIALISED) {
            self::$_LS_BASEPATH = realpath(__DIR__ . '..'. DIRECTORY_SEPARATOR . '..'); //move down from /inc/classes/ to LS root
            self::$_BASEPATH_INITIALISED = true;
        }
    }

    /**
     * Builds a file path with the relative path and checks for path traversals
     * 
     * @var    string $relativePath the relative path to be accessed
     * @return string|bool The full path or false if attempted directory traversal
     */
    public function getFullPath($relativePath) :string|bool 
    {

        $path = realpath(self::$_LS_BASEPATH . $this->_intermediatePath . $relativePath);
        //ensure that resulting path is still below the base path, return false if not
        if (str_starts_with($path, self::$_LS_BASEPATH . $this->_intermediatePath)) {
            return $path;
        } else {
            return false;
        }
    }

    /**
     * Sets a relative path on top of the basepath for any following operation
     * 
     * @var    string $intermediatePath a path to be added on top of basepath
     * @return bool true if set, false if not secure
     */
    public function setIntermediatePath($intermediatePath) 
    {
        //ensure path ends with system directory separator to prevent directory change
        if (!str_ends_with($intermediatePath, DIRECTORY_SEPARATOR)) {
            $intermediatePath .= DIRECTORY_SEPARATOR;
        }
        //ensure new path is not below basepath
        $path = realpath(self::$_LS_BASEPATH . $intermediatePath);
        if (str_starts_with($path, self::$_LS_BASEPATH)) {
            $this->_intermediatePath = $intermediatePath;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns relative path 
     * 
     * @return string the combination of base + intermediate path
     */
    public function getRelativePath() 
    {
        return self::$_LS_BASEPATH . $this->_intermediatePath;
    }    

    /**
     * Returns value of relative path
     * 
     * @return string the intermediate path set on top of the base path
     */
    public function getIntermediatePath() 
    {
        return $this->_intermediatePath;
    }

    /**
     * Returns value of base path
     * 
     * @return string the base path set on top of the base path
     */
    public function getBasePath() 
    {
        return self::$_LS_BASEPATH;
    }


}