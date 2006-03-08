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

