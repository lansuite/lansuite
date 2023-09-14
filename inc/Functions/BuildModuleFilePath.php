<?php

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

 /**
  * Builds the correct path to include a file from a particular module.
  *
  * $module and $file should only contain letters, numbers and valid chars like _ or -.
  * If this is not the case, an exception will be thrown.
  *
  * This function also protects for path traversal attacks.
  */
function BuildModuleFilePath(Filesystem $filesystem, string $rootDirectory, string $module, string $file): string
{
    // Some files contain a `_` or `-` char.
    // To avoid a regular expression, we allow the chars by replacing.
    $validChars = array('-', '_');

    // Check that they only contain valid chars like letters and numbers
    if (!ctype_alnum(str_replace($validChars, '', $module)) || !ctype_alnum(str_replace($validChars, '', $file))) {
        throw new \Exception("Path contains unexpected characters");
    }

    $pathToInclude = $rootDirectory . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $file . '.php';
    $pathToInclude = Path::canonicalize($pathToInclude);

    if (!$filesystem->exists($pathToInclude)) {
        throw new \Exception("File does not exist");
    }

    // Normally you would also check if the path is part of your root dir
    // Like in https://stackoverflow.com/a/4205278
    // This is not needed right here, because we already check for ctype_alnum above.

    return $pathToInclude;
}
