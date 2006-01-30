<?php
require_once("globals.inc.php");
$sTitrePrincipal = NULL;
$sSousTitre = NULL;
$sHead = <<<BLOCK_HTML_HEAD
<style type="text/css">
<!--
td.dialog_logo { width: 200px; }
div.dialog_titre_principal { left: 210px; }
-->
</style>
BLOCK_HTML_HEAD;
include_once(dir_template("dialogue","dialog-titre.tpl.php"));
?>

