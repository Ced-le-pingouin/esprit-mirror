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
** Fichier ................: menu_menu.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 30/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
** 
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_admin("awareness","awareness.inc.php",TRUE));

$oProjet = new CProjet();

// --------------------------
// Initialiser
// --------------------------
$g_iIdPers = $oProjet->retIdUtilisateur();
$g_iReelStatutUtilisateur = $oProjet->retReelStatutUtilisateur();

// Tableau contenant les formations ainsi que leur cours
$iNbrFormations = $oProjet->initFormationsUtilisateur(FALSE,FALSE,TRUE);

$g_iIdFormCourante = 0;
$g_iIdModCourant   = 0;

if (isset($oProjet->oFormationCourante) && is_object($oProjet->oFormationCourante))
{
	$g_iIdFormCourante = $oProjet->oFormationCourante->retId();
	
	if (isset($oProjet->oModuleCourant) && is_object($oProjet->oModuleCourant))
		$g_iIdModCourant = $oProjet->oModuleCourant->retId();
}

$g_iIdxForm = 0;
$g_bPremiereForm = FALSE;

// {{{ Permissions
if ($g_iIdPers > 0)
{
	$oStatutUtilisateur = new CStatutUtilisateur($oProjet->oBdd,$g_iIdPers);
	$oPermisUtilisateur = new CStatutPermission($oProjet->oBdd);
}
else
{
	// Permissions, par défaut, des visiteurs
	$bPeutVoirSessionFermee = $oProjet->verifPermission("PERM_VOIR_SESSION_FERMEE");
	$bPeutVoirSessionInv    = $oProjet->verifPermission("PERM_VOIR_SESSION_INV");
	
	$bPeutModifTousMod = $oProjet->verifPermission("PERM_MOD_TOUS_COURS");
	$bPeutVoirModFerme = $oProjet->verifPermission("PERM_VOIR_COURS_FERME");
	$bPeutVoirModInv   = $oProjet->verifPermission("PERM_VOIR_COURS_INV");
}
// }}}

// --------------------------
// Template
// --------------------------
$oTpl = new Template(dir_theme("zone_menu-menu.tpl",FALSE,TRUE));

$sTableauTitresFormation = ($iNbrFormations > 0 ? NULL : "\"&nbsp;\"");

$oBlocFormation = new TPL_Block("BLOCK_FORMATION",$oTpl);

$oSet_Description_Formation      = $oTpl->defVariable("SET_DESCRIPTION_FORMATION");
$oSet_Sans_Description_Formation = $oTpl->defVariable("SET_SANS_DESCRIPTION_FORMATION");
$oSet_Cours                      = $oTpl->defVariable("SET_COURS");
$oSet_Cours_Ouvert               = $oTpl->defVariable("SET_COURS_OUVERT");
$oSet_Cours_Fermer               = $oTpl->defVariable("SET_COURS_FERMER");
$oSet_Sans_Cours                 = $oTpl->defVariable("SET_SANS_COURS");
$oSet_Separateur_Intitule        = $oTpl->defVariable("SET_SEPARATEUR_INTITULE");

$oBlocFormation->beginLoop();

