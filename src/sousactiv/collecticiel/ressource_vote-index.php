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
// Titre
// ---------------------
$sFrameSrcTitre = "ressource_vote-titre.php";
$sTitrePrincipal = "Soumettre un document pour évaluation";

// ---------------------
// ---------------------
$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;

// Frameset
$sFrameSrcPrincipal = <<<BLOC_FRAME_PRINCIPALE
<frame name="Principale" src="ressource_vote.php{$sParamsUrl}" scrolling="auto">
BLOC_FRAME_PRINCIPALE;

// ---------------------
// Menu
// ---------------------
$sFrameSrcMenu = "ressource_vote-menu.php";

include_once(dir_template("dialogue","dialog-index.tpl.php"));

?>

