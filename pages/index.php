<?php
/*
Title: Pages Index
Author: Brandon Chong
Version: 2.1
*/

// Template Default set include
if($page<>""&&$page<>'index'){
	$pageFileSystem="pages/".$page.'/index.php';
	if(file_exists($pageFileSystem)){
		include($pageFileSystem);
	}else{
		include('pages/error/404.php');
	}
}else{
	$pageFileSystem='pages/home/index.php';
	if(file_exists($pageFileSystem)){
		include($pageFileSystem);
	}else{
		include('pages/error/404.php');
	}
}
?>