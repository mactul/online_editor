<?php
session_start();

function rm_rf($path)
{
    if (is_dir($path))
    {
        $objects = scandir($path);
        foreach ($objects as $object)
        {
            if ($object != "." && $object != "..")
            {
                rm_rf($path."/".$object);
            }
        }
        reset($objects);
        rmdir($path);
    }
    else
    {
        unlink($path);
    }
}


if(!$_SESSION['editor'])
{
    header("location: ./");
}
else
{
    rm_rf($_GET['filepath']);
    
    include("./utils.inc.php");
    header("location: ./?folder=".get_folder($_GET['filepath']));

}
?>