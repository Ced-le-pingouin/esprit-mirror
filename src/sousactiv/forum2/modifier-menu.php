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
** Fichier ................: modifier-menu.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 30/09/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Jérôme TOUZE
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sModaliteFenetre = (empty($_GET["modaliteFenetre"]) ? NULL : $_GET["modaliteFenetre"]);
$url_sMenu            = (empty($_GET["menu"]) ? NULL : $_GET["menu"]);

// ---------------------
// Menus
// ---------------------
if ($url_sMenu == "forum")
	include_once("modifier_forum-menu.inc.php");
else if ($url_sMenu == "sujet")
	include_once("modifier_sujet-menu.inc.php");
else if ($url_sMenu == "message")
	include_once("modifier_message-menu.inc.php");

// ---------------------
// Template
// ---------------------
$sBlockHead = NULL;

include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

