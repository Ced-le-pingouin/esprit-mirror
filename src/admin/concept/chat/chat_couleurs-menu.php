<?php

require_once ("globals.inc.php");

$sMenu = "<td"
	." align=\"right\">"
	."<a"
	." href=\"javascript: top.oPrincipal().ChangerCouleur();\""
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

