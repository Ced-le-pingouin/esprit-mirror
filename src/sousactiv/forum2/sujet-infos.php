<?php

/*
** Fichier ................: forum-infos.php
** Description ............: 
** Date de création .......: 24/09/2004
** Dernière modification ..: 13/11/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdSujet = (empty($HTTP_GET_VARS["idSujet"]) ? 0 : $HTTP_GET_VARS["idSujet"]);

// ---------------------
// Initialiser
// ---------------------
$oSujetForum = new CSujetForum($oProjet->oBdd,$url_iIdSujet);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("sujet-infos.tpl");

$oBlocInfosSujet = new TPL_Block("BLOCK_INFOS_SUJET",$oTpl);

if ($url_iIdSujet > 0)
{
	// Sujet
	$oBlocInfosSujet->remplacer("{sujet->titre}",$oSujetForum->retTitre());
	$oBlocInfosSujet->remplacer("{sujet->date_creation}",$oSujetForum->retDate("d/m/y"));
	
	// Personne
	$oSujetForum->initAuteur();
	$oBlocInfosSujet->remplacer("{personne->nom_complet}",$oSujetForum->oAuteur->retNomComplet());
	$oBlocInfosSujet->remplacer("{personne->pseudo}",$oSujetForum->oAuteur->retPseudo());
	
	$oBlocInfosSujet->afficher();
}
else
{
	$oBlocInfosSujet->effacer();
}

$oTpl->remplacer("{sujet->id}",$url_iIdSujet);

$oTpl->afficher();

$oProjet->terminer();

?>
