<?php

require_once ("globals.inc.php");

$sMenu = <<<BLOC_MENU
<td style="text-align: right;"><a href="javascript: top.close();">Fermer</a></td>
BLOC_MENU;
$oTpl = new Template(dir_theme("dialog-menu.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->effacer();

$oTpl->remplacer("{menu}",$sMenu);

$oTpl->afficher();

?>