foreach ($oProjet->aoFormations as $oFormation)
{
	$iIdForm       = $oFormation->retId();
	$sNomFormation = $oFormation->retNom();
	
	// {{{ Permissions par rapport à la formation et au statut que l'utilisateur a choisi
	if ($g_iIdPers > 0)
	{
		$oStatutUtilisateur->initStatuts($iIdForm);
		$iHautStatutUtilisateurForm = $oStatutUtilisateur->retSuperieurStatut($g_iReelStatutUtilisateur);
		$oPermisUtilisateur->initPermissions($iHautStatutUtilisateurForm);
		
		$bPeutVoirSessionFermee = $oPermisUtilisateur->verifPermission("PERM_VOIR_SESSION_FERMEE");
		$bPeutVoirSessionInv    = $oPermisUtilisateur->verifPermission("PERM_VOIR_SESSION_INV");
		
		// Cette variable est utilisée par la fonction initModules
		$bPeutModifTousMod = $oPermisUtilisateur->verifPermission("PERM_MOD_TOUS_COURS");
	}
	// }}}
	
	if (strlen($sNomFormation) < 1)
		$sNomFormation = $oFormation->retNomParDefaut();
	
	if (STATUT_EFFACE == ($iStatutFormation = $oFormation->retStatut()))
		continue;
	else if (STATUT_INVISIBLE == $iStatutFormation)
	{
		if ($bPeutVoirSessionInv)
			$iStatutFormation = STATUT_OUVERT;
		else
			continue;
	}
	else if (STATUT_FERME == $iStatutFormation && $bPeutVoirSessionFermee)
		$iStatutFormation = STATUT_OUVERT;
	
	$oBlocFormation->nextLoop();
	
	// Remplacement général
	$oBlocFormation->remplacer("{nom_formation}",$sNomFormation);
	
	$sTableauTitresFormation .= (isset($sTableauTitresFormation) ? "\n\t, " : NULL)
		."\"".rawurlencode(str_replace(" ","&nbsp;",$sNomFormation))."\"";
	
	$oBlock_Description_Formation = new TPL_Block("BLOCK_DESCRIPTION_FORMATION",$oBlocFormation);
	
	// Description de la formation
	$iLongueurDescr = strlen($oFormation->retDescr());
	
	if (($g_iIdFormCourante == $iIdForm /*|| $g_iIdFormCourante == $f*/))
	{
		$g_bPremiereForm = TRUE;
		$oTpl->remplacer("{numero_titre_formation}","'{$g_iIdxForm}'");
	}
	
	// Cours
	$oBlocModule = new TPL_Block("BLOCK_COURS",$oBlocFormation);
	
	if ($iLongueurDescr > 0)
	{
		$sUrlDescr = "description.php"
			."?idForm={$iIdForm}&idMod=0&idUnite=0&idSousActiv=0&idActiv=0"
			."&idNiveau={$iIdForm}"
			."&typeNiveau=".TYPE_FORMATION;
		
		// Ajouter la description de la formation
		$oBlocModule->ajouter($oSet_Description_Formation);
		
		$oBlocModule->remplacer("{index_formation}",$g_iIdxForm);
		$oBlocModule->remplacer("{id_formation}",$iIdForm);
		$oBlocModule->remplacer("{href_description}",dir_sousactiv(LIEN_PAGE_HTML,$sUrlDescr));
		
		unset($sUrlDescr);
	}
	else
		$oBlocModule->ajouter($oSet_Sans_Description_Formation);
	
	// Pour chaque formation retourner le statut le plus haut
	$iNbrModules = $oFormation->initModules($g_iIdPers,$iHautStatutUtilisateurForm,$bPeutModifTousMod);
	
	if ($iNbrModules == 0)
	{
		$oBlocModule->ajouter($oSet_Cours);
		$oBlocModule->remplacer("{cours}",$oSet_Sans_Cours);
	}
	else
	{
		// Afficher les modules
		foreach ($oFormation->aoModules as $oModule)
		{
			$iIdMod = $oModule->retId();
			
			// {{{ Permissions par rapport au module et au statut que l'utilisateur a choisi
			if ($g_iIdPers > 0)
			{
				$oStatutUtilisateur->initStatuts($iIdForm,$iIdMod,$oFormation->retInscrAutoModules());
				$oPermisUtilisateur->initPermissions($oStatutUtilisateur->retSuperieurStatut($g_iReelStatutUtilisateur));
				
				$bPeutVoirModFerme = $oPermisUtilisateur->verifPermission("PERM_VOIR_COURS_FERME");
				$bPeutVoirModInv   = $oPermisUtilisateur->verifPermission("PERM_VOIR_COURS_INV");
			}
			// }}}
			
			if (STATUT_INVISIBLE == ($iStatutModule = $oModule->retStatut()))
			{
				if ($bPeutVoirModInv)
					$iStatutModule = STATUT_OUVERT;
				else
					continue;
			}
			else if (STATUT_FERME == $iStatutModule && $bPeutVoirModFerme)
				$iStatutModule = STATUT_OUVERT;
			
			$sNomMod      = $oModule->retNom();
			$sIntituleMod = $oModule->retTexteIntitule();
			
			$bAffSeparateurIntitule = (strlen($sIntituleMod) > 0);
			
			$sIntitulePlusNomMod = ($bAffSeparateurIntitule
					? $sIntituleMod.$oSet_Separateur_Intitule
					: NULL)
				.$sNomMod;
			
			$oBlocModule->ajouter($oSet_Cours);
			
			if (STATUT_OUVERT == $iStatutFormation &&
				STATUT_OUVERT == $iStatutModule)
			{
				// Module ouvert
				$oBlocModule->remplacer("{cours}",$oSet_Cours_Ouvert);
				$oBlocModule->remplacer("{id_cours}",$iIdMod);
				$oBlocModule->remplacer("{href_cours}","zone_menu.php?idForm={$iIdForm}&idMod={$iIdMod}");
				$oBlocModule->remplacer("{index_formation}",$g_iIdxForm);
				$oBlocModule->remplacer("{nom_cours_encoder}",rawurlencode($sIntitulePlusNomMod));
			}
			else
			{
				// Module fermé
				$oBlocModule->remplacer("{cours}",$oSet_Cours_Fermer);
				$oBlocModule->remplacer("{id_cours}",0);
			}
			
			if ($g_iIdModCourant == $iIdMod)
				$sNomModCourant = rawurlencode($sIntitulePlusNomMod);
			
			$oBlocModuleIntitule = new TPL_Block("BLOCK_COURS_INTITULE",$oBlocModule);
			
			if ($sIntituleMod === NULL)
				$oBlocModuleIntitule->effacer();
			else
				$oBlocModuleIntitule->remplacer("{intitule_cours}",$sIntituleMod);
			
			$oBlocModuleIntitule->afficher();
			
			$oBlocModule->remplacer("{separateur_intitule}",($bAffSeparateurIntitule ? $oSet_Separateur_Intitule : NULL));
			$oBlocModule->remplacer("{nom_cours}",htmlentities($sNomMod));
		}
	}
	
	$oBlocModule->afficher();
	
	$g_iIdxForm++;
}

