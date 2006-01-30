<?php

/*
** Fichier ................: glossaire_composer-sous_menu.php
** Description ............:
** Date de création .......: 29/07/2004
** Dernière modification ..: 12/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$url_bAfficherMenu = (empty($HTTP_GET_VARS["menu"]) ? FALSE : TRUE);

$oTpl = new Template(dir_theme("dialogue/dialog-sous_menu.tpl",FALSE,TRUE));

$oBloc_SousMenu = new TPL_Block("BLOCK_SOUS_MENU",$oTpl);

$oSet_DialogSousMenu = $oTpl->defVariable("SET_DIALOG_SOUS_MENU");
$oSet_MenuSeparateur = $oTpl->defVariable("SET_MENU_SEPARATEUR");

if ($url_bAfficherMenu)
{
	// Appliquer les changements
	$oBloc_SousMenu->ajouter($oSet_DialogSousMenu);
	$oBloc_SousMenu->remplacer("{href->javascript}","top.oPrincipale().envoyer()");
	$oBloc_SousMenu->remplacer("{href->label}","Appliquer les changements");
	
	// Séparateur de menu
	$oBloc_SousMenu->ajouter($oSet_MenuSeparateur);
	
	// Annuler
	$oBloc_SousMenu->ajouter($oSet_DialogSousMenu);
	$oBloc_SousMenu->remplacer("{href->javascript}","top.oPrincipale().annuler()");
	$oBloc_SousMenu->remplacer("{href->label}","Annuler");
}
else
{
	$oBloc_SousMenu->ajouter("&nbsp;");
}

// Afficher le sous-menu
$oBloc_SousMenu->afficher();

$oTpl->afficher();
?>