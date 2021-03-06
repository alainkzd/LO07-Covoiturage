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
	<script type="text/javascript" src="date_heure.js"></script>
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
			<?php echo "<br/>"; ?>
			<span id="date_heure"></span>
			<script type="text/javascript">window.onload = date_heure('date_heure');</script>

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

			$_SESSION['pass'] = $_POST['pass'];
			// Hachage du mot de passe
			$pass_hache = sha1($_POST['pass']);

			// Vérification des identifiants
			$req = $bdd->prepare('SELECT ID FROM membres WHERE login = :login AND pass =:pass');
			$req->execute(array(
			    'login' => $_POST['login'],
			    'pass' => $pass_hache));

			$resultat = $req->fetch();


			if (!$resultat)
			{
			    echo 'Mauvais identifiant ou mot de passe !';
			}
			else
			{
			    session_start();
			    $_SESSION['ID'] = $resultat['ID'];
			    $_SESSION['login'] = $_POST['login'];
			    echo "Vous êtes connecté !<br/>";

			    if (isset($_SESSION['ID']) AND isset($_SESSION['login']))
				{
				    echo 'Bonjour ' .$_SESSION['login']."<br/>";
				    echo "<br/>Aujourd'hui, <a href='trajet.php'>je souhaite être conducteur</a><br/>";
				    echo "Aujourd'hui, <a href='passager.php'>je souhaite être passager</a><br/>";
				    echo "<br/><a href='continuerCreerDisc.php'>Envoyer un message</a><br/>";
				    echo "<a href='choixCommentaire.php'>Laisser un commentaire</a><br/>";
				    echo "<a href='choixNote.php'>Noter un utilisateur</a><br/>";
				    echo "<a href='choixProfil.php'>Consulter un profil</a><br/>";

				    echo "<br/><a href='notifications.php'>Mes notifications</a><br/>";
				    echo "<a href='moncompte.php'>Mon compte</a><br/>";
				    echo "<a href='mestrajets.php'>Mes trajets</a><br/>";
//!!!!!!!!!!!!!!!!!!!!!!!!!!!! COMPTE ADMINISTRATEUR !!!!!!!!!!!!!!!!!!!!!!				
				
				    $admin = "admin";

			    	    if ($_SESSION['login'] == $admin) {
					echo "<br/><a href='choixVisualisationCompte.php'>Visualisation de la liste des comptes</a><br/>";
				        echo "<a href='visualisationTrajets.php'>Visualisation de la liste des trajets</a><br/>";
				    }

				    //On vérifie si l'utilisteur en ligne a un solde négatif
				    //Dans ce cas, on lui envoie une notification pour lui indiquer qu'il est à découvert
				    //On l'invite à recharger son compte
				    //On récupère l'argent dont dispose l'utilisateur en ligne
				    $req = $bdd->prepare('SELECT argent FROM membres WHERE ID = :id');
			            $req->execute(array('id' => $_SESSION['ID']));
				    while ($donnees = $req->fetch())
				    {
					    $argentLigne = $donnees['argent'];
				    }
				    $req->closeCursor();

				    if ($argentLigne < 0) {
					$req = $bdd->prepare('INSERT INTO notifications(ID_concerne, type, notification) VALUES(:ID_concerne, :type, :notification)');
					$req->execute(array(
			    'ID_concerne' => $_SESSION['ID'],
			    'type' => 'AVIS DE DÉCOUVERT',
			    'notification' => "Vous présentez actuellement un découvert de ".$argentLigne." euros. Veuillez recharger votre compte dans les meilleurs délais sous peine de voir votre compte bloqué."
			    ));
				    }
				
				}
			}
			
			?>








		</div><!-- #contenu -->
	</div><!-- #centre -->

	<div id="pied">
		<p><strong>voiturehub.fr© Tous droits réservés</strong> UV LO07</p>
		<p id="copyright">
			<a href="deconnexion.php">[SE DÉCONNECTER]</a>
		</p>
	</div><!-- #pied -->

</div><!-- #global -->

</body>
