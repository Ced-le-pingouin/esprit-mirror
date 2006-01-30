<?php

require_once("globals.inc.php");

$url_sNomFormation = (isset($HTTP_GET_VARS["TP"]) ? $HTTP_GET_VARS["TP"] : "&nbsp;");
$url_sSousTitre = (isset($HTTP_GET_VARS["ST"]) ? $HTTP_GET_VARS["ST"] : "");

$oTpl = new Template(dir_theme("dialog-titre-2.tpl",FALSE,TRUE));

$oBlock_Head = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlock_Head->effacer();

$oTpl->remplacer("{titre_principal}",$url_sNomFormation);
$oTpl->remplacer("{sous_titre}",$url_sSousTitre);

$oTpl->afficher();

?>
