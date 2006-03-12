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
** Fichier ................: zone_menu.inc.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 30/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
** 
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

// --------------------------
// Initialisations
// --------------------------
$iIdForm = (is_object($oProjet->oFormationCourante) ? $oProjet->oFormationCourante->retId() : 0);
$iIdMod = (is_object($oProjet->oModuleCourant) ? $oProjet->oModuleCourant->retId() : 0);

$iIdPers                = $oProjet->retIdUtilisateur();
$iReelStatutUtilisateur = $oProjet->retReelStatutUtilisateur(); // Statut que l'utilisateur a choisi

if ($iIdPers < 1 && $iIdForm < 1)
	return;

// {{{ Permissions
$bPeutVoirToutesSessions = $oProjet->verifPermission("PERM_MOD_TOUTES_SESSIONS");
$bPeutVoirSessionFermee  = ($bPeutVoirToutesSessions | $oProjet->verifPermission("PERM_VOIR_SESSION_FERMEE"));
$bPeutVoirSessionInv     = ($bPeutVoirToutesSessions | $oProjet->verifPermission("PERM_VOIR_SESSION_INV"));

$bPeutVoirTousModules = $oProjet->verifPermission("PERM_MOD_TOUS_COURS");
$bPeutVoirModuleFerme = ($bPeutVoirTousModules | $oProjet->verifPermission("PERM_VOIR_COURS_FERME"));
$bPeutVoirModuleInv   = ($bPeutVoirTousModules | $oProjet->verifPermission("PERM_VOIR_COURS_INV"));
// }}}

// --------------------------
// Vérifier que la formation actuelle est encore dans la liste des formations
// --------------------------
$bRechercher = TRUE;

$oProjet->initFormationsUtilisateur(FALSE,FALSE,TRUE);

$iIdFormRech = 0;

foreach ($oProjet->aoFormations as $oFormation)
	if ($oFormation->retId() == $iIdForm) { $iIdFormRech = $iIdForm; break; }

if ($iIdFormRech > 0)
{
	$iStatut = $oProjet->oFormationCourante->retStatut();
	
	if (STATUT_OUVERT == $iStatut
		|| (STATUT_FERME == $iStatut && $bPeutVoirSessionFermee)
		|| (STATUT_INVISIBLE == $iStatut && $bPeutVoirSessionInv))
	{
		if ($iIdMod == 0)
			$bRechercher = FALSE;
		else if ($oProjet->oFormationCourante->initModules($iIdPers,$iReelStatutUtilisateur) > 0)
		{
			foreach ($oProjet->oFormationCourante->aoModules as $oModule)
			{
				if ($oModule->retId() != $iIdMod)
					continue;
				
				$iStatut = $oModule->retStatut();
				
				if (STATUT_FERME == $iStatut && !$bPeutVoirModuleFerme)
					continue;
				else if (STATUT_INVISIBLE == $iStatut && !$bPeutVoirModuleInv)
					continue;
				
				$bRechercher = FALSE;
				
				break;
			}
		}
	}
}

// --------------------------
// Rechercher la première formation ayant une description ou sinon se placer
// sur le premier module disponible
// --------------------------
if ($bRechercher)
{
	// Réinitialiser les variables
	$iIdForm = 0;
	$iIdMod  = 0;
	
	foreach ($oProjet->aoFormations as $oFormation)
	{
		// SI la formation ne possède pas de description OU de modules ALORS
		//     PASSER à la formation suivante
		// FINSI
		$iLongueurDescr = strlen($oFormation->retDescr());
		
		if ($iLongueurDescr == 0 &&
			$oFormation->initModules($iIdPers,$iReelStatutUtilisateur) == 0)
			continue;
		
		$iStatut = $oFormation->retStatut();
		
		if (STATUT_FERME == $iStatut && !$bPeutVoirSessionFermee && $iLongueurDescr == 0)
			continue;
		else if (STATUT_INVISIBLE == $iStatut && !$bPeutVoirSessionInv)
			continue;
		
		$iIdForm = $oFormation->retId();
		
		// Si la formation ne contient pas de description, il faudra se
		// placer sur le premier module disponible
		if ($iLongueurDescr == 0 &&
			isset($oFormation->aoModules) &&
			is_array($oFormation->aoModules))
		{
			foreach ($oFormation->aoModules as $oModule)
			{
				$iStatut = $oModule->retStatut();
				
				if (STATUT_FERME == $iStatut && !$bPeutVoirModuleFerme)
					continue;
				else if (STATUT_INVISIBLE == $iStatut && !$bPeutVoirModuleInv)
					continue;
				
				$iIdMod = $oModule->retId();
				
				break; // Quitter la boucle des modules
			}
			
			if ($iIdMod == 0)
				continue;
		}
		
		break; // Quitter la boucle des formations
	}
	
	// {{{ Sauvegarder les informations dans le cookie
	$oProjet->modifierInfosSession(SESSION_FORM,$iIdForm);
	$oProjet->modifierInfosSession(SESSION_MOD,$iIdMod);
	$oProjet->modifierInfosSession(SESSION_UNITE,0);
	$oProjet->modifierInfosSession(SESSION_ACTIV,0);
	$oProjet->modifierInfosSession(SESSION_SOUSACTIV,0,TRUE);
	// }}}
}

?>
