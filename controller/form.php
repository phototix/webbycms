<?php
$newInsertID="";$currentURl="http://".$_SERVER["HTTP_HOST"]."".$_SERVER["REQUEST_URI"];
$formset="";
if(isset($_GET["formset"])&&!empty($_GET["formset"])){
	$formset=$_GET["formset"];
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
