<?php

/*
** Fichier ................: sauver_modele_index.php
** Description ............: 
** Date de création .......: 16-01-2003
** Dernière modification ..: 16-01-2003
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

$sParamsUrl = "?NIVEAU=".(empty($HTTP_GET_VARS["NIVEAU"]) ? 0 : $HTTP_GET_VARS["NIVEAU"])
	."&ID_NIVEAU=".(empty($HTTP_GET_VARS["ID_NIVEAU"]) ? 0 : $HTTP_GET_VARS["ID_NIVEAU"]);

?>

<html>
<head>
<title>Ouvrir un mod&egrave;le</title>
<script type="text/javascript" language="javascript">
<!--

function oListe() { return top.frames["liste"]; }
function oPrincipal() { return top.frames["principal"]; }
function oMenu() { return top.frames["menu"]; }

function Ouvrir()
{
	oPrincipal().Envoyer();
}

function Annuler()
{
	oListe().location = "sauver_modele_liste.php<?=$iIdForm?>&ACTION=annuler";

	top.close();
}

//-->
</script>
</head>
<frameset rows="62,*,32" border="0">
<frame name="titre" src="ouvrir_modele_titre.php" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="true">
<frameset cols="209,*">
<frame name="liste" src="ouvrir_modele_liste.php<?=$sParamsUrl?>" frameborder="0" scrolling="auto" noresize="true">
<frame name="principal" src="ouvrir_modele.php" frameborder="0" scrolling="auto" noresize="true">
</frameset>
<frame name="menu" src="ouvrir_modele_menu.php" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="true">
</frameset>
</html>
