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
** Fichier ................: liste_inscrits.php
** Description ............:
** Date de création .......: 17/09/2002
** Dernière modification ..: 13/10/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$iIdForm     = (empty($_GET["idform"]) ? 0 : $_GET["idform"]);
$iStatutPers = (empty($_GET["STATUT_PERS"]) ? STATUT_PERS_ETUDIANT : $_GET["STATUT_PERS"]);
$iIdPers     = (empty($_GET["IDPERS"]) ? 0 : $_GET["IDPERS"]);
$iIdMod      = (empty($_GET["ID_MOD"]) ? 0 : $_GET["ID_MOD"]);
$sAction     = (empty($_GET["ACTIOND"]) ? NULL : $_GET["ACTIOND"]);

// ---------------------
// Effacer une personne inscrite
// ---------------------
if ($sAction == "effacer" && $iIdPers > 0)
{
	switch ($iStatutPers)
	{
		case STATUT_PERS_RESPONSABLE_POTENTIEL:
		case STATUT_PERS_RESPONSABLE:
			if ($iStatutPers == STATUT_PERS_RESPONSABLE)
				$oResp = new CFormation_Resp($oProjet->oBdd,$iIdForm);
			else
				$oResp = new CProjet_Resp($oProjet->oBdd);
			$oResp->effacer($iIdPers); $oResp = NULL;
			break;
			
		case STATUT_PERS_CONCEPTEUR_POTENTIEL:
		case STATUT_PERS_CONCEPTEUR:
			if ($iStatutPers == STATUT_PERS_CONCEPTEUR)
			{
				include_once(dir_database("module_concepteur.tbl.php"));
				$oConcepteur = new CFormation_Concepteur($oProjet->oBdd,$iIdForm,$iIdPers);
			}
			else
			{
				include_once(dir_database("projet_concepteur.tbl.php"));
				$oConcepteur = new CProjet_Concepteur($oProjet->oBdd,$iIdPers);
			}
			$oConcepteur->effacerConcepteur(); $oConcepteur = NULL;
			break;
			
		case STATUT_PERS_TUTEUR:
			include_once(dir_database("module_tuteur.tbl.php"));
			$oTuteur = new CFormation_Tuteur($oProjet->oBdd,$iIdForm,$iIdPers);
			$oTuteur->effacerTuteur(); $oTuteur = NULL;
			break;
			
		case STATUT_PERS_ETUDIANT:
			$oFormInscrit = new CFormation_Inscrit($oProjet->oBdd,$iIdForm,$iIdPers);
			$oFormInscrit->effacer(); $oFormInscrit = NULL;
			break;
	}
	
	$iIdPers = 0;
}

// ---------------------
// Rechercher les personnes inscrites
// ---------------------
$sNomsInscrits = NULL;
$sMajListeCours = NULL;
$iNbrInscrits = 0;

$strPas = _("Pas de %s trouvé");
$asPasDePersonnesTrouvees = array(
		NULL,
		NULL,
		sprintf($strPas,mb_strtolower($oProjet->retTexteStatutUtilisateur(STATUT_PERS_RESPONSABLE_POTENTIEL,"M"),"UTF-8")),
		sprintf($strPas,mb_strtolower($oProjet->retTexteStatutUtilisateur(STATUT_PERS_RESPONSABLE,"M"),"UTF-8")),
		sprintf($strPas,mb_strtolower($oProjet->retTexteStatutUtilisateur(STATUT_PERS_CONCEPTEUR_POTENTIEL,"M"),"UTF-8")),
		sprintf($strPas,mb_strtolower($oProjet->retTexteStatutUtilisateur(STATUT_PERS_CONCEPTEUR,"M"),"UTF-8")),
		NULL,
		sprintf($strPas,mb_strtolower($oProjet->retTexteStatutUtilisateur(STATUT_PERS_TUTEUR,"M"),"UTF-8")),
		NULL,
		sprintf("Pas d'%s trouvé",mb_strtolower($oProjet->retTexteStatutUtilisateur(STATUT_PERS_ETUDIANT,"M"),"UTF-8")),
		NULL
	);

