<?php

/*
** Fichier ................: deposer_fichiers.php
** Description ............: 
** Date de cr�ation .......: 25/01/2005
** Derni�re modification ..: 26/01/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_lib("systeme_fichiers.lib.php",TRUE));

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_sTitrePrincipalFenetre = (empty($HTTP_GET_VARS["tpf"]) ? "D�poser des fichiers" : stripslashes(rawurldecode($HTTP_GET_VARS["tpf"])));
$url_sRepDestination        = (empty($HTTP_GET_VARS["repDest"]) ? NULL : rawurldecode($HTTP_GET_VARS["repDest"]));
$url_bEffacerFichiers       = (empty($HTTP_GET_VARS["effFichiers"]) ? FALSE : $HTTP_GET_VARS["effFichiers"]);
$url_bDezippe               = (empty($HTTP_GET_VARS["dezipFichier"]) ? TRUE : $HTTP_GET_VARS["dezipFichier"]);

// ---------------------
// Initialiser
// ---------------------
$asRepertoiresCopie = array();

$sRepAbsDestination = dir_document_root($url_sRepDestination);

// Rechercher les r�pertoires de copie
if (!is_dir($sRepAbsDestination))
	mkdirr($sRepAbsDestination,0744);

if (is_dir($sRepAbsDestination))
{
	$asRepertoiresCopie["racine"] = ".";
	
	$d = dir($sRepAbsDestination);
	
	while (FALSE !== ($sFichier = $d->read()))
	{
		if (!is_dir($sRepAbsDestination.$sFichier) || $sFichier == "." || $sFichier == "..")
			continue;
		$asRepertoiresCopie[$sFichier] = $sFichier;
	}
	
	$d->close();
}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("deposer_fichiers.tpl");

$oBlocDeposerFichiers = new TPL_Block("BLOCK_DEPOSER_FICHIERS",$oTpl);

if (count($asRepertoiresCopie) > 0)
{
	// {{{ Onglet
	$oTplOnglet = new Template(dir_theme("onglet/onglet_tab.tpl",FALSE,TRUE));
	$sSetOnglet = $oTplOnglet->defVariable("SET_ONGLET");
	unset($oTplOnglet);
	// }}}
	
	// {{{ Formulaire
	$oTpl->remplacer("{form}","<form action=\"deposer_fichiers-valider.php\" method=\"post\" enctype=\"multipart/form-data\">");
	
	$oBlocDeposerFichiers->remplacer("{div.deposer.text}",$sSetOnglet);
	$oBlocDeposerFichiers->remplacer("{onglet->titre}",$oBlocDeposerFichiers->defVariable("VAR_ONGLET_TITRE"));
	$oBlocDeposerFichiers->remplacer("{onglet->texte}",$oBlocDeposerFichiers->defVariable("VAR_ONGLET_TEXTE"));
	
	$oBlocNomRepertoireCopie = new TPL_Block("BLOCK_NOM_REPERTOIRE_COPIE",$oBlocDeposerFichiers);
	$oBlocNomRepertoireCopie->beginLoop();
	
	foreach ($asRepertoiresCopie as $sCle => $sValeur)
	{
		$oBlocNomRepertoireCopie->nextLoop();
		$oBlocNomRepertoireCopie->remplacer(array("{option.value}","{option.label}"), array($sValeur,htmlentities($sCle)));
	}
	
	$oBlocNomRepertoireCopie->afficher();
	
	$oBlocDeposerFichiers->remplacer("{input.dezipFichier.checked}",($url_bDezippe ? NULL : " checked=\"checked\""));
	
	$oBlocDeposerFichiers->afficher();
	
	$oTpl->remplacer("{input.repDest.value}",$url_sRepDestination);
	$oTpl->remplacer("{input.effFichiers.value}",(int)$url_bEffacerFichiers);
	
	$oTpl->remplacer("{/form}","</form>");
	// }}}
}
else
{
	$oBlocDeposerFichiers->effacer();
}

$oTpl->remplacer("{title}",htmlentities($url_sTitrePrincipalFenetre));
$oTpl->afficher();

?>

