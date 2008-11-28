<?php
require_once("globals.inc.php");
$url_sTitrePrincipal = (empty($_GET["tp"]) ? NULL : stripslashes($_GET["tp"]));
$url_iIdFormation = (empty($_GET["idForm"]) ? NULL : stripslashes($_GET["idForm"]));
$sBlockHead = NULL;
$aMenus   = array();
$aMenus[] = array(_("Changer de formation"),"top.choix_formation('{$url_sTitrePrincipal}','{$url_iIdFormation}')",1,"text-align: left;");
$aMenus[] = array(_("Rafra&icirc;chir"),"top.frames['Principal'].location.reload(true)",3,"text-align: right");
$aMenus[] = array(_("Fermer"),"top.close()",2);
include_once(dir_template("dialogue","dialog-menu.tpl.php"));
?>

