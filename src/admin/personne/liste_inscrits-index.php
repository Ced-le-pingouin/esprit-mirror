<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

/*
** Fichier ................: liste_inscrits-index.php
** Description ............:
** Date de création .......: 02/09/2004
** Dernière modification ..: 18/01/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initModuleCourant();

$iIdForm    = (is_object($oProjet->oFormationCourante) ? $oProjet->oFormationCourante->retId() : 0);
$iIdMod     = (is_object($oProjet->oModuleCourant) ? $oProjet->oModuleCourant->retId() : 0);
$iMonIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

// ---------------------
// Initialiser
// ---------------------
$sTitrePrincipal = "Liste des inscrits au cours";

$sMenu = "0";
$iIdStatuts = NULL;

if ($iMonIdPers > 0)
{
	if (is_object($oProjet->oFormationCourante) &&
		$oProjet->verifPermission("PERM_OUTIL_INSCRIPTION"))
		$sMenu = ($oProjet->oFormationCourante->retId() > 0 ? "1" : "0");
	
	$iIdStatuts = STATUT_PERS_TUTEUR."x".STATUT_PERS_ETUDIANT."x".STATUT_PERS_RESPONSABLE;
}

// ---------------------
// Frame principale
// ---------------------
$sFramePrincipale = <<<BLOCK_FRAME_PRINCIPALE
<frame name="Principale" src="liste_inscrits.php" frameborder="0" scrolling="auto" noresize="noresize">
BLOCK_FRAME_PRINCIPALE;

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("dialog-index.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->effacer();

$oTpl->remplacer("{titre_page_html}",htmlentities($sTitrePrincipal));
$oTpl->remplacer("{frame_src_haut}","liste_inscrits-titre.php?tp=".rawurlencode($sTitrePrincipal));
$oTpl->remplacer("{frame_principal}",$sFramePrincipale);
$oTpl->remplacer("{frame_src_bas}","liste_inscrits-menu.php?menu={$sMenu}&idForm={$iIdForm}&idMod={$iIdMod}&idStatuts={$iIdStatuts}");

$oTpl->afficher();

$oProjet->terminer();

?>

