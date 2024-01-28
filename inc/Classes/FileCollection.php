<?php
namespace LanSuite;

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
    private array $_files=[];

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
     * Constructs FileColletion with a given set of paths
     * 
     * @param array $filePaths paths to files 
     * @return FileCollection A FileCollection with the most common
     */
    public static function constructCollection(array $filePaths) :FileCollection
    {
        $fileCollection = new FileCollection();
        $commonPath = Path::getLongestCommonBasePath(...$filePaths);
        $relativePath = Path::makeRelative($commonPath, FileCollection::$_basePath);
        $fileCollection->setRelativePath($relativePath);
        $fileArray = [];
        foreach ($filePaths as $file) {
            $fileArray[] = Path::makeRelative($file, $commonPath);
        }
        $fileCollection->_files = $fileArray;
        return $fileCollection;
    }

    public function scanDir($path='')
    {
        //scan a given path or base+rel-dir for files and return file objects
    }

     /**
     * Returns an initialzied Handler for the file provided
     * 
     */
    public function getFileHandle($filePath) :File
    {
        return new File($this->getFullPath($filePath));
    }
    
    public function getAllFileHandles() :array
    {
        $fileObjs=[];
        foreach ($this->_files as $fPath){
            $fileObjs[] = new File($this->getFullPath($fPath));
        }
        return $fileObjs;
    }
    /**
     * Function to be called once during LS initalisation to set base path for the installation
     * 
     * @param string $basePath 
     * @return void
     */
    private static function setBasePath($basePath) :void
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
     * @throws InvalidArgumentException if path is below allowed path
     * @return string The full path built
     */
    public function getFullPath($path) :string 
    {

        $path = Path::join(self::$_basePath, $this->_relativePath, $path);
        //ensure that resulting path is still below the base path, return false if not
        if (str_starts_with($path, Path::join(self::$_basePath, $this->_relativePath))) {
            if (is_dir($path) && !str_ends_with('$path', '/')) {
                $path .= '/';
            }
            return $path;
        } else {
            throw new \InvalidArgumentException('Invalid Path specified: ' .$path);
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
        //ensure path ends with directory separator
        if (str_starts_with($relativePath, '/')) {
            $relativePath = substr($relativePath, 1);
        }
        $relativePath = Path::canonicalize($relativePath);
        if (!str_ends_with($relativePath, '/')) {
            $relativePath .= '/';
        }
        $path = Path::join(self::$_basePath, $relativePath);

        //ensure new path is not below basepath
        if (str_starts_with($path, self::$_basePath)) {
            $this->_relativePath = $relativePath;
            return true;
        } else {
            return false;
        }
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