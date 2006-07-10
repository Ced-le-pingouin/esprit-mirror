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
** Fichier ................: exporter-liste.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 27/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_include("personnes.class.php"));

$oProjet = new CProjet();
$oProjet->verifPeutUtiliserOutils("PERM_OUTIL_EXPORT_TABLE_PERSONNE");

$oPersonnes = new CPersonnes($oProjet->oBdd);

$url_sTri      = (empty($_POST["TRI"]) ? "nom" : $_POST["TRI"]);
$url_sOrdreTri = (empty($_POST["ORDRE_TRI"]) ? $oPersonnes->ORDRE_TRI_CROISSANT : $_POST["ORDRE_TRI"]);

$oTpl = new Template("exporter-liste.tpl");

$oTpl->remplacer("{TRI->value}",$url_sTri);
$oTpl->remplacer("{ORDRE_TRI->value}",$url_sOrdreTri);

$oBloc_personne = new TPL_Block("BLOCK_PERSONNE",$oTpl);

// Récupérer les icônes de tri croissant/décroissant
$oSet_icone_asc  = $oTpl->defVariable("SET_IMAGE_TRI_ASC");
$oSet_icone_desc = $oTpl->defVariable("SET_IMAGE_TRI_DESC");

// Placer l'icône de tri
$asTrier = array("nom","prenom","pseudo");

foreach ($asTrier as $sTrier)
{
	$sOrdreTri = $oPersonnes->ORDRE_TRI_CROISSANT;
	$sImageTri = NULL;
	
	if ($url_sTri == $sTrier)
	{
		if ($url_sOrdreTri == $oPersonnes->ORDRE_TRI_CROISSANT)
		{
			$sOrdreTri = $oPersonnes->ORDRE_TRI_DESCROISSANT;
			$sImageTri = $oSet_icone_asc;
		}
		else
		{
			$sImageTri = $oSet_icone_desc;
		}
	}
	
	$oTpl->remplacer("{{$sTrier}->tri->ordre}",$sOrdreTri);
	$oTpl->remplacer("{{$sTrier}->image->tri}",$sImageTri);
}

if (isset($_POST["LISTE_IDPERS"]))
{
	// Définir le tri
	if ($url_sTri == "prenom")
		$oPersonnes->defTrierSur($oPersonnes->TRIER_PRENOM);
	elseif ($url_sTri == "pseudo")
		$oPersonnes->defTrierSur($oPersonnes->TRIER_PSEUDO);
	else
		$oPersonnes->defTrierSur($oPersonnes->TRIER_NOM);
	
	$oPersonnes->defOrdreTri($url_sOrdreTri);
	
	// Rechercher les personnes
	$aoPersonnes = $oPersonnes->retListePersonnesGraceIds(explode(",",$_POST["LISTE_IDPERS"]));
	
	$sCelluleCss = NULL;
	$iPositionPersonne = 1;
	
	$oBloc_personne->beginLoop();
	
	$iLettre = 0;
	
	foreach ($aoPersonnes as $oPersonne)
	{
		$sCelluleCss            = ($sCelluleCss == "cellule_clair" ? "cellule_fonce" : "cellule_clair");
		$sCelluleCssTrierNom    = ($url_sTri == "nom" ? "cellule_clair_fonce" : $sCelluleCss);
		$sCelluleCssTrierPrenom = ($url_sTri == "prenom" ? "cellule_clair_fonce" : $sCelluleCss);
		$sCelluleCssTrierPseudo = ($url_sTri == "pseudo" ? "cellule_clair_fonce" : $sCelluleCss);
		
		$lien = NULL;
		$sPremiereLettre = strtolower(substr($oPersonne->retNom(),0,1));
		
		if ($iLettre < ord($sPremiereLettre))
		{
			$iLettre = ord($sPremiereLettre);
			$lien = "<a name=\"{$sPremiereLettre}\"></a>";
		}
		
		$oBloc_personne->nextLoop();
		
		$oBloc_personne->remplacer("{id->lettre}",$sPremiereLettre.$iPositionPersonne);
		$oBloc_personne->remplacer("{personne->position}",$iPositionPersonne++);
		
		$oBloc_personne->remplacer("{td->personne->class}",$sCelluleCss);
		$oBloc_personne->remplacer("{personne->id}",$oPersonne->retId());
		
		$oBloc_personne->remplacer("{td->nom->class}",$sCelluleCssTrierNom);
		$oBloc_personne->remplacer("{personne->nom}",$oPersonne->retNom());
		
		$oBloc_personne->remplacer("{td->prenom->class}",$sCelluleCssTrierPrenom);
		$oBloc_personne->remplacer("{personne->prenom}",$oPersonne->retPrenom());
		
		$oBloc_personne->remplacer("{td->pseudo->class}",$sCelluleCssTrierPseudo);
		$oBloc_personne->remplacer("{personne->pseudo}",$oPersonne->retPseudo());
	}
	
	$oBloc_personne->afficher();
	
	$oTpl->remplacer("{LISTE_IDPERS->value}",$_POST["LISTE_IDPERS"]);
}
else
{
	$oBloc_personne->effacer();
	$oTpl->remplacer("{LISTE_IDPERS->value}",NULL);
}

$oTpl->afficher();

?>