switch ($iStatutPers)
{
	case STATUT_PERS_RESPONSABLE_POTENTIEL:
	case STATUT_PERS_RESPONSABLE:
		
		if ($iStatutPers == STATUT_PERS_RESPONSABLE_POTENTIEL)
			$oResp = new CProjet_Resp($oProjet->oBdd);
		else
			$oResp = new CFormation_Resp($oProjet->oBdd,$iIdForm);
		
		$iNbrInscrits = $oResp->initResponsables();
		
		for ($i=0; $i<$iNbrInscrits; $i++)
			$sNomsInscrits .= "<tr>\n"
				."<td width=\"1%\">"
				."<input type=\"radio\""
				." name=\"IDPERS\""
				." onfocus=\"blur()\""
				." value=\"".$oResp->aoPersonnes[$i]->retId()."\""
				.($i < 1 ? " checked" : NULL)
				.">"
				."</td>\n"
				."<td class=\"personne\">"
				.$oResp->aoPersonnes[$i]->retNomComplet(TRUE)
				."</td>\n"
				."</tr>\n";
		
		$sMajListeCours = "oFrmCours().location = '".retPageVide()."';";
		
		break;
		
	case STATUT_PERS_CONCEPTEUR_POTENTIEL:
	case STATUT_PERS_CONCEPTEUR:
		
		if ($iStatutPers == STATUT_PERS_CONCEPTEUR)
		{
			include_once(dir_database("module_concepteur.tbl.php"));
			$oFormationConcepteur = new CFormation_Concepteur($oProjet->oBdd,$iIdForm);
			$iNbrInscrits = $oFormationConcepteur->initConcepteurs();
			$aoConcepteurs = &$oFormationConcepteur->aoConcepteurs;
			$sMajListeCours = "oFrmCours().location = 'liste_cours.php?idform={$iIdForm}"
				.($iNbrInscrits > 0 ? "&IDPERS=".$aoConcepteurs[0]->retId() : NULL)
				."&STATUT={$iStatutPers}"
				."';";
		}
		else
		{
			include_once(dir_database("projet_concepteur.tbl.php"));
			$oConcepteur = new CProjet_Concepteur($oProjet->oBdd);
			
			if (($iNbrInscrits = $oConcepteur->initConcepteurs()) > 0)
				$aoConcepteurs = &$oConcepteur->aoConcepteurs;
			
			$sMajListeCours = "oFrmCours().location = '".retPageVide()."';";
		}
		
		for ($i=0; $i<$iNbrInscrits; $i++)
		{
			$sEvent = "majListeCours('liste_cours.php"
				."?idform={$iIdForm}"
				."&IDPERS=".$aoConcepteurs[$i]->retId()
				."&STATUT={$iStatutPers}')";
			
			$sNomsInscrits .= "<tr>\n"
				."<td width=\"1%\">"
				."<input type=\"radio\""
				." name=\"IDPERS\""
				." onclick=\"{$sEvent}\""
				." onfocus=\"blur()\""
				." value=\"".$aoConcepteurs[$i]->retId()."\""
				.($i < 1 ? " checked" : NULL)
				.">"
				."</td>\n"
				."<td class=\"personne\">"
				.($iStatutPers == STATUT_PERS_CONCEPTEUR ? "<a href=\"javascript: {$sEvent}; void(0);\" onclick=\"blur()\">" : NULL)
				.$aoConcepteurs[$i]->retNomComplet(TRUE)
				.($iStatutPers == STATUT_PERS_CONCEPTEUR ? "</a >" : NULL)
				."</td>\n"
				."</tr>\n";
		}
		
		break;
		
	case STATUT_PERS_TUTEUR:
		include_once(dir_database("module_tuteur.tbl.php"));
		
		$oFormationTuteur = new CFormation_Tuteur($oProjet->oBdd,$iIdForm);
		
		$iNbrInscrits = $oFormationTuteur->initTuteurs();
		
		$aoTuteurs = &$oFormationTuteur->aoTuteurs;
		
		for ($i=0; $i<$iNbrInscrits; $i++)
		{
			$sEvent = "majListeCours('liste_cours.php"
				."?idform={$iIdForm}"
				."&IDPERS=".$aoTuteurs[$i]->retId()
				."&STATUT={$iStatutPers}')";
			
			$sNomsInscrits .= "<tr>\n"
				."<td width=\"1%\">"
				."<input type=\"radio\" name=\"IDPERS\" value=\"".$aoTuteurs[$i]->retId()."\""
				." onclick=\"{$sEvent}\""
				." onfocus=\"blur()\""
				.($i < 1 ? " checked" : NULL)
				.">"
				."</td>\n"
				."<td class=\"personne\">"
				."<a href=\"javascript: {$sEvent}; void(0);\" onclick=\"blur()\">"
				.$aoTuteurs[$i]->retNomComplet(TRUE)
				."</a >"
				."</td>\n"
				."</tr>\n";
		}
		
		$sMajListeCours = "oFrmCours().location = 'liste_cours.php"
			.($iNbrInscrits > 0 ? "?idform=$iIdForm&IDPERS=".$aoTuteurs[0]->retId()."&STATUT={$iStatutPers}" : NULL)
			."';";
		
		unset($aoTuteurs,$oFormationTuteur);
		
		break;
		
	case STATUT_PERS_ETUDIANT:
		$oFormation = new CFormation($oProjet->oBdd,$iIdForm);
		$iNbrInscrits = $oFormation->initInscrits();
		
		for ($i=0; $i<$iNbrInscrits; $i++)
		{
			$sEvent = "majListeCours('liste_cours.php"
				."?idform={$iIdForm}"
				."&IDPERS=".$oFormation->aoInscrits[$i]->retId()
				."&STATUT={$iStatutPers}')";
			
			$sNomsInscrits .= "<tr>\n"
				."<td width=\"1%\">"
				."<input type=\"radio\" name=\"IDPERS\" value=\"".$oFormation->aoInscrits[$i]->retId()."\""
				." onclick=\"{$sEvent}\""
				." onfocus=\"blur()\""
				.($i == 0 ? " checked" : NULL)
				.">"
				."</td>\n"
				."<td class=\"personne\">"
				."<a href=\"javascript: {$sEvent}; void(0);\" onclick=\"blur()\">"
				.$oFormation->aoInscrits[$i]->retNomComplet(TRUE)
				."</a >"
				."</td>\n"
				."</tr>\n";
		}
		
		$sMajListeCours = "oFrmCours().location = 'liste_cours.php"
			.($i > 0 ? "?idform=$iIdForm&IDPERS=".$oFormation->aoInscrits[0]->retId()."&STATUT={$iStatutPers}" : NULL)
			."';";
		
		break;
}

