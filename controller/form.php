<?php
$newInsertID="";$currentURl="http://".$_SERVER["HTTP_HOST"]."".$_SERVER["REQUEST_URI"];

/* Ajax Form Handler */
$formset="";
if(isset($_GET["formset"])&&!empty($_GET["formset"])){
	$formset=$_GET["formset"];
}
$showLoading="";
if(isset($_GET["showLoading"])&&!empty($_GET["showLoading"])){
	$showLoading=$_GET["showLoading"];
}
/* 
Define URL after form submited (For AJAX Purpose only) 
Set $redirectURL='noredirect' to disable redirection after AJAX Submmit.
Make sure to set your XMLHttpRequest body if redirect is needed.
*/
$redirectURL="";
if(isset($_GET["redirectURL"])&&!empty($_GET["redirectURL"])){
	$redirectURL=$_GET["redirectURL"];
}

if($formset=="ajax"){
	session_start();
	require("conn.php");
	require("functions.php");
	require("common.php");
	if($form<>""){
	$systemForm="forms/".$form.".php";
		if(file_exists($systemForm)){
			include($systemForm);

			if($newInsertID<>""){
				?><script>window.location="<?=$currentURl?>";</script><?
			}
			/* Error Message Delivery Function (Use Session) */
			if($systemSucces<>""){
				if(isset($_SESSION["systemSucces"])){
					$_SESSION["systemSucces"]=$systemSucces;	
				}
			}
			if($systemError<>""){
				if(isset($_SESSION["systemError"])){
					$_SESSION["systemError"]=$systemError;	
				}
			}

			if($showLoading==""){
				?><h1>Loading...</h1><?
				if(($redirectURL==""||$redirectURL<>"")&&$redirectURL<>"noredirect"){
					if($redirectURL==""){ $redirectURL="/"; }
					?><script>window.location="<?=$redirectURL?>";</script><?
				}elseif($redirectURL=="noredirect"){

				}
			}

		}else{
			?><script>window.location="?<?=$form?>&<?=$formset?>";</script><?
		}
	}
}else{
	if($form<>""){
		$systemForm="controller/forms/".$form.".php";
		if(file_exists($systemForm)){
			include($systemForm);

			if($newInsertID<>""){
				?><script>window.location="<?=$currentURl?>";</script><?
			}
			/* Error Message Delivery Function (Use Session) */
			if($systemSucces<>""){
				if(isset($_SESSION["systemSucces"])){
					$_SESSION["systemSucces"]=$systemSucces;	
				}
			}
			if($systemError<>""){
				if(isset($_SESSION["systemError"])){
					$_SESSION["systemError"]=$systemError;	
				}
			}

		}else{
			?><script>window.location="?<?=$form?>";</script><?
		}
	}
}
?>
