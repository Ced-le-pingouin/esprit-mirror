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
$sStatutFormation = "";
if (isset($_GET['sAffiche']) && ($_GET['sAffiche'] == "Archives")) $sStatutFormation = "** Formations archiv&eacute;es **";
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
$oTpl->remplacer("{Statut.formation}",$sStatutFormation);
$oTpl->afficher();

$oProjet->terminer();

?>

