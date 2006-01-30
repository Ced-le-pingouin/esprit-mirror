<?php

require_once("globals.inc.php");
require_once(dir_locale("globals.lang"));

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
$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;

// ---------------------
// Composer le menu
// ---------------------
$aMenus = array();

$aMenus[] = array(BTN_FERMER,"top.close()");

include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

