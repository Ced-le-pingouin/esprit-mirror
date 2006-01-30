<?php
require_once("globals.inc.php");
$sBlockHead = NULL;
$aMenus = NULL;
if (isset($HTTP_GET_VARS["menu"]) && $HTTP_GET_VARS["menu"] == "1")
	$aMenus = array(
		array("Valider","top.oPrincipale().envoyer()"),
		array("Annuler","top.oPrincipale().annuler()")
		);
require_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>
