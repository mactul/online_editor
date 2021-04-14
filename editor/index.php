<?php
session_start();

include("./settings/theme.inc.php")

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.5">
    
    <title>Editor</title>
    
    <link rel="icon" type="image/png" href="thumbnails/editor_logo.png">
    
    <link rel="stylesheet" href="./settings/<?php echo $theme; ?>">
    
    <style>
        body {
            background-color: var(--background-color);
            color: var(--color);
            margin: 0;
            font-family: monospace;
            font-size: 14px;
        }
        
        .horizontal-navbar {
            top: 0px;
            width: 100%;
            height: 60px;
            background-color: brown;
            color: white;
            position: fixed;
            padding-left: 110px;
            line-height: 60px;
            display: inline-block;
            white-space: nowrap;
            z-index: 2;
        }
        
        
        .horizontal-navbar .title {
            font-size: 23px;
            font-weight: bold;
            float: left;
        }
        
        .horizontal-navbar .right {
            float: right;
            margin-right: 120px;
        }
        
        .horizontal-navbar .right .logo {
            height: 28px;
            vertical-align: middle;
            margin-right: 30px;
            cursor: pointer;
        }
        
        .vertical-navbar {
            top: 0;
            width: 100px;
            height: 100%;
            background-color: brown;
            position: fixed;
            text-align: center;
            z-index: 3;
        }
        
        .vertical-navbar a {
            color: white;
        }
        
        .vertical-navbar .logo {
            height: 40px;
            margin: 10px;
        }
        
        .container {
            position: relative;
            width: 100%;
            margin-top: 60px;
        }
        
        .files {
            padding-left: 100px;
        }
        
        .files a {
            color: var(--color);
            text-decoration: none;
        }
        
        .file {
            margin: 10px;
            width: 80px;
            height: 110px;
            border-radius: 15px;
            background-color: RGBA(80, 80, 80, 0.25);
            padding: 10px 5px;
            text-align: center;
            display: inline-block;
        }
        
        .file:hover {
            background-color: RGBA(80, 80, 80, 0.35);
        }
        
        .file img {
            width: 60px;
            height: 60px;
        }
        
        .file .text {
            height: 55px;
            line-height: 15px;
            overflow: hidden;
            word-break: break-all;
        }
        
        .shortcut {
            width: 50px;
            height: 70px;
            padding: 10px 5px;
            text-align: center;
            display: inline-block;
        }
        
        .shortcut img {
            height: 30px;
        }
        
        .shortcut .text {
            height: 30px;
            font-size: 12px;
            line-height: 15px;
            overflow: hidden;
            word-break: break-all;
        }
        
        #panel {
            position: absolute;
            background-color: var(--panel-color);
            z-index: 5;
            line-height: 40px;
            padding: 5px 15px 10px 20px;
            display: none;
            border-radius: 10px;
            width: 130px;
            border-left: 2px solid #757575;
            border-right: 2px solid #757575;
        }
        #panel div {
            font-weight: bold;
            border-bottom: 1px solid white;
            margin-bottom: 10px;
            word-break: break-all;
            line-height: 30px;
        }
        #panel a {
            color: var(--color);
            /*text-decoration: none;*/
        }
        #panel hr {
            border: none;
            border-bottom: 1px solid #757575;
        }
        
        .box {
            position: absolute;
            width: 430px;
            height: 180px;
            background-color: white;
            left: 50%;
            margin-left: -225px;
            z-index: 4;
            top: 0px;
            border-radius: 3px;
            border: 1px solid grey;
            box-shadow: 0px 2px 10px 1px rgba(0, 0, 0, 0.6);
            padding: 10px;
            color: black;
            display: none;
        }
        
        .box .title {
            font-size: 16px;
            text-align: center;
        }
        
        .box input[type="text"] {
            width: 320px;
        }
        
        .box .validate {
            margin-left: 270px;
        }
        
        .box .validate button, .box .validate input {
            width: 70px;
            height: 30px;
            border-radius: 4px;
            border: 1px solid #CCCCCC;
            font-size: 16px;
        }
        
        .box .validate button {
            color: #4285F4;
            background-color: white;
        }
        .box .validate input {
            color: white;
            background-color: #4285F4;
        }
        
        
    </style>
</head>


