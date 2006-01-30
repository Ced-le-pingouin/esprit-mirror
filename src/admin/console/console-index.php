<?php

require_once("globals.inc.php");

// ---------------------
// Initialisation
// ---------------------
$sParamUrl = NULL;
$sNomFichierIndex = "dialog-index-2.tpl";

// ---------------------
// Frame du Titre
// ---------------------
$sTitrePrincipal = "Console";
$sFrameSrcTitre = "console-titre.php";

// ---------------------
// Frame principal
// ---------------------

// Javascript
$sBlockHead = NULL;

// Frameset
$sFrameSrcPrincipal = "<frame src=\"console.php\" name=\"principal\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"yes\" noresize=\"noresize\">";

// ---------------------
// Frame du Menu
// ---------------------
$sFrameSrcMenu = "console-menu.php";
require_once(dir_template("dialogue","dialog-index.tpl.php"));
?>

