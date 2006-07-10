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
** Fichier ................: equipes-etudiants.php
** Description ............: 
** Date de création .......: 01/01/2003
** Dernière modification ..: 03/06/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Rechercher les variables de l'url
// ---------------------
if (isset($_POST["NIVEAU"]))
	$url_iNiveau = $_POST["NIVEAU"];
else if (isset($_GET["NIVEAU"]))
	$url_iNiveau = $_GET["NIVEAU"];
else
	$url_iNiveau = TYPE_FORMATION;

if (isset($_POST["ID_NIVEAU"]))
	$url_iIdNiveau = $_POST["ID_NIVEAU"];
else if (isset($_GET["ID_NIVEAU"]))
	$url_iIdNiveau = $_GET["ID_NIVEAU"];
else
	$url_iIdNiveau = $oProjet->oFormationCourante->retId();

if (isset($_POST["ID_EQUIPE"]))
	$url_iIdEquipe = $_POST["ID_EQUIPE"];
else if (isset($_GET["ID_EQUIPE"]))
	$url_iIdEquipe = $_GET["ID_EQUIPE"];
else
	$url_iIdEquipe = 0;

if (isset($_POST["FILTRE_PERSONNES"]))
	$url_iFiltrePersonnes = $_POST["FILTRE_PERSONNES"];
else if (isset($_GET["FILTRE_PERSONNES"]))
	$url_iFiltrePersonnes = $_GET["FILTRE_PERSONNES"];
else
	$url_iFiltrePersonnes = PERSONNE_SANS_EQUIPE;

//echo "NIVEAU={$url_iNiveau}<br>ID_NIVEAU={$url_iIdNiveau}<br>ID_EQUIPE={$url_iIdEquipe}<hr>";

// *************************************
//
// *************************************

$sReafficherMembres = NULL;

$bAutoInscrit = $oProjet->oFormationCourante->retInscrAutoModules();

// *************************************
// Ajouter une personne dans une équipe
// *************************************

if (isset($_POST["ID_PERS"]) && $url_iIdEquipe > 0)
{
	$oEquipeMembre = new CEquipe_Membre($oProjet->oBdd,$url_iIdEquipe);
	$oEquipeMembre->ajouterMembres($_POST["ID_PERS"]);
	$sReafficherMembres = "\n\ttop.oMembres().location = top.oMembres().location.pathname"
		." + \"?NIVEAU={$url_iNiveau}&ID_EQUIPE={$url_iIdEquipe}\";\n";
}

// *************************************
// Rechercher des personnes
// *************************************

$sCorpHtml = NULL;
$bTrouverPersonnes = TRUE;

if ($url_iFiltrePersonnes <> PERSONNE_INSCRITE)
{
	$bAppartenirEquipe = ($url_iFiltrePersonnes == PERSONNE_DANS_EQUIPE);
	
	switch ($url_iNiveau)
	{
		case TYPE_FORMATION:
			$oFormation = new CFormation($oProjet->oBdd,$url_iIdNiveau);
			$oFormation->initMembres($bAppartenirEquipe);
			$aoPersonnes = &$oFormation->aoMembres;
			break;
			
		case TYPE_MODULE:
			$oModule = new CModule($oProjet->oBdd,$url_iIdNiveau);
			$oModule->initMembres($bAppartenirEquipe,$bAutoInscrit);
			$aoPersonnes = &$oModule->aoMembres;
			break;
			
		case TYPE_RUBRIQUE:
			$oRubrique = new CModule_Rubrique($oProjet->oBdd,$url_iIdNiveau);
			$oRubrique->initMembres($bAppartenirEquipe,$bAutoInscrit);
			$aoPersonnes = &$oRubrique->aoMembres;
			break;
	}
}

