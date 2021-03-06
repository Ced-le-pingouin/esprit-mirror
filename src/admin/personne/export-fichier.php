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
require_once(dir_database("bdd.class.php"));
require_once(dir_include("personnes.class.php"));

$oProjet = new CProjet();

if (!is_object($oProjet->oUtilisateur))
	exit();

$oPersonnes = new CPersonnes($oProjet->oBdd);
$aoPersonnes = $oPersonnes->retListePersonnesGraceIds(explode(",",$_POST["LISTE_IDPERS"]));

$asListeChamps = explode(",",$_POST["LISTE_CHAMPS"]);
$asChampsValides = array("Nom","Prenom","Pseudo","DateNaiss","Sexe","Adresse","NumTel","Email","UrlPerso","Mdp");

$sRepRel = dir_tmp(NULL,FALSE);
$sRepAbs = dir_tmp(NULL,TRUE);

$sNomFichier = "personne-".$oProjet->oUtilisateur->retPseudo();

$sExtensionFichier = (isset($_POST["TYPE"]) ? $_POST["TYPE"] : "csv");

switch ($sExtensionFichier)
{
	case "html":
		require_once(dir_include("table2html.class.php"));
		
		// Nom du fichier
		$sNomFichier .= ".htm";
		$oExport = new CTable2HTML($sRepAbs.$sNomFichier);
		break;
		
	default:
		require_once(dir_include("table2csv.class.php"));
		
		// Nom du fichier
		$sNomFichier .= ".csv";
		$oExport = new CTable2CSV($sRepAbs.$sNomFichier);
}

// Envoyer ou non la liste des noms des champs
if (isset($_POST["ENVOYER_NOMS_CHAMPS"]) &&
	$_POST["ENVOYER_NOMS_CHAMPS"] == "on")
	$oExport->defChamps($asListeChamps);

// Composer la liste des personnes
foreach ($aoPersonnes as $oPersonne)
{
	$asLigneDonnees = array();
	
	foreach ($asChampsValides as $sChampValide)
		if (in_array($sChampValide,$asListeChamps))
		{
			eval("\$sValeurChamp = \$oPersonne->ret{$sChampValide}();");
			
			if ($sChampValide == "Email" && $sExtensionFichier == "html" && !empty($sValeurChamp))
				$sValeurChamp = "<a href=\"mailto: {$sValeurChamp}\">{$sValeurChamp}</a>";
			
			$asLigneDonnees[] = $sValeurChamp;
		}
	
	$oExport->defDonnees($asLigneDonnees);
}

$oExport->exporter();

$oProjet->terminer();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php echo inserer_feuille_style("admin/personnes.css")?>
<script type="text/javascript" language="javascript">
<!--
function exporter()
{
	top.oMenu().location = "exporter-dialog-menu.php";
	document.forms[0].submit();
}
//-->
</script>
</head>
<body onload="exporter()" class="exporter_personnes">
<p>&nbsp;</p>
<div align="center">
<p>Exportation de la liste des inscrits termin&eacute;e.</p>
<p>Veuillez fermer cette fen&ecirc;tre.</p>
</div>
<form action="<?php echo dir_lib('download.php',FALSE,FALSE)?>" method="get">
<input type="hidden" name="f" value="tmp/<?php echo $sNomFichier?>">
</form>
</body>
</html>
