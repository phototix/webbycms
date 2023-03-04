<?php
if (!isset($_SERVER['HTTP_HOST'])) {
	return;
}
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

// In AJAX mode
if($formset=="ajax"){
	session_start();
	require("conn.php");
	require("functions.php");
	require("common.php");
	if($form<>""){

	// Load page's forms file to form action in AJAX mode.
	$systemForm="pages/".$page."/forms/".$form.".php";
		if(file_exists($systemForm)){
			include($systemForm);

			// Redirect to currentURL if mysql query have new insertID
			if($newInsertID<>""){
				?><script>window.location="<?=$currentURl?>";</script><?php
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

			// Show loading text
			if($showLoading==""){
				?><h1>Loading...</h1><?php

				// Redirect to given redirectURL if exsited. Else nothing
				if(($redirectURL==""||$redirectURL<>"")&&$redirectURL<>"noredirect"){
					if($redirectURL==""){ $redirectURL="/"; }
					?><script>window.location="<?=$redirectURL?>";</script><?php
				}elseif($redirectURL=="noredirect"){
					// No redirection here.
				}
			}

		}else{
			// Return to Root Home and put missing form file in parameter.
			?><script>window.location="?<?=$form?>&<?=$formset?>";</script><?php
		}
	}

// In normal FORM post mode
}else{
	if($form<>""){

		// Load page's forms file to form action.
		$systemForm="pages/".$page."/forms/".$form.".php";
		if(file_exists($systemForm)){
			include($systemForm);

			// Redirect to currentURL if mysql query have new insertID
			if($newInsertID<>""){
				?><script>window.location="<?=$currentURl?>";</script><?php
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
			// Return to Root Home and put missing form file in parameter.
			?><script>window.location="?<?=$form?>";</script><?php
		}
	}
}
?>
