<?php
require_once("globals.inc.php");
$sHead =<<< BLOCK_HTML_HEAD
<style type="text/css">
<!--
td.dialog_logo { width: 180px; }
div.dialog_titre_principal { left: 185px; }
-->
</style>
BLOCK_HTML_HEAD;
$sTitrePrincipal = NULL;
$sSousTitre = NULL;
include_once(dir_template("dialogue","dialog-titre.tpl.php"));
?>

