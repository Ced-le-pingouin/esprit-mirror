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
** Template ...............: dialog-titre.tpl.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 24/12/2004
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
$url_sTitre     = (empty($HTTP_GET_VARS["tp"]) ? (empty($sTitrePrincipal) ? NULL : $sTitrePrincipal) : $HTTP_GET_VARS["tp"]);
$url_sSousTitre = (empty($HTTP_GET_VARS["st"]) ? (empty($sSousTitre) ? NULL : $sSousTitre) : $HTTP_GET_VARS["st"]);

$url_sTitre     = htmlentities(stripslashes($url_sTitre));
$url_sSousTitre = htmlentities(stripslashes($url_sSousTitre));

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("dialog-titre.tpl",FALSE,TRUE));

// {{{ Afficher/Effacer l'en-tête de la page html
$oBlock_Head = new TPL_Block("BLOCK_HEAD",$oTpl);

if (isset($sHead))
{
	$oBlock_Head->ajouter($sHead);
	$oBlock_Head->afficher();
}
else
{
	$oBlock_Head->effacer();
}
// }}}

// {{{ Sous-titre
$oBloc_SousTitre = new TPL_Block("BLOCK_SOUS_TITRE",$oTpl);

if (strlen($url_sSousTitre) > 0)
{
	$oBloc_SousTitre->remplacer("{sous_titre}",$url_sSousTitre);
	$oBloc_SousTitre->afficher();
}
else
{
	$oBloc_SousTitre->effacer();
}
// }}}

$oTpl->remplacer("{titre_principal}",$url_sTitre);

$oTpl->afficher();

?>

