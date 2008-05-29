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

require_once("globals.inc.php"); ?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html;  charset=utf-8">
<?php inserer_feuille_style("commun/dialog.css"); ?>
<script type="text/javascript" language="javascript">
<!--

function rafraichir()
{
	var sLocation = self.location.href.replace(/#bottom/,"");
	self.location.replace(sLocation + "#bottom");
}

//-->
</script>
</head>
<body class="dialog_sous_menu">
<table border="0" cellpadding="3" cellspacing="1" width="100%" height="100%" class="dialog_sous_menu">
<tr>
<?php
if (isset($_GET["AM"]))
	echo "<td colspan=\"2\">"
		."<a"
		." class=\"dialog_sous_menu\""
		." href=\"javascript: void(top.oPrincipal().imprimer());\""
		." onfocus=\"blur()\""
		.">Imprimer</a>"
		."</td>"
		."<td align=\"right\">"
		."<a"
		." class=\"dialog_sous_menu\""
		." href=\"javascript: void(top.oPrincipal().telecharger());\""
		." target=\"_top\""
		." onfocus=\"blur()\""
		.">Sauver</a>"
		."</td>\n";
else
	echo "<td>&nbsp;</td>\n";
?>
</tr>
</table>
</body>
</html>
