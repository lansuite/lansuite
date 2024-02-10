<?php

namespace LanSuite;
use Symfony\Component\Filesystem\Path;

$fileCollection = new FileCollection();
$fileCollection->setRelativePath('ext_inc/picgallery/');

$path = $request->query->get('file');
$path = Path::canonicalize($path);
if (str_starts_with($path, '/')) {
    $path = substr($path, 1);
}
$directoryPath = Path::getDirectory($path);

if (true) {
    switch ($request->query->getInt('step')) {
    default:
        $pic = $database->queryWithOnlyFirstRow("SELECT caption FROM %prefix%picgallery WHERE name = ?", [$path]);
        if (!$pic['caption']) {
            $pic['caption'] = "<i>".t('Unbekannt')."</i>";
        }
        $func->question(
            t('Willst du das Bild <b>%1 (%2)</b> wirklich l&ouml;schen?', $pic['caption'], $path), 
            "index.php?mod=picgallery&action=delete&step=2&file=$path&caption={$pic['caption']}", 
            "index.php?mod=picgallery&file=$directoryPath"
        );
        break;
    
    case 2:
        $deletionResult = $database->query("DELETE FROM %prefix%picgallery WHERE name = ?", [$path]);
        $fileObj = $fileCollection->getFileHandle($path);
        $fileObj->delete();
        $thumbFile = $fileCollection->getFileHandle($directoryPath . 'lsthumb_'. basename($path));
        if ($thumbFile->exists()) {
            $thumbFile->delete();
        }
        $func->confirmation(
            t('Das Bild <b>%1 (%2)</b> wurde gel&ouml;scht', $_GET["caption"], $path), 
            "index.php?mod=picgallery&file=$directoryPath"
        );
        break;
    
    // Delete directory
    case 10:
        $func->question(
            t('Möchtest du das Verzeichnis %1 wirklich löschen? Dabei werden alle darin enthaltenen Bilder mit gelöscht!', $path),
            "index.php?mod=picgallery&action=delete&step=11&file={$path}",
            "index.php?mod=picgallery&file=$path"
        );
        break;

    case 11:
        
        recursiveRemoveDirectory($fileCollection->getFullPath($path));
        $func->confirmation(t('Das Verzeichnis wurde erfolgreich gelöscht'), 'index.php?mod=picgallery');
        break;
    }
}
