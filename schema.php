<?php

	//***************************************
	//***********FILE FUNCTIONS**************
	//***************************************
	function readNBTFile($file)
	{
		$or = error_reporting(0);
		try
		{
			$nbt = new \Nbt\Service();
			$nbt = $nbt -> loadFile($file);
		}
		catch(Exception $e)
		{
			$nbt = null;
		}
		error_reporting($or);
		return $nbt;
	}

	function isSchemaFile($data)
	{
		if($data == null)
			return false;
		try
		{
			if($data->findChildByName("bounds") === false) return false;
			if($data->findChildByName("primaryDoorFace") === false) return false;
			if($data->findChildByName("primaryDoor") === false) return false;
			if($data->findChildByName("storage") === false) return false;
			if($data->findChildByName("doors") === false) return false;
			if($data->findChildByName("name") === false) return false;
			return true;
		}
		catch(Exception $e)
		{
		}
		return false;
	}

	function getBounds($data)
	{
		if($data == null)
			return null;
		$boundData = $data->findChildByName("bounds")->getValue();
		$bounds = array();
		$bounds[0] = $boundData[0] + $boundData[2] + 1;
		$bounds[1] = $boundData[4] + 1;
		$bounds[2] = $boundData[1] + $boundData[3] + 1;
		return $bounds;
	}

	function getSize($data)
	{
		if($data == null)
			return 0;
		$storeData = $data->findChildByName("storage")->getChildren();
		return count($storeData);
	}
	
	//***************************************
	//***********DATA FUNCTIONS**************
	//***************************************

	function isOwner($arr)
	{
		global $isLoggedIn;
		if(!$isLoggedIn) return false;
		global $userData;
		return $arr["userID"] == $userData["id"];
	}
	
	function addVoteData($data)
	{
		global $mysql;
		global $userData;
		global $isLoggedIn;
		if(!$isLoggedIn)
		{
			foreach($data as $key=>$value)
				$data[$key]["canVote"] = false;
		 	return $data;
		}
		$uid = $userData["id"];
		foreach($data as $key=>$value)
		{
			if(isAdmin())
				$data[$key]["canEdit"] = true;
			if(isOwner($value))
			{
				$data[$key]["canVote"] = false;
				$data[$key]["canEdit"] = true;
			}
			else
			{
				$s = 0;
				$id = $value["id"];
				$q = $mysql->fetch_array("SELECT score FROM schematicVotes WHERE userID='$uid' AND schemaID='$id'",MYSQLI_NUM,true);
				if(count($q) > 0)
					$s = $q[0];
				$data[$key]["voteState"]=$s;	
				$data[$key]["canVote"] = true;
			}
		}
		return $data;
	}

	function addSquashedUID($data)
	{
		if($data == null || !is_array($data)) return;
		foreach($data as $key=>$value)
		{
			if(isSet($value["name"]))
				$data[$key]["name"] = substr($value["name"],0,-7);
			if(isSet($value["userID"]))
			{
				$id = $value["userID"];
				$data[$key]["userIDHex"] = squashUserID($id);
			}
			if(isSet($value["bounds"]))
			{
				$d = array();
				$cd = explode(" ",$value["bounds"]);
				foreach($cd as $k2=>$v2)
				{
					$cdd = explode(":",$v2);
					$d[$cdd[0]]= $cdd[1];
				}
				$data[$key]["bounds"] = $d;
			}
			if(isSet($value["fileName"]))
			{
				$date = date("F d Y H:i:s",filemtime($value["fileName"]));
				$url = str_replace("/home/web/schema","http://foxpotato.com/schema",$value["fileName"]);
				$data[$key]["fileName"] = $url;
				$data[$key]["modified"] = $date;
			}
		}
		return $data;
	}

	function addUserData($data)
	{
		foreach($data as $key=>$value)
		{
			if(isSet($value["userID"]))
			{
				$udata = getUserData($value["userID"]);
				if($udata == null) continue;
				$data[$key]["userPic"] = $udata["picture"];
				$data[$key]["userLink"] = $udata["link"];
				$data[$key]["userName"] = getUsername($udata);
			}
		}
		return $data;
	}

	function formatDesc($data)
	{
		foreach($data as $key=>$value)
		{
			$desc = $value["description"];
			$desc = str_replace("\\\\","\\",$desc);
			$desc = str_replace(array("\\n","\\r"),"\n",$desc);
			$desc = str_replace("\\'","'",$desc);
			$data[$key]["description"] = $desc;
		}
		return $data;
	}

	function reindex($data)
	{
		$newData = array();
		foreach($data as $key=>$value)
		{
			$newData[$value["id"]] = $value;
		}
		return $newData;
	}

	function getSchemaData($query)
	{
		global $mysql;
		$q = "SELECT * FROM schematics $query";
		$d = $mysql -> fetch_array($q, MYSQLI_ASSOC,false);
		$d = addSquashedUID($d);
		$d = addVoteData($d);
  		$d = addUserData($d);
		$d = formatDesc($d);
		$d = reindex($d);
		return $d;
	}
?>
