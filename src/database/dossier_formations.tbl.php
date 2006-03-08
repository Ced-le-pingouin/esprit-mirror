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
** Fichier ................: dossier_formations.tbl.php
** Description ............:
** Date de création .......: 18/05/2005
** Dernière modification ..: 29/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

class CDossierForms
{
	var $iId;
	
	var $oBdd;
	var $oEnregBdd;
	
	var $iAncienNumOrdre;
	
	var $oPersonne;
	
	var $oPremierDossierForms;
	
	var $aoDossierForms;
	var $aoFormations;
	
	function CDossierForms (&$v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->initGraceIdDossierForms($v_iId);
	}
	
	function initGraceIdDossierForms ($v_iId)
	{
		$this->iId = $v_iId;
		
		if (!empty($this->iId))
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdDossierForms;
		}
		else
		{
			$sRequeteSql = "SELECT *"
				." FROM DossierFormations"
				." WHERE IdDossierForms='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function reorganiserNumOrdre ()
	{
		if (empty($this->iAncienNumOrdre))
			return;
		
		if ($this->iAncienNumOrdre > $this->oEnregBdd->OrdreDossierForms)
			$sRequeteSql = "UPDATE DossierFormations SET"
				." OrdreDossierForms=OrdreDossierForms+1"
				." WHERE IdPers='{$this->oEnregBdd->IdPers}'"
				." AND (OrdreDossierForms>='{$this->oEnregBdd->OrdreDossierForms}'"
				." AND OrdreDossierForms<'{$this->iAncienNumOrdre}')";
		else
			$sRequeteSql = "UPDATE DossierFormations SET"
				." OrdreDossierForms=OrdreDossierForms-1"
				." WHERE IdPers='{$this->oEnregBdd->IdPers}'"
				." AND (OrdreDossierForms>'{$this->iAncienNumOrdre}'"
				." AND OrdreDossierForms<='{$this->oEnregBdd->OrdreDossierForms}')";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$this->iAncienNumOrdre = NULL;
	}
	
	function initAjouter ($v_iIdPers)
	{
		$this->oEnregBdd->IdDossierForms = NULL;
		$this->oEnregBdd->NomDossierForms = "Dossier sans nom";
		$this->oEnregBdd->PremierDossierForms = "0";
		$this->oEnregBdd->IdPers = $v_iIdPers;
		$this->oEnregBdd->OrdreDossierForms = ($this->retNbDossierForms() + 1);
		$this->oEnregBdd->VisibleDossierForms = "1";
	}
	
	function effacerPremierDossier ($v_iIdPers=NULL)
	{
		if (isset($v_iIdPers))
			$this->oEnregBdd->IdPers = $v_iIdPers;
		
		$sRequeteSql = "UPDATE DossierFormations SET"
			." PremierDossierForms='0'"
			." WHERE IdPers='{$this->oEnregBdd->IdPers}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function ajouter ()
	{
		if ($this->oEnregBdd->PremierDossierForms == 1)
			$this->effacerPremierDossier();
		
		$this->reorganiserNumOrdre();
		
		$sRequeteSql = "INSERT INTO DossierFormations SET"
			." IdDossierForms=NULL"
			.", NomDossierForms='".MySQLEscapeString($this->oEnregBdd->NomDossierForms)."'"
			.", PremierDossierForms='{$this->oEnregBdd->PremierDossierForms}'"
			.", OrdreDossierForms='{$this->oEnregBdd->OrdreDossierForms}'"
			.", VisibleDossierForms='{$this->oEnregBdd->VisibleDossierForms}'"
			.", IdPers='{$this->oEnregBdd->IdPers}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	function enregistrer ()
	{
		if ($this->oEnregBdd->PremierDossierForms == 1)
			$this->effacerPremierDossier();
		
		$this->reorganiserNumOrdre();
		
		$sRequeteSql = "UPDATE DossierFormations SET"
			." NomDossierForms='".MySQLEscapeString($this->oEnregBdd->NomDossierForms)."'"
			.", PremierDossierForms='{$this->oEnregBdd->PremierDossierForms}'"
			.", OrdreDossierForms='{$this->oEnregBdd->OrdreDossierForms}'"
			.", VisibleDossierForms='{$this->oEnregBdd->VisibleDossierForms}'"
			.", IdPers='{$this->oEnregBdd->IdPers}'"
			." WHERE IdDossierForms='{$this->iId}'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacer ()
	{
		$this->verrouillerTables();
		
		$this->effacerFormations();
		
		$sRequeteSql = "DELETE FROM DossierFormations"
			." WHERE IdDossierForms='{$this->iId}'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$this->oBdd->deverrouillerTables();
	}
	
	function retPremierIdDossierForms ()
	{
		$sRequeteSql = "SELECT IdDossierForms FROM DossierFormations"
			." WHERE IdPers='{$this->oEnregBdd->IdPers}'"
			." ORDER BY OrdreDossierForms"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iIdDossierForms= $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		
		return $iIdDossierForms;
	}
	
	function defNom ($v_sNom) { $this->oEnregBdd->NomDossierForms = $v_sNom; }
	
	function defNumOrdre ($v_iNumOrdre)
	{
		if (isset($this->oEnregBdd->OrdreDossierForms))
			$this->iAncienNumOrdre = $this->oEnregBdd->OrdreDossierForms;
		
		$this->oEnregBdd->OrdreDossierForms = $v_iNumOrdre;
	}
	
	function defPremierDossier ($v_bPremierDossier) { $this->oEnregBdd->PremierDossierForms = (int)$v_bPremierDossier; }
	function defVisible ($v_bVisible) { $this->oEnregBdd->VisibleDossierForms = (int)$v_bVisible; }
	function defIdPersonne ($v_iIdPers) { $this->oEnregBdd->IdPers = $v_iIdPers; }
	
	function retId () { return (isset($this->iId) && is_numeric($this->iId) ? $this->iId : 0); }
	function retNom () { return $this->oEnregBdd->NomDossierForms; }
	function retNumOrdre () { return $this->oEnregBdd->OrdreDossierForms; }
	function retPremierDossier () { return ($this->oEnregBdd->PremierDossierForms == '1'); }
	function retVisible () { return ($this->oEnregBdd->VisibleDossierForms == '1'); }
	
	function retNbDossierForms ()
	{
		$sRequeteSql = "SELECT COUNT(*) FROM DossierFormations"
			." WHERE IdPers='{$this->oEnregBdd->IdPers}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbDossierForms = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		return $iNbDossierForms;
	}
	
	function initPersonne () { $this->oPersonne = new CPersonne($this->oBdd,$this->oEnregBdd->IdPers); }
	
	function initDossierForms ($v_iIdPers,$v_bInitFormations=FALSE,$v_aiIdForms=NULL,$v_bPremierDossierForms=FALSE)
	{
		$iIdxDossierForms = 0;
		$this->aoDossierForms = array();
		
		$sRequeteSql = "SELECT *"
			." FROM DossierFormations"
			." WHERE IdPers='{$v_iIdPers}'"
			.($v_bPremierDossierForms ? " AND PremierDossierForms='1'": NULL)
			." ORDER BY OrdreDossierForms ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoDossierForms[$iIdxDossierForms] = new CDossierForms($this->oBdd);
			$this->aoDossierForms[$iIdxDossierForms]->init($oEnreg);
			
			if ($v_bInitFormations)
				$this->aoDossierForms[$iIdxDossierForms]->initFormations($v_aiIdForms);
			
			$iIdxDossierForms++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxDossierForms;
	}
	
	function initPremierDossierForms ($v_iIdPers)
	{
		$sRequeteSql = "SELECT * FROM DossierFormations"
			." WHERE IdPers='{$v_iIdPers}'"
			." AND PremierDossierForms='1'"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->oPremierDossierForms = new CDossierForms($this->oBdd);
		$this->oPremierDossierForms->init($this->oBdd->retEnregSuiv($hResult));
		$this->oBdd->libererResult($hResult);
		
		return ($this->oPremierDossierForms->retId() > 0);
	}
	
	function verrouillerTables () { $this->oBdd->executerRequete("LOCK TABLES DossierFormations WRITE, DossierFormations_Formation WRITE"); }
	
	// {{{ Méthodes de la table DossierFormations_Formation
	function initFormations ($v_aiIdForms,$v_bFormationsSelectionnees=TRUE)
	{
		$iIdxForm = 0;
		$this->aoFormations = array();
		
		$sValeursRequete = NULL;
		
		if (is_array($v_aiIdForms))
			foreach ($v_aiIdForms as $iIdForm)
				$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
					."'{$iIdForm}'";
		
		$sRequeteSql = "SELECT Formation.*"
			.", IFNULL(DossierFormations_Formation.OrdreForm,32635) AS OrdreForm"
			." FROM Formation"
			." LEFT JOIN DossierFormations_Formation"
				." ON Formation.IdForm=DossierFormations_Formation.IdForm"
				." AND DossierFormations_Formation.IdDossierForms='".$this->retId()."'"
			.(is_array($v_aiIdForms) ? NULL : " LEFT JOIN DossierFormations USING (IdDossierForms)")
			.(is_array($v_aiIdForms) ? " WHERE Formation.IdForm IN ({$sValeursRequete})" : " WHERE DossierFormations.IdPers='{$this->oEnregBdd->IdPers}'")
			." ORDER BY OrdreForm ASC, Formation.NomForm ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			if ($v_bFormationsSelectionnees && $oEnreg->OrdreForm == 32635)
				continue;
			
			$this->aoFormations[$iIdxForm] = new CFormation($this->oBdd);
			$this->aoFormations[$iIdxForm]->init($oEnreg);
			$iIdxForm++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxForm;
	}
	
	function ajouterFormations ($v_aaDossierForms_Form)
	{
		$sValeursRequete = NULL;
		$iIdDossierForms = $this->retId();
		
		foreach ($v_aaDossierForms_Form as $v_aiDossierForms_Form)
			$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
				."('{$iIdDossierForms}', '".$v_aiDossierForms_Form["IdForm"]."', '".$v_aiDossierForms_Form["OrdreForm"]."')";
		
		if (isset($sValeursRequete))
		{
			$this->verrouillerTablesFormations();
			
			$this->effacerFormations();
			
			$sRequeteSql = "INSERT INTO DossierFormations_Formation"
				." (IdDossierForms, IdForm, OrdreForm)"
				." VALUES {$sValeursRequete}";
			$this->oBdd->executerRequete($sRequeteSql);
			
			$this->oBdd->deverrouillerTables();
		}
	}
	
	function effacerFormations ()
	{
		$sRequeteSql = "DELETE FROM DossierFormations_Formation"
			." WHERE IdDossierForms='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		if (rand(1,5) == 5)
			$this->optimiserTableFormations();
	}
	
	function verrouillerTablesFormations () { $this->oBdd->executerRequete("LOCK TABLES DossierFormations_Formation WRITE"); }
	function optimiserTableFormations () { $this->oBdd->executerRequete("OPTIMIZE TABLE DossierFormations_Formation"); }
	// }}}
}

?>
