<?php
	require("reqs.php");
	$numPerPage = 14;
	$currentPage = 0;

	function filter($arr)
	{
		global $userData;
		global $isLoggedIn;
		$totalFilter;
		if($isLoggedIn)
			$totalFilter = "(hidden!=1 OR hidden is null OR userID='".$userData["id"]."')";
		else
			$totalFilter = "(hidden!=1 OR hidden is null)";
		$i = 0;
		while(array_key_exists("f$i",$arr))
		{
			$filter = $arr["f$i"];
			$i++;
			$filterdata = explode("|",$filter);
			if(count($filterdata) == 2)
			{
				$type = $filterdata[0];
				$str = $filterdata[1];
				$d = "";
				if($type == "u")
					$d = "userID='".unsquashUserID($str)."'";
				if($type == "id")
					$d = "id='".mysql_real_escape_string($str)."'";
				if($d != "")
					$totalFilter .= ($totalFilter == "" ? $d : " AND $d");
			}
		}
		if($totalFilter != "") return "WHERE " . $totalFilter;
		return $totalFilter;
	}

	function limit($arr)
	{
		global $numPerPage;
		global $currentPage;
		$c = $numPerPage;
		$p = 0;
		if(array_key_exists("p",$arr))
			$p = intval($arr["p"]);
		$currentPage = $p;
		$p *= $c;
		return "LIMIT $p, $c";
	}

	function order($arr)
	{
		$o = "ORDER BY score DESC";
		if(array_key_exists("o",$arr))
		{
		}
		return $o;
	}

	$mysql->print_debug=false;
	$wfilt = filter($_GET);
	$w = $wfilt . " " . order($_GET) . " " . limit($_GET);
	$d = getSchemaData($w);
	$size = $mysql->fetch_array("SELECT count(id) FROM schematics $wfilt",MYSQLI_NUM,true)[0];
	$data = array("size"=>count($d),"data"=>$d,"totalSize"=>$size,"perPage"=>$numPerPage,"page"=>$currentPage);
	echo json_encode($data,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
