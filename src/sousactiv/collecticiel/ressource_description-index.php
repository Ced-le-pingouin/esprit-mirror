<?php

require_once("globals.inc.php");

// ---------------------
// Initialisation
// ---------------------
$sParamsUrl = NULL;

foreach ($HTTP_GET_VARS as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Frame du Titre
// ---------------------

$sFrameSrcTitre = "ressource_description-titre.php";
$sTitrePrincipal = "Description du document";

// ---------------------
// Frame principal
// ---------------------

// Javascript
$sBlockHead = NULL;

// Frameset
$sFrameSrcPrincipal =<<< BLOCK_HTML_HEAD
<frame name="Principale" src="ressource_description.php{$sParamsUrl}" scrolling="auto">
BLOCK_HTML_HEAD;

// ---------------------
// Frame du Menu
// ---------------------

$sFrameSrcMenu = "ressource_description-menu.php";

include_once(dir_template("dialogue","dialog-index.tpl.php"));

?>

