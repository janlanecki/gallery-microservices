<?php  
function clear($dir) {
    $mydir = opendir($dir);

    while ($file = readdir($mydir)) {
        if ($file != "." && $file != "..") {
            if (!is_dir($dir.$file)) {                   
                if (date("U",filectime($dir.$file)) < time() - 3600) {
                    unlink($dir.$file);
                }
            }
        }
    }

    closedir($mydir);
}
