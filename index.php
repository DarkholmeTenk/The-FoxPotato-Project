<?php
	require_once("reqs.php");
	echo "<html><head><title>The FoxPotato Project - TARDIS Schema Site</title><link rel='stylesheet' type='text/css' href='style.css'/>";
	echo "<meta name='viewport' content='width=device-width; maximum-scale=1; minimum-scale=1;' />";
	paperImport(array("paper-drawer-panel","iron-icons","paper-header-panel","paper-toolbar","polymer","paper-button","paper-icon-button","paper-card"));
	echo "</head><body>";
	handleVotes();
	paper("paper-drawer-panel","drawer-width='400px;' responsive-width='1200px'");
	echo "<div drawer>\n";
	printUserBox();
	printFileUploadBox();
	echo "</div>";
	echo "<div main>\n";
	paper("paper-header-panel");
	paper("paper-toolbar","class='tall'");
	paper("paper-icon-button","icon='menu' paper-drawer-toggle",null);
	echo "<div class='middle title'>\n";
	echo "<a href='$redirect_uri'><img class='logo' src='FPPSmall.png'/>The FoxPotato Project</a>";
	echo "</div>";
	paper("paper-toolbar",null,false);
	printSchemaBox(0);
	paper("paper-header-panel",null,false);
	echo "</div>\n";
	echo paper("paper-drawer-panel",null,false);
	echo "</body><html>";
?>
