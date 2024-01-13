<?php
namespace LanSuite;

$file = new File();
$file->setRelativePath('ext_inc/picgallery/');
$requestPath = $request->query->get('picurl');

if ($file->exists($requestPath)) {
    $fullPath = $file->getFullPath($requestPath);
    //set headers for download
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=" . basename($requestPath) . '"');
    header("Content-Length: ".filesize($fullPath));   
    //dump file content
    $file->outputFileContent($requestPath);
} else {
    echo "file does not exist!";
}