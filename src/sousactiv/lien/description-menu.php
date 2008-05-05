<?php
require_once("globals.inc.php");
$sBlockHead = NULL;
$aMenus = array(
	array("Imprimer...","top.frames['PRINCIPALE'].print()",1,"text-align: left;")
	, array("Fermer","top.close()",2));
include_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>

