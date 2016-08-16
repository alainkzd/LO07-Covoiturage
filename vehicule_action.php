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
			<h3>Espace membre</h3>
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


				$req = $bdd->prepare('UPDATE membres SET marque = :marque, modele = :modele, couleur= :couleur, misenservice= :misenservice WHERE login = :login');
$req->execute(array(
    'marque' => $_POST['marque'],
    'modele' => $_POST['modele'],
    'couleur' => $_POST['couleur'],
    'misenservice' => $_POST['misenservice'],
    'login' => $_SESSION['login']));
			
			echo "Le véhicule a été renseigné !<br/>";
			
			}
echo "<form method='post' action='connexion_action.php'>";
echo '<input type="hidden" name="pass" value="' . htmlspecialchars($_SESSION['pass']) . '" />'."\n";
echo '<input type="hidden" name="login" value="' . htmlspecialchars($_SESSION['login']) . '" />'."\n";
echo "<input type='submit' value='[ACCUEIL]' />";
echo "</form>";
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
