<?php

/*
** Fichier ................: outils-index.php
** Description ............:
** Date de création .......: 03/03/2005
** Dernière modification ..: 03/03/2005
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

// ---------------------
// Initialisation
// ---------------------

// {{{ Frame du titre
$sFrameSrcTitre  = "outils-titre.php";
$sTitrePrincipal = "Liste des votants";
$sSousTitre      = NULL;
// }}}

// {{{ Frame principale
$sFrameSrcPrincipal = <<<BLOC_FRAME_PRINCIPALE
<frame name="principale" src="liste_votants.php{$sParamsUrl}" frameborder="0" marginwidth="0" marginheight="0" scrolling="auto" noresize="noresize">
BLOC_FRAME_PRINCIPALE;

// {{{ Frame du menu
$sFrameSrcMenu = "outils-menu.php";
// }}}

// ---------------------
// Template
// ---------------------
$sBlockHead = NULL;

include_once(dir_template("dialogue","dialog-index.tpl.php"));

$oProjet->terminer();

?>

