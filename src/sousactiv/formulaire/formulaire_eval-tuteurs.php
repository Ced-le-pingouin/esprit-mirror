<?php

/*
** Fichier ................: formulaire.php
** Description ............:
** Date de création .......: 05/11/2004
** Dernière modification ..: 08/11/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initModuleCourant();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdFCSousActiv = (empty($HTTP_GET_VARS["idFCSousActiv"]) ? 0 : $HTTP_GET_VARS["idFCSousActiv"]);
$url_bEvalFC        = (empty($HTTP_GET_VARS["evalFC"]) ? 0 : $HTTP_GET_VARS["evalFC"]);

// ---------------------
// Initialiser
// ---------------------
$oProjet->oModuleCourant->initTuteurs();

$iMonIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("formulaire_eval-tuteurs.tpl");
$oBlockTuteur = new TPL_Block("BLOCK_TUTEUR",$oTpl);
$oBlockTuteur->beginLoop();

foreach ($oProjet->oModuleCourant->aoTuteurs as $oTuteur)
{
	$iIdPers = $oTuteur->retId();
	
	$oBlockTuteur->nextLoop();
	
	$oBlockTuteur->remplacer("{personne->id}",$iIdPers);
	$oBlockTuteur->remplacer("{personne->nom_complet}",htmlentities($oTuteur->retNomComplet()));
	$oBlockTuteur->remplacer("{formulaire_complete->id}",$url_iIdFCSousActiv);
	$oBlockTuteur->remplacer("{personne->peutEvaluer}",($iIdPers == $iMonIdPers) ? $url_bEvalFC : 0);
}

$oBlockTuteur->afficher();

$oTpl->afficher();

$oProjet->terminer();

?>

