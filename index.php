<?php
	date_default_timezone_set("Europe/London");
	include_once("settings.php");
	require_once("mysql.php");
	require_once("vendor/autoload.php");
	require_once("schema.php");
	require_once("data.php");
	require_once("user.php");
	echo "<html><head><title>The FoxPotato Project - TARDIS Schema Site</title><link rel='stylesheet' type='text/css' href='style.css'/>";
	echo "<meta name='viewport' content='width=device-width; maximum-scale=1; minimum-scale=1;' />";
	echo "</head><body>";
	handleVotes();
	echo "<div class='headerCont'>";
	printFileUploadBox();
	echo "<div class='logo header'><a href='$redirect_uri'><img class='logo' src='FPP.png'/></a></div>";
	printUserBox();
	echo "</div>";
	printSchemaBox(0);
	echo "</body><html>";
?>
