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
** Fichier ................: theme.global.php
** Description ............: Intègre les éléments globals à propos du design de 
**                           la plate-forme et insére les thèmes que 
**                           l'utilisateur a choisi.
** Date de création .......: 10-07-2001
** Dernière modification ..: 06-04-2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

function lien_feuille_style ($v_sFichierAInclure)
{
	return "<link"
		." type=\"text/css\""
		." rel=\"stylesheet\""
		." href=\"".dir_theme(trim($v_sFichierAInclure),FALSE,FALSE)."\""
		.">\n";
}

function inserer_feuille_style ($v_asFichiersCSS=NULL,$v_bAfficher=TRUE)
{
	// Le fichier "globals.css" est le premier feuille de style à afficher
	$sLienFichiersCSS = lien_feuille_style("globals.css");
	
	if ($v_asFichiersCSS != NULL)
		foreach(explode(";",trim($v_asFichiersCSS)) as $sFeuilleDeStyle)
			$sLienFichiersCSS .= lien_feuille_style($sFeuilleDeStyle);
	
	if ($v_bAfficher) echo $sLienFichiersCSS; else return $sLienFichiersCSS;
}

?>
