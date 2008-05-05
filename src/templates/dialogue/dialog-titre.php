<?php
require_once("globals.inc.php");
$sTitrePrincipal = (empty($sTitrePrincipal) ? NULL : $sTitrePrincipal);
$sSousTitre = (empty($sSousTitre) ? NULL : $sSousTitre);
include_once(dir_template("dialogue","dialog-titre.tpl.php"));
?>

