<?php
	date_default_timezone_set("Europe/London");
	include_once("settings.php");
	require_once("mysql.php");
	require_once("vendor/autoload.php");
	require_once("schema.php");
	require_once("data.php");
	require_once("user.php");
	echo "<html><head><title>TARDIS Schema Site</title><link rel='stylesheet' type='text/css' href='style.css'/></head><body>";
	echo "<div class='logo'><a href='http://foxpotato.com'><img class='logo' src='FPP.png'/></a></div>";
	handleVotes();
	printUserBox();
	printFileUploadBox();
	printSchemaBox(0);
	echo "</body><html>";
?>
