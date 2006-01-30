<?php

/*
** Fichier ................: ressource_transfert-index.php
** Description ............:
** Date de création .......: 27/11/2002
** Dernière modification ..: 05/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("collecticiel.lang"));

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sParamsUrl = NULL;

foreach ($HTTP_GET_VARS as $sCle => $sValeur)
	$url_sParamsUrl .= (isset($url_sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialisation
// ---------------------

// {{{ Frame du titre
$sFrameSrcTitre = "ressource_transfert-titre.php";
$sTitrePrincipal = TXT_TRANSFERER_DES_FICHIERS_TITRE;
// }}}

// {{{ Frame principale
$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;

$sFrameSrcPrincipal = <<<BLOCK_FRAME_SRC_PRINCIPALE
<frame name="Principale" src="ressource_transfert.php{$url_sParamsUrl}" scrolling="auto">
BLOCK_FRAME_SRC_PRINCIPALE;
// }}}

// {{{ Frame du menu
$sFrameSrcMenu = "ressource_transfert-menu.php";
// }}}

// ---------------------
// Template
// ---------------------
include_once(dir_template("dialogue","dialog-index.tpl.php"));

?>