if ($url_iFiltrePersonnes == PERSONNE_INSCRITE || $url_iIdEquipe == 0)
{
	// Dans le cas où il n'y a pas équipe à ce niveau
	// au lieu d'afficher "Pas d'étudiant" afficher la
	// liste des personnes inscritent
	if ($bAutoInscrit)
	{
		$oProjet->initInscritsFormation();
		$aoPersonnes = &$oProjet->aoInscrits;
	}
	else
	{
		include_once(dir_database("ids.class.php"));
		$oIds = new CIds($oProjet->oBdd,$url_iNiveau,$url_iIdNiveau);
		$oProjet->defModuleCourant($oIds->retIdMod());
		$oProjet->initInscritsModule();
		$aoPersonnes = &$oProjet->aoInscrits;
		unset($oIds);
	}
}

// ---------------------------
// ---------------------------
$sCocherVide = dir_theme("cocher-vide-0.gif");
$sRepIcone = dir_theme_commun("icones");

for ($iIdxMembre=0; $iIdxMembre<count($aoPersonnes); $iIdxMembre++)
	$sCorpHtml .= "<tr>"
		."<td nowrap=\"nowrap\">"
		."<a name=\"pos".($iIdxMembre+1)."\"></a>"
		.($url_iFiltrePersonnes == PERSONNE_SANS_EQUIPE && $url_iIdEquipe > 0
			? "<input type=\"checkbox\" name=\"ID_PERS[]\" value=\"".$aoPersonnes[$iIdxMembre]->retId()."\" onfocus=\"blur()\">"
			: "<img src=\"{$sCocherVide}\" border=\"0\">")
		."</td>"
		."<td><img src=\"{$sRepIcone}/".($aoPersonnes[$iIdxMembre]->retSexe() == "F" ? "girl.gif" : "boy.gif")."\" border=\"0\"></td>"
		."<td width=\"99%\" style=\"border: rgb(240,240,240) none 1px; border-bottom-style: dashed;\">"
		."&nbsp;&nbsp;"
		."<span id=\"nom_".($iIdxMembre+1)."\" style=\"display: none;\">".$aoPersonnes[$iIdxMembre]->retNom()."</span>"
		."<a"
		." href=\"javascript: profil('?idPers=".$aoPersonnes[$iIdxMembre]->retId()."'); void(0);\""
		." onclick=\"blur()\""
		.">".$aoPersonnes[$iIdxMembre]->retNomComplet(TRUE)."</a>"
		."<br>&nbsp;&nbsp;"
		."<small>".$aoPersonnes[$iIdxMembre]->retPseudo()."</small>"
		."</td>"
		."</tr>\n";

// ---------------------------
// Personne en vue
// ---------------------------
if ($iIdxMembre < 1)
	$sCorpHtml = "<tr><td align=\"center\">Pas d'&eacute;tudiant trouv&eacute;</td></tr>\n";

$oProjet->terminer();

?>
<html>
<head>
<META HTTP-EQUIV="Pragma" CONTENT="NO-CACHE">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style("equipes.css"); ?>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('globals.js.php')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('window.js')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('outils_admin.js')?>"></script>
<script type="text/javascript" language="javascript">
<!--

function init()
{<?php echo $sReafficherMembres; ?>
	return true;
}

function ceFormulaire()
{
	return document.forms[0];
}

function envoyer()
{
	ceFormulaire().submit();
}

function defNiveau(v_iNiveau)
{
	ceFormulaire().elements["NIVEAU"].value = v_iNiveau;
}

function setIdEquipe(v_iIdEquipe)
{
	ceFormulaire().elements["ID_EQUIPE"].value = v_iIdEquipe;
}

function defFiltre(v_iFiltre)
{
	ceFormulaire().elements["FILTRE_PERSONNES"].value = v_iFiltre;
}

function ajouter()
{
	envoyer();
}

//-->
</script>

</head>

<body class="personnes" onload="init()">
<a name="top"></a>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<?=$sCorpHtml?>
</table>
<input type="hidden" name="NIVEAU" value="<?=$url_iNiveau?>">
<input type="hidden" name="ID_NIVEAU" value="<?=$url_iIdNiveau?>">
<input type="hidden" name="ID_EQUIPE" value="<?=$url_iIdEquipe?>">
<input type="hidden" name="FILTRE_PERSONNES" value="<?=$url_iFiltrePersonnes?>">
</form>
</body>
</html>
