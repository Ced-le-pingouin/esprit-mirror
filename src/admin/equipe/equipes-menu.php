<?php

require_once ("globals.inc.php");

$sMenu = "<td width=\"99%\"><span id=\"status_bar\">&nbsp;</span></td>"
	."<td align=\"right\"><a href=\"javascript: top.close();\">Fermer</a></td>";

$sJavascript = "<script type=\"text/javascript\" language=\"javascript\">\n"
	."<!--\n"
	."function StatusBar(v_sTexteStatusBar)\n"
	."{\n"
	."\tif (document.getElementById(\"status_bar\"))\n"
	."\t\tdocument.getElementById(\"status_bar\").innerHTML = \"&nbsp;\"\n"
	."\t\t\t+ unescape(v_sTexteStatusBar);\n"
	."}\n"
	."//-->\n"
	."</script>\n";

$oTpl = new Template(dir_theme("dialog-menu.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->ajouter($sJavascript);
$oBlockHead->afficher();

$oTpl->remplacer("{menu}",$sMenu);

$oTpl->afficher();

?>

