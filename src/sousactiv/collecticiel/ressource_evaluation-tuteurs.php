<?php

/*
** Fichier ................: ressource_evaluation-tuteurs.php
** Description ............:
** Date de cr�ation .......: 04/04/2005
** Derni�re modification ..: 26/04/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iIdPers  = (empty($HTTP_GET_VARS["idPers"]) ? 0 : $HTTP_GET_VARS["idPers"]);
$url_iIdResSA = (empty($HTTP_GET_VARS["idResSA"]) ? 0 : $HTTP_GET_VARS["idResSA"]);

// ---------------------
// Initialiser
// ---------------------
if (STATUT_PERS_ETUDIANT != $oProjet->retStatutUtilisateur())
{
	$oProjet->initModuleCourant();
	$oProjet->oModuleCourant->initTuteurs();
	$poTuteurs = &$oProjet->oModuleCourant->aoTuteurs;
}
else
{
	// Les �tudiants ne verront que les tuteurs qui ont �valu� ce document
	$oRessourceSousActiv = new CRessourceSousActiv($oProjet->oBdd,$url_iIdResSA);
	$oRessourceSousActiv->initTuteurs();
	$poTuteurs = &$oRessourceSousActiv->aoTuteurs;
	unset($oRessourceSousActiv);
}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("ressource_evaluation-tuteurs.tpl");

$oBlocTuteur = new TPL_Block("BLOCK_TUTEUR",$oTpl);
$oBlocTuteur->beginLoop();

$iIdPremierTuteur = (count($poTuteurs) > 0 ? $poTuteurs[0]->retId() : 0);

foreach ($poTuteurs as $oTuteur)
{
	if (($iIdTuteur = $oTuteur->retId()) == $url_iIdPers)
		$iIdPremierTuteur = $iIdTuteur;
	
	$sSeparateurTuteur = ($oBlocTuteur->countLoops() > 0 ? "|" : NULL);
	
	$oBlocTuteur->nextLoop();
	
	$oBlocSeparateurTuteurs = new TPL_Block("BLOCK_SEPARATEUR_TUTEURS",$oBlocTuteur);
	
	if ($oBlocTuteur->countLoops() > 1)
		$oBlocSeparateurTuteurs->afficher();
	else
		$oBlocSeparateurTuteurs->effacer();
	
	$oBlocTuteur->remplacer("{tuteur.separateur}",$sSeparateurTuteur);
	$oBlocTuteur->remplacer("{tuteur.id}",$iIdTuteur);
	$oBlocTuteur->remplacer("{tuteur.nom}",$oTuteur->retNom());
	$oBlocTuteur->remplacer("{tuteur.prenom}",$oTuteur->retPrenom());
}

$oBlocTuteur->afficher();

// S�lectionner le tuteur par d�faut
$oTpl->remplacer("{tuteur.id}",$iIdPremierTuteur);
$oTpl->remplacer("{ressource.id}",$url_iIdResSA);

$oTpl->afficher();

$oProjet->terminer();

?>

