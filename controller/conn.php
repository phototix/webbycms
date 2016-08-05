<?php
/*
TItle: Database & Global Connection Handler
Authoer: Brandon Chong
Version: 3.0
Description: This conn.php file contains not just mysql database connections, 
also you can configure all your views or pages connections.
General Configurations of your web applications
*/

/* Live Database Connection */
$conn_mysql_host="";
$conn_mysql_username="";
$conn_mysql_password="";
$conn_mysql_database="";

/* Development Database Connection */
$conn_mysql_host_dev="localhost";
$conn_mysql_username_dev="root";
$conn_mysql_password_dev="1234";
$conn_mysql_database_dev="";

/* Here you can put your Localhost/Development Enviroment endpoint, so can be isolated from live database. */
$localhost_dev="";

/* Include PHP Variables and defines. */
require("define.php");

/* 
Define to use localhost development database and is the web application use any database 
Put 'yes' to enable, 'no' to disable.
*/
$useLocalMySql="yes";$useDataTable="no";

if($useDataTable=="yes"){
	if($useLocalMySql=="yes"){
		if($_SERVER["HTTP_HOST"]==$localhost_dev){
			$conn_mysql_host=$conn_mysql_host_dev;
			$conn_mysql_username=$conn_mysql_username_dev;
			$conn_mysql_password=$conn_mysql_password_dev;
			$conn_mysql_database=$conn_mysql_database_dev;
		}
	}
	if($conn_mysql_username==""||$conn_mysql_password==""||$conn_mysql_database==""){
		die('Database Config Error!');
	}else{
		$con = mysqli_connect($conn_mysql_host, $conn_mysql_username , $conn_mysql_password, $conn_mysql_database);
		if (!$con){
			die('Could not connect: ' . mysql_error());
		}
	}
}

/* Set your application timezone. */
date_default_timezone_set('Asia/Singapore');
?>
