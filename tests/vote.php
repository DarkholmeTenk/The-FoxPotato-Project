<?php
	$data = array();
	require_once("../reqs.php");
	$mysql -> print_debug = false;

	function fail($string)
	{
		$data["success"] = false;
		$data["reason"] = $string;
		echo json_encode($data);
	}

	if($isLoggedIn)
	{
		$uid = $userData["id"];
		if(isSet($_GET["schemaID"]) && isSet($_GET["score"]))
		{
			$sid = mysql_real_escape_string($_GET["schemaID"]);
			$score = $_GET["score"];
			$owner = $mysql->fetch_array("SELECT userID FROM schematics WHERE id='$sid'",MYSQLI_NUM,false);
			if(count($owner) != 1)
				return fail("No schema found");
			$owner = $owner[0][0];
			if($owner == $uid)
				return fail("Can't vote for yourself");
			if($score < -1 || $score > 1)
				return fail("Invalid score value");
			if($score == 0)
				$mysql->query("DELETE FROM schematicVotes WHERE userID='".$uid."' AND schemaID='$sid'");
			else
				$mysql->insert_update("schematicVotes",array("userID"=>$uid,"schemaID"=>$sid,"score"=>$score),array("userID","schemaID"));				 $mysql->query("UPDATE schematics SET score=COALESCE((SELECT sum(score) FROM schematicVotes WHERE schemaID='$sid'),0) WHERE id='$sid'");
			$data["success"] = true;
			$data["score"] = $score;
			$data["newState"] = getSchemaData("WHERE id='$sid'");
			echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			return;
		}
		else
			return fail("Malformed request");
	}
	else
		return fail("Not logged in");
?>
