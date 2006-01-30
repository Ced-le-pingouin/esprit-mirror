<?php
require_once("globals.inc.php");
$sHead = <<<BLOCK_HTML_HEAD
<link type="text/css" rel="stylesheet" href="theme://dossier_formations-titre.css">
BLOCK_HTML_HEAD;
$sTitrePrincipal = NULL;
$sSousTitre = NULL;
include_once(dir_template("dialogue","dialog-titre.tpl.php"));
?>

