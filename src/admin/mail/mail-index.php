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
** Fichier ................: mail-index.php
** Description ............:
** Date de création .......: 14/12/2004
** Dernière modification ..: 15/02/2005
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

foreach ($_GET as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialiser
// ---------------------
$sTitrePrincipale = "Envoi courriel";

// ---------------------
// Template
// ---------------------
$oTplFrameset = new Template(dir_theme("mail-index.inc.tpl",FALSE,TRUE));

$oTpl = new Template("mail-index.tpl");

$oBlocFrameset = new TPL_Block("BLOCK_FRAMESET",$oTpl);
$oBlocFrameset->ajouter($oTplFrameset->retDonnees());
$oBlocFrameset->afficher();

$oTpl->remplacer("{html.title}",htmlentities($sTitrePrincipale,ENT_COMPAT,"UTF-8"));

// {{{ Frames
$oTpl->remplacer("{frame.titre.src}","mail-titre.php?tp=".rawurlencode($sTitrePrincipale));
$oTpl->remplacer("{frame.infos.src}","mail-infos.php{$sParamsUrl}");
$oTpl->remplacer("{frame.principale.src}","mail.php");
$oTpl->remplacer("{frame.menu.src}","mail-menu.php");
// }}}

$oTpl->afficher();

?>

