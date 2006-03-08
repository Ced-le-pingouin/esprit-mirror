<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

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

