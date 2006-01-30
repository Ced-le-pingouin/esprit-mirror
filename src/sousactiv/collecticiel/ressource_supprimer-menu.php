<?php
require_once("globals.inc.php");
$aMenus = $sBlockHead = NULL;
if (isset($HTTP_GET_VARS["menu"]) && $HTTP_GET_VARS["menu"] == 1)
	$aMenus = array(
			array("Oui","top.frames['principale'].confirmer()"),
			array("Non","top.frames['principale'].annuler()")
		);
require_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>

