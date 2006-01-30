<?php
require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_bEvalFC = (empty($HTTP_GET_VARS["evalFC"]) ? 0 : $HTTP_GET_VARS["evalFC"]);

$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;

// ---------------------
// Composer le menu
// ---------------------
$aMenus   = array();

if ($url_bEvalFC)
{
	$aMenus[] = array("Valider","top.g_oFrames['principale'].valider()");
	$aMenus[] = array("Annuler","top.close()");
}
else
	$aMenus[] = array("Fermer","top.close()");

include_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>
