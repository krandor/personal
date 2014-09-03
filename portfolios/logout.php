<?php
session_start();
require_once('include/classes.php');
if($_SESSION['logged_in'])
{
	unset($_SESSION['logged_in']);
	unset($_SESSION['user_id']);
	unset($_SESSION['user']);
	session_destroy();
	header("Location: index.php");
}
?>