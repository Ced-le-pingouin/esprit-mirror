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
** Fichier ................: dialog-menu.tpl.php
** Description ............:
** Date de création .......: 15/06/2004
** Dernière modification ..: 28/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
**/

require_once(dir_locale("globals.lang"));

$oTpl = new Template(dir_theme("dialog-menu.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);

if (isset($sBlockHead))
{
	$oBlockHead->ajouter($sBlockHead);
	$oBlockHead->afficher();
}
else
	$oBlockHead->effacer();

// Menu
$sMenu = NULL;

if (isset($aMenus) && is_array($aMenus))
{
	$iCol = 1;
	$sMenuColonne = NULL;
	$sStyleColonne = "text-align: right;";
	
	foreach ($aMenus as $aMenu)
	{
		if (empty($aMenu[2]))
			$aMenu[2] = $iCol;
		
		if ($iCol != $aMenu[2])
		{
			$sMenu .= "<td style=\"{$sStyleColonne}\">{$sMenuColonne}</td>";
			$iCol++;
			$sMenuColonne = NULL;
			$sStyleColonne = "text-align: right;";
		}
		
		$sTexteLien = (!isset($aMenu[4]) || $aMenu[4] ? htmlentities($aMenu[0]) : $aMenu[0]);
		$sMenuColonne .= (isset($sMenuColonne) ? "&nbsp;|&nbsp;" : NULL)
			.(isset($aMenu[1])
				? "<a"
					." href=\"javascript: void(0);\""
					." onclick=\"".$aMenu[1]."; return false;\""
					." onfocus=\"blur()\""
					.">{$sTexteLien}</a>"
				: $sTexteLien);
		
		if (!empty($aMenu[3]))
			$sStyleColonne = $aMenu[3];
	}
	
	if (isset($sMenuColonne))
		$sMenu .= "<td style=\"{$sStyleColonne}\">{$sMenuColonne}</td>";
}

// Par défaut, afficher le bouton "fermer"
if (!isset($sMenu))
	$sMenu = "<td align=\"right\"><a"
		." href=\"javascript: top.close();\""
		." onfocus=\"blur()\""
		.">".BTN_FERMER."</a></td>";

$oTpl->remplacer("{menu}",$sMenu);

$oTpl->afficher();

?>

