<?php
	session_start();
	$redirect_uri = 'http://foxpotato.com';

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

	function printUserBox()
	{
		global $userData;
		global $isLoggedIn;
		global $authUrl;
		echo "<div class='userBox box'>";
		if($isLoggedIn)
		{
			echo "<img class='circle-image' src='".$userData['picture']."' width=100px height=100px /><br/>";
			echo "<p class='welcome'>Welcome <a href='".$userData['link']."'>".$userData['name']."</a></p>";
			echo "<div class='logout'><a href='?logout'>Logout</a></div>";
		}
		else
		{
			echo "<a href='$authUrl'>Sign in using Google</a>";
		}
		echo "</div>";
	}
?>
