<?php

/*
** Fichier ................: formulaire_index.php modifié à partir de concept_index.php
** Description ............:
** Date de création .......: 01-09-2001
** Dernière modification ..: 02-02-2004
** Auteurs ................: Ludovic Flamme
** Emails .................: ute@umh.ac.be
**
*/
/*
require_once("globals.inc.php");

$oProjet = new CProjet();
*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN" "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Conception d'activités en ligne - Unite de Technologie de l'Education</title>
<script language="javascript">
	function defTexteStatut(v_sTitle) {	top.frames["menu"].document.getElementById("id_status").innerHTML = unescape(v_sTitle); }
</script>
</head>

<frameset rows="66,*,24" border="0" frameborder="0" framespacing="0">
	<frame src="formulaire_titre.php" name="FORMFRAMETITRE" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="true">
	<frameset cols="210,*" border="0" frameborder="0" framespacing="0">
		<frame name="FORMFRAMEMENU" src="formulaire_menu.php?bMesForms=1" marginwidth="2" marginheight="2" frameborder="0" noresize="true" scrolling="no">
		<frameset rows="*,50%,20" border="0" frameborder="0" framespacing="0">
			<frame name="FORMFRAMELISTE" src="formulaire_liste.php" frameborder="0" scrolling="yes">
			<frame name="FORMFRAMEMODIF" src="formulaire_modif.php" frameborder="0" scrolling="yes" noresize="true">
			<frame name="FORMFRAMEMODIFMENUBAS" src="formulaire_modif_menu_bas.php" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize>
		</frameset>
	</frameset>
	<frame name="menu" src="formulaire_bas.php" frameborder="0" marginwidth="0" marginheight="0" noresize scrolling="no">
</frameset>
</html>
