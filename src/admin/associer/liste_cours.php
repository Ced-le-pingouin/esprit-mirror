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
** Fichier ................: liste_cours.php
** Description ............:
** Date de création .......: 18/09/2002
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
$iIdForm   = (empty($_GET["idform"]) ? 0 : $_GET["idform"]);
$iIdPers   = (empty($_GET["IDPERS"]) ? 0 : $_GET["IDPERS"]);
$iIdStatut = (empty($_GET["STATUT"]) ? 0 : $_GET["STATUT"]);

// ---------------------
// Initialiser la formation
// ---------------------
$oFormation = new CFormation($oProjet->oBdd,$iIdForm);

$bAutoInscription = $oFormation->retInscrAutoModules();

// ---------------------
// Initialiser les variables globales
// ---------------------
$iNbrModules = 0;

$sListeCours = NULL;

$sAppliquerChangements = "<div align=\"right\">"
	."<a href=\"javascript: rechargerListeCours(); void(0);\""
	." onfocus=\"blur()\""
	.">Appliquer les changements</a>"
	."</div>\n";

// ---------------------
// Rechercher tous les cours
// ---------------------
switch ($iIdStatut)
{
	case STATUT_PERS_CONCEPTEUR:
	case STATUT_PERS_TUTEUR:
		
		if ($iIdPers < 1)
			break;
		
		if ($iIdStatut == STATUT_PERS_CONCEPTEUR)
		{
			include_once(dir_database("module_concepteur.tbl.php"));
			$oModule = new CModule_Concepteur($oProjet->oBdd,0,$iIdPers);
		}
		else
		{
			include_once(dir_database("module_tuteur.tbl.php"));
			$oModule = new CModule_Tuteur($oProjet->oBdd,0,$iIdPers);
		}
		
		if (isset($_GET["ENVOYER"]))
		{
			// Appliquer les modifications
			$oModule->effacerModules($oFormation->retId());
			
			if (isset($_GET["IDCOURS"]))
				$oModule->ajouterModules($_GET["IDCOURS"]);
			
			if ($_GET["ENVOYER"] == "1")
				return;
		}
		
		$iNbrModules = $oModule->initModules(TRUE,$iIdForm);
		
		$aoModules = &$oModule->aoModules;
		
		for ($i=0; $i<$iNbrModules; $i++)
			$sListeCours .= "<tr>"
				."<td style=\"font-size: 10pt\">"
				."<input type=\"checkbox\" name=\"IDCOURS[]\" value=\"".$aoModules[$i]->retId()."\""
				.($aoModules[$i]->estSelectionne ? " checked" : NULL)
				.">"
				."&nbsp;"
				.$aoModules[$i]->retNom()
				."</td>"
				."</tr>\n";
		
		unset($aoModules,$oModule);
		
		break;
		
	case STATUT_PERS_ETUDIANT:
		
		if ($iIdPers < 1)
			break;
		
		$oModule = new CModule_Inscrit($oProjet->oBdd,0,$iIdPers);
		
		if (isset($_GET["ENVOYER"]))
		{
			$oModule->effacerModules($iIdForm);
			
			if (isset($_GET["IDCOURS"]) && count($_GET["IDCOURS"]) > 0)
				$oModule->ajouterModules($_GET["IDCOURS"]);
			
			if ($_GET["ENVOYER"] == "1")
				return;
		}
		
		$iNbrModules = $oModule->initModules(TRUE,$iIdForm);
		
		$aoModules = &$oModule->aoModules;
		
		$oEquipe_Membre = new CEquipe_Membre($oProjet->oBdd);
		
		for ($i=0; $i<$iNbrModules; $i++)
		{
			$sNomModule = $aoModules[$i]->retNom();
			
			if (!$aoModules[$i]->estSelectionne && $aoModules[$i]->estMembre)
			{
				// Effacer ce membre associé à une équipe de type module
				$oEquipe_Membre->effacerMembre($iIdPers,TYPE_MODULE,$aoModules[$i]->retId());
				$aoModules[$i]->estMembre = FALSE;
			}
			
			if ($bAutoInscription)
			{
				$sAppliquerChangements = NULL;
				
				$sListeCours .= "<tr>"
					."<td style=\"font-size: 10pt\">"
					."&nbsp;<img src=\"".dir_theme("cocher-plein-0.gif")."\" border=\"0\">"
					."&nbsp;"
					.($bAutoInscription ? "<span style=\"color: #CAC8BB;\">$sNomModule</span>" : $sNomModule)
					."<input type=\"hidden\" name=\"IDCOURS[]\" value=\"".$aoModules[$i]->retId()."\">"
					."</td>"
					."</tr>\n";
			}
			else
			{
				$sListeCours .= "<tr>"
					."<td style=\"font-size: 10pt\">"
					."<input type=\"checkbox\" name=\"IDCOURS[]\" value=\"".$aoModules[$i]->retId()."\""
					.($aoModules[$i]->estSelectionne ? " checked" : NULL)
					.($bAutoInscription ? " readonly" : NULL)
					.">"
					."&nbsp;"
					.($aoModules[$i]->estMembre ? "<span class=\"Attention\" style=\"cursor: help;\" title=\""._("Associ&eacute; &agrave; une &eacute;quipe")."\">$sNomModule</span>" : $sNomModule)
					."</td>"
					."</tr>\n";
			}
		}
		
		unset($aoModules,$oModule);
		
		break;
}

if ($iNbrModules < 1)
{
	$sAppliquerChangements = NULL;
	$sListeCours = "<tr><td>&nbsp;</td></tr>\n";
}

$oProjet->terminer();

?>
<html>
<head>
<?php inserer_feuille_style("associer_personnes.css"); ?>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript" src="globals.js"></script>
<script type="text/javascript" language="javascript">
<!--

function rechargerListeCours()
{
	// Recharger la liste des cours
	document.forms[0].elements["ENVOYER"].value = "2";
	majListeCours();
}

//-->
</script>
</head>
<body>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
<?php echo $sAppliquerChangements?>
<table border="0" cellpadding="3" cellspacing="1" width="100%">
<?php echo $sListeCours; ?>
</table>
<input type="hidden" name="IDPERS" value="<?php echo $iIdPers?>">
<input type="hidden" name="STATUT" value="<?php echo $iIdStatut?>">
<input type="hidden" name="ENVOYER" value="1">
<input type="hidden" name="idform" value="<?php echo $iIdForm?>">
</form>
</body>
</html>
