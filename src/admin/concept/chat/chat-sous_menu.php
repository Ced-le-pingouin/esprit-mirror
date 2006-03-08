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
<?php inserer_feuille_style("dialog.css"); ?>
</head>
<body class="dialog_sous_menu">
<table border="0" cellpadding="3" cellspacing="0" width="100%" height="100%" class="dialog_sous_menu">
<tr>
<?php
if (isset($HTTP_GET_VARS["AM"]))
	echo "<td align=\"right\">"
		."<a"
		." class=\"dialog_sous_menu\""
		." href=\"javascript: void(top.oPrincipal().envoyer());\""
		." onfocus=\"blur()\""
		.">Appliquer les changements</a>"
		."&nbsp;&nbsp;&#8226;&nbsp;&nbsp;"
		."<a"
		." class=\"dialog_sous_menu\""
		." href=\"javascript: top.oPrincipal().location = top.oPrincipal().location; void(0);\""
		." target=\"_top\""
		." onfocus=\"blur()\""
		.">Annuler</a>"
		."</td>\n";
else
	echo "<td>&nbsp;</td>\n";
?>
</tr>
</table>
</body>
</html>
