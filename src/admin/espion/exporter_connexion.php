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
** Fichier ................: exporter_connexion.php
** Description ............: 
** Date de création .......: 26/02/2003
** Dernière modification ..: 09/07/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

require_once("globals.inc.php");
require_once(dir_database("evenement.tbl.php"));

$oProjet = new CProjet();
$oProjet->verifPeutUtiliserOutils("PERM_OUTIL_EXPORT_TABLE_EVENEMENT");

// ---------------------
// Exporter le fichier CSV
// ---------------------
$sNomFichierCSV = "even-".$oProjet->oUtilisateur->retPseudo().".csv";

$oEvenement = new CEvenement($oProjet->oBdd);
$sNomAExporter = $oEvenement->initFichierExporter($sNomFichierCSV,$oProjet->oFormationCourante->retId());

$sFichierExporter = ereg_replace(dir_document_root(),"/",$sNomAExporter);

$oProjet->terminer();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Exporter le fichier des connexions</title>
<script type="text/javascript" language="javascript">
<!--

function envoyer() { return document.forms[0].submit(); }

//-->
</script>
</head>
<body onload="envoyer()">
<form action="<?=dir_code_lib('download.php',FALSE,FALSE)?>" method="get" target="_self">
<input type="hidden" name="f" value="<?=$sFichierExporter?>">
</form>
</body>
</html>

