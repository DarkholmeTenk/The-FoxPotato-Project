<?php
require_once("../reqs.php");
$mysql->print_debug=false;
function handleUploadData()
{
	global $userData;
	global $isLoggedIn;
	global $mysql;

	if(!$isLoggedIn)
		return fail("Need to be logged in to upload schemas!");

	$fileData = $_FILES['schemaToUpload'];
	switch($fileData['error'])
	{
		case UPLOAD_ERR_OK: break;
		default: return fail("File upload failed");
	}
	if($fileData['size'] >= 500000)
		return fail("File too large");
	$data = readNBTFile($fileData['tmp_name']);
	if(!isSchemaFile($data))
		return fail("Invalid schema file!");
	if(!isSet($_POST["desc"]) || strlen($_POST["desc"]) <= 5)
		return fail("Description too short");
	$name = mysql_real_escape_string($fileData["name"]);
	$bounds = getBounds($data);
	$bounds = "X:".$bounds[0] . " Y:" .$bounds[1] . " Z:".$bounds[2];
	$blocks = getSize($data);
	$desc = mysql_real_escape_string(isset($_POST["desc"]) ? $_POST["desc"] : "No description");
	$imgur = mysql_real_escape_string(isset($_POST["imgur"]) ? $_POST["imgur"] : "");
	$userID = $userData["id"];
	$id = $mysql->insert_update("schematics",array("name"=>$name,"bounds"=>$bounds,"userID"=>$userID,"blocks"=>$blocks,"description"=>$desc,"imgurAlbum"=>$imgur),array("userID","name"));
	$fileName = "/home/web/schema/" . $userID. "/".$id;
	if(!file_exists($fileName))
		mkdir($fileName,0755,true);
	$fileName .= "/" . $name;
	$mysql->query("UPDATE schematics SET fileName='$fileName' WHERE id='" . $id."'");
	if(!move_uploaded_file($fileData['tmp_name'],$fileName))
	{
		$mysql->query("DELETE FROM schematics WHERE id='$id'");
		return fail("Unknown reason");
	}
	else
	{
		touch($fileName);
		$returnVal = array('success'=>"true");
		echo json_encode($returnVal);
	}
}

handleUploadData();

?>
