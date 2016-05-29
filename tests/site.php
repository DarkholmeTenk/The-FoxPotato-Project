<html ng-app='mainApp' ng-controller='siteController'>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
	require_once("../reqs.php");
	echo "<base href='$redirect_uri'>";
	angImport();
	echo "<script src='tests/script.js'></script>\n";
	echo "<script src='tests/site.js'></script>\n";
	echo "<script src='tests/user.js'></script>\n";
	echo "<script src='tests/schema.js'></script>\n";
?>
<link rel='stylesheet' type='text/css' href='style.css'/>
</head>
<?php
	$contInit = "imgur.cid='$imgurid'; " . ($isLoggedIn ? "logIn('".$userData['id']."');":"");
	echo "<body ng-init=\"$contInit\">\n";
?>
<div layout-fill layout='row'>
<md-sidenav md-component-id="left" class="site-sidenav md-sidenav-left">
	<?php printUserBox() ?>
	<md-list flex>
		<md-list-item> TEST1</md-list-item>
		<md-list-item> TEST2</md-list-item>
	</md-list>
</md-sidenav>
<div id='toast-container' layout-fill layout='column'>
	<md-toolbar flex class='md-tall' md-scroll-shrink='false' md-shrink-speed-factor='0.2'>
		<div class='md-toolbar-tools-bottom'>
			<md-button class='md-icon-button' ng-click='openMenu()'><md-icon>menu</md-icon></md-button>
			<img hide show-gt-xs src='FPPSmall.png'></img>
			The FoxPotato Project
		</div>
	</md-toolbar>
	<div flex layout='row'>
	<md-content flex>
		<div flex layout-align='center center' ng-controller='schemaController' ng-init="<?php echo $contInit ?>"ng-include='"template/schemapage.html"'></div>
	</md-content>
	</div>
</div>
<md-sidenav md-component-id="right" class="site-sidenav md-sidenav-right">
	<div ng-controller='schemaController'>
		<md-list flex>
			<md-list-item> TEST1</md-list-item>
			<md-list-item> TEST2</md-list-item>
		</md-list>
	</div>
</md-sidenav>
</div>
</body>
</html>
