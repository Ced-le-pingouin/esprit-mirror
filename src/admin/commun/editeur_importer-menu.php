<?php

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
$url_sMenu = (empty($HTTP_GET_VARS["menu"]) ? NULL : $HTTP_GET_VARS["menu"]);
switch ($url_sMenu)
{
	case "annuler":
		$aMenus = array(array("Annuler","top.close()"));
		break;
	case "importer":
		$aMenus = array(array("Importer","top.importer()"), array("Annuler","top.close()"));
		break;
	case "recommencer":
		$aMenus = array(array("Recommencer","top.recommencer()"), array("Annuler","top.close()"));
		break;
	default:
		$aMenus = NULL;
}
require_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>

