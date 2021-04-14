<?php
session_start();


function rcopy($source, $dest)
{
    echo $source . "   " . $dest . "<br>";
    if(is_dir($source))
    {
        mkdir($dest);
        $objects = scandir($source);
        foreach($objects as $object)
        {
            if($object != ".." and $object != ".")
            {
                rcopy($source."/".$object, $dest."/".$object);
            }
        }
    }
    else
    {
        copy($source, $dest);
    }
}


function have_extension($name)
{
    $name = end(explode("/", $name));
    return strpos($name, ".") !== false;
}

if(!$_SESSION['editor'])
{
    header("location: ./");
}
else
{
    if(have_extension($_GET['filepath']))
    {
        $name = substr($_GET['filepath'], 0, strripos($_GET['filepath'], '.'));
        $extension = substr($_GET['filepath'], strripos($_GET['filepath'], '.'));
    }
    else
    {
        $name = $_GET['filepath'];
        $extension = "";
    }
    
    
    $i = 1;
    while(file_exists($name . "(" . $i .")" . $extension))
    {
        $i++;
    }
    
    $name = $name . "(" . $i .")" . $extension;
    
    rcopy($_GET['filepath'], $name);
    
    include("./utils.inc.php");
    header("location: ./?folder=".get_folder($name));
    
}
?>