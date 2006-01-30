<?php

/*
** Fichier ................: sousactiv_inv-index.php
** Description ............:
** Date de cr�ation .......: 16/11/2005
** Derni�re modification ..: 24/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iIdSousActiv = $HTTP_GET_VARS["idSousActiv"];

$sParamsUrl = NULL;

foreach ($HTTP_GET_VARS as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialiser
// ---------------------
$oSousActiv = new CSousActiv($oProjet->oBdd,$url_iIdSousActiv);

$sTitrePrincipal = "Acc�s";
$sSousTitre = $oSousActiv->retNom();

// {{{ Ins�rer ces lignes dans l'en-t�te de la page html
$sBlockHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript" src="sousactiv_inv.js"></script>
BLOCK_HTML_HEAD;
// }}}

// {{{ Frame principale
$sFrameSrcPrincipal = <<<BLOCK_FRAME_PRINCIPALE
<frame name="Principale" src="sousactiv_inv.php{$sParamsUrl}" frameborder="0" marginwidth="5" marginheight="5" scrolling="auto" noresize="noresize">
BLOCK_FRAME_PRINCIPALE;
// }}}

$sFrameSrcMenu = "sousactiv_inv-menu.php";

// ---------------------
// Template
// ---------------------
include_once(dir_template("dialogue","dialog-index.tpl.php",TRUE));

$oProjet->terminer();

?>

