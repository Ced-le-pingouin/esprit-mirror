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
** Fichier ................: connexion.php
** Description ............: 
** Date de création .......: 24/02/2003
** Dernière modification ..: 03/09/2004
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
// Récupérer les variables de l'url
// ---------------------
$url_iTri     = (isset($_GET["tri"]) ? $_GET["tri"] : $oEvenement->TRI_DERNIERE_CONNEXION);
$url_bSensTri = (isset($_GET["sens"]) ? $_GET["sens"] : $oEvenement->TRI_DESCENDANT);
$url_iIdPers  = (empty($_GET["idPers"]) ? 0 : $_GET["idPers"]);

// ---------------------
// Initialiser
// ---------------------
$iStatut = $oProjet->retStatutUtilisateur();
$iIdForm = $oProjet->oFormationCourante->retId();

// ---------------------
// ---------------------
$sContenuTable = NULL;

$sFlecheVersHaut = "&nbsp;<img src=\"".dir_theme("sort-incr.gif")."\" border=\"0\">";
$sFlecheVersBas = "&nbsp;<img src=\"".dir_theme("sort-desc.gif")."\" border=\"0\">";

$oEvenement->defIdFormation($iIdForm);
$oEvenement->defModeTri($url_iTri);
$oEvenement->defTriAscendant($url_bSensTri);

$iNbrEven = $oEvenement->initEvenements();

// Nom/Trier ?/Identifiant du tri
$asTitres = array(
	array("#",FALSE,NULL),
	array("Nom",TRUE,$oEvenement->TRI_NOM),
	array("Prénom",TRUE,$oEvenement->TRI_PRENOM),
	array("Pseudo",TRUE,$oEvenement->TRI_PSEUDO),
	array("Nombre de connexions",TRUE,$oEvenement->TRI_NBR_CONNEXIONS),
	array("Dernière connexion",TRUE,$oEvenement->TRI_DERNIERE_CONNEXION),
	array("Déconnexion",TRUE,$oEvenement->TRI_DERNIERE_DECONNEXION),
	array("Durée",TRUE,$oEvenement->TRI_TEMPS_CONNEXIONS),
	array("-",FALSE,NULL));

$sContenuTable .= "<tr>";

for ($i=0; $i<count($asTitres); $i++)
{
	$sContenuTable .= "<td"
		." class=\"cellule_sous_titre\""
		.">";
	
	if ($asTitres[$i][1])
		$sContenuTable .= "<a href=\"javascript: self.location='connexion.php"
		."?tri={$asTitres[$i][2]}"
		."&sens=".($url_iTri == $asTitres[$i][2] && $url_bSensTri == $oEvenement->TRI_DESCENDANT ? 1 : 0).""
		."&idPers={$url_iIdPers}';\""
		." target=\"_self\">"
		.$asTitres[$i][0]
		."</a>"
		.($url_iTri == $asTitres[$i][2] ? ($url_bSensTri == $oEvenement->TRI_ASCENDANT ? $sFlecheVersHaut : $sFlecheVersBas) : NULL);
	else
		$sContenuTable .= $asTitres[$i][0];

	$sContenuTable .= "</td>";
}

$sContenuTable .= "</tr>";

$sNomClasseCss = NULL;

for ($iIdxEven=0; $iIdxEven<$iNbrEven; $iIdxEven++)
{
	if (!is_object($oEvenement->aoEvenements[$iIdxEven]->oConnecte) ||
		($url_iIdPers > 0 && $oEvenement->aoEvenements[$iIdxEven]->oConnecte->retId() != $url_iIdPers))
		continue;
		
	$sNomClasseCss = (isset($sNomClasseCss) ? NULL : " class=\"cellule_clair\"");
	$sNomClasseCssTri = (isset($sNomClasseCss) ? " class=\"cellule_clair_fonce\"" : " class=\"cellule_clair\"");
	
	$sContenuTable .= "<tr>"
		."<td class=\"numero_ligne\" align=\"right\">"
		.($iIdxEven+1)
		."</td>"
		."<td".($url_iTri == $oEvenement->TRI_NOM ? $sNomClasseCssTri : $sNomClasseCss)." nowrap=\"true\">"
		.$oEvenement->aoEvenements[$iIdxEven]->oConnecte->retNom()
		."</td>"
		."<td".($url_iTri == $oEvenement->TRI_PRENOM ? $sNomClasseCssTri : $sNomClasseCss)." align=\"center\" nowrap=\"true\">"
		.$oEvenement->aoEvenements[$iIdxEven]->oConnecte->retPrenom()
		."</td>"
		."<td".($url_iTri == $oEvenement->TRI_PSEUDO ? $sNomClasseCssTri : $sNomClasseCss)." width=\"1%\" align=\"center\" nowrap=\"true\">"
		.$oEvenement->aoEvenements[$iIdxEven]->oConnecte->retPseudo()
		."</td>"
		."<td".($url_iTri == $oEvenement->TRI_NBR_CONNEXIONS ? $sNomClasseCssTri : $sNomClasseCss)." width=\"1%\" align=\"center\">"
		.$oEvenement->aoEvenements[$iIdxEven]->retNbrConnexion()
		."</td>"
		."<td".($url_iTri == $oEvenement->TRI_DERNIERE_CONNEXION ? $sNomClasseCssTri : $sNomClasseCss)." width=\"1%\" align=\"center\" nowrap=\"true\">"
		.$oEvenement->aoEvenements[$iIdxEven]->retDateDerniereConnexion()
		."</td>"
		."<td".($url_iTri == $oEvenement->TRI_DERNIERE_DECONNEXION ? $sNomClasseCssTri : $sNomClasseCss)." width=\"1%\" align=\"center\" nowrap=\"true\">"
		.$oEvenement->aoEvenements[$iIdxEven]->retDateDerniereDeconnexion()
		."</td>"
		."<td".($url_iTri == $oEvenement->TRI_TEMPS_CONNEXIONS ? $sNomClasseCssTri : $sNomClasseCss)." width=\"1%\" align=\"center\">"
		.$oEvenement->aoEvenements[$iIdxEven]->retTempsConnexion(TRUE)
		."</td>"
		."<td{$sNomClasseCss} width=\"1%\" align=\"center\">&nbsp;"
		."<a href=\"javascript: details('".$oEvenement->aoEvenements[$iIdxEven]->oConnecte->retId()."');\""
		." title=\"Cliquez ici pour voir la liste complète des connexions\""
		." onfocus=\"blur()\">D&eacute;tails</a>"
		."&nbsp;</td>"
		."</tr>\n";
}

$oProjet->terminer();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style("trace.css"); ?>
<script type="text/javascript" language="javascript">
<!--

var winDetail = null;

function details(v_iIdPers)
{
	var sURL = "detail_connexion-index.php?idPers=" + v_iIdPers;
	var sNomFenetre = "WinDetail";
	var sCaracteristiques = "width=640,height=480,resizable=1";
	
	winDetail = open(sURL,sNomFenetre,sCaracteristiques);
	winDetail.focus();
}

function Recharger()
{
	self.location = "<?php echo $_SERVER['PHP_SELF']?>";
	top.oExporter().location = "<?php echo dir_admin("espion","exporter_connexion.php",FALSE)?>";
}

function fermerToutesFenetres()
{
	if (winDetail != null && !winDetail.closed)
		winDetail.close();
}

//-->
</script>
</head>
<body onunload="fermerToutesFenetres()">
<table border="0" cellpadding="2" cellspacing="1" width="100%">
<?php echo $sContenuTable?>
</table>
</body>
</html>

