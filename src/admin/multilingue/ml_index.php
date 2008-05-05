<?php

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$sParamsUrl = NULL;

foreach ($_GET as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialisation
// ---------------------
$sTitrePrincipal = "Outils multilingues";

$sBlockHead = NULL;

$sFrameSrcTitre = NULL;

$sFrameSrcPrincipal = <<<BLOC_FRAME_PRINCIPALE
<frame name="principale" src="ml.php{$sParamsUrl}" scrolling="auto" noresize="noresize">
BLOC_FRAME_PRINCIPALE;

$sFrameSrcMenu = NULL;

include_once(dir_template("dialogue","dialog-index.tpl.php"));

?>
