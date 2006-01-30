<?php

/*
** Fichier ................: editeur.php
** Description ............:
** Date de création .......: 23/06/2004
** Dernière modification ..: 25/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

$g_iIdStatutUtilisateur = $oProjet->retStatutUtilisateur();

// ---------------------
// Template
// ---------------------
$oTpl_Onglet = new Template(dir_theme("onglet/onglet_tab.tpl",FALSE,TRUE));
$oSet_Onglet = $oTpl_Onglet->defVariable("SET_ONGLET");

$oTpl = new Template("editeur.tpl");

// Editeur
$oTpl_Editeur = new Template("editeur.inc.tpl");

// {{{ Tableau de bord
$oBlocTableauDeBord = new TPL_Block("BLOCK_TABLEAU_DE_BORD",$oTpl_Editeur);

if ($g_iIdStatutUtilisateur < STATUT_PERS_ETUDIANT)
	$oBlocTableauDeBord->afficher();
else
	$oBlocTableauDeBord->effacer();
// }}}

$oSet_Editeur = $oTpl_Editeur->defVariable("SET_EDITEUR");

// Visualiseur
$oSet_Visualiseur = $oTpl->defVariable("SET_VISUALISEUR");

$oBloc_Editeur = new TPL_Block("BLOCK_EDITEUR",$oTpl);
$oBloc_Editeur->ajouter($oSet_Onglet);
$oBloc_Editeur->remplacer("{onglet->titre}","Editeur");
$oBloc_Editeur->remplacer("{onglet->texte}",$oSet_Editeur);
$oBloc_Editeur->remplacer("{edition->style}","width: 100%; height: 355px;");
$oBloc_Editeur->afficher();

$oBloc_Visualiseur = new TPL_Block("BLOCK_VISUALISATEUR",$oTpl);
$oBloc_Visualiseur->ajouter($oSet_Visualiseur);
$oBloc_Visualiseur->afficher();

$oTpl->remplacer("{editeur->nom}","edition");

$oTpl->remplacer("editeur://",dir_admin("commun"));
$oTpl->remplacer("icones://",dir_icones());

$oTpl->afficher();

$oProjet->terminer();

?>