if (!$g_bPremiereForm)
	$oTpl->remplacer("{numero_titre_formation}","'0'");

$oBlocFormation->afficher();

$oBlock_OnLoad = new TPL_Block("BLOCK_ONLOAD",$oTpl);

if ($g_iIdFormCourante > 0 && $g_iIdModCourant == 0)
{
	$oTmp = new TPL_Block("SET_MODULE_ONLOAD",$oTpl);
	$oTmp->effacer();
	
	// Au démarrage, Afficher la description de la formation
	if (strlen($oProjet->oFormationCourante->retDescr()) > 0)
	{
		$oBlock_OnLoad->ajouter($oTpl->defVariable("SET_FORMATION_ONLOAD"));
		$oBlock_OnLoad->remplacer("{id_onload}",$g_iIdFormCourante);
		$oBlock_OnLoad->afficher();
	}
	else
	{
		$oTmp = new TPL_Block("SET_FORMATION_ONLOAD",$oTpl);
		$oTmp->effacer();
		
		$oBlock_OnLoad->effacer();
	}
}
else if ($g_iIdModCourant > 0)
{
	$oTmp = new TPL_Block("SET_FORMATION_ONLOAD",$oTpl);
	$oTmp->effacer();
	
	// Au démarrage, si la formation ne contient pas de description
	// alors, afficher le contenu du premier cours.
	$oBlock_OnLoad->ajouter($oTpl->defVariable("SET_MODULE_ONLOAD"));
	
	$oBlock_OnLoad->remplacer("{id_onload}",$g_iIdModCourant);
	$oBlock_OnLoad->remplacer("{nom_cours_onload_encoder}",(isset($sNomModCourant) ? $sNomModCourant : '&nbsp;'));
	
	$oBlock_OnLoad->afficher();
}
else
{
	$oBlock_OnLoad->effacer();
	
	$oTmp = new TPL_Block("SET_FORMATION_ONLOAD",$oTpl);
	$oTmp->effacer();
	
	$oTmp = new TPL_Block("SET_MODULE_ONLOAD",$oTpl);
	$oTmp->effacer();
}

$oTpl->remplacer("{tableau_titres_formation}","new Array({$sTableauTitresFormation})");

$oTpl->afficher();

$oProjet->terminer();

?>

