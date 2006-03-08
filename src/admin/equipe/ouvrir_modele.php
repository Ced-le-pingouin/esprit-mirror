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
** Fichier ................: ouvrir_modele.php
** Description ............: 
** Date de création .......: 16-01-2003
** Dernière modification ..: 11-02-2003
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2003 UTE. All rights reserved.
**
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// *************************************
//
// *************************************

$url_iNiveau        = (empty($HTTP_GET_VARS["NIVEAU"]) ? 0 : $HTTP_GET_VARS["NIVEAU"]);
$url_iIdNiveau      = (empty($HTTP_GET_VARS["ID_NIVEAU"]) ? 0 : $HTTP_GET_VARS["ID_NIVEAU"]);
$url_sFichierModele = (empty($HTTP_GET_VARS["FICHIER_MODELE"]) ? NULL : rawurldecode($HTTP_GET_VARS["FICHIER_MODELE"]));
$url_sAction        = (empty($HTTP_GET_VARS["ACTION"]) ? NULL : $HTTP_GET_VARS["ACTION"]);

// *************************************
//
// *************************************

$sCorpConsigne = NULL;
$sCorpInformations = NULL;
$sCorpTableUtilisateurs = NULL;
$bFichierEffacer = "250";

// *************************************
//
// *************************************

$fichierModele = dir_modeles("equipes",$url_sFichierModele,TRUE);

if (is_file($fichierModele))
{
	$sNomFichier = "&nbsp;<img src=\"".dir_theme("signet-1.gif")."\" border=\"0\">&nbsp;".$url_sFichierModele;
	
	$sBuffer = implode("",@file($fichierModele));

	$oModele = unserialize($sBuffer);

	$iNbrEquipes = count($oModele->aiIdPers);
	
	$iNbrEtudiants = 0;

	if ($url_sAction == "ajouter" && $iNbrEquipes > 0)
	{
include_once(dir_database("ids.class.php"));

		$oIds = new CIds($oProjet->oBdd,$url_iNiveau,$url_iIdNiveau);
				
		$aiInscrits = array();
		
		// Ajouter une nouvelle équipe
		$oEquipe = new CEquipe($oProjet->oBdd);
		
		for ($iModeleEquipe=0; $iModeleEquipe<$iNbrEquipes; $iModeleEquipe++)
		{
			$aiMembres = array();
			
			for ($iMembre=0; $iMembre<count($oModele->aiIdPers[$iModeleEquipe]); $iMembre++)
				$aiMembres[$iMembre] = $oModele->aiIdPers[$iModeleEquipe][$iMembre];

			$oEquipe->defNom($oModele->asNomEquipe[$iModeleEquipe]);
			$oEquipe->defIdFormation($oIds->retIdForm());
			$oEquipe->defIdModule($oIds->retIdMod());
			$oEquipe->defIdRubrique($oIds->retIdRubrique());
			$oEquipe->verrouillerTables();
			$oEquipe->ajouter();
			
			$oEquipe->ajouterMembres($aiMembres);
		}		
		
		$oEquipe->oBdd->deverrouillerTables();
		
		unset($oModeleEquipe,$aiMembres);	
		
		echo "<html>"
			."<head>"
			."\n<script type=\"text/javascript\" language=\"javascript\"><!--\n"
			."function RafraichirFermer() "
			."{ top.opener.location = top.opener.location;\n top.close(); }\n"
			."\n//--></script>"
			."</head>"
			."<body onload=\"RafraichirFermer()\">"
			."</html>";
		exit();
	}
	else if ($url_sAction == "supprimer")
	{
		$bFichierEffacer = (@unlink($fichierModele) ? "true" : "false");
		$url_sFichierModele = NULL;
		$iNbrEquipes = 0;
	}

	for ($i=0; $i<$iNbrEquipes; $i++)
	{
		$sCorpTableUtilisateurs .= "<tr>"
			."<td>&nbsp;</td>"
			."<td class=\"Cellule_Sous_Titre\" colspan=\"2\">"
			.$oModele->asNomEquipe[$i]
			."</td>"
			."</tr>\n"
			."<tr>"
			."<td class=\"cellule_sous_titre\" width=\"1%\">&nbsp;</td>"
			."<td class=\"cellule_sous_titre\" style=\"text-align: left;\">&nbsp;Nom&nbsp;</div></td>"
			."<td class=\"cellule_sous_titre\" width=\"1%\">&nbsp;Pseudo&nbsp;</td>"
			."</tr>\n";

		$sNomClass = NULL;

		for ($j=0; $j<count($oModele->aiIdPers[$i]); $j++)
		{
			$sNomClass = (isset($sNomClass) ? NULL : " class=\"cellule_clair\"");

			$oPers = new CPersonne($oProjet->oBdd,$oModele->aiIdPers[$i][$j]);

			$sCorpTableUtilisateurs .= "<tr>"
				."<td{$sNomClass}>&nbsp;</td>"
				."<td{$sNomClass}><b>".$oPers->retNomComplet()."</b></td>"
				."<td{$sNomClass} align=\"center\">".$oPers->retPseudo()."</td>"
				."</tr>\n";

			$iNbrEtudiants++;
		}

		if ($j == 0)
			$sCorpTableUtilisateurs .= "<tr>"
				."<td class==\"cellule_clair\" colspan=\"3\" style=\"text-align: center;\"><span class=\"Attention\">Pas de membre</span></td>"
				."</tr>\n";

		// Ajouter une ligne vide
		$sCorpTableUtilisateurs .= "<tr><td colspan=\"3\">&nbsp;</td></tr>";
	}

	$sCorpInformations = "<tr><td class=\"Cellule_Sous_Titre\" colspan=\"4\">Informations générales</td></tr>\n"
		."<tr>"
		."<td class=\"cellule_sous_titre\">&nbsp;Nom de l'auteur&nbsp;</td>"
		."<td class=\"cellule_sous_titre\">&nbsp;Date de création&nbsp;</td>"
		."<td class=\"cellule_sous_titre\">Nombre d'équipes</td>"
		."<td class=\"cellule_sous_titre\">Nombre d'étudiants</td>"
		."</tr>\n"
		."<tr>"
		."<td><b>".$oModele->sAuteur."</b></td>"
		."<td align=\"center\">".$oModele->sDateCreation."</td>"
		."<td align=\"center\">{$iNbrEquipes}</td>"
		."<td align=\"center\">{$iNbrEtudiants}</td>"
		."</tr>\n";

	// *************************************
	//
	// *************************************

	$sConsigne = trim(stripslashes($oModele->sDescription));

	if (!empty($sConsigne))
		$sCorpConsigne = "<p class=\"cellule_clair\">".nl2br($sConsigne)."<p>";
}
else
	$url_sFichierModele = NULL;

