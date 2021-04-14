<?php

if(substr($_POST["folder"], -1) == "/")
{
    $filepath = $_POST["folder"] . $_FILES['file']['name'];
}
else
{
    $filepath = $_POST["folder"] . "/" . $_FILES['file']['name'];
}

$resultat = move_uploaded_file($_FILES['file']['tmp_name'], $filepath);
if($resultat)
{
    header('location: ./?folder='.$_POST["folder"]);
}
else
{
    echo "Error during import<br><br><a href='./?folder=".$_POST["folder"]."'>close</a>";
}

?>