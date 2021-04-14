<?php include("./settings/theme.inc.php"); ?>

<html>
<head>
    <meta charset="utf-8">
    
    <title>Editor</title>
    
    <link rel="icon" type="image/png" href="thumbnails/editor_logo.png">
    
    <link rel="stylesheet" href="./settings/<?php echo $theme; ?>">
    <style>
        body {
            margin: 0px;
            font-size: 15px;
            background-color: var(--background-color);
        }
    </style>
</head>
<body>
    <img src="<?php echo $_GET['filepath']; ?>">
    <br>
    <br>
    <?php
    
    include("./utils.inc.php");
    
    ?>
    <a href="./?folder=<?php echo get_folder($_GET['filepath']); ?>"><button type="button">back</button></a>
    
</body>
</html>