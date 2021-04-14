<?php

session_start();

if(!$_SESSION['editor'])
{
    header("location: ./");
}
else
{
    mkdir($_GET['filepath'], 0777, true);
    
    header("location: ./?folder=" . $_GET['filepath']);
}

?>