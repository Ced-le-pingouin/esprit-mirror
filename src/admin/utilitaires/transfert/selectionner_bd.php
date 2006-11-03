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

// ---------------------
// Ouvrir une connexion avec le serveur de la base de données
// ---------------------
$hConnexion = @mysql_connect($g_sNomServeurTransfert,$g_sNomProprietaireTransfert,$g_sMotDePasseTransfert);

if ($hConnexion === FALSE)
{
	echo "<p style=\"text-align: center\">Permission refusée</p>\n";
	return;
}

// ---------------------
// Rechercher les bases de données disponibles
// ---------------------
$aoBdds = array();

if (isset($hConnexion))
{
	$hResult_bdds = mysql_list_dbs($hConnexion);
	
	while ($aEnreg = mysql_fetch_array($hResult_bdds))
	{
		$sNomBdd = $aEnreg[0];
		$hResult_tables = mysql_list_tables($sNomBdd,$hConnexion);
		
		for ($i = 0; $i < mysql_num_rows($hResult_tables); $i++)
			if (mysql_tablename($hResult_tables, $i) == "SousActiv_Ressource_SousActiv")
			{
				$aoBdds[] = $sNomBdd;
				break;
			}
		
		mysql_free_result($hResult_tables);
	}
	
	mysql_free_result($hResult_bdds);
}

// ---------------------
// Fermer la connexion vers le serveur
// ---------------------
mysql_close($hConnexion);

// ---------------------
// Composer la liste des bases de données source
// ---------------------
$sOptionsBddsSrc = NULL;

$sBddValide = dir_document_root();

for ($i=0; $i<count($aoBdds); $i++)
{
	// Vérifier que le répertoire de la plate-forme soit accessible
	if (is_dir("{$sBddValide}{$aoBdds[$i]}"))
		$sOptionsBddsSrc .= "<option"
			." value=\"".mb_convert_encoding($aoBdds[$i],"HTML-ENTITIES","UTF-8")."\""
			.($aoBdds[$i] == $url_sNomBddSrc ? " selected" : NULL)
			.">".mb_convert_encoding($aoBdds[$i],"HTML-ENTITIES","UTF-8")."</option>\n";
}
?>
<table border="0" cellspacing="0" cellpadding="7" width="100%">
<tr>
<td rowspan="3" style="width: 100px; vertical-align: top;">
<script type="text/javascript" language="javascript">
<!--
top.afficher_etape();
//-->
</script>
<small>S&eacute;lectionnez la base de donn&eacute;es source</small>
</td>
</tr>
<tr><td><img src="<?=dir_theme_commun("espacer.gif")?>" width="1" height="5" border="0"></td></tr>
<tr>
<td>
<fieldset>
<legend>&nbsp;Base de donn&eacute;es source&nbsp;</legend>
<table border="0" cellspacing="0" cellpadding="10" width="100%">
<tr>
<td>
<select name="select_bdd_src" onchange="document.forms[0].elements['NOM_BDD_SRC'].value=this.options[this.selectedIndex].value" style="width: 100%;">
<option value="">Sélectionner une base de données source</option>
<?=$sOptionsBddsSrc?>
</select>
</td>
</tr>
</table>
</fieldset>
</td>
</tr>
<?php $bOk = TRUE; ?>