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

$url_iNiveau = (empty($HTTP_GET_VARS["NIVEAU"]) ? 0 : $HTTP_GET_VARS["NIVEAU"]);
$url_iIdNiveau = (empty($HTTP_GET_VARS["ID_NIVEAU"]) ? 0 : $HTTP_GET_VARS["ID_NIVEAU"]);

$sCorpListe = NULL;

$sNomRepertoire = dir_modeles("equipes",NULL,TRUE);

if ($dir = @opendir($sNomRepertoire))
{
	while (($fichier = @readdir($dir)) !== FALSE)
		if ($fichier != "." && $fichier != "..")
			$sCorpListe .= "<tr>"
				."<td class=\"cellule_fonce\">"
				."<img src=\"".dir_theme("modele.gif")."\" border=\"0\">"
				."</td>"
				."<td class=\"cellule_clair\" width=\"99%\">"
				."<a href=\"ouvrir_modele.php?NIVEAU={$url_iNiveau}&ID_NIVEAU={$url_iIdNiveau}&FICHIER_MODELE=".rawurlencode($fichier)."\""
				." onfocus=\"blur()\""
				." target=\"principal\""
				.">"
				.$fichier
				."</a>"
				."</td></tr>\n";
	
	@closedir($dir);
}

?>

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="Tue, 20 Aug 1996 14:25:27 GMT">

<?php inserer_feuille_style("menu"); ?>

<script type="text/javascript" language="javascript">
<!--

function effacer()
{
	var bOk = top.oPrincipal().effacerModele();

	if (typeof(bOk) == "boolean" && bOk)
		alert("Le fichier a été supprimé du serveur");
}

function rafraichir()
{
	self.location.reload(true);
}

//-->
</script>

</head>
<body>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td style="background-color: #FFFFFF;">
<table border="0" cellpadding="2" cellspacing="1" width="100%">
<tr><td class="cellule_sous_titre" colspan="2">Liste des mod&egrave;les</td></tr>
<?php echo $sCorpListe; ?>
<tr><td colspan="2">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td><a href="javascript: effacer();" onfocus="blur()" title="Effacer le mod&egrave;le de droite">Effacer</a></td>
<td align="right"><a href="javascript: rafraichir();" onfocus="blur()">Rafraichir</a></td>
</tr>
</table>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
