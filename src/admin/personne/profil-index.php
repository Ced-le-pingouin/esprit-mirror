<?php

/*
** Fichier ................: personne-index.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 26/10/2005
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
if (isset($HTTP_GET_VARS["nv"]))
	$sParamUrl = "?nv=".$HTTP_GET_VARS["nv"];
else if (isset($HTTP_GET_VARS["idPers"]))
	$sParamUrl = "?idPers=".$HTTP_GET_VARS["idPers"];
else
	$sParamUrl = NULL;

if (isset($HTTP_GET_VARS["titre"]))
	$sTitrePrincipal = $HTTP_GET_VARS["titre"];
else
	$sTitrePrincipal = "Profil";

// ---------------------
// Initialiser
// ---------------------
$sFrameSrcTitre = NULL;

$sFrameSrcPrincipal = <<<BLOC_FRAME_PRINCIPALE
<frame name="Principal" src="profil.php{$sParamUrl}" frameborder="0" scrolling="no" noresize="noresize">
BLOC_FRAME_PRINCIPALE;

$sFrameSrcMenu = "profil-menu.php";

// ---------------------
// Template
// ---------------------
include_once(dir_template("dialogue","dialog-index.tpl.php"));

?>

