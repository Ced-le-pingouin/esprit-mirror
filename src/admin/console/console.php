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

// @m: 06-01-2004@
require_once("globals.inc.php");

$sListeMessages = NULL;

$sFichierLog = dir_admin("console","log/errors.log",TRUE);

if (isset($_POST["VIDER"]) && $_POST["VIDER"] == "1")
{
	$fp = fopen($sFichierLog,"w");
	fclose($fp);
}
else if (file_exists($sFichierLog))
{
	$iIndex = 0;
	
	$sNomClass = NULL;
	
	$fp = fopen($sFichierLog,"r");
	
	while (!feof($fp))
	{
		$sLigne = fgets($fp,4096);
		
		if (strlen($sLigne) > 0)
		{
			list($sMessage,$sDate,$sNomFichier,$iNumLigne) = explode(":",$sLigne);
			
			$sNomClass = ($sNomClass == "cellule_fonce" ? "cellule_clair" : "cellule_fonce");
			
			$sListeMessages .= "<tr>"
				."<td class=\"cellule_sous_titre\">&nbsp;".($iIndex+1)."&nbsp;</td>"
				."<td class=\"$sNomClass\"><input type=\"checkbox\" name=\"ligne[]\" value=\"{$iIndex}\"></td>"
				."<td class=\"$sNomClass\">{$iNumLigne}</td>"
				."<td class=\"$sNomClass\">&nbsp;{$sNomFichier}&nbsp;</td>"
				."<td class=\"$sNomClass\" nowrap=\"nowrap\">&nbsp;".rawurldecode($sDate)."&nbsp;</td>"
				."<td class=\"$sNomClass\">&nbsp;".rawurldecode($sMessage)."&nbsp;</td>"
				."</tr>\n";
			
			$iIndex++;
		}
	};
	
	fclose($fp);
}

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style(); ?>
<script type="text/javascript" language="javascript">
<!--
function vider()
{
	if (confirm("Etes-vous certain de vouloir vider le fichier ?"))
	{
		document.forms[0].VIDER.value="1";
		document.forms[0].submit();
	}
}
//-->
</script>
</head>
<body>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" target="principal">
<table border="0" cellspacing="1" cellpadding="2" width="100%">
<tr>
<td width="1%">&nbsp;</td>
<td class="cellule_sous_titre" width="1%"><input type="checkbox" name="nnn"></td>
<td class="cellule_sous_titre" width="1%">&nbsp;#&nbsp;</td>
<td class="cellule_sous_titre" width="1%">&nbsp;Nom&nbsp;du&nbsp;fichier&nbsp;</td>
<td class="cellule_sous_titre" width="1%">&nbsp;Date&nbsp;</td>
<td class="cellule_sous_titre" width="95%">&nbsp;Message&nbsp;</td>
</tr>
<?php echo $sListeMessages; ?>
</table>
<input type="hidden" name="VIDER" value="0">
</form>
</body>
</html>