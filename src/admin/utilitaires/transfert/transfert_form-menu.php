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
** Fichier ................: transfert_form-menu.php
** Description ............:
** Date de création .......: 23/08/2004
** Dernière modification ..: 17/02/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$url_iNumPage = (empty($HTTP_GET_VARS["page"]) ? 0 : $HTTP_GET_VARS["page"]);

$sBlockHead = NULL;

// ---------------------
// Composer le menu
// ---------------------
$aMenus = array();

if ($url_iNumPage == -1)
{
	$aMenus[] = array("","");
}
else if ($url_iNumPage > 0)
{
	if ($url_iNumPage < 4)
		$aMenus[] = array("Annuler","top.close()",1,"text-align: left;");
	
	if ($url_iNumPage > 1 && $url_iNumPage < 4)
		$aMenus[] = array("&#8249;&nbsp;Pr&eacute;c&eacute;dent","top.precedent()",2,NULL,FALSE);
	
	if ($url_iNumPage < 3)
		$aMenus[] = array("Suivant&nbsp;&#8250;","top.suivant()",2,NULL,FALSE);
	else if ($url_iNumPage == 3)
		$aMenus[] = array("Confirmer","top.suivant()",2);
	else
		$aMenus[] = array("Fermer","top.fermer()",2);
}
else
{
	$aMenus[] = array("Fermer","top.close()");
}

include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

