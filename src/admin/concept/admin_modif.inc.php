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
** Fichier ................: admin_modif.inc.php
** Description ............: 
** Date de création .......: 04/06/2004
** Dernière modification ..: 07/06/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

/*function adminRetListeStatuts ($v_sNomListeStatuts,$v_aaListeStatuts)
{
	$sListeStatuts = NULL;
	
	foreach ($v_aaListeStatuts as $aStatut)
		$sListeStatuts .= "<option"
			." value=\"{$aStatut[0]}\""
			.($aStatut[2] ? " selected" : NULL)
			.">{$aStatut[1]}</option>";
	
	return "<select name=\"{$v_sNomListeStatuts}\">{$sListeStatuts}</select>";
}*/

function adminEntrerNom ($v_sNom,$v_mValeur)
{
	global $g_bModifier;
	
	echo "\n<!-- Nom -->\n\n"
		."<tr>\n"
		."<td><div class=\"intitule\">Nom&nbsp;:</div></td>\n"
		."<td>"
		."<input type=\"text\""
		." name=\"{$v_sNom}\""
		." size=\"53\""
		." value=\"{$v_mValeur}\""
		." style=\"width: 100%;\""
		.($g_bModifier ? NULL : " disabled")
		.">" // <input>
		."</td>\n"
		."</tr>\n\n";
}

function adminRetListeModalites ($v_sNomListeModalites,$v_aaListeModalites)
{
	global $g_bModifier;
	
	$sListeModalites = NULL;
	
	foreach ($v_aaListeModalites as $aModalite)
		$sListeModalites .= "<option"
			." value=\"".$aModalite[0]."\""
			.($aModalite[2] ? " selected" : NULL)
			.">".htmlentities($aModalite[1])."</option>";
	
	return "<select name=\"{$v_sNomListeModalites}\""
		.($g_bModifier ? NULL : " disabled")
		.">{$sListeModalites}</select>";
}

?>
