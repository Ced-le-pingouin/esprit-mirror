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
** Fichier ................: mail-erreur-index.php
** Description ............:
** Date de création .......: 17/12/2004
** Dernière modification ..: 20/01/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_template("dialogue/dialog_simple.class.php"));

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_asDestinatairesCourriel = (empty($HTTP_POST_VARS["destinataireCourriel"]) ? NULL : $HTTP_POST_VARS["destinataireCourriel"]);

// ---------------------
// Composer la liste des destinataires n'ayant pas ou que leur adresse courriel
// est erronée
// ---------------------
$oDialogSimple = new CDialogSimple("Message de confirmation");

if (is_array($url_asDestinatairesCourriel) &&
	count($url_asDestinatairesCourriel) > 0)
{
	$sListeDestinatairesErrones = "var asListeDestinatairesErrones = new Array();\n";
	
	$iIdxDestinataireErrone = 0;
	
	foreach ($url_asDestinatairesCourriel as $sDestinataireErrone)
	{
		$iPosEtoile = strpos($sDestinataireErrone,"*");
		$sListeDestinatairesErrones .= "asListeDestinatairesErrones[".$iIdxDestinataireErrone++."] = \"".substr($sDestinataireErrone,$iPosEtoile,(strpos($sDestinataireErrone,"%20%3C")))."\";\n";
	}
	
	if ($iIdxDestinataireErrone > 0)
		$oDialogSimple->insererDansBlocJavascript($sListeDestinatairesErrones);
}

$oDialogSimple->defSrcPrincipale("mail_erreur.php?erreur=".(isset($sListeDestinatairesErrones) ? "1" : NULL));
$oDialogSimple->defSrcMenu("mail_erreur-menu.php");
$oDialogSimple->afficher();

?>

