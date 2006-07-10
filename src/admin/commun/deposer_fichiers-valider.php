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
** Fichier ................: deposer_fichiers-valider.php
** Description ............: 
** Date de création .......: 26/01/2005
** Dernière modification ..: 16/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_code_lib("fichiers_permis.inc.php"));

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sNomFichierCopier = (empty($_FILES["nomFichierCopier"]["name"]) ? "none" : $_FILES["nomFichierCopier"]["name"]);
$url_sRepDestination   = (empty($_POST["repDest"]) ? NULL : $_POST["repDest"]);
$url_bDezippe          = (empty($_POST["dezipFichier"]) ? TRUE : FALSE);

// ---------------------
// Déposer un fichier
// ---------------------
if ($url_sNomFichierCopier != "none" && validerFichier($url_sNomFichierCopier))
{
$sBlocPageHtml = <<<BLOC_PAGE_HTML
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript">
<!--
function init()
{
	if (window.opener && window.opener.deposer_fichiers_callback)
		window.opener.deposer_fichiers_callback();
	self.close();
}
//-->
</script>
</head>
<body onload="init()">
</body>
</html>
BLOC_PAGE_HTML;
	
	$url_sNomRepertoireCopie = $url_sRepDestination;
	
	if ("." != $_POST["nomRepertoireCopie"])
		$url_sNomRepertoireCopie .= $_POST["nomRepertoireCopie"]."/";
	
	$sDestination = dir_document_root($url_sNomRepertoireCopie);
	
	move_uploaded_file($_FILES["nomFichierCopier"]["tmp_name"],$sDestination.$url_sNomFichierCopier);
	
	@chmod($sDestination.$url_sNomFichierCopier,0644);
	
	// Décompressé le fichier zip
	if ($url_bDezippe)
		unzip($sDestination,$url_sNomFichierCopier);
}
else
{
$sBlocPageHtml = <<<BLOC_PAGE_HTML
<html>
<head>
</head>
<body>
<p>&nbsp;</p>
<p style="text-align: center; font-weight: bold;">Ce type de fichier n'est pas autorisé par la plate-forme</p>
</body>
</html>
BLOC_PAGE_HTML;
}

echo $sBlocPageHtml;

exit();

?>
