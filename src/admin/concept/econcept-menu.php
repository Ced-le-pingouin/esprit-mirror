<?php
require_once("globals.inc.php");
$url_sTitrePrincipal = (empty($HTTP_GET_VARS["tp"]) ? NULL : stripslashes($HTTP_GET_VARS["tp"]));
$sBlockHead = NULL;
$aMenus   = array();
$aMenus[] = array("Changer de formation","top.choix_formation('{$url_sTitrePrincipal}')",1,"text-align: left;");
$aMenus[] = array("Rafraîchir","top.rafraichir()",2,"text-align: center;");
$aMenus[] = array("Fermer","top.close()",3);
include_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>

