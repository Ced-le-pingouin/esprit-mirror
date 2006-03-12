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
** Fichier ................: modifier_message-index.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 13/11/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("bdd.class.php"));

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sModaliteFenetre = (empty($HTTP_GET_VARS["modaliteFenetre"]) ? NULL : $HTTP_GET_VARS["modaliteFenetre"]);
$url_iIdForum         = (empty($HTTP_GET_VARS["idForum"]) ? 0 : $HTTP_GET_VARS["idForum"]);
$url_iIdSujet         = (empty($HTTP_GET_VARS["idSujet"]) ? 0 : $HTTP_GET_VARS["idSujet"]);
$url_iIdMessage       = (empty($HTTP_GET_VARS["idMessage"]) ? 0 : $HTTP_GET_VARS["idMessage"]);
$url_iIdNiveau        = (empty($HTTP_GET_VARS["idNiveau"]) ? 0 : $HTTP_GET_VARS["idNiveau"]);
$url_iTypeNiveau      = (empty($HTTP_GET_VARS["typeNiveau"]) ? 0 : $HTTP_GET_VARS["typeNiveau"]);
$url_iIdEquipe        = (empty($HTTP_GET_VARS["idEquipe"]) ? 0 : $HTTP_GET_VARS["idEquipe"]);

// ---------------------
// Initialiser
// ---------------------
$oForum = new CForum(new CBDD(),$url_iIdForum);
$bForumParEquipe = ($oForum->retModalite() != MODALITE_POUR_TOUS);
unset($oForum);

// ---------------------
// Définir le titre de la fenêtre
// ---------------------
if ($url_sModaliteFenetre == "ajouter")
	$sTitrePrincipal = "Nouveau message".($bForumParEquipe && $url_iIdEquipe == 0 ? " (toutes les équipes)" : NULL);
else if ($url_sModaliteFenetre == "modifier")
	$sTitrePrincipal = "Modifier le message";
else if ($url_sModaliteFenetre == "supprimer")
	$sTitrePrincipal = "Supprimer le message";

// ---------------------
// Paramètres de l'url
// ---------------------
$sParamsUrl = "modifier_message.php"
	."?modaliteFenetre={$url_sModaliteFenetre}"
	."&idSujet={$url_iIdSujet}"
	."&idMessage={$url_iIdMessage}"
	."&idNiveau={$url_iIdNiveau}"
	."&typeNiveau={$url_iTypeNiveau}"
	."&idEquipe={$url_iIdEquipe}";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
   "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
<head>
<title><?=htmlentities($sTitrePrincipal)?></title>
<script type="text/javascript" language="javascript">
<!--
function oPrincipale() { return top.frames["MESSAGE"]; }
function oMenu() { return  top.frames["MENU"]; }
//-->
</script>
</head>
<frameset rows="*,26" border="0">
<frame name="MESSAGE" src="<?=$sParamsUrl?>" frameborder="0" marginwidth="5" marginheight="10" scrolling="no" noresize="noresize">
<frame name="MENU" src="" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</html>

