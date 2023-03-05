<?php
session_start();
/*
TItle: WebbyCMS
Authoer: Brandon Chong
Version: 1.0
*/

/*
Global settings
*/
?>
<?php require_once('controller/conn.php'); ?>
<?php require_once('controller/functions.php'); ?>
<?php require_once('controller/common.php'); ?>
<?php require_once('controller/form.php'); ?>
<?php
/*
Below is all front related.
To render and handle page info and form processing
*/
?>
<?php require_once("includes/htmlstart.php"); ?>
<?php require_once("includes/views.php"); ?>
<?php require_once("includes/javascripts.php"); ?>
<?php require_once("includes/htmlend.php"); ?>