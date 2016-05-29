<?php
$mysql = new Mysql($db, "localhost", $dbun, $dbpw, 3306);
$webComponentsIncluded = false;

function angImport()
{
	echo '	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.css">
		<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.5.3/angular.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.5.3/angular-animate.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.5.3/angular-aria.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.5.3/angular-messages.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>';
}

function paperImport($types)
{
	global $redirect_uri;
	global $webComponentsIncluded;
	if(!$webComponentsIncluded)
	{
		echo "<script src='http://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js'></script>\n";
		echo "<script type='text/javascript' src='$redirect_uri/bower_components/webcomponentsjs/webcomponents-lite.min.js' ></script>\n";
		$webComponentsIncluded = true;
	}
	echo "<link rel='import' href='$redirect_uri/bower_components/polymer/polymer.html'>\n";
	foreach($types as $type)
		echo "<link rel='import' href='bower_components/$type/$type.html' />\n";
}

function paper($type,$extra="",$open=true)
{
	if($open === null)
		echo "<$type $extra></$type>";
	elseif($open)
		echo "<$type $extra>";
	else
		echo "</$type>";
}

function getURL($gets=null, $useOldGet=false, $useRedirectUri=false)
{
	global $redirect_uri;
	if($useRedirectUri || $gets==null)
		$url = $redirect_uri . "?";
	else
		$url = "?";
	if($useOldGet)
	{
		foreach($_GET as $key=>$value)
		{
			if(is_array($value))
			{
				foreach($value as $sKey=>$sValue)
					$url .= "$key[$sKey]=$sValue&";
			}
			else
				$url .= "$key=$value&";
		}	
	}
	if($gets != null)
		foreach($gets as $key=>$value)
		{
			if(is_array($value))
			{
				foreach($value as $sKey=>$sValue)
					$url .= "$key[$sKey]=$sValue&";
			}
			else
				$url .= "$key=$value&";
		}
	return substr($url,0,-1);
}

function addUser($userData)
{
	global $mysql;
	$id = $userData["id"];
	$givenName=$userData["givenName"];
	$link=$userData["link"];
	$email=$userData["email"];
	$picture=$userData["picture"];
	$mysql->insert_update("googleUsers",array("id"=>$id,"givenName"=>$givenName,"link"=>$link,"email"=>$email,"picture"=>$picture),array("id"));
}

function printVoteImage($button, $score)
{
	$image = "vote";
	switch($button)
	{
		case -1: $image .= "Down"; break;
		case  0: $image .= "Null"; break;
		case  1: $image .= "Up"; break;
	}
	if($button != 0)
	{
		if($score == $button)
			$image .= "On";
		else
			$image .= "Off";
	}
	$image .= ".png";
	echo "<input type='image' src='$image'>";
}

function printVotes($schemaID,$schemaScore)
{
	global $mysql;
	global $userData;
	global $isLoggedIn;
	$score = 0;
	if($isLoggedIn)
	{
		$data = $mysql -> fetch_array("SELECT score FROM schematicVotes WHERE userID='".$userData["id"]."' AND schemaID='$schemaID'",MYSQLI_NUM,true);
		if(count($data) > 0)
			$score = $data[0];
	}
	for($i = -1; $i <=1; $i++)
	{
		if($isLoggedIn)
		{
			echo "<form method=POST>";
			echo "<input type='hidden' name='v' value='$i'>";
			echo "<input type='hidden' name='vs' value='$schemaID'>";
		}
		printVoteImage($i,$score);
		if($isLoggedIn)
			echo "</form>";
	}
	if($schemaScore == null)
		$schemaScore = 0;
	echo "<br>Votes: " . $schemaScore;
}

function printSchemaBox($page)
{
	global $mysql;
	$schemaFolder = "/home/web/schema";
	$schemaUrl = "http://foxpotato.com/schema";
	$total = $mysql -> fetch_array("SELECT count(ID) FROM schematics WHERE hidden is null",MYSQL_NUM,true)[0];
	$numPerPage = 50;
	$index = $numPerPage * $page;
	$data = $mysql->fetch_array("SELECT * FROM schematics WHERE hidden is null ORDER BY score DESC LIMIT $index,$numPerPage", MYSQLI_ASSOC, false);
	$template = file_get_contents("template/schema.php");
	echo "<div class='schemaBox'>";
	for($i = 0; $i<count($data); $i++)
	{
		$dataEntry = $data[$i];
		$date = date("F d Y H:i:s",filemtime($dataEntry["fileName"]));
		$ownerEntry = getUserData($dataEntry["userID"]);
		$text = $template;
		$text = str_replace('$OWNERPIC$',	$ownerEntry["picture"],		$text);
		$text = str_replace('$OWNERLINK$',	$ownerEntry["link"],		$text);
		$text = str_replace('$OWNERNAME$',	getUsername($ownerEntry),	$text);
		$text = str_replace('$SCHEMANAME$',	str_replace(".schema","",$dataEntry["name"]),$text);
		$text = str_replace('$SCHEMABLOCKS$',	$dataEntry["blocks"],		$text);
		$text = str_replace('$SCHEMAX$',	"TBC",				$text);
		$text = str_replace('$SCHEMAY$',	"TBC",				$text);
		$text = str_replace('$SCHEMAZ$',	"TBC",				$text);
		$text = str_replace('$SCHEMADATE$',	$date,				$text);
		$text = str_replace('$SCHEMADESC$',	$dataEntry["description"],	$text);
		echo $text;
		/*
		paper("paper-material","class='marginTop'");
		echo "<table class='schemaTable'>";
		echo "<tr class='schemaNewRow'>";
		echo "<td class='schemaUserTD'><img class='smallUserIcon' src='". $ownerEntry["picture"] . "'/><br>";
		echo "<a href='".$ownerEntry['link'] ."'>".getUsername($ownerEntry)."</a></td>";
		echo "<th>Schematic Name:<td>" . str_replace(".schema","",$dataEntry["name"]);
		echo "<th>Blocks:<td>" . $dataEntry["blocks"];
		echo "<th>Size:<td>" . $dataEntry["bounds"];
		echo "<tr><td><th>Modified:<td>".date("F d Y H:i:s",filemtime($dataEntry["fileName"]));
		echo "<th colspan=3>";
		printVotes($dataEntry["id"],$dataEntry["score"]);
		echo "<td><a href='".str_replace($schemaFolder,$schemaUrl,$dataEntry["fileName"])."'>Download here</a>";
		echo "<tr><td><td colspan=6>".$dataEntry["description"]."</tr>\n";
		echo "</table>";
		paper("paper-material",null,false);*/
	}
	echo "</div>";
	return $total > ($index + $numPerPage);
}

