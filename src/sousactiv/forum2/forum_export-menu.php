<?php
require_once("globals.inc.php");
$sBlockHead = NULL;
$aMenus = array();
$aMenus[] = array("Exporter","top.oPrincipale().exporter()");
$aMenus[] = array("Annuler","top.close()");
require_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>
