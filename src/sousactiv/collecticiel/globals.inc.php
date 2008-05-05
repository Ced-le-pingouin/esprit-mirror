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
** Fichier ................: globals.inc.php
** Description ............: 
** Date de création .......: 28-10-2002
** Dernière modification ..: 10-12-2002
** Auteurs ................: Filippo Porco
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

// *************************************
//
// *************************************

require_once("../../globals.inc.php");

// *************************************
//
// *************************************

define("PAS_DOCUMENTS_SELECTIONNER",0);
define("TRANSFERT_REUSSI_SAUF",2);
define("TRANSFERT_ECHOUE",1);
define("TRANSFERT_REUSSI",255);

// *************************************
//
// *************************************

function collecticiel_erreur ($v_sMessageErreur)
{
	echo "<html>"
		."<head>"
	        ."<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n"
		.inserer_feuille_style(NULL,FALSE)
		."</head>"
		."<body>"
		."<div align=\"center\">"
		."<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"70%\">"
		."<tr>"
		."<td>{$v_sMessageErreur}</td>"
		."</tr>"
		."</table>"
		."</div>"
		."</body>"
		."</html>";
}

function creer_repertoire ($v_sNomRepertoire)
{
	$sChemin = NULL;
		
	foreach (explode("/",$v_sNomRepertoire) as $sNomRepertoire)
	{
		$sChemin .= "{$sNomRepertoire}/";
		
		if (!is_dir($sChemin))
			mkdir($sChemin,0744);
	}
}

?>
