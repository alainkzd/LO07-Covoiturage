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
			<img alt="" src="picto/07.png" />
			<span><a href='index.html'>VOITUREHUB.FR</a></span>
		</h1>
		<p class="sous-titre">
			Trouvez le covoiturage qu'il vous faut parmi nos annonces !
		</p>
	</div><!-- #entete -->

	<div id="centre">

		<div id="contenu">
			<h3>Espace passager</h3>
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


			echo 'Bonjour '.$_SESSION['login']."<br/>";


			$reponse = $bdd->prepare('SELECT ID_trajet, ID_conducteur, nbplacesRes FROM reservations WHERE ID_passager = :ID');
			$reponse->execute(array('ID' => $_SESSION['ID']));
			while ($donnees = $reponse->fetch())
			{
				$IDtrajet = $donnees['ID_trajet'];
				$IDconducteur = $donnees['ID_conducteur'];
				$nbplacesReservees = $donnees['nbplacesRes'];
			}	
			$reponse->closeCursor();

			//On récupère le nombre de places et le prix du  trajet
			$reponse = $bdd->prepare('SELECT nbplaces, prix FROM trajets WHERE ID = :ID');
			$reponse->execute(array('ID' => $IDtrajet));
			while ($donnees = $reponse->fetch())
			{
				$nbplaces = $donnees['nbplaces'];
				$prixtrajet = $donnees['prix'];
			}	
			$reponse->closeCursor();

			//On récupère la somme d'argent dont disposait le passager
			$reponse = $bdd->prepare('SELECT argent FROM membres WHERE ID = :ID');
			$reponse->execute(array('ID' => $_SESSION['ID']));
			while ($donnees = $reponse->fetch())
			{
				$ancargentPass = $donnees['argent'];
			}	
			$reponse->closeCursor();

			//On récupère la somme d'argent dont disposait le conducteur
			$reponse = $bdd->prepare('SELECT argent FROM membres WHERE ID = :ID');
			$reponse->execute(array('ID' => $IDconducteur));
			while ($donnees = $reponse->fetch())
			{
				$ancargentCond = $donnees['argent'];
			}	
			$reponse->closeCursor();


			//On restitue les places
			$req = $bdd->prepare('UPDATE trajets SET nbplaces = :nvnbplaces WHERE ID = :id_trajet');
			$req->execute(array(
			    'nvnbplaces' => $nbplaces + $nbplacesReservees,
			    'id_trajet' => $IDtrajet));			
			$req->closeCursor();

			//On restitue l'argent au passager en gardant une pénalité de 10 euros
			$req = $bdd->prepare('UPDATE membres SET argent = :nvargent WHERE ID = :id_passager');
			$req->execute(array(
			    'nvargent' => $ancargentPass + $prixtrajet - 10,
			    'id_passager' => $_SESSION['ID']));			
			$req->closeCursor();

			//On reprend l'argent au conducteur en lui laissant quand même 10 euros
			$req = $bdd->prepare('UPDATE membres SET argent = :nvargent WHERE ID = :id_conducteur');
			$req->execute(array(
			    'nvargent' => $ancargentCond - $prixtrajet + 10,
			    'id_conducteur' => $IDconducteur));			
			$req->closeCursor();

		
			

			//On supprime la réservation dans la table reservations
			$req = $bdd->prepare('DELETE FROM reservations WHERE ID_trajet= :IDTrajet');
			$req->execute(array('IDTrajet' => $IDtrajet));			
			$req->closeCursor();


			
			$remboursement = $prixtrajet*$nbplacesReservees-10;
			//On envoie une notification au conducteur
			$req = $bdd->prepare('INSERT INTO notifications(ID_concerne, type, notification) VALUES(:ID_concerne, :type, :notification)');
			$req->execute(array(
			    'ID_concerne' => $IDconducteur,
			    'type' => 'ANNULATION D\'UN PASSAGER',
			    'notification' => "Pour le trajets ".$_SESSION['depart']."-".$_SESSION['arrivee']." que vous proposez pour le ".$_SESSION['datehoraire'].", l'utilisateur ".$_SESSION['login']." a annulé sa réservation. En conséquence, la somme de ".$remboursement." euros vous a été débitée pour rembourser la réservation. Cela comprend à une indemnité de 10 euros."));
			$req->closeCursor();


			echo "Vous venez d'annuler un trajet.<br/>";
			echo "Le prix du trajet vient de vous être remboursé contre une pénalité de 10 euros<br/>";