function handleUpload()
{
	global $userData;
	global $mysql;
	if(isset($_POST["submit"]))
	{
		$fileData = $_FILES['schemaToUpload'];
		switch($fileData['error'])
		{
			case UPLOAD_ERR_OK: break;
			default: echo "File upload failed<br>";return;
		}
		if($fileData['size'] >= 500000)
		{
			echo "File too large";
			return;
		}
		$data = readNBTFile($fileData['tmp_name']);
		if(!isSchemaFile($data))
		{
			echo "Invalid schema file!";
			return;
		}
		$name = mysql_real_escape_string($fileData["name"]);
		$bounds = getBounds($data);
		$bounds = "X:".$bounds[0] . " Y:" .$bounds[1] . " Z:".$bounds[2];
		$blocks = getSize($data);
		$desc = mysql_real_escape_string(isset($_POST["desc"]) ? $_POST["desc"] : "No description");
		$userID = $userData["id"];
		//$id = $mysql->query_id("INSERT INTO schematics(name,bounds,userID,blocks,description) VALUES('$name','$bounds','$userID','$blocks','$desc')");
		$id = $mysql->insert_update("schematics",array("name"=>$name,"bounds"=>$bounds,"userID"=>$userID,"blocks"=>$blocks,"description"=>$desc),array("userID","name"));
		$fileName = "/home/web/schema/" . $userID. "/".$id;
		if(!file_exists($fileName))
			mkdir($fileName,0755,true);
		$fileName .= "/" . $name;
		$mysql->query("UPDATE schematics SET fileName='$fileName' WHERE id='" . $id."'");
		if(!move_uploaded_file($fileData['tmp_name'],$fileName))
		{
			$mysql->query("DELETE FROM schematics WHERE id='$id'");
			echo "Upload failed";
		}
		else
		{
			echo "Upload successful!<br>";
			touch($fileName);
		}
		
	}	
}

function fail($string,$os=null)
{
	$data["success"] = false;
	$data["reason"] = $string;
	if($os != null)
		$data["oldState"] = $os;
	echo json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
}


function printFileUploadBox()
{
	global $isLoggedIn;
	echo "<div class='uploadBox'>";
	if($isLoggedIn)
	{
		handleUpload();
		echo "<form method='POST' enctype='multipart/form-data'>";
		echo "Upload a schematic<br>";
		echo "<input type='file' name='schemaToUpload'><br>";
		echo "<textarea name='desc' rows=5 cols=50 placeholder='Enter a description for the schematic'></textarea><br>";
		echo "<input type='submit' value='Upload Schema' name='submit'>";
		echo "</form>";
	}
	else
	{
		echo "You must be logged in to upload schematics!";
	}
	echo "</div>";
}

$userDataArray = array();
function getUserData($id)
{
	global $mysql;
	global $userDataArray;
	if(array_key_exists($id,$userDataArray))
		return $userDataArray[$id];
	$data = $mysql->fetch_array("SELECT * FROM googleUsers WHERE id='$id'", MYSQLI_ASSOC);
	$userDataArray[$id] = $data;
	return $data;
}

function vote($schemaID,$score)
{
	global $isLoggedIn;
	global $userData;
	global $mysql;
	if($isLoggedIn)
	{
		$score = min(1,max(-1,$score));
		if($score != 0)
			$mysql->insert_update("schematicVotes",array("userID"=>$userData["id"],"schemaID"=>$schemaID,"score"=>$score),array("userID","schemaID"));
		else
			$mysql->query("DELETE FROM schematicVotes WHERE userID='".$userData["id"]."' AND schemaID='$schemaID'");
		$mysql->query("UPDATE schematics SET score=COALESCE((SELECT sum(score) FROM schematicVotes WHERE schemaID='$schemaID'),0) WHERE id='$schemaID'");
	}
}


function handleVotes()
{
	$array = $_POST;
	if(isset($array["v"]) && isset($array["vs"]))
	{
		vote($array["vs"],$array["v"]);
	}
}
?>
