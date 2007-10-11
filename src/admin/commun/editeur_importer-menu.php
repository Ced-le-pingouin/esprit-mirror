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
** Fichier ................: editeur_importer-menu.php
** Description ............:
** Date de création .......: 30/06/2004
** Dernière modification ..: 01/07/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

require_once("globals.inc.php");
$sBlockHead = NULL;
$url_sMenu = (empty($_GET["menu"]) ? NULL : $_GET["menu"]);
switch ($url_sMenu)
{
	case "annuler":
		$aMenus = array(array("Annuler","top.close()"));
		break;
	case "importer":
		$aMenus = array(
			array("Insérer","top.importer_ins()"),
			array("Remplacer","top.importer_rpl()"),
			array("Annuler","top.close()") );
		break;
	case "recommencer":
		$aMenus = array(array("Recommencer","top.recommencer()"), array("Annuler","top.close()"));
		break;
	default:
		$aMenus = NULL;
}
require_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>

