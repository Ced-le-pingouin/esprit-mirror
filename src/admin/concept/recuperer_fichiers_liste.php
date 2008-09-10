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

/*
** Fichier ................: recuperer_fichiers_liste.php
** Description ............: 
** Date de création .......: 22-08-2002
** Dernière modification ..: 10-10-2002
** Auteurs ................: Filippo Porco
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

require_once("globals.inc.php");
require_once dir_lib('std/FichierInfo.php', true);
require_once dir_include('FichierEsprit.php', true);

if (!empty($_GET))
{
	$IdForm = $_GET["FORM"];
	$IdActiv = $_GET["ACTIV"];
}
else if (!empty($_POST))
{
	$IdForm = $_POST["FORM"];
	$IdActiv = $_POST["ACTIV"];
}

$sRepCours = dir_cours($IdActiv,$IdForm);

if (!empty($_GET['s']))
{
	$fichierAEffacer = new FichierInfo(dir_root_plateform($_GET['s']));
	if ($fichierAEffacer->existe())
		$fichierAEffacer->supprimer(true);
	header("Location:".basename(__FILE__)."?FORM={$IdForm}&ACTIV={$IdActiv}");
	
}

function listdir ($v_sRepertoire,$aoListDirs,$iNiveau=0)
{
	$fp = @opendir($v_sRepertoire);

	//$asFichiers = array();

	while ($file = @readdir($fp))
	{
		if ($file == "." || $file == ".." || ereg(".php",$file))
			continue;
			
		$f = $v_sRepertoire."/".$file;
		
		$r = ereg_replace(dir_document_root(),"/",dirname($f));
		
		$aoListDirs[] = array($file,!is_dir($f),$iNiveau,$r);

		if (is_dir($f))
			$aoListDirs = listdir($f,$aoListDirs,$iNiveau+1);
	}

	@closedir($fp);

	@clearstatcache();
	
	return $aoListDirs;
}

$oProjet = new CProjet();

$oActiv = new CActiv($oProjet->oBdd,$IdActiv);

$rep = ereg_replace("/\$",NULL,$sRepCours);

$aoListeFichiers = array();

$aoListeFichiers = listdir($rep,$aoListeFichiers,0);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php echo inserer_feuille_style()?>
<script type="text/javascript" language="javascript">
<!--

function init()
{
	var fctConfirmation = function()
	{
		return confirm('Êtes-vous sûr(e) de vouloir supprimer ce fichier/dossier ?');
	}

	var liens = document.getElementsByTagName('a');
	for (var i in liens)
		if (liens[i].className == 'supprimer')
			liens[i].onclick = fctConfirmation;
}

function Selectionner(v_bSelectionner)
{
	var obj = document.forms[0].elements;
	var total=obj.length;
	
	for (i=0; i<total; i++)
		if (v_bSelectionner)
			obj[i].checked = true;
		else
			obj[i].checked = !obj[i].checked;
}

//-->
</script>

<style type="text/css">
<!--
body 
{
	background-color: #FFFFFF; 
}

td, img { vertical-align: top; }

.supprimer { color: rgb(125,63,65); font-style: normal; font-weight: bold; }
//-->
</style>

</head>

<body onload="init()">

<table border="0" cellpadding="0" cellspacing="0">
<tr>
<td valign="top"><img src="images/branchbottom.gif" width="20" height="20" border="0">&nbsp;<img src="images/folderopen.gif" border="0">&nbsp;<strong><?php echo $oActiv->retNom(); ?></strong></td></tr>
<?php

$tot = count ($aoListeFichiers);

$niveau_precedent = $parent = 1;
$dernier = array ();

$sFichierDownload = dir_lib("download.php",FALSE);

for ($i=0; $i<$tot; $i++)
{
	$branche = "<img src=\"images/linebottom.gif\" width=\"20\" height=\"20\" hspace=\"0\" vspace=\"0\" border=\"0\">&nbsp;";

	list($fichier,$bEstFichier,$niveau_actuel,$sRepertoireActuel) = $aoListeFichiers[$i];

	if ($i<$tot-1)
		$niveau_suivant = $aoListeFichiers[$i+1][2];
		
	// Est-il le dernier ?
	$dernier[$niveau_actuel] = TRUE;
	
	for ($j=$i+1; $j<$tot; $j++)
		if ($niveau_actuel > $aoListeFichiers[$j][2]) break;
		else if ($niveau_actuel == $aoListeFichiers[$j][2])
		{
			$dernier[$niveau_actuel] = FALSE; break;
		}
	
	// Donner les lignes horizontales
	for ($j=0; $j<$niveau_actuel; $j++)
		if ($dernier[$j])
			//$branche .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			$branche .= "<img src=\"images/linebottom.gif\" hspace=\"0\" vspace=\"0\" border=\"0\">&nbsp;";
		else
			//$branche .= "|&nbsp;&nbsp;&nbsp;";
			$branche .= "<img src=\"images/line.gif\" hspace=\"0\" vspace=\"0\" border=\"0\">&nbsp;";

	if ($dernier[$niveau_actuel])
		//$branche .= "`--&nbsp;";
		$branche .= "<img src=\"images/branchbottom.gif\" hspace=\"0\" vspace=\"0\" border=\"0\">";
	else
		//$branche .= "|--&nbsp;";
		$branche .= "<img src=\"images/branch.gif\" hspace=\"0\" vspace=\"0\" border=\"0\">";
	
	echo "<tr><td height=\"10\">{$branche}&nbsp;"
		.($bEstFichier ? NULL : "<img src=\"images/folderopen.gif\" hspace=\"0\" vspace=\"0\" border=\"0\">&nbsp;");
	
	$urlFichier = $sRepertoireActuel.'/'.$fichier;
	if ($bEstFichier)
	{
		echo "<a href=\"{$sFichierDownload}?f=".rawurlencode($urlFichier)
		    ."\" style=\"white-space: nowrap;\" onfocus=\"blur()\">"
			.stripslashes($fichier)."</a>";
	}
	else
	{
		echo "{$fichier}";
	}
	
	$fichierActu = new FichierEsprit(dir_root_plateform($urlFichier));
	if (!$fichierActu->estSpecial())
		echo "&nbsp;<a href=\"".basename(__FILE__)."?FORM={$IdForm}&ACTIV={$IdActiv}&s=".rawurlencode($urlFichier)."\" class=\"supprimer\">(Supprimer)</a>";
	
	echo "</td></tr>\n";
}

?>
</table>

</body>

</html>

<?php $oProjet->Terminer (); ?>