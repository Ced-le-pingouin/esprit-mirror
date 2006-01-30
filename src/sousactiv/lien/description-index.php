<?php

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
$url_iIdNiveau   = (empty($HTTP_GET_VARS["idNiveau"]) ? 0 : $HTTP_GET_VARS["idNiveau"]);
$url_iTypeNiveau = (empty($HTTP_GET_VARS["typeNiveau"]) ? 0 : $HTTP_GET_VARS["typeNiveau"]);
$url_bEncadrer   = (empty($HTTP_GET_VARS["encadrer"]) ? 0 : $HTTP_GET_VARS["encadrer"]);

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
<title><?=htmlentities($sTitrePageHtml)?></title>
</head>
<frameset rows="65,*,24" border="0" frameborder="0" framespacing="0">
<frame name="TITRE" src="description-titre.php?tp=<?=rawurlencode($sTitrePageHtml)?>" frameborder="0" scrolling="no" marginwidth="0" marginheight="0" noresize="noresize">
<frame name="PRINCIPALE" src="description.php<?=$sParamsUrl?>" frameborder="0" scrolling="auto" noresize="noresize">
<frame name="MENU" src="description-menu.php" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</html>