if (!isset($url_sFichierModele))
{
	$sNomFichier = "<div class=\"Cellule_Sous_Titre\" align=\"center\">Sélectionner un fichier modèle dans la liste de gauche.</div>";
	$sCorpConsigne = NULL;
	$sCorpInformations = NULL;
	$sCorpTableUtilisateurs = NULL;
}

$oProjet->terminer();

?>

<html>
<head><?php inserer_feuille_style(); ?></head>
<script type="text/javascript" language="javascript">
<!--

function init()
{<?php echo ($url_sAction == "supprimer" ? "\n\ttop.oListe().rafraichir();": NULL); ?>
}

function Envoyer()
{
	document.forms[0].submit();
}

function effacerModele()
{
	var sNomModele = "<?=$url_sFichierModele?>";
	
	if (sNomModele.length < 1)
		alert("Avant d'effacer un modèle d'équipe choisissez un modèle dans la liste ci-dessus.");
	else if (confirm("Êtes-vous certain de vouloir supprimer ce modèle d'équipe\n(" + sNomModele + ")."))
	{
		document.forms[0].elements["ACTION"].value = "supprimer";
		document.forms[0].submit();
		
		return true;
	}
	
	return false;
}

//-->
</script>
<body onload="init()">
<h1 class="Cellule_Sous_Titre"><?=$sNomFichier?></h1>
<?=$sCorpConsigne?>
<table border="0" cellspacing="1" cellpadding="2" width="100%">
<?=$sCorpInformations?>
</table>
<br>
<table border="0" cellspacing="1" cellpadding="2" width="100%">
<?=$sCorpTableUtilisateurs?>
</table>
<form action="<?=$HTTP_SERVER_VARS['PHP_SELF']?>" method="get">
<input type="hidden" name="NIVEAU" value="<?=$url_iNiveau?>">
<input type="hidden" name="ID_NIVEAU" value="<?=$url_iIdNiveau?>">
<input type="hidden" name="ACTION" value="ajouter">
<input type="hidden" name="FICHIER_MODELE" value="<?=$url_sFichierModele?>">
</form>
</body>
</html>
