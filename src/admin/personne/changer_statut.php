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
** Fichier ................: changer_statut.php
** Description ............: Changer de statut de l'utilisateur
** Date de création .......: 27/02/2002
** Dernière modification ..: 20/05/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Gestion
// ---------------------
$sParamsRechargerFenParent = NULL;

if (!empty($_POST["idStatut"]))
{
	$aamNiveaux = array(
		array(&$oProjet->oFormationCourante,"idForm")
		, array(&$oProjet->oModuleCourant,"idMod")
		, array(&$oProjet->oRubriqueCourante,"idUnite")
		, array(&$oProjet->oActivCourante,"idActiv")
		, array(&$oProjet->oSousActivCourante,"idSousActiv"));
	
	foreach ($aamNiveaux as $amNiveau)
		$sParamsRechargerFenParent .= (isset($sParamsRechargerFenParent) ? "&" : "?")
			.$amNiveau[1]."=".(is_object($amNiveau[0]) ? $amNiveau[0]->retId() : 0);
	
	$oProjet->changerStatutUtilisateur($_POST["idStatut"],TRUE);
	
	// Nous devons mettre à jour la fenêtre parente
	// et fermer absolument cette fenêtre car elle sera orpheline.
	echo "<html>"
		."<head>"
		."<script type=\"text/javascript\" language=\"javascript\">"
		."<!--\n"
		."top.opener.recharger('{$sParamsRechargerFenParent}');"
		."top.close();"
		."\n//-->"
		."</script>"
		."</head>"
		."<body>"
		."</body>"
		."</html>\n";
		
	exit();
}

// ---------------------
// Initialiser
// ---------------------
$iIdPers = $oProjet->retIdUtilisateur();
$iReelStatutUtilisateur = $oProjet->retReelStatutUtilisateur();

$iIdForm = 0;
$iIdMod = 0;
$bInscritAutoModules = TRUE;

if (is_object($oProjet->oModuleCourant))
	if (is_object($oProjet->oFormationCourante))
	{
		$iIdForm = $oProjet->oFormationCourante->retId();
		$bInscritAutoModules = $oProjet->oFormationCourante->retInscrAutoModules();
		
		if (isset($oProjet->oModuleCourant))
			$iIdMod = $oProjet->oModuleCourant->retId();
	}

$oStatutUtilisateur = new CStatutUtilisateur($oProjet->oBdd,$iIdPers);
$oStatutUtilisateur->initStatuts($iIdForm,$iIdMod,$bInscritAutoModules);

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("changer_statut.tpl",FALSE,TRUE));

$oBlocListeStatuts = new TPL_Block("BLOCK_LISTE_STATUTS",$oTpl);
$oBlocListeStatuts->beginLoop();

for ($iIdxStatut=STATUT_PERS_PREMIER; $iIdxStatut<STATUT_PERS_DERNIER; $iIdxStatut++)
	if ($oStatutUtilisateur->aiStatuts[$iIdxStatut])
	{
		$bStatutActuel = ($iReelStatutUtilisateur == $iIdxStatut);
		$sStatutActuel = $oProjet->retTexteStatutUtilisateur($iIdxStatut);
		
		$oBlocListeStatuts->nextLoop();
		
		$oBlocListeStatuts->remplacer("{nom_radio_statut}","idStatut");
		$oBlocListeStatuts->remplacer("{valeur_statut}",$iIdxStatut);
		$oBlocListeStatuts->remplacer("{selectionner_statut}",($bStatutActuel ? " checked=\"checked\"": NULL));
		$oBlocListeStatuts->remplacer("{nom_statut}",$sStatutActuel.($bStatutActuel ? "&nbsp;<img src=\"theme://icones/etoile.gif\" width=\"13\" height=\"13\" border=\"0\" alt=\"\" />": NULL));
	}

$oBlocListeStatuts->afficher();

$oTpl->afficher();
$oProjet->terminer();
?>