/*
			echo "Vous venez de réserver un trajet ".$_SESSION['depart']."-".$_SESSION['arrivee']." pour le ".$_SESSION['datehoraire']." et nous vous en remercions.<br/>";
			$montantTotal = $_POST['arrivee']*$_SESSION['prix'];
			echo "La somme de ".$montantTotal." euros vient d'être débitée de votre compte.<br/>";


			//On récupère la somme d'argent dont disposait le passager avant la transaction
			$req = $bdd->prepare('SELECT argent FROM membres WHERE ID = :id');
			$req->execute(array('id' => $_SESSION['ID']));
			while ($donnees = $req->fetch())
			{
			    $_SESSION['argent'] = $donnees['argent'];
			}
			$req->closeCursor();

			$_SESSION['argent'] = $_SESSION['argent'] - $montantTotal;
			echo "Vous disposez dorénavant de ".$_SESSION['argent']." euros.<br/>";

			//On met à jour dans la base de données le somme dont dispose le passager
			$req = $bdd->prepare('UPDATE membres SET argent = :nvargent WHERE ID = :id_passager');
			$req->execute(array(
			    'nvargent' => $_SESSION['argent'],
			    'id_passager' => $_SESSION['ID']));			
			$req->closeCursor();



			//On récupère la somme d'argent dont disposait le conducteur avant la transaction
			$req = $bdd->prepare('SELECT argent FROM membres WHERE ID = :id');
			$req->execute(array('id' => $_SESSION['id_conducteur']));
			while ($donnees = $req->fetch())
			{
			    $_SESSION['argentConducteur'] = $donnees['argent'];
			}
			$req->closeCursor();


			//On met à jour dans la base de données le somme dont dispose le conducteur
			$req = $bdd->prepare('UPDATE membres SET argent = :nvargent WHERE ID = :id_conducteur');
			$req->execute(array(
			    'nvargent' => $montantTotal + $_SESSION['argentConducteur'],
			    'id_conducteur' => $_SESSION['id_conducteur']));			
			$req->closeCursor();
			


			//On diminue le nombre de places disponibles
			$nbplacesDispo = $_SESSION['nbplaces'] - $_POST['arrivee'];
			echo "Il reste ".$nbplacesDispo." places pour ce trajet.<br/>";
			$req = $bdd->prepare('UPDATE trajets SET nbplaces = :nvnbplaces WHERE ID_conducteur = :id_conducteur AND depart = :depart AND arrivee = :arrivee');
			$req->execute(array(
			    'nvnbplaces' => $nbplacesDispo,
			    'id_conducteur' => $_SESSION['id_conducteur'],
			    'depart' => $_SESSION['depart'],
			    'arrivee' => $_SESSION['arrivee']));			
			$req->closeCursor();

			//On récupère l'ID du trajet
			$req = $bdd->prepare('SELECT ID FROM trajets WHERE ID_conducteur = :id_conducteur AND depart = :depart AND arrivee = :arrivee');
			$req->execute(array(
			    'id_conducteur' => $_SESSION['id_conducteur'],
			    'depart' => $_SESSION['depart'],
			    'arrivee' => $_SESSION['arrivee']));
			while ($donnees = $req->fetch())
			{
			    $IDtrajet = $donnees['ID'];
			}
			$req->closeCursor();

			//On insère l'ID du trajet, l'ID du passager et l'ID du conducteur dans la table reservations. On fait une jointure
			$req = $bdd->prepare('INSERT INTO reservations(ID_trajet, ID_conducteur, ID_passager, nbplacesRes) VALUES(:ID_trajet, :ID_conducteur, :ID_passager, :nbplacesRes)');
			$req->execute(array(
			    'ID_trajet' => $IDtrajet,
			    'ID_conducteur' => $_SESSION['id_conducteur'],
			    'ID_passager' => $_SESSION['ID'],
			    'nbplacesRes' => $_POST['arrivee']));
			$req->closeCursor();
*/
			}
			?>
		</div><!-- #contenu -->
	</div><!-- #centre -->

	<div id="pied">
		<p><strong>voiturehub.fr© Tous droits réservés</strong> UV LO07</p>
		<p id="copyright">
			<a href="deconnexion.php">Se déconnecter</a>
		</p>
	</div><!-- #pied -->

</div><!-- #global -->

</body>
