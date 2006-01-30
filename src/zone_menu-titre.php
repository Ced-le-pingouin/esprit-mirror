<?php

/*
** Fichier ................: zone_menu-titre.php
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

// ---------------------
// Initialiser
// ---------------------
$asRechTpl = array(
	"{personne.nom}"
	, "{personne.prenom}"
	, "{personne.pseudo}"
	, "{personne.statut}"
);

$amReplTpl = array(
	(is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retNom() : NULL)
	, (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retPrenom() : NULL)
	, (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retPseudo() : NULL)
	, $oProjet->retTexteStatutUtilisateur()
);

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme_commun("zone_menu-titre.tpl",FALSE,TRUE));
$oTpl->remplacer($asRechTpl,$amReplTpl);
$oTpl->afficher();

$oProjet->terminer();

?>

