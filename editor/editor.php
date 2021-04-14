<?php

session_start();

if(!$_SESSION['editor'])
{
    header("location: ./");
}
else
{
    $name=$_GET["filepath"];
    
    $file=fopen($name, 'rb');
    $file_content=fread($file, filesize($name));
    fclose($file);
    
    if(substr($name, strripos($name, '.'))==".html" or substr($name, strripos($name, '.'))==".php" or substr($name, strripos($name, '.'))==".xml")
    {
        $flags = "HTML";
    }
    else if(substr($name, strripos($name, '.'))==".css")
    {
        $flags = "CSS";
    }
    else
    {
        $flags = "0";
    }
    
    include("./settings/theme.inc.php");
    
    ?>
    
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Editor</title>
        
        <link rel="icon" type="image/png" href="thumbnails/editor_logo.png">
        
        <link rel="stylesheet" href="./settings/<?php echo $theme; ?>">
        
        <style>
            body {
                margin: 0px;
                font-size: 15px;
                background-color: var(--background-color);
            }
            .hidden {
                display: none;
            }
            #lines {
                top: 5px;
                border: none;
                padding: 4px 2px 0px 2px;
                position: absolute;
                width: 39px;
                height: 10%;
                font-family: monospace;
                font-size: 15px;
                background-color: transparent;
                color: var(--color);
                overflow: hidden;
                line-height: 18px;
                text-align: right;
            }
            #code_area {
                left: 44px;
                top: 5px;
                position: absolute;
                width: 95%;
                min-width: 1000px;
                z-index: 2;
                background-color: transparent;
                font-family: monospace;
                font-size: 15px;
                padding: 4px 4px 0px 4px;
                color: var(--color);
                text-shadow: 0px 0px 0px transparent;
                -webkit-text-fill-color: transparent;
                word-break: break-all;
                overflow: hidden;
                line-height: 18px;
            }
            #display_screen {
                left: 44px;
                color: var(--color);
                position: absolute;
                top: 5px;
                width: 95%;
                min-width: 1000px;
                z-index: 1;
                font-family: monospace;
                font-size: 15px;
                padding: 5px;
                word-break: break-all;
                white-space: pre-wrap;
                line-height: 18px;
            }
            #save_button {
                position: absolute;
                margin: 32px;
                font-size: 20px;
                padding: 20px 30px;
            }
            #save_close_button {
                position: absolute;
                margin: 32px 150px;
                font-size: 20px;
                padding: 20px 30px;
            }
            
            #suggestions {
                display: none;
                position: absolute;
                border: 1px solid grey;
                z-index: 3;
                color: var(--color);
                font-family: monospace;
                font-size: 15px;
                line-height: 18px;
                background-color: var(--background-color);
                padding: 5px 10px;
            }
            #suggestions .active {
                background-color: grey;
            }
        </style>
    </head>
    <body>
    
    <div id="suggestions"></div>
    
    <textarea id="lines" disabled><?php
    for($i = 1; $i < 10000; $i++)
    {
        echo $i."\n";
    }
    ?></textarea>
    
    <form action="merge_file.php" method="post" id="form">
        <input type="hidden" name="filepath" value="<?php echo $_GET["filepath"]; ?>">
        <input type="hidden", name="redirect" id="redirect" value="1">
        
        <textarea class="hidden" name="original_file_content"><?php
        echo str_replace("\t", "    ", htmlspecialchars($file_content));
        ?></textarea>
        <textarea name="new_file_content" id="code_area" autocapitalize="none" autocomplete="off" spellcheck="false" required><?php
        echo str_replace("\t", "    ", htmlspecialchars($file_content));
        ?></textarea>
        
        <div id="display_screen"></div>
        
        
        <input id="save_button" type="submit" value="save">
        
        <input id="save_close_button" type="submit" value="save & close" onclick="document.getElementById('redirect').value = 0">
        
    </form>
    
    <script src="./js/coloration.js"></script>
    
    <script type="text/javascript">
    
        DOUBLE_QUOTES = 0o1;
        SIMPLE_QUOTES = 0o2;
        TRIPLE_QUOTES = 0o4;
        LONG_COMMENT  = 0o10;
        SHORT_COMMENT = 0o20;
        HTML          = 0o40;
        CSS           = 0o100;
        BALISE_HTML   = 0o200;
        ARGS_HTML     = 0o400;
        SCRIPT_BALISE = 0o1000;
        STYLE_BALISE  = 0o2000;
        BLOCK_CSS     = 0o4000;
        VALUE_CSS     = 0o10000;
        PHP           = 0o20000;
        
        
        START_FLAGS = <?php echo $flags; ?>;
        
        
        document.onkeydown = function(e)
        {
            // shortcuts
            if(e.ctrlKey && e.keyCode==83)  // Ctrl + S
            {
                (navigator.appName.substring(0,3)=="Mic") ? event.returnValue = false : e.preventDefault();
                CancelEvent(e);
                document.forms['form'].submit();
            }
            else if(e.ctrlKey && e.keyCode==81)  // Ctrl + Q
            {
                (navigator.appName.substring(0,3)=="Mic") ? event.returnValue = false : e.preventDefault();
                CancelEvent(e);
                document.getElementById('redirect').value = 0;
                document.forms['form'].submit();
            }
            else if(e.ctrlKey && e.keyCode==90)  // Ctrl + Z
            {
                (navigator.appName.substring(0,3)=="Mic") ? event.returnValue = false : e.preventDefault();
                CancelEvent(e);
                document.execCommand('undo', false, null);
            }
            else if(e.keyCode==116 || e.which==116 || e.keyCode==120 || e.which==120)  // F5 and F9 (do the same)
            {
                CancelEvent(e);
                e.returnValue = false;
                e.keyCode = 0;
                window.open('<?php echo $_GET["filepath"] ?>', '_blank');
            }
        }
    </script>
    <script src="./js/suggestions.js"></script>
    <script src="./js/textarea_color.js"></script>
    
    </body>
    </html>
    
    <?php
}
?>