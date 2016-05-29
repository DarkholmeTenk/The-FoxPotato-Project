<html ng-app='mainApp' ng-controller='schemaController'>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
	require_once("../reqs.php");
	echo "<base href='$redirect_uri'>";
	angImport();
	echo "<script src='tests/script.js'></script>\n";
	echo "<script src='tests/schema.js'></script>\n";
?>
<link rel='stylesheet' type='text/css' href='style.css'/>
</head>
<?php
	if($isLoggedIn)
		echo "<body ng-init=\"imgur.cid='$imgurid'; logIn(".$userData['id'].");\">\n";
	else
		echo "<body ng-init=\"imgur.cid='$imgurid'\">\n";
?>
<div layout-fill layout-align='center center' ng-include='"template/schemapage.html"'>
</div>
</body>
</html>
