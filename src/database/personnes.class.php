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
** Fichier ................: personnes.class.php
** Description ............:
** Date de création .......: 19/01/2005
** Dernière modification ..: 27/01/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_database("equipe.tbl.php"));

class CPersonnes
{
	var $oProjet;
	
	var $aoPersonnes;
	
	function CPersonnes (&$v_oProjet)
	{
		$this->oProjet = &$v_oProjet;
		$this->vider();
	}
	
	/**
	 * Initialise un tableau contenant toutes les personnes inscrites à cette formation
	 * 
	 * @param	v_iIdFormation donne le numéro de la formation
	 *
	 * @param	v_iIdStatutForm sélectionne les types de personnes (Repsonsables, tuteurs et/ou étudiants)
	 *  
	 * @return	le nombre de personnes inscrites dans cette formation
	 */
	function initGraceIdFormation($v_iIdFormation, $v_iIdStatutForm)
	{
		if (($iIdForm = $this->retIdForm()) < 1)
			return 0;
		
		$aoPersonnes = array();
		
		switch ($v_iIdStatutForm)
		{
			case STATUT_PERS_RESPONSABLE:
				$this->oProjet->oFormationCourante->initResponsables();
				$aoPersonnes = &$this->oProjet->oFormationCourante->aoResponsables;
				break;
				
			case STATUT_PERS_TUTEUR:
				$this->oProjet->oFormationCourante->initTuteurs();
				$aoPersonnes = &$this->oProjet->oFormationCourante->aoTuteurs;
				break;
				
			case STATUT_PERS_ETUDIANT:
				$this->oProjet->oFormationCourante->initInscrits();
				$aoPersonnes = &$this->oProjet->oFormationCourante->aoInscrits;
				break;
		}
		
		foreach ($aoPersonnes as $oPersonne)
			$this->aoPersonnes[$oPersonne->retPseudo()] = $oPersonne;
		
		return count($this->aoPersonnes);
		
		
	}
	function initPersonnes ($v_sRequeteSql)
	{
		$iIdxPers = 0;
		
		if (isset($v_sRequeteSql))
		{
			$hResult = $this->oProjet->oBdd->executerRequete($v_sRequeteSql);
			
			while ($oEnreg = $this->oProjet->oBdd->retEnregSuiv($hResult))
			{
				$this->aoPersonnes[$oEnreg->Pseudo] = new CPersonne($this->oProjet->oBdd);
				$this->aoPersonnes[$oEnreg->Pseudo]->init($oEnreg);
				$iIdxPers++;
			}
			
			$this->oProjet->oBdd->libererResult($hResult);
		}
		
		return $iIdxPers;
	}
	
	function initGraceIdStatut ($v_iIdStatut,$v_bVerifStricte=TRUE)
	{
		if (($iIdForm = $this->retIdForm()) < 1)
			return 0;
		
		$bInscrAutoModules = (!$v_bVerifStricte || $this->oProjet->oFormationCourante->retInscrAutoModules());
		
		$iIdMod = $this->retIdMod();
		
		$aoPersonnes = array();
		
		switch ($v_iIdStatut)
		{
			case STATUT_PERS_RESPONSABLE:
				$this->oProjet->oFormationCourante->initResponsables();
				$aoPersonnes = &$this->oProjet->oFormationCourante->aoResponsables;
				break;
				
			case STATUT_PERS_TUTEUR:
				if ($iIdMod > 0)
				{
					$this->oProjet->oModuleCourant->initTuteurs();
					$aoPersonnes = &$this->oProjet->oModuleCourant->aoTuteurs;
				}
				break;
				
			case STATUT_PERS_ETUDIANT:
				if ($bInscrAutoModules)
				{
					$this->oProjet->oFormationCourante->initInscrits();
					$aoPersonnes = &$this->oProjet->oFormationCourante->aoInscrits;
				}
				else if ($iIdMod > 0)
				{
					$this->oProjet->oModuleCourant->initInscrits();
					$aoPersonnes = &$this->oProjet->oModuleCourant->aoInscrits;
				}
				break;
		}
		
		foreach ($aoPersonnes as $oPersonne)
			$this->aoPersonnes[$oPersonne->retPseudo()] = $oPersonne;
		
		return count($this->aoPersonnes);
	}
	
	function initGraceIdStatuts ($v_aiIdStatuts)
	{
		foreach ($v_aiIdStatuts as $iIdStatut)
			$this->initGraceIdStatut($iIdStatut);
		
		return count($this->aoPersonnes);
	}
	
	function initGraceIdEquipe ($v_iIdEquipe)
	{
		$oEquipe = new CEquipe($this->oProjet->oBdd,$v_iIdEquipe,TRUE);
		
		foreach ($oEquipe->aoMembres as $oPersonne)
			$this->aoPersonnes[$oPersonne->retPseudo()] = $oPersonne;
		
		return count($this->aoPersonnes);
	}
	
	function initGraceIdEquipes ($v_aiIdEquipes)
	{
		foreach ($v_aiIdEquipes as $iIdEquipe)
			$this->initGraceIdEquipes($iIdEquipe);
		
		return count($this->aoPersonnes);
	}
	
	function initGraceIdPers ($v_miIdPers)
	{
		if (settype($v_miIdPers,"array"))
		{
			$sValeursRequete = NULL;
			
			foreach ($v_miIdPers as $iIdPers)
			{
				if ($iIdPers > 0)
				{
					$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
						."'{$iIdPers}'";
				}
				else if ("tous" == $iIdPers)
				{
					$sValeursRequete = $iIdPers;
					break;
				}
			}
			
			if (isset($sValeursRequete))
			{
				$sRequeteSql = "SELECT * FROM Personne"
					.("tous" == $sValeursRequete ? NULL : " WHERE IdPers IN ({$sValeursRequete})")
					." ORDER BY Nom ASC, Prenom ASC";
				$this->initPersonnes($sRequeteSql);
			}
		}
		
		return count($this->aoPersonnes);
	}
	
	function retIdForm () { return (isset($this->oProjet->oFormationCourante) && is_object($this->oProjet->oFormationCourante) ? $this->oProjet->oFormationCourante->retId() : 0); }
	function retIdMod () { return (isset($this->oProjet->oModuleCourant) && is_object($this->oProjet->oModuleCourant) ? $this->oProjet->oModuleCourant->retId() : 0); }
	function vider () { $this->aoPersonnes = array(); }
}

?>

