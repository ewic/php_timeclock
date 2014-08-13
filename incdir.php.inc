<?php 
// Include all files in the directory indicated by $dir_name
// Removes . and .. from the list of files
// Also, ignore .bak files and .svn directories

function include_php_dir($dir_name, $debug=false) {
    global $console;
        if (!is_dir($dir_name)) { return false; }
        $handle = opendir($dir_name);
        while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
	                 if (!preg_match("/bak$/i",$file) && !preg_match("/\.svn/",$file))
                     { 
                        if ($debug == true)
                            $console[] = 'included php file: '.$file;
                        include_once("$dir_name/$file"); }
                }
        }
}
function include_dir($dirname,$debug=false) {
	include_php_dir($dirname,$debug); 
}

function include_javascript_dir($dir_name, $debug=false) {
    global $console;
    $o = "";
        if (!is_dir($dir_name)) { return false; }
        $handle = scandir($dir_name);
        foreach ($handle as $file) {
                if ($file != "." && $file != "..") {
                     if (!preg_match("/bak$/i",$file) && !preg_match("/\.svn/",$file)) {
                        if ($debug == true)
                            $console[] = 'included js file: '.$file;
                $o .= "<script src=\"".$dir_name."/".$file."\" language=\"javascript\" type=\"text/javascript\"></script>\n";
            }
                }
        }
    return $o;
}

function include_stylesheet_dir($dir_name, $debug=false) {
    global $console;
    $o = "";
        if (!is_dir($dir_name)) { return false; }
        $handle = scandir($dir_name);
        foreach ($handle as $file) {
                if ($file != "." && $file != "..") {
                     if (!preg_match("/bak$/i",$file) && !preg_match("/\.svn/",$file)) {
                        if ($debug == true)
                            $console[] = 'included css file: '.$file;
                $o .= "<link href=\"".$dir_name."/".$file."\" rel=\"stylesheet\" type=\"text/css\">\n";
             }
                }
        }
    return $o;
}

?>