<body>
    <?php
    
    function simplify_path($path)
    {
        $path = explode("/", $path);
        $out = "";
        $i = count($path)-1;
        while($i >= 0)
        {
            if($path[$i] == ".." and isset($path[$i-1]))
            {
                if($path[$i-1] == "..")
                {
                    $out = $path[$i] . "/" . $out;
                }
                else
                {
                    $i--;
                }
            }
            else
            {
                $out = $path[$i]."/".$out;
            }
            $i--;
        }
        if($out[strlen($out)-2] == "/")
        {
            return substr($out, 0, -1);
        }
        else
        {
            return $out;
        }
        
    }
    
    function get_thumbnail($filename)
    {
        if(substr($filename, -4) == ".php")
        {
            return "file_php.png";
        }
        else if(substr($filename, -3) == ".js")
        {
            return "file_js.png";
        }
        else if(substr($filename, -5) == ".html")
        {
            return "file_html.png";
        }
        else if(substr($filename, -4) == ".css")
        {
            return "file_css.png";
        }
        else if(substr($filename, -4) == ".txt")
        {
            return "file_txt.png";
        }
        else if(substr($filename, -3) == ".py")
        {
            return "file_py.png";
        }
        else if(substr($filename, -3) == ".sh")
        {
            return "file_sh.png";
        }
        else if(substr($filename, -4) == ".png" or substr($filename, -4) == ".jpg" or substr($filename, -4) == ".bmp" or substr($filename, -4) == ".gif" or substr($filename, -4) == ".svg" or substr($filename, -5) == ".webp" or substr($filename, -5) == ".jpeg")
        {
            return "file_img.png";
        }
        else
        {
            return "file.png";
        }
    }
    
    
    include("./settings/password.inc.php");
    if(isset($_POST["password"]) and hash("sha512", $_POST["password"]) == $pass)
    {
         $_SESSION["editor"] = True;
    }
    
    if($_SESSION["editor"])
    {
        include("./settings/root_directory.inc.php");
        $root_dir = $dir;
        if(!isset($_GET['folder']))
        {
            $_GET['folder']="";
        }
        $dir=simplify_path($_GET['folder']);
        if(empty($dir))
        {
            include("./settings/root_directory.inc.php");
        }
        
        if(file_exists("./settings/settings.json"))
        {
            $settings = json_decode(file_get_contents("./settings/settings.json"), true);
        }
        else
        {
            $settings = array("show_hidden" => false);
        }
        
        ?>
        
        <div class="horizontal-navbar">
            <div class="title">
                Online Editor
            </div>
            <div class="right">
                <img class="logo" src="./thumbnails/create_folder_logo.png" alt="create folder" onclick="redirect_folder();" title="create new folder">
                <img class="logo" src="./thumbnails/create_file_logo.png" alt="create file" onclick="redirect_file();" title="create new file">
                <img class="logo" src="./thumbnails/upload_logo.png" alt="upload" onclick="document.getElementById('box').style.display = 'block';" title="upload a file">
            </div>
        </div>
        
        <div class="vertical-navbar">
            <img src="./thumbnails/editor_logo.png" class="logo" alt="logo">
            <br>
            <br>
            <?php
            if(file_exists("./settings/shortcuts"))
            {
                $file=fopen("./settings/shortcuts", 'rb');
                $shortcuts=explode("
", fread($file, filesize("./settings/shortcuts")));
                fclose($file);
                for($i = 0; $i < count($shortcuts); $i++)
                {
                    if(strlen($shortcuts[$i]) > 1)
                    {
                        $shortcut = explode(">", $shortcuts[$i]);
                        ?>
                        <a data-id="<?php echo $i; ?>" data-name="<?php echo $shortcut[0]; ?>" data-path="<?php echo $shortcut[1]; ?>" href="./?folder=<?php echo $shortcut[1]; ?>">
                            <div data-id="<?php echo $i; ?>" data-name="<?php echo $shortcut[0]; ?>" data-path="<?php echo $shortcut[1]; ?>" class="shortcut">
                                <img data-id="<?php echo $i; ?>" data-name="<?php echo $shortcut[0]; ?>" data-path="<?php echo $shortcut[1]; ?>" src="./thumbnails/folder_logo.png">
                                <div data-id="<?php echo $i; ?>" data-name="<?php echo $shortcut[0]; ?>" data-path="<?php echo $shortcut[1]; ?>" class="text">
                                    <?php echo $shortcut[0]; ?>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                }
            }
            ?>
            <a href="javascript: new_shortcut();" title="add new shortcut">
                <div class="shortcut">
                    <img src="./thumbnails/create_folder_logo.png" alt="add shortcut">
                </div>
            </a>
            
            <a href="javascript: show_settings();" title="settings">
                <div class="shortcut">
                    <img src="./thumbnails/settings_logo.png" alt="settings">
                </div>
            </a>
        </div>
        
        <div id="panel"></div>
        
        <div class="box" id="box">
            <form action="upload_file.php" method="post" enctype="multipart/form-data">
                <div class="title">Upload file to current folder</div>
                <br>
                <br>
                <input type="hidden" name="folder" value="<?php echo $dir; ?>">
                <input type="file" name="file" required>
                <br>
                <br>
                <br>
                <br>
                <br>
                <div class="validate">
                    <button type="button" onclick="document.getElementById('box').style.display = 'none';">Cancel</button>
                    <input type="submit" value="OK">
                </div>
            </form>
        </div>
        
        <div class="box" id="box2">
            <form action="edit_shortcut.php" method="post">
                <input type="hidden" name="folder" value="<?php echo $dir; ?>">
                <input type="hidden" name="id" value="" id="shortcut_hidden_post_id">
                <input type="hidden" name="delete" value="0" id="shortcut_hidden_post_delete">
                <div class="title" id="shortcut_box_title">Add new shortcut</div>
                <div id="shortcut_box_content">
                    <br>
                    The name of your shortcut (14 chars max)
                    <br>
                    <input type="text" name="name" id="shortcut_box_input_name" required>
                    <br>
                    <br>
                    The folder's path
                    <br>
                    <input type="text" name="path" id="shortcut_box_input_path" required>
                    <br>
                    <br>
                </div>
                <div class="validate">
                    <button type="button" onclick="document.getElementById('box2').style.display = 'none';">Cancel</button>
                    <input type="submit" value="OK">
                </div>
            </form>
        </div>
        
        <div class="box" id="box3">
            <form action="edit_settings.php" method="post">
                <input type="hidden" name="folder" value="<?php echo $dir; ?>">
                <div class="title">Settings</div>
                <br>
                Root directory (loaded when folder path is omitted)
                <br>
                <input type="text" name="root" value="<?php echo $root_dir; ?>" required>
                <br>
                <br>
                <?php
                if($theme == "colors_light.css")
                {
                    ?>
                    <label for="dark">Dark theme</label><input type="radio" name="theme" value="0" id="dark"> <label for="light">light theme</label><input type="radio" name="theme" value="1" id="light" checked>
                    <?php
                }
                else
                {
                    ?>
                    <label for="dark">Dark theme</label><input type="radio" name="theme" value="0" id="dark" checked> <label for="light">light theme</label><input type="radio" name="theme" value="1" id="light">
                    <?php
                }
                ?>
                <br>
                <br>
                <label for="show_hidden">Show hiddens files</label> <input type="checkbox" name="show_hidden" id="show_hidden"<?php if($settings["show_hidden"]) { echo " checked"; } ?>>
                <br>
                <div class="validate">
                    <button type="button" onclick="document.getElementById('box3').style.display = 'none';">Cancel</button>
                    <input type="submit" value="OK">
                </div>
            </form>
        </div>
        
        <div class="container">
            <div class="files">
                <?php
                
                $iterator = scandir($dir);
                for($a = 0; $a < count($iterator); $a++)
                {
                    $file = $iterator[$a];
                    
                    if(is_dir($dir . $file) and $file != "." and ($file[0] != '.' or $file == ".." or $settings["show_hidden"]) and is_readable($dir . $file) and ($file != ".." or realpath($dir) != "/"))
                    {
                        ?>
                        <a data-name="<?php echo $dir . $file; ?>" href="./?folder=<?php echo $dir . $file; ?>">
                            <div data-name="<?php echo $dir . $file; ?>" class="file">
                                <img data-name="<?php echo $dir . $file; ?>" src="./thumbnails/folder.png">
                                <div data-name="<?php echo $dir . $file; ?>" class="text">
                                    <?php echo $file; ?>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                }
                
                for($a = 0; $a < count($iterator); $a++)
                {
                    $file = $iterator[$a];
                    
                    if(!is_dir($dir . $file) and ($file[0] != '.' or $settings["show_hidden"]) and substr(mime_content_type($dir . $file), 0, 5) == "image")
                    {
                        ?>
                        <a data-name="<?php echo $dir . $file; ?>" href="./view_image.php?filepath=<?php echo $dir . $file; ?>">
                            <div data-name="<?php echo $dir . $file; ?>" class="file">
                                <img data-name="<?php echo $dir . $file; ?>" src="./thumbnails/<?php echo get_thumbnail($file); ?>">
                                <div data-name="<?php echo $dir . $file; ?>" class="text">
                                    <?php echo $file; ?>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                    else if(!is_dir($dir . $file) and ($file[0] != '.' or $settings["show_hidden"]))
                    {
                        ?>
                        <a data-name="<?php echo $dir . $file; ?>" href="./editor.php?filepath=<?php echo $dir . $file; ?>">
                            <div data-name="<?php echo $dir . $file; ?>" class="file">
                                <img data-name="<?php echo $dir . $file; ?>" src="./thumbnails/<?php echo get_thumbnail($file); ?>">
                                <div data-name="<?php echo $dir . $file; ?>" class="text">
                                    <?php echo $file; ?>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }
    else
    {
        ?>
        <form action="./?<?php echo $_SERVER['QUERY_STRING']; ?>" method="post">
        
        <input type="password" name="password">
        
        <br>
        <br>
        
        <input type="submit" value="log in">
        
        </form>
        <?php
    
    }
    
    ?>
    
    <script>
        window.oncontextmenu = function (e) {
            CancelEvent(e);
            let panel = document.getElementById("panel");
            panel.style.left = e.x + "px";
            panel.style.top = e.y + "px";
            
            let id = e.target.getAttribute("data-id");
            if(id == null)
            {
                let path = e.target.getAttribute("data-name");
                if(path != null)
                {
                    var name = path.split("/");
                    name = name[name.length - 1];
                }
                if(path == null || name == "..")
                {
                    panel.innerHTML = "<a href='javascript: redirect_folder();'>new folder</a><br><a href='javascript: redirect_file();'>new file</a>";
                }
                else
                {
                    panel.innerHTML = "<div>" + name + "</div><a href='./delete_file.php?filepath=" + path + "' onclick='return confirm(`voulez vous supprimer \"" + name + "\" ?`)'>delete</a><br><a href='javascript: redirect_rename(`" + path + "`, `" + name + "`);'>rename/move</a><br><a href='duplicate_file.php?filepath=" + path + "'>duplicate</a><br><a href='download_file.php?filepath=" + path + "'>Download</a><hr><a href='javascript: redirect_folder();'>new folder</a><br><a href='javascript: redirect_file();'>new file</a>";
                }
            }
            else
            {
                panel.innerHTML = "<a href='javascript: edit_shortcut(`"+id+"`, `" + e.target.getAttribute("data-name") + "`, `" + e.target.getAttribute("data-path") + "`);'>edit shortcut</a><br><a href='javascript: delete_shortcut(`"+id+"`);'>delete shortcut</a><hr><a href='javascript: new_shortcut();'>new shortcut</a>";
            }
            panel.style.display = "block";
        }
        
        window.onclick = function(e) {
            let panel = document.getElementById("panel");
            panel.innerHTML = "";
            panel.style.display = "none";
        }
        
        function redirect_rename(path, name)
        {
            let result = prompt("rename or move: "+name, path);
            if(result != null)
                location.href = "./rename_file.php?filepath=" + path + "&new_name=" + result;
        }
        
        function redirect_folder()
        {
            let result = prompt("new folder: ", "<?php echo $dir; ?>");
            if(result != null)
                location.href = "./new_folder.php?filepath=" + result;
        }
        
        function redirect_file()
        {
            let result = prompt("new file: ", "<?php echo $dir; ?>");
            if(result != null)
                location.href = "./new_file.php?filepath=" + result;
        }
        
        function new_shortcut()
        {
            document.getElementById('box2').style.display = 'block';
            document.getElementById("shortcut_hidden_post_id").value = "";
            document.getElementById("shortcut_hidden_post_delete").value = "0";
            document.getElementById("shortcut_box_title").innerHTML = "Add new shortcut";
            document.getElementById("shortcut_box_content").innerHTML = "<br>The name of your shortcut (14 chars max)<br><input type='text' name='name' id='shortcut_box_input_name' required><br><br>The folder's path<br><input type='text' name='path' id='shortcut_box_input_path' required><br><br>";
            document.getElementById("shortcut_box_input_name").value = "";
            document.getElementById("shortcut_box_input_path").value = "";
        }
        
        function edit_shortcut(id, name, path)
        {
            document.getElementById('box2').style.display = 'block';
            document.getElementById("shortcut_hidden_post_id").value = id;
            document.getElementById("shortcut_hidden_post_delete").value = "0";
            document.getElementById("shortcut_box_title").innerHTML = "Edit shortcut";
            document.getElementById("shortcut_box_content").innerHTML = "<br>The name of your shortcut (14 chars max)<br><input type='text' name='name' id='shortcut_box_input_name' required><br><br>The folder's path<br><input type='text' name='path' id='shortcut_box_input_path' required><br><br>";
            document.getElementById("shortcut_box_input_name").value = name;
            document.getElementById("shortcut_box_input_path").value = path;
            
        }
        
        function delete_shortcut(id)
        {
            document.getElementById('box2').style.display = 'block';
            document.getElementById("shortcut_hidden_post_id").value = id;
            document.getElementById("shortcut_hidden_post_delete").value = "1";
            document.getElementById("shortcut_box_title").innerHTML = "Delete shortcut";
            document.getElementById("shortcut_box_content").innerHTML = "<br><br>Do you realy want to delete this shortcut ?<br><br><br><br><br>";
        }
        
        function show_settings(id)
        {
            document.getElementById('box3').style.display = 'block';
        }
        
        function CancelEvent(e)
        {
            if(e)
            {
                e.stopPropagation();
                e.preventDefault();
            }
            if(window.event)
            {
                window.event.cancelBubble = true;
                window.event.returnValue  = false;
                return;
            }
        }
    </script>
</body>
</html>