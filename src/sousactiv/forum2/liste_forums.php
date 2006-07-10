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
** Sous-activité ..........: liste-forums.php
** Description ............: 
** Date de création .......: 28/05/2004
** Dernière modification ..: 03/06/2004
** Auteurs ................: Filippo PORCO, Jérôme TOUZE
** Emails .................: ute@umh.ac.be
**
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForum = (empty($_GET["idForum"]) ? 0 : $_GET["idForum"]);

// ---------------------
// Déclarer les fonctions
// ---------------------
function retLienForum($v_oForum)
{
	return "<a"
		." href=\"forum-sujets.php?idForum=".$v_oForum->retId()."\""
		." target=\"SUJETS\""
		." class=\"dialog_menu_item\""
		." onfocus=\"blur()\""
		.">".$v_oForum->retNom()."</a>";
}

// ---------------------
// Initialiser le forum principal/sous-forums
// ---------------------
$oForum = new CForum($oProjet->oBdd,$url_iIdForum);
$iNbSousForums = $oForum->initSousForums();

// ---------------------
// Template
// ---------------------
$oTpl = new Template("liste_forums.tpl");

$oSet_Menu_Ajouter    = $oTpl->defVariable("SET_MENU_AJOUTER");
$oSet_Menu_Modifier   = $oTpl->defVariable("SET_MENU_MODIFIER");
$oSet_Menu_Supprimer  = $oTpl->defVariable("SET_MENU_SUPPRIMER");
$oSet_Menu_Separateur = $oTpl->defVariable("SET_MENU_SEPARATEUR");

// Bloc menu
$oTplMenu = new Template(dir_theme("dialogue/dialog-menu.tpl",FALSE,TRUE));
$oSet_Bloc_Menu = $oTplMenu->defVariable("SET_BLOC_MENU");
$oSet_Menu_Separateur = $oTplMenu->defVariable("SET_MENU_SEPARATEUR");

$oBloc_Menu_Forum = new TPL_Block("BLOCK_MENU_FORUM",$oTpl);
$oBloc_Menu_Forum->ajouter($oSet_Bloc_Menu);
$oBloc_Menu_Forum->remplacer("{dialog_menu->titre}","Liste des forums");

$oBloc_Menu_Item = new TPL_Block("BLOCK_MENU_ITEM",$oBloc_Menu_Forum);
$oBloc_Menu_Item->beginLoop();

$oBloc_Menu = new TPL_Block("BLOCK_MENU",$oBloc_Menu_Forum);

// Forum principal
$oBloc_Menu_Item->nextLoop();
$oBloc_Menu_Item->remplacer("{dialog_menu->item}","<div style=\"padding: 5px; font-weight: bold; text-align: center;\">"
	.retLienForum($oForum)
	."</div>");

// Sous-forums
if ($iNbSousForums > 0)
{
	foreach ($oForum->aoSousForums as $oSousForum)
	{
		$oBloc_Menu_Item->nextLoop();
		$oBloc_Menu_Item->remplacer("{dialog_menu->item}",
			"<input type=\"radio\" name=\"idForum\" onfocus=\"blur()\">&nbsp;&nbsp;"
			.retLienForum($oSousForum));
	}
}

// Menu
$oBloc_Menu->remplacer("{dialog_menu->menu}",$oSet_Menu_Ajouter
	.$oSet_Menu_Separateur.$oSet_Menu_Modifier
	.$oSet_Menu_Separateur.$oSet_Menu_Supprimer);
$oBloc_Menu->remplacer("{forum->id}",$oForum->retId());
$oBloc_Menu->afficher();

$oBloc_Menu_Item->afficher();
$oBloc_Menu_Forum->afficher();

$oTpl->afficher();

$oProjet->terminer();
?>

