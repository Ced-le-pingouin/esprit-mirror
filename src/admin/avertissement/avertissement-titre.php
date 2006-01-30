<?php
require_once("globals.inc.php");
$sTitrePrincipal = NULL;
$sSousTitre = NULL;
$sHead = <<<BLOC_HTML_HEAD
<style type="text/css">
<!--
td.dialog_logo { }
div.dialog_titre_principal { }
-->
</style>
BLOC_HTML_HEAD;
include_once(dir_template("dialogue","dialog-titre.tpl.php"));
?>

