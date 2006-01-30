<?php

/*
** Fichier ................: dialog-index.tpl.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 26/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
** 
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

// ---------------------
// Initialiser
// ---------------------
if (empty($sNomFichierIndex))
	$sNomFichierIndex = "dialog-index.tpl";

if (empty($sFrameSrcTitre))
	$sFrameSrcTitre = dir_template("dialogue","dialog-titre.php",FALSE);

if (empty($sFrameSrcMenu))
	$sFrameSrcMenu = dir_template("dialogue","dialog-menu.php",FALSE);

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme($sNomFichierIndex,FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);

if (empty($sBlockHead))
	$oBlockHead->effacer();
else
{
	$oBlockHead->ajouter($sBlockHead);
	$oBlockHead->afficher();
}

$oTpl->remplacer("{titre_page_html}",htmlentities($sTitrePrincipal));
$oTpl->remplacer("{frame_src_haut}","{$sFrameSrcTitre}"
	."?tp=".rawurlencode($sTitrePrincipal)
	.(isset($sSousTitre) ? "&st=".rawurlencode($sSousTitre) : NULL));
$oTpl->remplacer("{frame_principal}",$sFrameSrcPrincipal);
$oTpl->remplacer("{frame_src_bas}","{$sFrameSrcMenu}");

$oTpl->afficher();

?>

