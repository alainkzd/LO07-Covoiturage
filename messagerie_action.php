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
    
    <form action="messagerie_action_post.php" method="post">
        <p>
        <label for="message">Message à envoyer</label> :  <input type="text" name="message" id="message" /><br />
        <input type="submit" value="Envoyer" />
	</p>
    </form>

<?php
session_start();

//Connexion à la base de données
try
{
	$bdd = new PDO('mysql:host=localhost; dbname=carhub; charset=utf8', 'root', 'motdepasse');
}
catch(Exception $e)
{
	die('Erreur :'.$e->getMessage());
}

//On récupère le login de celui en ligne (celui qui contacte)
$req = $bdd->prepare('SELECT login FROM membres WHERE ID = :id');

$req->execute(array('id' => $_SESSION['ID']));
while ($donnees = $req->fetch())
{
    $login1 = $donnees['login'];
}

$req->closeCursor();


//On récupère le login de celui qui est contacté
$login2 = $_POST["interloc"];
$_SESSION['interloc'] = $login2;
$login2 = $_SESSION['interloc'];



//DANS LES DEUX SENS
//On récupère l'ID de la conversation dans le premiers sens
$req = $bdd->prepare('SELECT ID_conversation FROM conversations WHERE login1 = :login1 OR login1 = :login2 AND login2 = :login2 OR login2 = :login1');
$req->execute(array('login1' => $login1, 'login2' => $login2));
while ($donnees = $req->fetch())
{
    $IDconv = $donnees['ID_conversation'];
}
$req->closeCursor();


$reponse = $bdd->prepare('SELECT login, message FROM messagerie WHERE ID_conversation = :ID_conversation ORDER BY ID_message DESC');
$reponse->execute(array('ID_conversation' => $IDconv));

// Affichage de chaque message (toutes les données sont protégées par htmlspecialchars)
while ($donnees = $reponse->fetch())
{
	echo '<p><strong>' . htmlspecialchars($donnees['login']) . '</strong> : ' . htmlspecialchars($donnees['message']) . '</p>';
}
$reponse->closeCursor();

echo "<form method='post' action='connexion_action.php'>";
echo '<input type="hidden" name="pass" value="' . htmlspecialchars($_SESSION['pass']) . '" />'."\n";
echo '<input type="hidden" name="login" value="' . htmlspecialchars($_SESSION['login']) . '" />'."\n";
echo "<input type='submit' value='[ACCUEIL]' />";
echo "</form>";

?>
	<div id="pied">
		
		<p><strong><a href="deconnexion.php">[SE DÉCONNECTER]</a></strong></p>
		<p id="copyright">
			<strong>voiturehub.fr© Tous droits réservés</strong> UV LO07
		</p>
	</div><!-- #pied -->
    </body>
</html>
