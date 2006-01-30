#!/usr/bin/php
<?php

/*
** Fichier ................: inscription.php
** Description ............:
** Date de création .......: 06/11/2004
** Dernière modification ..: 10/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Usage ..................: php -f inscription.php -- "nom_fichier.csv"
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

// ----------------------
// Effacer le fichier l'ancien fichier sql
// ----------------------
echo "1.  Effacer l'ancien fichier sql\n";
@unlink("personne.sql");

// ----------------------
// Ouvrir le fichier CSV
// ----------------------
echo "2.  Ouvrir le fichier csv\n";
$sFichierCSV = $GLOBALS['argv'][1];

$fp = fopen($sFichierCSV,"r");

// ----------------------
// Rechercher les données
// ----------------------
$sInscriptions = NULL;
$sLigne = NULL;
$iPosMdp = -1;

// ----------------------
// La première ligne
// ----------------------
echo "2.1.  Ecrire les entêtes de la table\n";
if ($sLigne = fgetcsv($fp,1000,";"))
{
	$iNbChamps = count($sLigne);
	$sNomsChamps = NULL;
	for ($c=0; $c<$iNbChamps; $c++)
	{
		if ($sLigne[$c] == "Mdp") $iPosMdp = $c;
		$sNomsChamps .= ($c>0 ? ", " : NULL).$sLigne[$c];
	}
}

// ----------------------
// Données
// ----------------------
while ($sLigne = fgetcsv($fp,1000,";"))
{
	$sDonnees = NULL;
	$iNbChamps = count($sLigne);
	
	for ($c=0; $c<$iNbChamps; $c++)
	{
		$sDonnee = trim($sLigne[$c]);
		if ($sDonnee == "NULL") $sDonnee = "";
		$sDonnee = mysql_escape_string($sDonnee);
		$sDonnees .= ($c>0 ? ", " : NULL)
			.($iPosMdp == $c ? "PASSWORD('{$sDonnee}')" : "'{$sDonnee}'");
	}
	
	$sInscriptions .= "INSERT INTO Personne ({$sNomsChamps}) VALUES ({$sDonnees});\n";
}

if (isset($sInscriptions))
	$sInscriptions = str_replace("\'","''",$sInscriptions);

// ----------------------
// Fermer le fichier
// ----------------------
fclose($fp);

// ----------------------
// Créer le fichier des inscriptions
// ----------------------
$fp = fopen("personne.sql","w");
fwrite($fp,$sInscriptions,strlen($sInscriptions));
fclose($fp);

?>

