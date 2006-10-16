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

require_once("globals.inc.php");

if (isset($_GET["CouleurChat"]))
	$url_sCouleurChat = $_GET["CouleurChat"];
else if (isset($_POST["CouleurChat"]))
	$url_sCouleurChat = $_POST["CouleurChat"];
else
	$url_sCouleurChat = 0;

$asTableau = file("couleurs.txt");

$sListeCouleurs = NULL;

while (@list(,$sLigne) = each($asTableau))
{
	$sLigne = trim($sLigne);
	
	list($sNomCouleur,$sValeurCouleur) = split(";",$sLigne);
	
	$sListeCouleurs .= "<tr>"
		."<td width=\"1%\" style=\"background-color: #FFFFFF\">"
		."<input"
		." type=\"radio\""
		." name=\"CouleurChat\""
		." value=\"{$sLigne}\""
		." onfocus=\"blur()\""
		.($url_sCouleurChat == $sLigne ? " checked" : NULL)
		.">"
		."</td>"
		."<td style=\"background-color: #FFFFFF\"><b>{$sNomCouleur}</b></td>"
		."<td width=\"50%\" style=\"background-color: rgb({$sValeurCouleur});\">&nbsp;</td>"
		."</tr>\n";
}

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style("chat.css"); ?>
<script type="text/javascript" language="javascript">
<!--

function ChangerCouleur()
{
	var obj = document.forms[0].elements["CouleurChat"];
	
	for (i=0; i<obj.length; i++)
		if (obj[i].checked)
			break;
	top.opener.ChangerCouleur(obj[i].value);
	top.close();
}

//-->
</script>
</head>
<body class="couleurs">
<form>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td style="background-color: #222222;">
<table border="0" cellpadding="3" cellspacing="1" width="100%">
<?php echo $sListeCouleurs?>
</table>
</td></tr></table>
</form>
</body>
</html>
