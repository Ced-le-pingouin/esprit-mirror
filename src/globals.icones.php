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
** Fichier ................: globals.icones.php
** Description ............: 
** Date de création .......: 14/02/2005
** Dernière modification ..: 13/04/2005
** Auteurs ................: Cédric FLOQUET <cedric.floquet@umh.ac.be>
**                           Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

function retLienEnvoiCourriel ($v_sParamsUrl,$v_bTemplate=TRUE)
{
	if (!$v_bTemplate) $sChemin = dir_theme_commun();
	$sLien = "<a"
		." href=\"javascript: void(0);\""
		." onclick=\"choix_courriel('{$v_sParamsUrl}'); return false;\""
		." onfocus=\"blur()\""
		." title=\"Envoyer un courriel\""
		.">"
		."<img src=\"commun://icones/24x24/courriel_envoye.gif\" width=\"24\" height=\"24\" border=\"0\">"
		."</a>";
	return ($v_bTemplate ? $sLien : str_replace("commun://",$sChemin,$sLien));
}

function retLienListeInscrits ($v_bTemplate=TRUE)
{
	if (!$v_bTemplate) $sChemin = dir_theme_commun();
	$sLien = "<a"
		." href=\"javascript: void(0);\""
		." onclick=\"liste_inscrits(); return false;\""
		." onfocus=\"blur()\""
		." title=\"Consulter la liste des inscrits\""
		.">"
		."<img src=\"commun://icones/24x24/liste_inscrits.gif\" width=\"24\" height=\"24\" border=\"0\">"
		."</a>";
	return ($v_bTemplate ? $sLien : str_replace("commun://",$sChemin,$sLien));
}

?>
