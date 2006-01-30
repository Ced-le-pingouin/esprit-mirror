<?php
require_once("globals.inc.php");
$sBlockHead = NULL;
$aMenus = array();
if (isset($HTTP_GET_VARS["exporter"]) && $HTTP_GET_VARS["exporter"] == "1")
	$aMenus[] = array("Exporter","top.exporter()",1);
$aMenus[] = array("Rafraîchir","top.recharger()",1,"text-align: left;");
$aMenus[] = array("Fermer","top.close()",2);
require_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>

