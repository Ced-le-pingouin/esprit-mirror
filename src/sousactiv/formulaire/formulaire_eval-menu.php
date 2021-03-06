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

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_bEvalFC = (empty($_GET["evalFC"]) ? 0 : $_GET["evalFC"]);

$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;

// ---------------------
// Composer le menu
// ---------------------
$aMenus   = array();

if ($url_bEvalFC)
{
	$aMenus[] = array("Valider","top.g_oFrames['principale'].valider()");
	$aMenus[] = array("Annuler","top.close()");
}
else
	$aMenus[] = array("Fermer","top.close()");

include_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>
