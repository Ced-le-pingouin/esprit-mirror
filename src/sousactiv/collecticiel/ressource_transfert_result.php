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

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdSousActiv = (empty($_GET["idSA"]) ? 0 : $_GET["idSA"]);
$url_aiIdResSA    = (empty($_GET["idResSA"]) ? NULL : explode("x",$_GET["idResSA"]));
$url_iErreur      = (empty($_GET["err"]) ? NULL : $_GET["err"]);

// ---------------------
// Initialiser
// ---------------------
$oSousActiv = new CSousActiv($oProjet->oBdd,$url_iIdSousActiv);

// ---------------------
// Construire la liste des documents non transféré
// ---------------------
if (isset($url_aiIdResSA))
{
	$sListeTransfert = "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"100%\">";
	
	$sListeTransfert .= "<tr>"
		."<td class=\"cellule_sous_titre\">&nbsp;Fichier source&nbsp;</td>"
		."<td class=\"cellule_sous_titre\">&nbsp;Collecticiel de&nbsp;destination&nbsp;</td>"
		."<td class=\"cellule_sous_titre\">&nbsp;Déposé par&nbsp;</td>"
		."<td class=\"cellule_sous_titre\">&nbsp;Transf&eacute;r&eacute;&nbsp;</td>"
		."</tr>";
	
	$sNomClassCSS = NULL;
	
	foreach ($url_aiIdResSA as $iIdResSA)
	{
		$oRessourceSousActiv = new CRessourceSousActiv($oProjet->oBdd,$iIdResSA);
		
		$oRessourceSousActiv->initExpediteur();
		
		$sNomClassCSS = ($sNomClassCSS == "cellule_clair" ? "cellule_fonce" : "cellule_clair");
		
		$sListeTransfert .= "<tr>"
			."<td class=\"{$sNomClassCSS}\">&nbsp;<b>".$oRessourceSousActiv->retNom()."</b></td>"
			."<td class=\"{$sNomClassCSS}\" align=\"center\">".$oSousActiv->retNom()."</td>"
			."<td class=\"{$sNomClassCSS}\" align=\"center\">".$oRessourceSousActiv->oExpediteur->retNomComplet()."</td>"
			."<td class=\"{$sNomClassCSS}\" width=\"1%\" align=\"center\">&nbsp;<span class=\"Texte_Negatif\">non</span>&nbsp;</td>"
			."</tr>";
	}
	
	$sListeTransfert .= "</table>";
}

// ---------------------
// Construire le corp de la page avec un message d'erreur
// ---------------------
switch ($url_iErreur)
{
	case PAS_DOCUMENTS_SELECTIONNER:
	//   --------------------------
		$sTexteHTML = "<div align=\"center\">"
			."<h3>"
			."Vous devez, au moins, s&eacute;lectionner un document &agrave; transf&eacute;rer (Rubrique 1)"
			."</h3>"
			."</div>";
		
		break;
		
	case TRANSFERT_ECHOUE:
	//   ----------------
		$sTexteHTML = "<h3>"
			."Transfert &eacute;chou&eacute;&nbsp;:"
			."</h3>"
			."<br><br>"
			.$sListeTransfert
			."<br><br><div align=\"center\"><h3>Vérifier que les étudiants associés à ces fichiers<br>sont bien inscrits dans le collecticiel cible.</h3></div>";
		
		break;
		
	case TRANSFERT_REUSSI_SAUF:
	//   ---------------------
		$sTexteHTML = "<h3>"
			."Transfert r&eacute;ussi sauf les fichiers suivants&nbsp;:"
			."</h3>"
			."<br><br>"
			.$sListeTransfert
			."<br><br><div align=\"center\"><h3>Vérifier que les étudiants associés à ces fichiers<br>sont bien inscrits dans le collecticiel cible.</h3></div>";
		
		break;
		
	default:
		$sTexteHTML = "<div align=\"center\">"
			."<h3>"
			."Transfert r&eacute;ussi&nbsp;!"
			."</h3>"
			."</div>";
}

if ($url_iErreur == TRANSFERT_REUSSI || $url_iErreur == TRANSFERT_REUSSI_SAUF)
	$sTexteHTML .= "<script type=\"text/javascript\" language=\"javascript\">\n"
		."<!--\n"
		."top.opener.location = top.opener.location;\n"
		."//-->\n"
		."</script>\n";
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style(); ?>
</head>
<body>
<?php echo $sTexteHTML; ?>
</body>
</html>

