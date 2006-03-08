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
** Date de cr�ation .......: 14/05/2004
** Derni�re modification ..: 30/09/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           J�r�me TOUZE
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_sModaliteFenetre = (empty($HTTP_GET_VARS["modaliteFenetre"]) ? NULL : $HTTP_GET_VARS["modaliteFenetre"]);
$url_sMenu            = (empty($HTTP_GET_VARS["menu"]) ? NULL : $HTTP_GET_VARS["menu"]);

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

