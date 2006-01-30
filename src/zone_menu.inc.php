<?php

/*
** Fichier ................: zone_menu.inc.php
** Description ............:
** Date de cr�ation .......:
** Derni�re modification ..: 30/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
** 
** Unit� de Technologie de l'Education
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
// V�rifier que la formation actuelle est encore dans la liste des formations
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
// Rechercher la premi�re formation ayant une description ou sinon se placer
// sur le premier module disponible
// --------------------------
if ($bRechercher)
{
	// R�initialiser les variables
	$iIdForm = 0;
	$iIdMod  = 0;
	
	foreach ($oProjet->aoFormations as $oFormation)
	{
		// SI la formation ne poss�de pas de description OU de modules ALORS
		//     PASSER � la formation suivante
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
