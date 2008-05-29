<?php
require_once("globals.inc.php");
$sHead = <<<BLOCK_HTML_HEAD
<link type="text/css" rel="stylesheet" href="css://chat.css">
BLOCK_HTML_HEAD;
$sTitrePrincipal = "Archives des conversations";
$sSousTitre = NULL;
include_once(dir_template("dialogue","dialog-titre.tpl.php"));
?>
