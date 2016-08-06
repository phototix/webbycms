<?php
/* Automation on detect any page in pages folder */
$dir = "pages";
// Fetch Directories in pages and fetch json data for Website Meta and Other purpose.
if (is_dir($dir)){
  if ($dh = opendir($dir)){
    while (($folder = readdir($dh)) !== false){
    	if($folder=="."||$folder==".."||$folder=="index.php"||$folder=="Icon"){}else{
  			$pageFolderJson="pages/".$folder."/page.json";
  			$pageFolderIndex="pages/".$folder."/index.php";
  			if(file_exists($pageFolderJson)&&file_exists($pageFolderIndex)){
  				if($page==$folder){ $webTitle=pageJson($folder); }
  			}
  		}
    }
    closedir($dh);
  }
}
?>