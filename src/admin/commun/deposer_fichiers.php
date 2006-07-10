<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

/*
** Fichier ................: deposer_fichiers.php
** Description ............: 
** Date de création .......: 25/01/2005
** Dernière modification ..: 26/01/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_lib("systeme_fichiers.lib.php",TRUE));

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sTitrePrincipalFenetre = (empty($_GET["tpf"]) ? "Déposer des fichiers" : stripslashes(rawurldecode($_GET["tpf"])));
$url_sRepDestination        = (empty($_GET["repDest"]) ? NULL : rawurldecode($_GET["repDest"]));
$url_bEffacerFichiers       = (empty($_GET["effFichiers"]) ? FALSE : $_GET["effFichiers"]);
$url_bDezippe               = (empty($_GET["dezipFichier"]) ? TRUE : $_GET["dezipFichier"]);

// ---------------------
// Initialiser
// ---------------------
$asRepertoiresCopie = array();

$sRepAbsDestination = dir_document_root($url_sRepDestination);

// Rechercher les répertoires de copie
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
		$oBlocNomRepertoireCopie->remplacer(array("{option.value}","{option.label}"), array($sValeur,htmlentities($sCle,ENT_COMPAT,"UTF-8")));
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

$oTpl->remplacer("{title}",htmlentities($url_sTitrePrincipalFenetre,ENT_COMPAT,"UTF-8"));
$oTpl->afficher();

?>

