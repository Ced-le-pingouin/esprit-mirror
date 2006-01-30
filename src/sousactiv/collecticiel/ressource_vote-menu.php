<?php
require_once("globals.inc.php");
$sBlockHead = NULL;
$aMenus = NULL;
if (isset($HTTP_GET_VARS["voter"]) && $HTTP_GET_VARS["voter"] == "1")
	$aMenus = array(
			array("Confirmer","top.frames['Principale'].voter()"),
			array("Annuler","top.frames['Principale'].annuler()")
		);
include_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>

