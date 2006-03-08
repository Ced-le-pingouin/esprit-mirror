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
** Fichier ................: admin_index.php
** Description ............:
** Date de création .......: 28-12-2002
** Dernière modification ..: 29-12-2002
** Auteurs ................: Filippo Porco
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

$iIdForm = (isset($HTTP_POST_VARS["ID_FORM"]) ? $HTTP_POST_VARS["ID_FORM"] : NULL);

require_once("globals.inc.php");

$oProjet = new CProjet();

$oFormation = new CFormation($oProjet->oBdd,$iIdForm);

if (isset($HTTP_POST_VARS["EFFACER"]))
{
	$oFormation->effacer();
}

$oFormation->initFormationsEffacer();

$sCorpHTML = $sNomClasse = NULL;

for ($iIdxForm=0; $iIdxForm<count($oFormation->aoFormations); $iIdxForm++)
{
	$sNomClasse = ($sNomClasse == "cellule_clair" ? "cellule_fonce" : "cellule_clair");

	$sCorpHTML .= "<tr>"
		."<td class=\"$sNomClasse\" width=\"1%\">"
		."<input type=\"radio\" name=\"ID_FORM\""
		." value=\"".$oFormation->aoFormations[$iIdxForm]->retId()."\""
		.">"
		."</td>"
		."<td class=\"$sNomClasse\">"
		.$oFormation->aoFormations[$iIdxForm]->retNom()
		."</td>"
		."</tr>\n";
}

if ($iIdxForm > 0)
	$sCorpHTML = "<p class=\"Cellule_Sous_Titre\">Attention, cette opération est dangereuse. Vous allez effacer définitivement la formation.</p>"
		."<form action=\"".$HTTP_SERVER_VARS["PHP_SELF"]."\" method=\"post\">"
		."<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">"
		.$sCorpHTML
		."</table>\n"
		."<a href=\"javascript: document.forms[0].submit();\">supprimer</a>"
		."<input type=\"hidden\" name=\"EFFACER\" value=\"1\">"
		."</form>\n";
else
	$sCorpHTML = "<div align=\"center\">"
		."<span class=\"Cellule_Sous_Titre\">"
		."Corbeille à formations est vide"
		."</span>"
		."</div>";

?>

<html>
<head>
<title>Corbeille</title>
<?php inserer_feuille_style(); ?>
</head>
<body>
<?php echo $sCorpHTML; ?>
</body>
</html>
