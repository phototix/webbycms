<?php
if(!empty($_COOKIE["webbycms_visitor"])&&$_COOKIE["webbycms_visitor"]<>""){
	updateVisitor($_COOKIE["webbycms_visitor"]);
}else{
	$uniqueInsert=uniqid();
	$result=newVisitor($uniqueInsert);
	setcookie("webbycms_visitor", $uniqueInsert ,time()+(86400 * 30) * 12, "/");
}
?>