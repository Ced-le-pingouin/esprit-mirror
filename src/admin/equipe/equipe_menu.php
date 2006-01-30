<?php 

require_once("globals.inc.php");

if (isset($HTTP_GET_VARS["ACTION"]) && $HTTP_GET_VARS["ACTION"] != "sup")
	$sMenu = "<a href=\"javascript: void(0);\" onclick=\"top.oPrincipal().valider()\">Valider</a>"
		."&nbsp;|&nbsp;" 
		."<a href=\"javascript: void(0);\" onclick=\"top.close()\">Annuler</a>";
else if (isset($HTTP_GET_VARS["ACTION"]) && $HTTP_GET_VARS["ACTION"] == "sup")
	$sMenu = "<a href=\"javascript: void(0);\" onclick=\"top.oPrincipal().valider()\">Oui</a>"
		."&nbsp;|&nbsp;" 
		."<a href=\"javascript: void(0);\" onclick=\"top.close()\">Non</a>";
else
		$sMenu = "<a href=\"javascript: void(0);\" onclick=\"top.close()\">Fermer</a>";

$oTpl = new Template(dir_theme("dialog-menu.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->effacer();

$oTpl->remplacer("{menu}","<td align=\"right\">{$sMenu}</td>");

$oTpl->afficher();
?>
