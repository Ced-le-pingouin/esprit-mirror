<?php
require_once("globals.inc.php");
$sHead = <<<BLOCK_HTML_HEAD
<link type"text/css" rel="stylesheet" href="css://sousactive/collecticiel.css">
BLOCK_HTML_HEAD;
$sTitrePrincipal = NULL;
$sSousTitre = NULL;
include_once(dir_template("dialogue","dialog-titre.tpl.php"));
?>
