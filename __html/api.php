<?php
	function sanitize($str)
	{
		return str_replace("<", "&lt;", $str);
	}

	function req_db($req, $params = [])
	{
		$db = new SQLite3("./truc.db");

		$results = $db->prepare($req);
		foreach ($params as $key => $value) {
			$results->bindParam($key, $value);
		}
		return $results->fetchArray();
	}

	// function req_db($req)
	// {
	// 	$db = new SQLite3("./truc.db");

	// 	$results = $db->query($req);
	// 	return $results->fetchArray();
	// }



	$req = $_SERVER["REQUEST_URI"];

	header("Content-Type: application/json");

	$toret = array();

	if($req == "/api/calcul/")
	{
		if(isset($_POST["calcul"]))
		{
			if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			else $cip = $_SERVER["REMOTE_ADDR"];

			req_db("INSERT INTO calculs(ip_addr, user_agent, calcul) VALUES('".$cip."', '".htmlspecialchars($_SERVER["HTTP_USER_AGENT"])."', '".str_replace("'", "", $_POST["calcul"])."');");

			try
			{
				eval('$toret["result"] = '.$_POST["calcul"].';');
			}
			catch(ParseError $e)
			{
				$toret["result"] = "ERROR";
			}
		}
	}

	if($req == "/api/user/")
	{
		if(isset($_POST["username"]))
		{
			$toret = req_db("SELECT * FROM users WHERE username = '".str_replace("'", "", $_POST["username"])."';");
		}
	}

	if($req == "/api/user/create/")
	{
		if(isset($_POST["username"]) && isset($_POST["password"]))
		{
			$toret = req_db("INSERT INTO users (username, password, admin) VALUES('".str_replace("'", "", $_POST["username"])."', '".str_replace("'", "", $_POST["password"])."', 0);");
		}
	}

	if($req == "/api/user/update/")
	{
		if(isset($_POST["username"]))
		{
			if((md5($_POST["username"]) == $_COOKIE["JSESSID"]) || ($_SESSION["CONNECTED"] == 2))
			{
				if(isset($_POST["password"])) $toret = req_db("UPDATE users SET password = '".$_POST["password"]."' WHERE username = '".$_POST["username"]."';");
				if(isset($_POST["admin"])) $toret = req_db("UPDATE users SET admin = '".$_POST["admin"]."' WHERE username = '".$_POST["username"]."';");
			}
		}
	}

	printf(json_encode($toret));
?>
