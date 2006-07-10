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
** Fichier ................: transfert_form.php
** Description ............:
** Date de création .......: 18/08/2004
** Dernière modification ..: 28/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iNumPage   = $_GET["page"];
$url_sNomBddSrc = empty($_GET["NOM_BDD_SRC"]) ? NULL : $_GET["NOM_BDD_SRC"];
$url_sNomBddDst = empty($_GET["NOM_BDD_DST"]) ? NULL : $_GET["NOM_BDD_DST"];
$url_iIdFormSrc = empty($_GET["ID_FORM_SELECT"]) ? 0 : $_GET["ID_FORM_SELECT"];

$url_bCopierForums       = empty($_GET["COPIER_FORUMS"]) ? 1 : $_GET["COPIER_FORUMS"];
$url_bCopierSujetsForums = empty($_GET["COPIER_SUJETS_FORUMS"]) ? 1 : $_GET["COPIER_SUJETS_FORUMS"];
$url_bCopierChats        = empty($_GET["COPIER_CHATS"]) ? 1 :$_GET["COPIER_CHATS"];

// ---------------------
// Sélectionner la page qui doit être affichée par rapport au numéro
// de page
// ---------------------
$bOk = FALSE;

if ($url_iNumPage == "1")
	$sNomFichierInclure = "selectionner_bd.php";
else if ($url_iNumPage == "2")
	$sNomFichierInclure = "selectionner_form.php";
else if ($url_iNumPage == "3")
	$sNomFichierInclure = "confirmer_copie.php";
else if ($url_iNumPage == "4")
	$sNomFichierInclure = "visualiser_copie.php";
else
	$sNomFichierInclure = NULL;

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style(); ?>
<script type="text/javascript" language="javascript">
<!--
function init() {
	top.changer_menu();
}
//-->
</script>
</head>
<body onload="init()" style="background-image: none;">
<form method="get">
<?php if (isset($sNomFichierInclure)) include_once($sNomFichierInclure); ?>
<input type="hidden" name="page" value="<?=$url_iNumPage?>">
<input type="hidden" name="NOM_BDD_SRC" value="<?=$url_sNomBddSrc?>">
<input type="hidden" name="NOM_BDD_DST" value="<?=$g_sNomBdd?>">
<input type="hidden" name="ID_FORM_SELECT" value="<?=$url_iIdFormSrc?>">
<input type="hidden" name="COPIER_FORUMS" value="1">
<input type="hidden" name="COPIER_SUJETS_FORUMS" value="1">
<input type="hidden" name="COPIER_CHATS" value="1">
</form>
<script type="text/javascript" language="javascript">
<!--
<?php if (!$bOk) echo "\ttop.page = 0;\n"; ?>
//-->
</script>
</body>
</html>