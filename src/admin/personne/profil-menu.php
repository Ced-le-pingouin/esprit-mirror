<?php

require_once ("globals.inc.php");

if (isset($HTTP_GET_VARS["corriger"]))
	$sMenu = "<td"
		." align=\"right\">"
		."<a"
		." href=\"javascript: top.location=top.location; void(0);\""
		." onclick=\"blur()\""
		.">Recommencer</a>"
		."&nbsp;|&nbsp;"
		."<a href=\"javascript: top.close();\">Annuler</a>"
		."</td>";
else
	$sMenu = "<td"
		." align=\"right\">"
		."<a"
		." href=\"javascript: top.frames['Principal'].document.forms[0].submit(); void(0);\""
		." onclick=\"blur()\""
		.">Valider</a>"
		."&nbsp;|&nbsp;"
		."<a href=\"javascript: top.close();\">Annuler</a>"
		."</td>";

$oTpl = new Template(dir_theme("dialog-menu.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->effacer();

$oTpl->remplacer("{menu}",$sMenu);

$oTpl->afficher();

?>

