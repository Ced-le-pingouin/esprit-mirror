<?php

/*
** Fichier ................: info_bulle-index.php
** Description ............:
** Date de création .......: 11/06/2004
** Dernière modification ..: 11/06/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education (UTE)
**
*/

$sParamsUrl = "?type=".(empty($HTTP_GET_VARS["type"]) ? 0 : $HTTP_GET_VARS["type"])
	."&idType=".(empty($HTTP_GET_VARS["idType"]) ? 0 : $HTTP_GET_VARS["idType"]);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
   "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
<head>
<title>Confirmation</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript">
<!--
function oPrincipale() { return top.frames["principale"]; }
function oMenu() { return top.frames["menu"]; }
function envoyer() {top.opener.envoyer(); }
//-->
</script>
</head>
<frameset rows="*,25" border="0" frameborder="0" framespacing="3">
<frame name="principale" src="changer_type.php<?=$sParamsUrl?>" marginwidth="10" marginheight="10" frameborder="0" scrolling="no" noresize="noresize">
<frame name="menu" src="changer_type-menu.php?menu=1" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize="noresize">
</frameset>
</html>
