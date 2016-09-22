<?php
/*
Title: Pages Index
Author: Brandon Chong
Version: 2.1
*/

// Template Default set include

if($systemDemo=="yes"){
	
	if($systemDemoStart=="yes"){
		if($page<>""&&$page<>'index'){
			$pageFileSystem="pages/".$page.'/index.php';
			if(file_exists($pageFileSystem)){

				include("pages/includes/layout/bodystart.php");
				include($pageFileSystem);
				include("pages/includes/layout/bodyend.php");

			}else{
				include('pages/error/404.php');
			}
		}else{
			$pageFileSystem='pages/home/index.php';
			if(file_exists($pageFileSystem)){
				include("pages/includes/layout/bodystart.php");
				include($pageFileSystem);
				include("pages/includes/layout/bodyend.php");
			}else{
				include('pages/error/404.php');
			}
		}
	}else{

		if($systemWebLaunch==1){
			if($page<>""&&$page<>'index'){
				$pageFileSystem="pages/".$page.'/index.php';
				if(file_exists($pageFileSystem)){
					include("pages/includes/layout/bodystart.php");
					include($pageFileSystem);
					include("pages/includes/layout/bodyend.php");
				}else{
					include('pages/error/404.php');
				}
			}else{
				$pageFileSystem='pages/home/index.php';
				if(file_exists($pageFileSystem)){
					include("pages/includes/layout/bodystart.php");
					include($pageFileSystem);
					include("pages/includes/layout/bodyend.php");
				}else{
					include('pages/error/404.php');
				}
			}
		}else{
			$pageFileSystem='pages/demo/index.php';
			if(file_exists($pageFileSystem)){
				include($pageFileSystem);
			}else{
				include('pages/error/404.php');
			}
		}

	}

}else{

	if($page<>""&&$page<>'index'){

		$pageFileSystem="pages/".$page.'/index.php';
		if(file_exists($pageFileSystem)){
			include("pages/includes/layout/bodystart.php");
			include($pageFileSystem);
			include("pages/includes/layout/bodyend.php");
		}else{
			include('pages/error/404.php');
		}

	}else{
		
		$pageFileSystem='pages/home/index.php';
		if(file_exists($pageFileSystem)){
			include("pages/includes/layout/bodystart.php");
			include($pageFileSystem);
			include("pages/includes/layout/bodyend.php");
		}else{
			include('pages/error/404.php');
		}


	}

}
?>