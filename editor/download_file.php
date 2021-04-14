<?php
session_start();


if(!$_SESSION['editor'])
{
    header("location: ./");
}
else
{
    $filename = end(explode("/", $_GET['filepath']));
    if(is_dir($_GET['filepath']))
    {
        $rootPath = realpath($_GET['filepath']);
        
        $zip = new ZipArchive();
        $zip->open('file.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                
                $zip->addFile($filePath, $relativePath);
            }
        }
        
        // Zip archive will be created only after closing object
        $zip->close();
        
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename="'.$filename.'.zip"');
        header('Content-Length: ' . filesize("file.zip"));
        readfile("file.zip");
        
        unlink('file.zip');
    }
    else
    {
        $file_content = file_get_contents($_GET['filepath']);
        
        header('Content-type: application');
        header('Content-length: '. filesize($_GET['filepath']));
        header('Content-disposition: attachment; filename="'.$filename.'"');
        
        echo $file_content;
    }
}
?>