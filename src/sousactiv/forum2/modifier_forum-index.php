<?php

/*
** Fichier ................: modifier_forum-index.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 08/10/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Jérôme TOUZE
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

$url_sModaliteFenetre = $HTTP_GET_VARS["modaliteFenetre"];
$url_iIdForumParent   = (empty($HTTP_GET_VARS["idForumParent"]) ? "0" : $HTTP_GET_VARS["idForumParent"]);

// ---------------------
// Définir le titre de la fenêtre
// ---------------------
if ($url_sModaliteFenetre == "ajouter")
	$sTitrePrincipal = "Ajouter un nouveau forum";
else if ($url_sModaliteFenetre == "modifier")
	$sTitrePrincipal = "Modifier le forum";
else if ($url_sModaliteFenetre == "supprimer")
	$sTitrePrincipal = "Supprimer le forum";

$sParamsUrl = "?modaliteFenetre={$url_sModaliteFenetre}"
	."&idForumParent={$url_iIdForumParent}";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
   "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
<head>
<title><?=htmlentities($sTitrePrincipal)?></title>
<script type="text/javascript" language="javascript" src="forum.js"></script>
</head>
<frameset rows="*,26" border="0">
<frame name="FORUM" src="modifier_forum.php<?=$sParamsUrl?>" frameborder="0" marginwidth="5" marginheight="10" scrolling="no" noresize="noresize">
<frame name="MENU" src="" frameborder="0" marginwidth="5" marginheight="5" scrolling="no" noresize="noresize">
</frameset>
</html>
