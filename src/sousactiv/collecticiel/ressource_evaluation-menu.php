<?php

require_once("globals.inc.php");

$sMenu = $sBlockHead = NULL;

if (isset($HTTP_GET_VARS["eval"]) && $HTTP_GET_VARS["eval"] == "1")
	$aMenus = array(
		array("Valider","top.oPrincipale().valider();"),
		array("Annuler","top.close();")
	);

require_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

