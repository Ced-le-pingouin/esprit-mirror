<?php
require_once("globals.inc.php");
$sBlockHead = NULL;
$aMenus = array(
		array("Rafraîchir","top.frames['principal'].location=top.frames['principal'].location",1,"text-align: left;")
		, array("Vider","top.frames['principal'].vider()",2,"text-align: center;")
		, array("Fermer","top.close()",3)
	);
require_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>
