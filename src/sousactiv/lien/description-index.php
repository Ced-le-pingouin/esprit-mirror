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
** Fichier ................: description-index.php
** Description ............:
** Date de création .......: 23/11/2004
** Dernière modification ..: 18/02/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdNiveau   = (empty($_GET["idNiveau"]) ? 0 : $_GET["idNiveau"]);
$url_iTypeNiveau = (empty($_GET["typeNiveau"]) ? 0 : $_GET["typeNiveau"]);
$url_bEncadrer   = (empty($_GET["encadrer"]) ? 0 : $_GET["encadrer"]);

// ---------------------
// Initialiser
// ---------------------
switch ($url_iTypeNiveau)
{
	case TYPE_FORMATION:
		$oFormation = new CFormation($oProjet->oBdd,$url_iIdNiveau);
		$sTitrePageHtml = $oFormation->retNom();
		unset($oFormation);
		break;
		
	case TYPE_RUBRIQUE:
		$oRubrique = new CModule_Rubrique($oProjet->oBdd,$url_iIdNiveau);
		$sTitrePageHtml = $oRubrique->retNom();
		unset($oRubrique);
		break;
		
	case TYPE_SOUS_ACTIVITE:
		$oSousActiv = new CSousActiv($oProjet->oBdd,$url_iIdNiveau);
		$sTitrePageHtml = $oSousActiv->retNom();
		unset($oSousActiv);
		break;
		
	default:
		$sTitrePageHtml = NULL;
}

$sParamsUrl = "?idNiveau={$url_iIdNiveau}&typeNiveau={$url_iTypeNiveau}&encadrer={$url_bEncadrer}&indirect=1";

$oProjet->terminer();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" 
	"http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title><?php echo emb_htmlentities($sTitrePageHtml)?></title>
</head>
<frameset rows="65,*,24" border="0" frameborder="0" framespacing="0">
<frame name="TITRE" src="description-titre.php?tp=<?php echo rawurlencode($sTitrePageHtml)?>" frameborder="0" scrolling="no" marginwidth="0" marginheight="0" noresize="noresize">
<frame name="PRINCIPALE" src="description.php<?php echo $sParamsUrl?>" frameborder="0" scrolling="auto" noresize="noresize">
<frame name="MENU" src="description-menu.php" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</html>
