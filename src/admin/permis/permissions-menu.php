<?php

require_once("globals.inc.php");

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_sMenu = (empty($_GET["menu"]) ? NULL : $_GET["menu"]);

// ---------------------
// Initialiser
// ---------------------

// Ins�rer ces lignes dans l'en-t�te de la page html
$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;

// ---------------------
// Composer le menu
// ---------------------
$aMenus = array();

$aMenus[] = array("Permissions","top.frames['Principale'].showOnly('permission'); top.frames['Haut'].showTitre('permission'); top.frames['Haut'].init();");
$aMenus[] = array("Admin","top.frames['Principale'].showOnly('admin');top.frames['Haut'].showTitre('admin')");

$aMenus[] = array("<Fermer>","top.close()");

// ---------------------
// Afficher le menu
// ---------------------
include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

