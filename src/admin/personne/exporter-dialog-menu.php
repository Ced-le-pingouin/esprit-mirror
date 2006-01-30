<?php
require_once("globals.inc.php");
$sBlockHead = NULL;
$aMenus = NULL;
if (isset($HTTP_GET_VARS["exporter"]) && $HTTP_GET_VARS["exporter"] == "1")
	$aMenus = array(
		array("Exporter","top.oPrincipale().exporter()"),
		array("Fermer","top.close()")
		);
include_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>
