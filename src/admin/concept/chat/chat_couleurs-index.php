<?php

/*
** Fichier ................: chat_couleurs-index.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 11/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

if (isset($HTTP_GET_VARS["CouleurChat"]))
	$url_sCouleurChat = $HTTP_GET_VARS["CouleurChat"];
else if (isset($HTTP_POST_VARS["CouleurChat"]))
	$url_sCouleurChat = $HTTP_POST_VARS["CouleurChat"];
else
	$url_sCouleurChat = 0;
?>
<html>
<head>
<title>Les couleurs du monde</title>
<script type="text/javascript" language="javascript">
<!--
function oPrincipal() { return top.frames["principal"]; }
//-->
</script>
</head>
</html>
<frameset rows="*,50,24" border="0">
<frame src="chat_couleurs.php?CouleurChat=<?=$url_sCouleurChat?>" name="principal" frameborder="0" marginwidth="0" marginheight="0" scrolling="auto" noresize="noresize">
<frame src="chat_couleurs-site.php" name="site" frameborder="0" scrolling="no" marginwidth="0" marginheight="0" noresize="noresize">
<frame src="chat_couleurs-menu.php" name="menu" frameborder="0" scrolling="no" marginwidth="0" marginheight="0" noresize="noresize">
</frameset>
