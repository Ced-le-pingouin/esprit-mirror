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
** Fichier ................: login-formations.php
** Description ............:
** Date de création .......: 21/09/2004
** Dernière modification ..: 14/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// {{{ Permet d'afficher que les formations accessibles qu'aux visiteurs
$oProjet->oUtilisateur = NULL;
$oProjet->asInfosSession[SESSION_FORM] = 0;
// }}}

// ---------------------
// Template
// ---------------------
$oTpl                 = new Template(dir_theme("login/login-formations.tpl",FALSE,TRUE));
$oBlocInfosPlateforme = new TPL_Block("BLOCK_INFOS_PLATEFORME",$oTpl);
$oBlocListeFormations = new TPL_Block("BLOCK_LISTE_FORMATIONS",$oBlocInfosPlateforme);
$oBlocFormation       = new TPL_Block("BLOCK_FORMATION",$oBlocListeFormations);

$sRepHttpPlateforme = dir_http_plateform();

if ($oProjet->initFormationsUtilisateur() > 0)
{
	$oBlocFormation->beginLoop();
	
	foreach ($oProjet->aoFormations as $oFormation)
	{
		$sUrl = "<a"
			." href='{$sRepHttpPlateforme}index2.php"
				."?idForm=".$oFormation->retId()
			."'"
			." target='_top'"
			.">".htmlentities($oFormation->retNom(),ENT_COMPAT,"UTF-8")."</a>";
		$oBlocFormation->nextLoop();
		$oBlocFormation->remplacer("{formation->url}",$sUrl);
	}
	
	$oBlocFormation->afficher();
	$oBlocListeFormations->afficher();
}
else
{
	$oBlocListeFormations->effacer();
}

$oBlocInfosPlateforme->afficher();

foreach (explode("\n\r",$oTpl->data) as $sLigne)
	echo "document.writeln(unescape(\"".rawurlencode($sLigne)."\"));";

$oProjet->terminer();

?>

