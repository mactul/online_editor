<?php
session_start();


if(!$_SESSION['editor'])
{
    header("location: ./");
}
else
{
    include("./utils.inc.php");
    
    rename($_GET['filepath'], $_GET['new_name']);
    
    header('location: ./?folder='.get_folder($_GET['new_name']));
    
}
?>