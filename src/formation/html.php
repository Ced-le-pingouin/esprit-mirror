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
** Date de création .......: 01/03/2001
** Dernière modification ..: 02/09/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Cédric FLOQUET <cedric.floquet@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("../../../globals.inc.php");
require_once dirname(__FILE__).'/hotpotatoes.inc.php';

$oProjet = new CProjet();

// ---------------------
// Initialisations
// ---------------------
$url_sNomFichier	= (empty($_GET["fi"]) ? NULL : $_GET["fi"]);
$url_iIdActiv		= (empty($_GET["idActiv"]) ? 0 : $_GET["idActiv"]);
$url_iIdSousActiv	= (empty($_GET["idSousActiv"]) ? 0 : $_GET["idSousActiv"]);
$url_iIdHotpot  	= (empty($_GET["IdHotpot"]) ? FALSE : $_GET["IdHotpot"]);
$url_iNumeroPage	= (empty($_GET["NumeroPage"]) ? 1 : $_GET["NumeroPage"]);

$iIdStatutUtilisateur = $oProjet->retStatutUtilisateur();
$iIdUtilisateur = $oProjet->retIdUtilisateur();

if (empty($_GET["IdExercice"]))
{
//	list($usec, $sec) = explode(' ', microtime());
//	$iGraine = mt_srand((float) $sec + ((float) $usec * 1000000)); // initialisation de la variable al�atoire versions < php 4.2.0
//	$iNbAleatoire = mt_rand(1,10000000);
//	$url_iIdSessionExercice = $iNbAleatoire;
	$url_iIdSessionExercice = $iIdUtilisateur."_".$_GET["IdHotpot"]."_".time();
	$sUrl = $_SERVER["REQUEST_URI"]."&IdExercice=$url_iIdSessionExercice";
	
	// on recharge la page si elle n'a pas le param�tre "IdExercice".
	header('location:'.$sUrl);
}
else $url_iIdSessionExercice = $_GET["IdExercice"];

$bOk = FALSE;

// ---------------------
// Importer la page html du cours
// ---------------------
$ext = NULL;

if (!empty($url_sNomFichier))
{
	$sNomFichier = stripslashes(urldecode($url_sNomFichier));
	
	// Vérifie si le fichier existe
	if (file_exists($sNomFichier))
	{
		$bOk = TRUE;
		eregi("(\.[[:alnum:]]+$)",$sNomFichier,$tmp);
		$ext = mb_strtolower($tmp[0],"UTF-8");
	}
}

if ($bOk && $url_iIdHotpot && ($ext == ".htm" || $ext == ".html") && $url_iIdActiv && $url_iIdSousActiv && ($iIdStatutUtilisateur != STATUT_PERS_VISITEUR)) {
	hotpot_patch_file($sNomFichier,$url_iIdHotpot,$url_iIdActiv,$url_iIdSousActiv, $url_iIdSessionExercice, $url_iNumeroPage); // on affiche et on s'arrête
	exit();
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
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
				."<div style=\"text-align: center;\"><a href=\"$sNomFichier\" onfocus=\"blur()\">Télécharger le fichier</a></div>"
				."</td></tr>\n</table>\n"
				."</td></tr>\n</table>\n";
			echo "<p>&nbsp;</p><p>&nbsp;</p></body>";
	}

?>
</html>
