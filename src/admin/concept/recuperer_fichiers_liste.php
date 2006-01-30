<?php

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

if (!empty($HTTP_GET_VARS))
{
	$IdForm = $HTTP_GET_VARS["FORM"];
	$IdActiv = $HTTP_GET_VARS["ACTIV"];
}
else if (!empty($HTTP_POST_VARS))
{
	$IdForm = $HTTP_POST_VARS["FORM"];
	$IdActiv = $HTTP_POST_VARS["ACTIV"];
}

$sRepCours = dir_cours($IdActiv,$IdForm);

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
<?=inserer_feuille_style()?>
<script type="text/javascript" language="javascript">
<!--

function init()
{
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

a, td {vertical-align: top;}

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
	
	if ($bEstFichier)
	{
		$rep = rawurlencode($sRepertoireActuel."/".$fichier);
		
		echo "<a href=\"{$sFichierDownload}?f=$rep\" style=\"white-space: nowrap;\" onfocus=\"blur()\">"
			.stripslashes($fichier)."</a>";
	}
	else
	{
		echo "{$fichier}";
	}

	echo "</td></tr>\n";
}

?>
</table>

</body>

</html>

<?php $oProjet->Terminer (); ?>
