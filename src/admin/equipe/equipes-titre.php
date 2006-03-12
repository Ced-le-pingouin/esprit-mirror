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
** Fichier ................: equipes-index.php
** Description ............: 
** Date de création .......: 01/01/2003
** Dernière modification ..: 17/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
if (isset($HTTP_GET_VARS["TP"]))
	$url_sTitrePrincipal = $HTTP_GET_VARS["TP"];
else
	$url_sTitrePrincipal = "&nbsp;";

// ---------------------
// Initialiser
// ---------------------
$sBlockHead = "<link type=\"text/css\" rel=\"stylesheet\" href=\"theme://equipes.css\">";

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("dialog-titre.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->ajouter($sBlockHead);
$oBlockHead->afficher();

$oTpl->remplacer("{titre_principal}",$url_sTitrePrincipal);
$oTpl->remplacer("{sous_titre}","");

$oTpl->afficher();

?>
