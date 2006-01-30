<?php

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

