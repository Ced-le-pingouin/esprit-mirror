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
** Fichier ................: detail_connexion.php
** Description ............:
** Date de création .......: 25/02/2003
** Dernière modification ..: 16/07/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("evenement.tbl.php"));

$oProjet = new CProjet();
$oEvenement = new CEvenement($oProjet->oBdd);

// ---------------------
// ---------------------
$url_iIdPers   = (isset($_GET["idPers"]) ? $_GET["idPers"] : 0);
$url_bParForms = (isset($_GET["PARFORM"]) ? 1 : 0);
$url_iTri      = (isset($_GET["tri"]) ? $_GET["tri"] : $oEvenement->TRI_CONNEXION);
$url_bSensTri  = (isset($_GET["sens"]) ? $_GET["sens"] : $oEvenement->TRI_DESCENDANT);

// *************************************
//
// *************************************

$sContenuTable = NULL;

$sFlecheVersHaut = "&nbsp;<img src=\"".dir_theme("sort-incr.gif")."\" border=\"0\">";
$sFlecheVersBas = "&nbsp;<img src=\"".dir_theme("sort-desc.gif")."\" border=\"0\">";

$oPersonne = new CPersonne($oProjet->oBdd,$url_iIdPers);
$sNomCompletUtilisateur = phpString2js($oPersonne->retNomComplet());
unset($oPersonne);

$oEvenement->defIdFormation($oProjet->oFormationCourante->retId());
$oEvenement->defParFormations($url_bParForms);
$oEvenement->defPersonne($url_iIdPers);
$oEvenement->defModeTri($url_iTri);
$oEvenement->defTriAscendant($url_bSensTri);

$g_iIdxEven = 0;

$iIdFormActuelle = $oProjet->oFormationCourante->retId();

$iNbrEven = $oEvenement->initEvenements();

while ($g_iIdxEven < $iNbrEven)
{
	$iCompteur = 1;
	
	// --------------------------------
	// En-têtes du tableau
	// --------------------------------
	
	$asTitres = array(
		array("&nbsp;&nbsp;&nbsp;&nbsp;#",FALSE,NULL),
		array("Connexion",TRUE,$oEvenement->TRI_CONNEXION),
		array("Déconnexion",TRUE,$oEvenement->TRI_DECONNEXION),
		array("Durée",TRUE,$oEvenement->TRI_TEMPS_CONNEXIONS));
	
	$sContenuTable .= "<tr>";
	
	for ($i=0; $i<count($asTitres); $i++)
	{
		$sContenuTable .= "<td class=\"cellule_sous_titre\">";
		
		if ($asTitres[$i][1])
			$sContenuTable .= "<a href=\"javascript: self.location='detail_connexion.php"
			."?tri={$asTitres[$i][2]}"
			."&sens=".($url_iTri == $asTitres[$i][2] && $url_bSensTri == $oEvenement->TRI_DESCENDANT ? 1 : 0).""
			."&idPers={$url_iIdPers}"
			.($url_bParForms ? "&PARFORM=1" : NULL)
			."';\""
			." target=\"_self\">"
			.$asTitres[$i][0]
			."</a>"
			.($url_iTri == $asTitres[$i][2] ? ($url_bSensTri == $oEvenement->TRI_ASCENDANT ? $sFlecheVersHaut : $sFlecheVersBas) : NULL);
		else
			$sContenuTable .= "<span class=\"Texte_Sous_Titre\">{$asTitres[$i][0]}</span>";
		
		$sContenuTable .= "</td>";
	}
	
	$sContenuTable .= "</tr>";
	
	// --------------------------------
	// Liste des connexions
	// --------------------------------
	
	$sNomClasseCss = NULL;
	
	for ($iIdxEven=$g_iIdxEven; $iIdxEven<$iNbrEven; $iIdxEven++)
	{
		//if ($oEvenement->aoEvenements[$iIdxEven]->retIdFormation() <> $iIdFormActuelle)
			//break;
		
		$sNomClasseCss = (isset($sNomClasseCss) ? NULL : " class=\"cellule_clair\"");
		$sNomClasseCssTri = (isset($sNomClasseCss) ? " class=\"cellule_clair_fonce\"" : " class=\"cellule_clair\"");
		
		$sContenuTable .= "<tr>"
			."<td width=\"1%\" class=\"numero_ligne\">".$iCompteur++."</td>"
			."<td".($url_iTri == $oEvenement->TRI_CONNEXION ? $sNomClasseCssTri : $sNomClasseCss)." align=\"center\">"
			.$oEvenement->aoEvenements[$iIdxEven]->retConnexion()
			."</td>"
			."<td".($url_iTri == $oEvenement->TRI_DECONNEXION ? $sNomClasseCssTri : $sNomClasseCss)." align=\"center\">"
			.$oEvenement->aoEvenements[$iIdxEven]->retDeconnexion()
			."</td>"
			."<td".($url_iTri == $oEvenement->TRI_TEMPS_CONNEXIONS ? $sNomClasseCssTri : $sNomClasseCss)." align=\"center\" width=\"1%\">"
			."&nbsp;".$oEvenement->aoEvenements[$iIdxEven]->retTempsConnexion()."&nbsp;"
			."</td>"
			."</tr>\n";
	}
	
	// --------------------------------
	// Durée totale des connexions par projet/par formations
	// --------------------------------
	
	$sContenuTable .= "<tr>"
		."<td colspan=\"3\" align=\"right\">"
		."<strong>Dur&eacute;e totale des connexions&nbsp;:</strong>"
		."</td>"
		."<td align=\"center\" width=\"1%\" style=\"border: #000000 none 1px; border-top-style: solid;\"><span class=\"Attention\">"
		."&nbsp;".$oEvenement->retDureeTotaleConnexions($iIdFormActuelle)."&nbsp;"
		."</span></td>"
		."</tr>\n";

	$g_iIdxEven = $iIdxEven;

}

if ($g_iIdxEven < 1)
	$sContenuTable .= "<tr>"
		."<td colspan=\"4\" align=\"center\">"
		."<strong>Pas de trace de connexion trouvé</strong>"
		."</td>"
		."</tr>\n";

$oProjet->terminer();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"
<?php inserer_feuille_style("trace.css"); ?>
<script type="text/javascript" language="javascript">
<!--
function changerSousTitre() {
	top.changerSousTitre("<?=$sNomCompletUtilisateur?>");
}
//-->
</script>
</head>
<body onload="changerSousTitre()">
<table border="0" cellpadding="5" cellspacing="1" width="100%">
<?=$sContenuTable?>
</table>
</body>
</html>
