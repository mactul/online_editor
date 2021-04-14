<?php
session_start();


if(!$_SESSION['editor'])
{
    header("location: ./");
}
else
{
    if(!empty($_POST['root']))
    {
        $file=fopen("./settings/root_directory.inc.php", "w");
        fputs($file, "<?php

$"."dir = '".str_replace("'", "\'", $_POST['root'])."';

?>");
        fclose($file);
    }
    
    if($_POST["theme"])
    {
        $file=fopen("./settings/theme.inc.php", "w");
        fputs($file, "<?php

$"."theme = 'colors_light.css';

?>");
        fclose($file);
    }
    else
    {
        $file=fopen("./settings/theme.inc.php", "w");
        fputs($file, "<?php

$"."theme = 'colors.css';

?>");
        fclose($file);
    }
    
    $settings = array();
    
    if($_POST['show_hidden'] == "on")
    {
        $settings["show_hidden"] = true;
    }
    else
    {
        $settings["show_hidden"] = false;
    }
    
    $file=fopen("./settings/settings.json", "w");
    fputs($file, json_encode($settings));
    fclose($file);
    
    header('location: ./?folder='.$_POST['folder']);
}

?>