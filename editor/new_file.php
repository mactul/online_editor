<?php

session_start();

if(!$_SESSION['editor'])
{
    header("location: ./");
}
else
{   
    $f=fopen($_GET['filepath'], "x+");
    
    fputs($f, '<html>
<head>
    <meta charset="utf-8">
    
    <title></title>
</head>
<body>
    
    
</body>
</html>');
    
    include("./utils.inc.php");
    header('location: ./?folder='.get_folder($_GET['filepath']));

}

?>