<?php

require_once("globals.inc.php");

// ---------------------
// Initialisation
// ---------------------

$sParamUrl = NULL;

// ---------------------
// Frame du Titre
// ---------------------

$sFrameSrcTitre = "ressource_deposer-titre.php";
$sTitrePrincipal = "Déposer un document";

// ---------------------
// Frame principal
// ---------------------

// Javascript/Style
$sBlockHead = NULL;

// Frameset
$sFrameSrcPrincipal = "<frame"
	." src=\"ressource_deposer.php{$sParamUrl}\""
	." marginwidth=\"0\" marginheight=\"0\""
	." name=\"Principale\""
	." scrolling=\"no\">";

// ---------------------
// Frame du Menu
// ---------------------
$sFrameSrcMenu = "ressource_deposer-menu.php?menu=deposer";

include_once(dir_template("dialogue","dialog-index.tpl.php"));

?>

