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
** Fichier ................: html.php
** Description ............: 
** Date de cr�ation .......: 01/03/2001
** Derni�re modification ..: 02/09/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           C�dric FLOQUET <cedric.floquet@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("../../../globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Initialisations
// ---------------------
$url_sNomFichier  = (empty($HTTP_GET_VARS["fi"]) ? NULL : $HTTP_GET_VARS["fi"]);
$url_iIdSousActiv = (empty($HTTP_GET_VARS["idSousActiv"]) ? 0 : $HTTP_GET_VARS["idSousActiv"]);

$bOk = FALSE;

// ---------------------
// Importer la page html du cours
// ---------------------
$ext = NULL;

if (!empty($url_sNomFichier))
{
	$sNomFichier = stripslashes(urldecode($url_sNomFichier));
	
	// V�rifie si le fichier existe
	if (file_exists($sNomFichier))
	{
		$bOk = TRUE;
		eregi("(\.[[:alnum:]]+$)",$sNomFichier,$tmp);
		$ext = strtolower($tmp[0]);
	}
}

if ($ext == ".htm" ||
	$ext == ".html" ||
	$ext == ".doc" ||
	$ext == ".pdf" ||
	$ext == ".xml" ||
	$ext == ".pps" ||
	$ext == ".ppt" ||
	$ext == ".swf" ||
	$ext == ".rtf")
{
	if ($bOk)
		header("Location: {$sNomFichier}");
	
	exit();
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php inserer_feuille_style("zdc_frame_principale.css"); ?>
</head>
<?php 
if ($bOk)
	switch ($ext)
	{
		
		case ".htm":
		case ".html":
			include_once($sNomFichier);
			break;
			
		case ".gif":
		case ".jpg":
		case ".png":
			echo "<body>";
			echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" height=\"80%\">\n"
				."<tr><td>"
				."<div style=\"text-align: center;\"><img src=\"$sNomFichier\" border=\"0\"></div>"
				."</td></tr>\n</table>\n";
			echo "<p>&nbsp;</p><p>&nbsp;</p></body>";
			break;
		
		case ".txt":
			echo "<body>";
			echo join("<br>",file($sNomFichier));
			echo "<p>&nbsp;</p><p>&nbsp;</p></body>";
			break;
		
		default:
			echo "<body>";
include_once(dir_database("sous_activite.tbl.php"));
			
			$oSousActiv = new CSousActiv($oProjet->oBdd,$url_iIdSousActiv);
			
			echo "<b>".$oSousActiv->retDescr()."</b><br><br><br>";
			echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"0\" width=\"80%\" style=\"background-color: #EEEECC;\" align=\"center\">\n"
				."<tr><td>"
				."<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">\n"
				."<tr><td style=\"background-color: #FFFFBB;\">"
				."<div style=\"text-align: center;\"><a href=\"$sNomFichier\" onfocus=\"blur()\">T�l�charger le fichier</a></div>"
				."</td></tr>\n</table>\n"
				."</td></tr>\n</table>\n";
			echo "<p>&nbsp;</p><p>&nbsp;</p></body>";
	}

?>
</html>
