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
    //class constants for default filters
    const SECURITY_BLACKLIST = ['/^\.htaccess$/','/.php.?$/', '/user\.ini$/'];
    const IMAGE_WHITELIST = ['/.*\.(png|jpg|jpeg|webp|gif|bmp|ico)$/'];
    const ARCHIVE_WHITELIST = ['/.*\.(zip|rar)$/'];


    private static string $basePath='';
    private static bool $basePathInitialized=false;
    private string $relativePath='';
    private Filesystem $fileSystem;
    private array $files=[];
    private array $blacklist= fileCollection::SECURITY_BLACKLIST;
    private array $whitelist=[];

    /**
     * Constructor sets base path and initializes FS object
     *
     * @param Filesystem $fs Filesystem-object for dependency injection
     */
    public function __construct(Filesystem $fs = new Filesystem)
    {
        $this->fileSystem = $fs;
        //ensure basepath is initialized before use, default to LS ROOT_DIRECTORY
        if (!self::$basePathInitialized) {
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
        $relativePath = Path::makeRelative($commonPath, FileCollection::$basePath);
        $fileCollection->setRelativePath($relativePath);
        $fileArray = [];
        foreach ($filePaths as $file) {
            $fileArray[] = Path::makeRelative($file, $commonPath);
        }
        $fileCollection->files = $fileArray;
        return $fileCollection;
    }

    /**
     * scans the given path for files and folders and returns elements that are OK regarding the set white & blacklist
     *
     * @param string $path the path to scan on top on the path set for the fileCollection. Empty by default
     * @param bool $recursive If true then subfolders will be searched also and files returned. False by default
     */
    public function scanDir(string $path='', bool $recursive=false) :array
    {
        //scan a given path or base+rel-dir for files and return file objects that evaluate fine for the black/whitelist set
        $fileSystemObjects = array_diff(scandir($this->getFullPath($path)), array('..', '.'));

        foreach ($fileSystemObjects as $fsObj) {

        }

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
        foreach ($this->files as $fPath){
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
    public static function setBasePath($basePath) :void
    {
        if (!self::$basePathInitialized) {
            self::$basePath = Path::canonicalize($basePath);
            //ensure string ends with directory separator
            if (!str_ends_with(self::$basePath, '/')) {
                self::$basePath .='/';
            }
            self::$basePathInitialized = true;
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

        $path = Path::join(self::$basePath, $this->relativePath, $path);
        //ensure that resulting path is still below the base path, return false if not
        if (str_starts_with($path, Path::join(self::$basePath, $this->relativePath))) {
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
    public function setRelativePath(string $relativePath) :bool
    {
        //ensure path ends with directory separator
        if (str_starts_with($relativePath, '/')) {
            $relativePath = substr($relativePath, 1);
        }
        $relativePath = Path::canonicalize($relativePath);
        if (!str_ends_with($relativePath, '/')) {
            $relativePath .= '/';
        }
        $path = Path::join(self::$basePath, $relativePath);

        //ensure new path is not below basepath
        if (str_starts_with($path, self::$basePath)) {
            $this->relativePath = $relativePath;
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
        return $this->fileSystem->exists($fullPath);
    }

    /**
     * Returns relative path
     *
     * @return string the combination of base + relative path
     */
    public function getCurrentPath()
    {
        return self::$basePath . $this->relativePath;
    }

    /**
     * Returns value of relative path
     *
     * @return string the relative path set on top of the base path
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     * Returns value of base path
     *
     * @return string the base path set on top of the base path
     */
    public function getBasePath()
    {
        return self::$basePath;
    }


    /**************************************
     * White / Blacklist related functions
     *************************************/

    /**
     * Sets whitelist für filenames and extensions
     *
     * @param array $entries the whitelist entries to be set
     *
     * @return void No return value
     */
    public function setWhitelist(array $entries) : void
    {
        $this->whitelist = $entries;

    }

    /**
     * Sets blacklist for filenames and extensions
     *
     * @param array $entries the blacklist entries to be set
     *
     * @return void No return value
     */
    public function setBlacklist(array $entries) : void
    {
        $this->blacklist = $entries;

    }

    /**
     * Adds an entry to the blacklist without removing existing enties
     *
     * @param array $entries the blacklist entries to be added
     *
     * @return void No return value
     */
    public function addToBlacklist(array $entries) : void
    {
        $this->blacklist = array_merge($this->blacklist,$entries);
    }

    /**
     * Gets whitelist für filenames and extensions
     *
     * @return array $entries the whitelist entries to be set
     */
    public function getWhitelist() : array
    {
        return $this->whitelist;
    }

    /**
     * Gets blacklist für filenames and extensions
     *
     * @return array $entries the blacklist entries to be set
     */
    public function getBlacklist() : array
    {
        return $this->blacklist;
    }

    /**
     * Checks a file URI against both black and whitelist if they are defined
     *
     * @param string $fileName
     *
     * @returns bool true if filename does not match any regex in blacklist and matches whitelist - if defined
     */
    public function checkLists($fileName)
    {
        //check against all blacklist entries, return false if anything matches
        if ($this->blacklist){
            foreach ($this->blacklist as $blackListEntry) {
                if (preg_match($blackListEntry, $fileName)!== 0) {
                    return false;
                }
            }
        }
        //check whitelist entries, return true if any matches
        if ($this->whitelist) {
            foreach ($this->whitelist as $whiteListEntry) {
                if (preg_match($whiteListEntry, $fileName)== 1) {
                    return true;
                }
            }
            //no match against defined whitelist, thus false
            return false;
        }
        //no match in blacklist, no whitelist defined, thus OK
        return true;
    }
}
