<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
		<title>Mini-chat</title>
		<link rel="stylesheet" type="text/css" href="base.css" media="all" />
		<link rel="stylesheet" type="text/css" href="index.css" media="screen" />
</head>
<style>
form
{
text-align:center;
}
</style>
<body>
	<?php
	session_start();
	// Connexion à la base de données
	try
	{
		$bdd = new PDO('mysql:host=localhost; dbname=carhub; charset=utf8', 'root', 'motdepasse');
	}
	catch(Exception $e)
	{
		die('Erreur :'.$e->getMessage());
	}


	//On récupère le login de celui qui est commenté avec une variable de session, puisque le poste n'est plus valble
	$login2 = $_SESSION["commente"];

	//On récupère le login de celui qui commente
	$login1 = $_SESSION['login'];



	// Insertion du message à l'aide d'une requête préparée
	echo $_POST['commentaire'];

	$req = $bdd->prepare('INSERT INTO commentaires (login_commentant, login_commente, commentaire) VALUES(?, ?, ?)');
	$req->execute(array($login1, $login2, $_POST['commentaire']));

	 echo "<form method='POST' action = 'commentaire_action.php'>";

	echo '<input type="hidden" name="commente" value="' . htmlspecialchars($login2) . '" />'."\n";

	echo "<input type='submit' value='[Retourner aux commentaires]'>";

	echo "</form>";


	// Redirection du visiteur vers la page du minichat
	//header('Location: messagerie_action.php');
	?>
</body>
</html>
