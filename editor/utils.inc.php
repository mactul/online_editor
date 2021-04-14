<?php

function get_folder($filepath)
{   
    $i = strlen($filepath)-2;
    while($i >= 0 and $filepath[$i]!='/')
    {
        $i--;
    }
    return substr($filepath, 0, $i)."/";
}


?>