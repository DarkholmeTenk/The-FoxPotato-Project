<?php
	session_start();
	$isLoggedIn = false;
	$client = new Google_Client();
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);
	$client->setRedirectUri($redirect_uri);
	$client->addScope("https://www.googleapis.com/auth/userinfo.email");
	
	$objOAuthService = new Google_Service_Oauth2($client);

	if (isset($_REQUEST['logout']) && isset($_SESSION['access_token']))
	{
		$client->revokeToken($_SESSION['access_token']);
		unset($_SESSION['access_token']);
  		header('Location: ' . $redirect_uri); //redirect user back to page
	}

	
	if (isset($_GET['code']))
	{
		$client->authenticate($_GET['code']);
		$_SESSION["access_token"] = $client->getAccessToken();
		addUser($objOAuthService->userinfo->get());
  		header('Location: ' . $redirect_uri); //redirect user back to page
	}

	if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
		try
		{
			$client->setAccessToken($_SESSION['access_token']);
			$userData = $objOAuthService->userinfo->get();
			$userDataArray[$userData["id"]] = $userData;
			$isLoggedIn = true;
		}
		catch(Exception $e)
		{
			unset($_SESSION['access_token']);
		}
	}
	else
	{
		$authUrl = $client->createAuthUrl();
		$isLoggedIn = false;
	}

	function blockConvert($id,$ob,$nb,$n,$nc)
	{	
		$len = strlen($id);
		$str = "";
		//echo "Converting: $id<br/>";
		for($i = 0; $i < ceil($len/$n); $i++)
		{
			$o = $len - ($i * $n);
			$sstr = substr($id,max(0,$o-$n),$n);
			$cstr = base_convert($sstr,$ob,$nb);
			//echo "convert $sstr $cstr<br>";
			while(strlen($cstr) < $nc)
				$cstr = "0".$cstr;
			$str = $cstr . $str;
		}
		return $str;
	}

	function unsquashUserID($id)
	{
		return blockConvert($id,36,10,2,3);	
	}

	function squashUserID($id)
	{
		return blockConvert($id,10,36,3,2);	
		/*$len = strlen($id);
		$str = "";
		for($i = 0; $i < ceil($len/4); $i++)
		{
			$o = $len - ($i * 4);
			$sstr = str_split($id,$o,max(0,$o-4));
			$cstr = base_convert($sstr,10,36);
			while(strlen($cstr) < 2)
				$cstr = "0".$cstr;
			$str = $cstr . $str;
		}
		return $str;*/
	}

	function handleNameChange()
	{
		global $userData;
		global $mysql;
		if(!isset($_POST["displayName"])) return;
		$newName = mysql_real_escape_string($_POST["displayName"]);
		$count = $mysql->num_entries("SELECT id FROM googleUsers WHERE displayName='$newName' AND id!='".$userData["id"]."'");
		if($count != 0) return;
		$userData["displayName"] = $newName;
		$mysql -> query("UPDATE googleUsers SET displayName='$newName' WHERE id='".$userData["id"]."'");
	}

	function isAdmin()
	{
		global $isLoggedIn;
		if(!$isLoggedIn) return false;
		global $userData;
		return $userData["id"] == 118436258441283568772;
	}

	function printUserBox()
	{
		global $userData;
		global $isLoggedIn;
		global $authUrl;
		handleNameChange();
		echo "<div class='userBox marginTop'>\n";
		if($isLoggedIn)
		{
			echo "<img class='circle-image' src='".$userData['picture']."' width=100px height=100px /><br/>";
			if(!isset($_GET["settings"]))
				echo "<p class='welcome'>Welcome <a href='".$userData['link']."'>".getUsername($userData)."</a></p>";
			else
			{
				echo "<form action='".getUrl()."' method='post'>";
				echo "<p class='welcome'>Welcome";
				echo "<input type='text' name='displayName' value='".getUsername($userData)."'/>";
				echo "<input type='submit' value='Submit'>";
				echo "</p></form>";
			}
			echo "<div class='userBoxBottom'>";
			echo "<div class='settings'><a href='?settings'><img src='Settings.png' width=16px height=16px /></a></div>";
			echo "<div class='logout'><a href='?logout'>Logout</a></div></div>";
		}
		else
		{
			echo "<a href='$authUrl'>Sign in using Google</a>";
		}
		echo "</div>";
	}

	function getUsername($userData)
	{
		global $mysql;
		if(!isset($userData["displayName"]))
		{
			$arr = $mysql -> fetch_array("SELECT displayName FROM googleUsers WHERE id='".$userData["id"]."'",MYSQL_NUM,true);
			$name = $arr[0];
			$userData["displayName"]=$name;
		}
		return $userData["displayName"] == null ? $userData["givenName"]:$userData["displayName"];
	}
?>
