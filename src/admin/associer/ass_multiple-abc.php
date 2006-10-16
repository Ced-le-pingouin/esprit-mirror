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
<?php echo inserer_feuille_style()?>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="language" src="<?php echo dir_javascript('globals.js')?>"></script>
<script type="text/javascript" language="language" src="ass_multiple.js"></script>
<script type="text/javascript" language="language">
<!--
var g_sRech = null;
//-->
</script>
<style type="text/css">
<!--
input { width: 30px; }
td.cellule_sous_titre { height: 20px; }
-->
</style>
</head>
<body>
<table border="1" cellspacing="0" cellpadding="0" width="100%">
<tr><td>&nbsp;</td></tr>
<?php
{
	$l = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$i = 0;
	while ($i<strlen($l)) {
		echo "<tr><td class=\"cellule_sous_titre\" align=\"center\">&nbsp;<a href=\"javascript: sePlacerPersonne('$l[$i]',oFramePersonnes());\">$l[$i]</a>&nbsp;</td></tr>";
		$i++;
	}
}
?>
</table>
</body>
</html>
