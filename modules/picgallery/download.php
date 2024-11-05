<?php
namespace LanSuite;

$fileCollection = new FileCollection();
$fileCollection->setRelativePath('ext_inc/picgallery/');
$requestPath = $request->query->get('picurl');
$file = $fileCollection->getFileHandle($requestPath);

if ($file->exists()) {
    $fullPath = $fileCollection->getFullPath($requestPath);
    
    //set headers for download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($requestPath) . '"');
    header('Content-Length: '.filesize($fullPath));

    //dump file content
    $file->outputFileContent();
} else {
    echo "file does not exist!";
}
