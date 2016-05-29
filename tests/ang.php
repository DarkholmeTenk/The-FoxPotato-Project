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
<div class='frameContainer' id='toast-container' layout-fill ng-init='updateData("")'>
<md-content layout-fill>
<md-progress-circular ng-if='!schemaData' md-mode="indeterminate"></md-progress-circular>
<md-whiteframe class="md-whiteframe-1dp marginTop" flex='60' flex-xs='100' flex-sm='90' layout='column' layout-align="stretch stretch" ng-if='schemas' ng-repeat='item in schemas'>
<div ng-if='!item.edit' ng-include="'template/schema.html'"></div>
<div ng-if='item.edit' ng-include="'template/schemaform.html'"></div>
</md-whiteframe>
</md-content>
</div>
</body>
</html>
