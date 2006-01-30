<?php

/*
** Fichier ................: zone_cours-titre.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 30/05/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initRubriqueCourante();

// ---------------------
// Initialiser
// ---------------------
$asRechTpl = array(
	"{formation.nom}"
	,"{module.nom}"
	,"{rubrique.nom}"
	, "{personne.nom}"
	, "{personne.prenom}"
	, "{personne.pseudo}"
	, "{personne.statut}"
);

$amReplTpl = array(
	htmlentities($oProjet->oFormationCourante->retNom())
	, htmlentities($oProjet->oModuleCourant->retNom())
	, htmlentities($oProjet->oRubriqueCourante->retNom())
	, (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retNom() : NULL)
	, (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retPrenom() : NULL)
	, (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retPseudo() : NULL)
	, $oProjet->retTexteStatutUtilisateur()
);

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme_commun("zone_cours-titre.tpl",FALSE,TRUE));
$oTpl->remplacer($asRechTpl,$amReplTpl);
$oTpl->afficher();

$oProjet->terminer();

?>

