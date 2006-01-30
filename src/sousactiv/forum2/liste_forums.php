<?php

/*
** Sous-activit� ..........: liste-forums.php
** Description ............: 
** Date de cr�ation .......: 28/05/2004
** Derni�re modification ..: 03/06/2004
** Auteurs ................: Filippo PORCO, J�r�me TOUZE
** Emails .................: ute@umh.ac.be
**
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iIdForum = (empty($HTTP_GET_VARS["idForum"]) ? 0 : $HTTP_GET_VARS["idForum"]);

// ---------------------
// D�clarer les fonctions
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

