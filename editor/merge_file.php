<?php

session_start();

define("EQUAL", 0);
define("DELETE", 1);
define("ADD", 2);


function get_changes($base, $new)
{
    $result = [];
    
    $pbase = 0;
    $pnew = 0;
    
    $len_base = count($base);
    $len_new = count($new);
    
    while($pbase < $len_base and $pnew < $len_new)
    {
        if($base[$pbase] == $new[$pnew])
        {
            $result[] = [EQUAL, $base[$pbase]];
        }
        else
        {
            $i = $pnew;
            while($i < $len_new and $new[$i] != $base[$pbase])
            {
                $i += 1;
            }
            if($i == $len_new)
            {
                // the line has been deleted in the new
                $result[] = [DELETE, $base[$pbase]];
                
                $i = $pbase;
                while($i < $len_base and $base[$i] != $new[$pnew])
                {
                    $i += 1;
                }
                if($i == $len_base)
                {
                    // this line has been added (actually it's a modification)
                    $result[] = [ADD, $new[$pnew]];
                }
                else
                {
                    $pbase += 1;
                    while($pbase < $i)
                    {
                        $result[] = [DELETE, $base[$pbase]];
                        $pbase += 1;
                    }
                    $pnew -= 1;
                    $pbase -= 1;
                }
            }
            else
            {
                // there has been insertion
                while($pnew < $i)
                {
                    $result[] = [ADD, $new[$pnew]];
                    $pnew += 1;
                }
                $pnew -= 1;
                $pbase -= 1;
            }
        }
        $pnew += 1;
        $pbase += 1;
    }
    
    while($pbase < $len_base)
    {
        $result[] = [DELETE, $base[$pbase]];
        $pbase += 1;
    }
    while($pnew < $len_new)
    {
        $result[] = [ADD, $new[$pnew]];
        $pnew += 1;
    }
            
    return $result;
}

function merge($changes1, $changes2)
{
    $p1 = 0;
    $p2 = 0;
    
    $len_changes1 = count($changes1);
    $len_changes2 = count($changes2);

    $result = "";
    while($p1 < $len_changes1 and $p2 < $len_changes2)
    {
        if($changes1[$p1][0] == EQUAL and $changes2[$p2][0] == EQUAL)
        {
            // normally they are necessarily equal
            if($changes1[$p1][1] != $changes2[$p2][1])
            {
                echo "error: catastrophe there is nothing that works";
                die();
            }

            $result .= $changes1[$p1][1] . '
';
            $p1 += 1;
            $p2 += 1;
        }
        else if($changes1[$p1][0] == ADD and $changes2[$p2][0] == ADD)
        {
            // both wrote about the same part: conflict
            // the one who has written the most wins.
            $temp_result1 = "";
            $count1 = 0;
            while($p1 < $len_changes1 and $changes1[$p1][0] == ADD)
            {
                $temp_result1 .= $changes1[$p1][1] . '
';
                $count1 += 1;
                $p1 += 1;
            }
            $temp_result2 = "";
            $count2 = 0;
            while($p2 < $len_changes2 and $changes2[$p2][0] == ADD)
            {
                $temp_result2 .= $changes2[$p2][1] . '
';
                $count2 += 1;
                $p2 += 1;
            }
            if($count2 > $count1)
            {
                $result .= $temp_result2;
            }
            else
            {
                $result .= $temp_result1;
            }
        }
        else if($changes1[$p1][0] == ADD)
        {
            while($changes1[$p1][0] == ADD)
            {
                $result .= $changes1[$p1][1] . '
';
                $p1 += 1;
            }
        }
        else if($changes2[$p2][0] == ADD)
        {
            while($changes2[$p2][0] == ADD)
            {
                $result .= $changes2[$p2][1] . '
';
                $p2 += 1;
            }
        }
        else if($changes1[$p1][0] == DELETE or $changes2[$p2][0] == DELETE)
        {
            $p1 += 1;
            $p2 += 1;
        }
    }
    
    while($p1 < $len_changes1)
    {
        if($changes1[$p1][0] == EQUAL or $changes1[$p1][0] == ADD)
        {
            $result .= $changes1[$p1][1] . '
';
        }
        $p1++;
    }
    
    while($p2 < $len_changes2)
    {
        if($changes2[$p2][0] == EQUAL or $changes2[$p2][0] == ADD)
        {
            $result .= $changes2[$p2][1] . '
';
        }
        $p2++;
    }
    
    return substr($result, 0, -2);
}



if(!$_SESSION['editor'])
{
    header("location: ./");
}
else
{
    $name=$_POST["filepath"];
    
    $file=fopen($name, 'rb');
    
    $server_file_content = [];
    $line = "";
    while(($line = fgets($file)) !== false)
    {
        $server_file_content[] = str_replace(array('
', PHP_EOL), '', $line);
    }
    
    fclose($file);
    
    
    $original_file_content = explode('
', $_POST["original_file_content"]);
    
    $new_file_content = explode('
', $_POST["new_file_content"]);
    
    $merged_file_content = merge(get_changes($original_file_content, $new_file_content), get_changes($original_file_content, $server_file_content));
    
    
    $file=fopen($name, "w");
    fputs($file, $merged_file_content);
    fclose($file);
    
    
    if($_POST["redirect"])
    {
        header("location: ./editor.php?filepath=".$name);
    }
    else
    {
        include("./utils.inc.php");
          
        header("location: ./?folder=".get_folder($name));
    }
    
}

?>