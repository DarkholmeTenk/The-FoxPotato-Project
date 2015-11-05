<?php
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
	
?>
