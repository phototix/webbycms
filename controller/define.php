<?php
// Add Custom error handler function
function customError($errno, $errstr, $errfile, $errline) {
  echo '<div class="alert alert-danger"><p><b>Error:</b> ['.$errno.'] '.$errstr.' <br>at '.$errfile.': line-> '.$errline.'</div></p>';
}
// Return Error into function
set_error_handler("customError");

$row="";
$systemError="";$systemSucces="";
if(empty($_SESSION["systemError"])){
	$_SESSION["systemError"]="";
}
if(empty($_SESSION["systemSucces"])){
	$_SESSION["systemSucces"]="";
}
$page="";
if(isset($_GET["page"])&&!empty($_GET["page"])){
	$page=$_GET["page"];
}
if($page==""||$page=="index"){
	$page="home";
}
$cate="";
if(isset($_GET["cate"])&&!empty($_GET["cate"])){
	$cate=$_GET["cate"];
}
$action="";
if(isset($_GET["action"])&&!empty($_GET["action"])){
	$action=$_GET["action"];
}
$id="";
if(isset($_GET["id"])&&!empty($_GET["id"])){
	$id=$_GET["id"];
}
$form="";
if(isset($_POST["form"])&&!empty($_POST["form"])){
	$form=$_POST["form"];
}
$token="";
if(isset($_POST["token"])&&!empty($_POST["token"])){
	$token=$_POST["token"];
}

/* Pre Defined Variables for Date & Time - This will used for Token generation */
$Today=date("Y-m-d");
$Day=date("d");
$Month=date("m");
$Year=date("Y");
$Hour=date("g");
$Minute=date("i");
$Seconds=date("s");
$Time=date("g:i A");

$Token=md5($Today."webbycms".uniqid().$Time);
?>
