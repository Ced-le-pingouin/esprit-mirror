<?php

/*
** Fichier ................: liste_equipes-index.php
** Description ............: 
** Date de création .......: 08/12/2002
** Dernière modification ..: 17/05/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
** 
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("liste_equipes.lang"));

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
$sTitrePrincipal = TITRE;

$sBlockHead = NULL;

$sFrameSrcTitre = "liste_equipes-titre.php";

$sFrameSrcPrincipal = <<<BLOC_FRAME_PRINCIPALE
<frame name="principale" src="liste_equipes.php{$sParamsUrl}" scrolling="auto" noresize="noresize">
BLOC_FRAME_PRINCIPALE;

$sFrameSrcMenu = NULL;

include_once(dir_template("dialogue","dialog-index.tpl.php"));

?>

