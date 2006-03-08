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

require_once("globals.inc.php"); 

$sLigneMenu = NULL;

function dialogue_ajouter_element ($v_sLien,$v_sAligner=NULL,$v_sLargeurColonne=NULL)
{
	global $sLigneMenu;
	
	$sLigneMenu	.= "<td"
		.(isset($v_sLargeur) ? " width=\"{$v_sLargeurColonne}\"" : NULL)
		.">"
		.(isset($v_sAligner) ? "<div align=\"{$v_sAligner}\">{$v_sLien}</div>" : $v_sLien)
		."</td>";
}

function dialogue_afficher_menu ()
{
	global $sLigneMenu;
	
	echo "<html>"
		."<head>"
		.lierFichiersCSS("menu.css",FALSE)
		."</head>"
		."<body>"
		."<table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" width=\"100%\" height=\"100%\">"
		."<tr>{$sLigneMenu}</tr></table>"
		."</body>"
		."</html>";
}

?>
