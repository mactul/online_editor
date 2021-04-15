<?php

function get_file($filename)
{
    $data = http_build_query(
        array(
            'pass' => 'dvsb5$#je!@0g2*;a-(6×y÷4',
            'fichier' => $filename
        )
    );

    return file_get_contents('https://cdd-cloud.ml/editor/emetteur/?'.$data, false);
}

if(empty($_POST["password"]))
{
    ?>
    <html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <form action="./install.php" method="post">
            <?php
            if(file_exists("./editor/settings/password.inc.php"))
            {
                ?>
                Please enter your editor password
                <br>
                If you forgot it or you want to reset it, delete editor folder and run this script
                <?php
            }
            else
            {
                ?>
                set your editor password:
                <?php
            }
            ?>
            <br>
            <input type="password" name="password">
            <br>
            <br>
            <input type="submit" value="set">
            
        </form>
    </body>
    </html>
    <?php
}
else
{
    if(file_exists("./editor/settings/password.inc.php"))
    {
        include("./editor/settings/password.inc.php");
    }
    else
    {
        $pass = false;
    }
    if(!file_exists("./editor/settings/password.inc.php") || $pass === hash("sha512", $_POST["password"]))
    {
        if(!file_exists("./editor/settings"))
        {
            mkdir("./editor/settings", 0777, true);
        }
        
        $fichier=fopen("./editor/settings/password.inc.php", "w");
        fputs($fichier, '<?php $'.'pass="'.hash("sha512", $_POST["password"]).'"; ?>');
        fclose($fichier);
        
        $fichier=fopen("./editor/settings/root_directory.inc.php", "w");
        fputs($fichier, '<?php $'.'dir="../"; ?>');
        fclose($fichier);
        
        $fichier=fopen("./editor/settings/colors.css", "w");
        fputs($fichier, get_file("../settings/colors.css"));
        fclose($fichier);
        
        $fichier=fopen("./editor/settings/colors_light.css", "w");
        fputs($fichier, get_file("../settings/colors_light.css"));
        fclose($fichier);
        
        $fichier=fopen("./editor/settings/theme.inc.php", "w");
        fputs($fichier, "<?php

$"."theme = 'colors.css';

?>");
        fclose($fichier);
        
        $fichier=fopen("./editor/settings/update.php", 'w');
        fputs($fichier, get_file("./update.php"));
        fclose($fichier);
        
        header("location: ./editor/settings/update.php");
    }
    else
    {
        echo "password incorrect";
    }

}