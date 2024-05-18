<?php
function req_db($req, $params = []) {
    $db = new SQLite3("./truc.db");
    $stmt = $db->prepare($req);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $results = $stmt->execute();
    echo "Paramètres de la requête : ";
    print_r($params);
    // Exécute la requête SQL et retourne le premier résultat sous forme de tableau
    if ($results === false) {
        echo "Échec de l'exécution de la requête.";
        return false;
    } else {
        echo "Requête réussie.";
        $toret = $results->fetchArray();
        return $toret;
    }
}

if (isset($_POST["username"]) && isset($_POST["password"])) {
    // Get input
    $username = htmlspecialchars($_POST["username"]); 
    $password = htmlspecialchars($_POST["password"]);

    $a = req_db( "SELECT * FROM users WHERE username = :username AND password = :password LIMIT 1;", [
        ':username' => $username,
        ':password' => $password
    ]);

    if (!$a) {
        // L'utilisateur n'existe pas, insérer nouvel utilisateur
        printf ("Successfully et de MANIERE SAFE HEINNNNN created user. Redirecting...");
        req_db('INSERT INTO users(username, password, admin) VALUES(:username, :password, :admin);', [
            ':username' => $username,
            ':password' => $password,
            ':admin' => 1
        ]);

        session_start();
        $_SESSION["CONNECTED"] = 1;
        $_SESSION["USERNAME"] = $username;
        setcookie("JSESSID", md5($username), time() + 3600, "/");
    } else {
        // L'utilisateur existe déjà, affichage du message d'erreur
        printf("Failed connecting. Redirecting...");
    }
}
?>
<script>
    window.onload = function() { setTimeout(function() { window.location.href = "/"; }, 2000); }
</script>



