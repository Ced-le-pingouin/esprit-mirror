<?php
require_once("globals.inc.php");
$url_sTitrePrincipal = (empty($_GET["tp"]) ? NULL : stripslashes($_GET["tp"]));
$sBlockHead = NULL;
$aMenus   = array();
$aMenus[] = array(_("Changer de formation"),"top.choix_formation('{$url_sTitrePrincipal}')",1,"text-align: left;");
$aMenus[] = array(_("Fermer"),"top.close()",2);
include_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>

