<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<meta charset="UTF-8">
	<title>
		Voiturehub.fr: Trouvez le covoiturage qu'il vous faut
	</title>
	<!-- La feuille de styles "base.css" doit être appelée en premier. -->
	<link rel="stylesheet" type="text/css" href="base.css" media="all" />
	<link rel="stylesheet" type="text/css" href="index.css" media="screen" />
</head>

<body>

<div id="global">

	<div id="entete">
		<h1>
			<img alt="" src="http://upload.wikimedia.org/wikipedia/commons/thumb/7/7f/Autoroute_icone.svg/64px-Autoroute_icone.svg.png" />
			<span><a href='index.html'>VOITUREHUB.FR</a></span>
		</h1>
		<p class="sous-titre">
			Trouvez le covoiturage qu'il vous faut parmi nos annonces !
		</p>
	</div><!-- #entete -->

	<div id="centre">

		<div id="contenu">
			<h3>Messagerie</h3>
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

			

		        if (isset($_SESSION['ID']) AND isset($_SESSION['login']))
			{
			echo 'Bonjour '.$_SESSION['login'].",<br/>";

			//On récupère l'ancienne note moyenne et l'ancien nombre de notes
			$ancnote = $_POST['ancnote'];
			$ancnbnotes = $_POST['ancnbnotes'];

			//On récupère la note attribuée que l'on muliplie par 5 car est /5
			$noteAttribuee = $_POST['note'];
			
			//On récupère le login noté
			$loginnote = $_SESSION['loginnote'];

			//On met à jour le nombre de notes
			$nvnbnotes = $ancnbnotes + 1;
			
			//On met à jour la note moyenne
			$nvnotemoyenne = ( ($ancnote*$ancnbnotes) + $noteAttribuee ) / $nvnbnotes;




			//On met à jour dans la base de données la note et le nombre de notes
			$req = $bdd->prepare('UPDATE membres SET note = :nvnote, nbnotes = :nvnbnotes  WHERE login = :loginnote');
			$req->execute(array(
			    'nvnote' => $nvnotemoyenne,
			    'nvnbnotes' => $nvnbnotes,
			    'loginnote' => $loginnote));			
			$req->closeCursor();

			echo "Merci d'avoir pris le temps de noter un conducteur.<br/>";
			echo "Le conducteur ".$loginnote." a désormais une note moyenne de ".$nvnotemoyenne."/5 sur une base de ".$nvnbnotes." notes.<br/>";


echo "<form method='post' action='connexion_action.php'>";
echo '<input type="hidden" name="pass" value="' . htmlspecialchars($_SESSION['pass']) . '" />'."\n";
echo '<input type="hidden" name="login" value="' . htmlspecialchars($_SESSION['login']) . '" />'."\n";
echo "<input type='submit' value='[ACCUEIL]' />";
echo "</form>";

			}

			?>
		</div><!-- #contenu -->
	</div><!-- #centre -->

	<div id="pied">

		<p><strong><a href="deconnexion.php">[SE DÉCONNECTER]</a></strong></p>
		<p id="copyright">
			<strong>voiturehub.fr© Tous droits réservés</strong> UV LO07
		</p>
	</div><!-- #pied -->

</div><!-- #global -->

</body>