if ($iNbrInscrits < 1)
	$sNomsInscrits = "<tr>"
		."<td class=\"Infos\">"
		."&#8250;&nbsp;"
		.emb_htmlentities($asPasDePersonnesTrouvees[$iStatutPers])
		."</td>"
		."</tr>\n";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<?php inserer_feuille_style("admin/personnes.css"); ?>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript('globals.js.php')?>"></script>
<script type="text/javascript" language="javascript" src="globals.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript('window.js')?>"></script>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript('outils_admin.js')?>"></script>
<script type="text/javascript" language="javascript">
<!--
function init() {<?php echo (isset($sMajListeCours) ? " {$sMajListeCours} " : NULL); ?>}
//-->
</script>
</head>
<body onload="init()" class="associer_personnes">
<form method="get">
<table border="0" cellspacing="2" cellpadding="0" width="100%">
<?php echo $sNomsInscrits; ?>
</table>
<input type="hidden" name="ACTIOND" value="effacer">
<input type="hidden" name="STATUT_PERS" value="<?php echo $iStatutPers; ?>">
<input type="hidden" name="ID_MOD" value="<?php echo $iIdMod; ?>">
<input type="hidden" name="idform" value="<?php echo $iIdForm; ?>">
</form>
<?php
if ($sAction == "effacer")
	echo "<script language=\"javascript\">\noFrmPersonne().document.forms[0].submit();\n</script>"
?>
</body>
</html>

