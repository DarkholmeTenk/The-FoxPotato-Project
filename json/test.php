<?php
	require("reqs.php");
	$x = "118436258441283568772";
	$d = squashUserID($x);
	$r = unsquashUserID($d);
	echo "$d<br/>";
	echo "$x<br/>";
	echo "$r<br/>";
?>
