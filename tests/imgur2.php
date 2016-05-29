<html ng-app='mainApp' ng-controller='schemaController'>
<head>
<?php
	require_once("../reqs.php");
	echo "<base href='$redirect_uri'>";
	angImport();
?>
<script src='tests/script.js'></script>
<script src='tests/schema.js'></script>
</head>
<?php
	echo "<body ng-init='imgur.cid=\"$imgurid\"'>";
?>
<md-input-container><label>Album ID</label><input ng-model='req'></input></md-input-container>
<md-button ng-click='imgur.getAlbumInfo(req)'>Submit</md-button>
{{display}}
<div ng-repeat='image in images'>
<img ng-src="http://i.imgur.com/{{image}}m.png"></img>
</div>
</body>
</html>
