<?php
session_start();


if(!$_SESSION['editor'])
{
    header("location: ./");
}
else
{
    if(empty($_POST['id']) && $_POST['id'] !== "0")
    {
        //add
        $file=fopen("./settings/shortcuts", "a");
        fputs($file, htmlspecialchars($_POST["name"]).'>'.htmlspecialchars($_POST["path"]).'
');
        fclose($file);
    }
    else if(empty($_POST['delete']))
    {
        //edit
        $file=fopen("./settings/shortcuts", 'rb');
        $shortcuts=explode("
", fread($file, filesize("./settings/shortcuts")));
        fclose($file);
        
        $shortcuts[$_POST['id']] = htmlspecialchars($_POST["name"]).'>'.htmlspecialchars($_POST["path"]);
        $temp_string = "";
        for($i = 0; $i < count($shortcuts)-1; $i++)
        {
            $temp_string .= $shortcuts[$i] . '
';
        }
        
        $file=fopen("./settings/shortcuts", "w");
        fputs($file, $temp_string);
        fclose($file);
    }
    else
    {
        //delete
        $file=fopen("./settings/shortcuts", 'rb');
        $shortcuts=explode("
", fread($file, filesize("./settings/shortcuts")));
        fclose($file);
        
        unset($shortcuts[$_POST['id']]);
        
        $temp_string = "";
        for($i = 0; $i < count($shortcuts)-1; $i++)
        {
            $temp_string .= $shortcuts[$i] . '
';
        }
        
        $file=fopen("./settings/shortcuts", "w");
        fputs($file, $temp_string);
        fclose($file);
    }
    
    header('location: ./?folder='.$_POST['folder']);
}

?>