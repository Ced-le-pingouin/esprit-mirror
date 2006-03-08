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

?>
<html>
<head>
<?php 

lierFichiersCSS("dialog.css");

if (!isset($sDialogueTitre))
	$sDialogueTitre = _("Bo&icirc;te de dialogue sans titre");

if (isset($sDialogueLogo))
	$sDialogueLogo = "<img src=\"".dir_theme($sDialogueLogo)."\" border=\"0\">";
else
	$sDialogueLogo = "&nbsp;";
?>
</head>
<body class="haut">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td width="1%" align="left" valign="top"><?=$sDialogueLogo?></td>
</tr>
</table>
<div class="dialog_titre_principal">&nbsp;<?=$sDialogueTitre?></div>
<div class="dialog_sous_titre"><?=$sDialogueSousTitre?></div>
</body>
</html>
