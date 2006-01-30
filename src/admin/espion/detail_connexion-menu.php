<?php
require_once("globals.inc.php");
$sBlockHead = NULL;
$aMenus = array(
		array("Rafraîchir","top.recharger()",1,"text-align: left;")
		, array("Fermer","top.close()",2)
	);
require_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>

