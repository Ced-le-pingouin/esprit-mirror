<?php

/*
** Fichier ................: sauver_modele_index.php
** Description ............: 
** Date de création .......: 14-01-2003
** Dernière modification ..: 10-02-2003
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

$amVariablesUrl = array(array("NIVEAU",0),array ("ID_NIVEAU",0));


$sParamsUrl = NULL;
	
foreach ($amVariablesUrl as $amVariableUrl)
{
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?").$amVariableUrl[0]."=";
	
	if (!empty($HTTP_POST_VARS[$amVariableUrl[0]]))
		$sParamsUrl .= $HTTP_POST_VARS[$amVariableUrl[0]];
	else if (!empty($HTTP_GET_VARS[$amVariableUrl[0]]))
		$sParamsUrl .= $HTTP_GET_VARS[$amVariableUrl[0]];
	else
		$sParamsUrl .= $amVariableUrl[1];
}

?>

<html>
<head>
<title>Enregistrer le mod&egrave;le</title>
<script type="text/javascript" language="javascript">
<!--

function oListe() { return top.frames["liste"]; }
function oPrincipal() { return top.frames["principal"]; }
function oMenu() { return top.frames["menu"]; }

function Enregistrer()
{
	if (oPrincipal().Enregistrer())
	{
	 	alert("Le fichier a bien été enregistrer");
		oListe().onload = top.close();
	}
	else
		alert("Erreur: Vérifiez que vous avez entré un nom de fichier.");
}

function Annuler()
{
	self.close();
}

//-->
</script>
</head>
<frameset rows="60,*,32">
<frame name="titre" src="sauver_modele_titre.php" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="true">
<frameset cols="209,*">
<frame name="liste" src="sauver_modele_liste.php<?=$sParamsUrl?>" frameborder="0" scrolling="auto" noresize="true">
<frame name="principal" src="sauver_modele.php<?=$sParamsUrl?>" frameborder="0" scrolling="auto" noresize="true">
</frameset>
<frame name="menu" src="sauver_modele_menu.php" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="true">
</frameset>
</html>
