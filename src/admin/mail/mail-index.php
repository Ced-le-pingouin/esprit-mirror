<?php

/*
** Fichier ................: mail-index.php
** Description ............:
** Date de création .......: 14/12/2004
** Dernière modification ..: 15/02/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$sParamsUrl = NULL;

foreach ($HTTP_GET_VARS as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialiser
// ---------------------
$sTitrePrincipale = "Envoi courriel";

// ---------------------
// Template
// ---------------------
$oTplFrameset = new Template(dir_theme("mail-index.inc.tpl",FALSE,TRUE));

$oTpl = new Template("mail-index.tpl");

$oBlocFrameset = new TPL_Block("BLOCK_FRAMESET",$oTpl);
$oBlocFrameset->ajouter($oTplFrameset->retDonnees());
$oBlocFrameset->afficher();

$oTpl->remplacer("{html.title}",htmlentities($sTitrePrincipale));

// {{{ Frames
$oTpl->remplacer("{frame.titre.src}","mail-titre.php?tp=".rawurlencode($sTitrePrincipale));
$oTpl->remplacer("{frame.infos.src}","mail-infos.php{$sParamsUrl}");
$oTpl->remplacer("{frame.principale.src}","mail.php");
$oTpl->remplacer("{frame.menu.src}","mail-menu.php");
// }}}

$oTpl->afficher();

?>

