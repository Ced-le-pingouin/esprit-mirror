<?php

/*
** Fichier ................: mail-erreur-index.php
** Description ............:
** Date de création .......: 17/12/2004
** Dernière modification ..: 20/01/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_template("dialogue/dialog_simple.class.php"));

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_asDestinatairesCourriel = (empty($HTTP_POST_VARS["destinataireCourriel"]) ? NULL : $HTTP_POST_VARS["destinataireCourriel"]);

// ---------------------
// Composer la liste des destinataires n'ayant pas ou que leur adresse courriel
// est erronée
// ---------------------
$oDialogSimple = new CDialogSimple("Message de confirmation");

if (is_array($url_asDestinatairesCourriel) &&
	count($url_asDestinatairesCourriel) > 0)
{
	$sListeDestinatairesErrones = "var asListeDestinatairesErrones = new Array();\n";
	
	$iIdxDestinataireErrone = 0;
	
	foreach ($url_asDestinatairesCourriel as $sDestinataireErrone)
	{
		$iPosEtoile = strpos($sDestinataireErrone,"*");
		$sListeDestinatairesErrones .= "asListeDestinatairesErrones[".$iIdxDestinataireErrone++."] = \"".substr($sDestinataireErrone,$iPosEtoile,(strpos($sDestinataireErrone,"%20%3C")))."\";\n";
	}
	
	if ($iIdxDestinataireErrone > 0)
		$oDialogSimple->insererDansBlocJavascript($sListeDestinatairesErrones);
}

$oDialogSimple->defSrcPrincipale("mail_erreur.php?erreur=".(isset($sListeDestinatairesErrones) ? "1" : NULL));
$oDialogSimple->defSrcMenu("mail_erreur-menu.php");
$oDialogSimple->afficher();

?>

