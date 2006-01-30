<?php

/*
** Fichier ................: collecticiel-index.php
** Description ............:
** Date de création .......: 15/04/2005
** Dernière modification ..: 05/10/2005
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
$url_sParamsUrl = NULL;

foreach ($HTTP_GET_VARS as $sCle => $sValeur)
	$url_sParamsUrl .= (isset($url_sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialiser
// ---------------------
$g_bResponsable  = $oProjet->verifPermission("PERM_EVALUER_COLLECTICIEL");
$g_bResponsable |= $oProjet->verifPermission("PERM_VOIR_TOUS_COLLECTICIELS");

$sFramesetRows = ($g_bResponsable ? "50,1,*" : "0,0,*");

$oProjet->terminer();

?>
<html>
<frameset rows="<?=$sFramesetRows?>" border="0" frameborder="0">
<frame name="filtres" src="collecticiel-filtre.php<?=$url_sParamsUrl?>" frameborder="0" marginwidth="5" marginheight="5" noresize="noresize" scrolling="no">
<frame name="" src="<?=dir_theme('frame_separation.htm')?>" frameborder="0" marginwidth="10" marginheight="10" scrolling="no">
<frame name="collecticiel" src="" frameborder="0" marginwidth="10" marginheight="10" scrolling="auto">
</frameset>
</html>

