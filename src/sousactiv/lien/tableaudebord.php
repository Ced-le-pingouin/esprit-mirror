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
** Fichier ................: tableaudebord.php
** Description ............:
** Date de création .......: 10/11/2005
** Dernière modification ..: 10/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Initialiser
// ---------------------
$iModalite = $oProjet->oSousActivCourante->retModalite(TRUE);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("tableaudebord.tpl");

// {{{ Consigne
$sDescription = convertBaliseMetaVersHtml($oProjet->oSousActivCourante->retDescr());

$oTpl->remplacer("{consigne}",$sDescription);
// }}}

// {{{ Lien du tableau de bord
$oTpl->remplacer("{tableau_de_bord}",convertLien(MODALITE_PAR_EQUIPE == $iModalite ? "[tableaudebord /e]" : "[tableaudebord /i]"));
$oTpl->remplacer("{tableaudebord.niveau.id}",$oProjet->oRubriqueCourante->retId());
$oTpl->remplacer("{tableaudebord.niveau.type}",TYPE_RUBRIQUE);
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

