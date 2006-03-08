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
** Fichier ................: ressource_sous_activ.tbl.php
** Description ............: 
** Date de création .......: 01/06/2001
** Dernière modification ..: 15/07/2005
** Auteurs ................: Cédric FLOQUET <cedric.floquet@umh.ac.be>
**                           Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_database("ressource.tbl.php"));
require_once(dir_database("evaluation.tbl.php"));

class CRessourceSousActiv
	extends CRessource
{
	var $iIdResSousActiv;
	
	var $aiIdsParent;
	
	var $aoVotants;
	var $aoEvaluations;
	var $oEquipe;
	var $oIdsParents;
	
	var $aoTuteurs;
	
	var $oRessourceAttache;
	
	function CRessourceSousActiv (&$v_oBdd,$v_iIdResSousActiv=0)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdResSousActiv;
		$this->oEnregBdd = NULL;
		
		if ($v_iIdResSousActiv > 0)
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdResSousActiv;
		}
		else
		{
			$sRequeteSql = "SELECT *"
				." FROM Ressource_SousActiv"
				." LEFT JOIN Ressource USING (IdRes)"
				." WHERE Ressource_SousActiv.IdResSousActiv='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		$oIds = new CIds($this->oBdd,TYPE_SOUS_ACTIVITE,$this->retIdSousActiv());
		$this->aiIdsParent = $oIds->retTableIds();
		
		return $this->retId();
	}
	
	/**
	 * Une ressource attachée est un document que le tuteur aura déposé sur
	 * le serveur et attaché avec l'évaluation de l'étudiant.
	 *
	 */
	function initRessourceAttache ()
	{
		
		$this->oRessourceAttache = NULL;
		
		if (($iIdResSA = $this->retId()) > 0)
		{
			$sRequeteSql = "SELECT Ressource.*"
				." FROM Ressource_SousActiv_FichierEvaluation"
				." LEFT JOIN Ressource USING (IdRes)"
				." WHERE IdResSousActiv='{$iIdResSA}'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->oRessourceAttache = new CRessource($this->oBdd);
				$this->oRessourceAttache->init($oEnreg);
			}
			
			$this->oBdd->libererResult($hResult);
		}
		
		return is_object($this->oRessourceAttache);
	}
	
	function ajouterRessourceAttache ($v_iIdRes)
	{
		$sRequeteSql = "REPLACE INTO Ressource_SousActiv_FichierEvaluation SET"
			." IdResSousActiv='".$this->retId()."'"
			.", IdRes='{$v_iIdRes}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerRessourceAttache ()
	{
		$this->initRessourceAttache();
		
		if (is_object($this->oRessourceAttache))
		{
			$sRequeteSql = "DELETE FROM Ressource_SousActiv_FichierEvaluation"
				." WHERE IdResSousActiv='".$this->retId()."'"
				." AND IdRes='".$this->oRessourceAttache->retId()."'";
			$this->oBdd->executerRequete($sRequeteSql);
			
			$this->oRessourceAttache->effacer($this->retRepertoire(NULL,TRUE));
		}
		
		$this->oRessourceAttache = NULL;
	}
	
	function verifDeposerDocuments ($v_iTypeTransfert=0,$v_aoEquipes=NULL,$v_iIdPers=NULL)
	{
		if (TYPE_TRANSFERT_IE == $v_iTypeTransfert ||
			TYPE_TRANSFERT_EE == $v_iTypeTransfert)
		{
			if (is_array($v_aoEquipes))
			{
				foreach ($v_aoEquipes as $oEquipe)
					if ($oEquipe->verifMembre($v_iIdPers))
						return TRUE;
			}
			
			return FALSE;
		}
		
		return TRUE;
	}
	
	function ajouter ($v_iIdSousActiv,$v_iStatutResSousActiv=NULL,$v_iIdResSource=0)
	{
		if (!isset($v_iStatutResSousActiv))
			$v_iStatutResSousActiv = STATUT_RES_ORIGINAL;
		
		if ($v_iStatutResSousActiv == STATUT_RES_TRANSFERE)
		{
			if ($v_iIdResSource == 0)
				$iIdResSource = $this->retIdRes();
			else
				$iIdResSource = $v_iIdResSource;
		}
		
		if ($iIdResSource < 1)
			$iIdResSource = 0;
		
		parent::ajouter();
		
		parent::init();
		
		$sRequeteSql = "INSERT INTO Ressource_SousActiv SET"
			." IdResSousActiv=NULL"
			.", IdSousActiv='{$v_iIdSousActiv}'"
			.", IdRes='".parent::retId()."'"
			.", StatutResSousActiv='{$v_iStatutResSousActiv}'"
			.", IdResSousActivSource='{$iIdResSource}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$this->iIdResSousActiv = $this->oBdd->retDernierId();
	}
	
	function verrouillerTables ($v_bExecuterRequete=TRUE)
	{
		$sListeTables = "Ressource WRITE"
			.", Ressource_SousActiv WRITE"
			.", Ressource_SousActiv_Evaluation WRITE"
			.", Ressource_SousActiv_Vote WRITE"
			.", SousActiv_Ressource_SousActiv WRITE";
		
		if ($v_bExecuterRequete)
			$this->oBdd->executerRequete("LOCK TABLES ".$sListeTables);
		
		return $sListeTables;
	}
	
	function effacer ()
	{
		if (($iIdResSousActiv = $this->retId()) > 0)
		{
			// Effacer le document attaché à l'évaluation
			$this->effacerRessourceAttache();
			
			$sRequeteSql = "LOCK TABLES"
				." SousActiv_Ressource_SousActiv WRITE"
				.", Ressource_SousActiv_Vote WRITE"
				.", Ressource_SousActiv_Evaluation WRITE"
				.", Ressource_SousActiv WRITE"
				.", Ressource WRITE";
			$this->oBdd->executerRequete($sRequeteSql);
			
			$this->effacerGalerie();
			
			$this->effacerVotes();
			
			$this->effacerEvaluations();
			
			$this->effacerRessource();
			
			$this->effacerRessourceSousActiv();
			
			$this->oBdd->executerRequete("UNLOCK TABLES");
		}
	}
	
	function effacerRessourceSousActiv ()
	{
		$sRequeteSql = "DELETE FROM Ressource_SousActiv"
			." WHERE IdResSousActiv='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerGalerie ()
	{
		$sRequeteSql = "DELETE FROM SousActiv_Ressource_SousActiv"
			." WHERE IdResSousActiv='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerVotes ()
	{
		$sRequeteSql = "DELETE FROM Ressource_SousActiv_Vote"
			." WHERE IdResSousActiv='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerEvaluations ()
	{
		$sRequeteSql = "DELETE FROM Ressource_SousActiv_Evaluation"
			." WHERE IdResSousActiv='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerRessource ()
	{
		@unlink($this->retRepertoire($this->retUrl(),TRUE));
		
		$sRequeteSql = "DELETE FROM Ressource"
			." WHERE IdRes='".$this->retIdParent()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function initTuteurs ()
	{
		$iIndexTuteur = 0;
		$this->aoTuteurs = array();
		
		$sRequeteSql = "SELECT Personne.*"
			." FROM Ressource_SousActiv_Evaluation"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Ressource_SousActiv_Evaluation.IdResSousActiv='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoTuteurs[$iIndexTuteur] = new CPersonne($this->oBdd);
			$this->aoTuteurs[$iIndexTuteur]->init($oEnreg);
			$iIndexTuteur++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIndexTuteur;
	}
	
	function initIdsParents ()
	{
		$oIds = new CIds($this->oBdd,TYPE_SOUS_ACTIVITE,$this->retIdSousActiv());
		$this->oIdsParents = $oIds->retIds();
		return is_object($this->oIdsParents);
	}
	
	function initSelectionner ($v_iIdParent)
	{
		$sRequeteSql = "SELECT * FROM SousActiv_Ressource_SousActiv"
			." WHERE IdSousActiv='$v_iIdParent'"
			." AND IdResSousActiv='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($this->oBdd->retNbEnregsDsResult($hResult))
			$this->bEstSelectionne = TRUE;
		else
			$this->bEstSelectionne = FALSE;
	}
	
	function retNbVotants ()
	{
		$iNbVotes = 0;
		
		if (($iIdResSousActiv = $this->retId()) > 0)
		{
			$sRequeteSql = "SELECT COUNT(*)"
				." FROM Ressource_SousActiv_Vote"
				." WHERE IdResSousActiv='{$iIdResSousActiv}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$iNbVotes = $this->oBdd->retEnregPrecis($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		return $iNbVotes;
	}
	
	function initVotants ()
	{
		$iIdxVote = 0;
		
		$this->aoVotants = array();
		
		$sRequeteSql = "SELECT Personne.*"
			." FROM Ressource_SousActiv_Vote"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Ressource_SousActiv_Vote.IdResSousActiv='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoVotants[$iIdxVote] = new CPersonne($this->oBdd);
			$this->aoVotants[$iIdxVote]->init($oEnreg);
			$iIdxVote++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxVote;
	}
	
	function retIdSousActiv () { return $this->oEnregBdd->IdSousActiv; }
	
	function initEvaluations ()
	{
		$iIndexEval = 0;
		
		$sRequeteSql = "SELECT *"
			." FROM Ressource_SousActiv_Evaluation"
			." WHERE IdResSousActiv='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoEvaluations[$iIndexEval] = new CEvaluation($this->oBdd);
			$this->aoEvaluations[$iIndexEval]->init($oEnreg);
			$iIndexEval++;
		}
		
		$this->oBdd->libererResult($hResult);
	}
	
	function retTexteStatut ($v_iStatut=NULL)
	{
		if (empty($v_iStatut))
			$v_iStatut = $this->retStatut();
		
		switch ($v_iStatut)
		{
			case STATUT_RES_ORIGINAL: $r_sTexteStatut = "original"; break;
			case STATUT_RES_EN_COURS: $r_sTexteStatut = ($this->retNbVotants() > 0 ? "vote en cours" : "en cours"); break;
			case STATUT_RES_SOUMISE: $r_sTexteStatut = "soumis"; break;
			case STATUT_RES_APPROF: $r_sTexteStatut = "à approfondir"; break;
			case STATUT_RES_ACCEPTEE: $r_sTexteStatut = "accepté"; break;
			case STATUT_RES_TRANSFERE: $r_sTexteStatut = "transféré"; break;
			default: $r_sTexteStatut = "-";
		}
		
		return $r_sTexteStatut;
	}
	
	function retTransfere ()
	{
		$sRequeteSql = "SELECT IdResSousActiv"
			." FROM Ressource_SousActiv"
			." WHERE IdResSousActivSource='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$bTransfere = ($this->oBdd->retEnregSuiv($hResult) ? TRUE : FALSE);
		$this->oBdd->libererResult($hResult);
		return $bTransfere;
	}
	
	function retPeutVoter () { return (STATUT_RES_EN_COURS == $this->retStatut()); }
	
	function retEstSoumise ()
	{
		return ($this->retStatut() == STATUT_RES_SOUMISE ||
				$this->retStatut() == STATUT_RES_APPROF ||
				$this->retStatut() == STATUT_RES_ACCEPTEE);
	}
	
	function retEstEvaluee ()
	{
		return ($this->retStatut() == STATUT_RES_APPROF ||
				$this->retStatut() == STATUT_RES_ACCEPTEE);
	}
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0);  }
	function retIdParent () { return $this->oEnregBdd->IdRes; }
	function retStatut () { return $this->oEnregBdd->StatutResSousActiv; }
	function retIdResSousActivSource () { return $this->oEnregBdd->IdResSousActivSource; }
	
	function initExpediteur ()
	{
		$this->oExpediteur = new CPersonne($this->oBdd,$this->retIdExped());
		
		if (STATUT_RES_TRANSFERE == $this->retStatut())
		{
			$oSousActiv = new CSousActiv($this->oBdd,$this->retIdSousActiv());
			
			if (MODALITE_PAR_EQUIPE == $oSousActiv->retModalite(TRUE))
				$this->initEquipe();
		}
	}
	
	function initResSousActivSource ()
	{
		$this->oResSousActivSource = NULL;
		
		if (STATUT_RES_TRANSFERE == $this->retStatut())
		{
			$iIdResSousActivSource = $this->retIdResSousActivSource();
			
			do
			{
				$this->oResSousActivSource = new CRessourceSousActiv($this->oBdd,$iIdResSousActivSource);
			}
			while (STATUT_RES_TRANSFERE == $this->oResSousActivSource->retStatut());
		}
		
		return is_object($this->oResSousActivSource);
	}
	
	function initEquipe ($v_bInitMembres=FALSE)
	{
		$this->oEquipe = new CEquipe($this->oBdd);
		return $this->oEquipe->initEquipe($this->retIdExped(),$this->retIdSousActiv(),TYPE_SOUS_ACTIVITE,$v_bInitMembres);
	}
	
	function retRepertoire ($v_sFichierAInclure=NULL,$v_bCheminAbsolu=FALSE)
	{
		return dir_collecticiel($this->aiIdsParent[TYPE_FORMATION],$this->aiIdsParent[TYPE_ACTIVITE],$v_sFichierAInclure,$v_bCheminAbsolu);
	}
}

?>
