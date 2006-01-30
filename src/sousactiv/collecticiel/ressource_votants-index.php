<?php

/*
** Fichier ................: ressource_votants-index.php
** Description ............: 
** Date de création .......: 26/11/2004
** Dernière modification ..: 09/05/2005
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
$url_iIdResSA  = (empty($HTTP_GET_VARS["idResSA"]) ? 0 : $HTTP_GET_VARS["idResSA"]);
$url_iIdEquipe = (empty($HTTP_GET_VARS["idEquipe"]) ? 0 : $HTTP_GET_VARS["idEquipe"]);

// ---------------------
// Initialisation
// ---------------------
$oEquipe = new CEquipe($oProjet->oBdd,$url_iIdEquipe);

$sParamsUrl = "?idResSA={$url_iIdResSA}&idEquipe={$url_iIdEquipe}";

// ---------------------
// Frame du Titre
// ---------------------
$sFrameSrcTitre = "ressource_votants-titre.php";

$sTitrePrincipal = "Liste des votants";
$sSousTitre      = ($url_iIdEquipe > 0 ? $oEquipe->retNom() : NULL);

// Javascript
$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;

// Frameset
$sFrameSrcPrincipal = <<<BLOC_FRAME_PRINCIPALE
<frame name="principale" src="ressource_votants.php{$sParamsUrl}" frameborder="0" marginwidth="0" marginheight="0" scrolling="auto" noresize="noresize">
BLOC_FRAME_PRINCIPALE;

// ---------------------
// Frame du Menu
// ---------------------

$sFrameSrcMenu = "ressource_votants-menu.php";

include_once(dir_template("dialogue","dialog-index.tpl.php"));

$oProjet->terminer();

?>

