<?php
	require_once("reqs.php");
	$mysql -> print_debug = false;

	if($isLoggedIn)
	{
		$uid = $userData["id"];
		$_POST = json_decode(file_get_contents('php://input'),true);
		if(isSet($_POST["data"]))
		{
			$udata = $_POST["data"];
			$sid = mysql_real_escape_string($udata["id"]);
			$data = getSchemaData("WHERE id='$sid'");
			if(count($data) != 1)
				return fail("No schema found");
			$owner = $data[$sid]["userID"];
			if(isAdmin() || $owner == $uid)
			{
				$desc = mysql_real_escape_string($udata["description"]);
				$imgur = mysql_real_escape_string($udata["imgurAlbum"]);
				if($desc == null || strlen($desc) <= 5)
					return fail("New description too short",$data);
				if(strlen($imgur) >= 7)
					return fail("Imgur link too long",$data);
				$darr = array("id"=>$sid,"description"=>$desc,"imgurAlbum"=>$imgur);
				if($mysql->insert_update("schematics",$darr,array("id"),true,"",false) !== false)
				{
					$data["success"] = true;
					echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
					return $data;
				}
				else
					return fail("Unknown error",$udata);
			}
			else
				return fail("You do not have permission to edit this schema",$udata);
		}
		else
			return fail("Malformed request");
	}
	else
		return fail("Not logged in");
?>
