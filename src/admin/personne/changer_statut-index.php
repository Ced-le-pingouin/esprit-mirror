<?php

/*
** Fichier ................: changer_statut-index.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 27/10/2005
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
$sParamsUrl = NULL;

foreach ($HTTP_GET_VARS as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialisation
// ---------------------
$sTitrePrincipal = "Statuts";

$sBlockHead = NULL;

$sFrameSrcTitre = NULL;

$sFrameSrcPrincipal = <<<FRAME_PRINCIPALE
<frame name="Principale" src="changer_statut.php" frameborder="0" marginwidth="10" marginheight="20" scrolling="auto" noresize="noresize">
FRAME_PRINCIPALE;

$sFrameSrcMenu = "changer_statut-menu.php";

include_once(dir_template("dialogue","dialog-index.tpl.php"));

?>
