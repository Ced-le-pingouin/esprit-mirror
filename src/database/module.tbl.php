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
** Fichier ................: module.tbl.php
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

require_once(dir_database("rubrique.tbl.php"));

define("INTITULE_MODULE","Cours");

/**
 * Cette classe...
 *
 * @class CModule
 * @see CBddMySql
 */
class CModule
{
	var $iId;
	
	var $oBdd;
	var $oEnregBdd;
	
	var $aoTuteurs;
	var $aoInscrits;
	
	var $aoModules;
	var $oRubriqueCourante;
	var $aoRubriques;
	var $oIntitule;
	
	var $aoForums;
	var $aoCollecticiels;
	
	function CModule (&$v_oBdd,$v_iIdMod=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdMod;
		
		if ($this->iId > 0)
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdMod;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Module"
				." WHERE IdMod='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		// Rechercher l'intitulé du module
		if (is_object($this->oEnregBdd))
			$this->oIntitule = new CIntitule($this->oBdd,$this->oEnregBdd->IdIntitule);
	}
	
	function retNumOrdreMax ()
	{
		$sRequeteSql = "SELECT MAX(OrdreMod) FROM Module"
			." WHERE IdForm='".$this->retIdParent()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNumMax = $this->oBdd->retEnregPrecis($hResult,0);
		$this->oBdd->libererResult($hResult);
		return $iNumMax;
	}
	
	function copier ($v_iIdForm,$v_bRecursive=TRUE)
	{
		$iIdMod = $this->copierModule($v_iIdForm);
		
		if ($v_bRecursive && $iIdMod > 0)
			$this->copierRubriques($iIdMod);
		
		return $iIdMod;
	}
	
	function copierModule ($v_iIdForm)
	{
		if ($v_iIdForm < 1)
			return 0;
		
		$sRequeteSql = "INSERT INTO Module SET"
			." IdMod=NULL"
			.", NomMod='".MySQLEscapeString($this->oEnregBdd->NomMod)."'"
			.", DescrMod='".MySQLEscapeString($this->oEnregBdd->DescrMod)."'"
			.", StatutMod='".$this->retStatut()."'"
			.", IdForm='{$v_iIdForm}'"
			.", DateDebMod=NOW()"
			.", DateFinMod=NOW()"
			.", InscrSpontEquipeM='0'"
			.", NbMaxDsEquipeM='3'"
			.", IdPers='0'"
			.", OrdreMod='{$this->oEnregBdd->OrdreMod}'"
			.", IdIntitule='{$this->oEnregBdd->IdIntitule}'"
			.", NumDepartIntitule='{$this->oEnregBdd->NumDepartIntitule}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return $this->oBdd->retDernierId();
	}
	
	function copierRubriques ($v_iIdMod)
	{
		$this->initRubriques();
		foreach ($this->aoRubriques as $oRubrique)
			$oRubrique->copier($v_iIdMod);
		$this->aoRubriques = NULL;
	}
	
	function rafraichir ()
	{
		if ($this->retId() > 0) $this->init();
	}
	
	function retNombreLignes ($v_iIdForm=NULL)
	{
		if ($v_iIdForm == NULL)
			if (($v_iIdForm = $this->retIdParent()) == NULL)
				return 0;
		
		$sRequeteSql = "SELECT COUNT(*) FROM Module"
			." WHERE IdForm='{$v_iIdForm}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbrLignes = $this->oBdd->retEnregPrecis($hResult,0);
		$this->oBdd->libererResult($hResult);
		
		return $iNbrLignes;
	}
	
	/**
	 * Retourner les informations du module précédent.
	 */
	function retInfosIntituleModPrecedent ($v_iIdForm)
	{
		$asInfosIntituleModPrecedent = array("IdIntitule" => "1" // Cours
			,"NumDepartIntitule" => "1");
		
		$sRequeteSql = "SELECT IdIntitule, NumDepartIntitule FROM Module"
			." WHERE IdForm='{$v_iIdForm}'"
			." ORDER BY OrdreMod DESC"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($oEnregBdd = $this->oBdd->retEnregSuiv($hResult))
		{
			$iIdIntitule = $oEnregBdd->IdIntitule;
			$iNumDepartIntitule = ($oEnregBdd->NumDepartIntitule > 0
				? $oEnregBdd->NumDepartIntitule + 1
				: 0);
			
			$asInfosIntituleModPrecedent = array("IdIntitule" => $iIdIntitule
				,"NumDepartIntitule" => $iNumDepartIntitule);
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $asInfosIntituleModPrecedent;
	}
	
	/**
	 * Cette fonction permet d'ajouter un module à une formation.
	 *
	 * @param v_iIdForm Numéro d'identifiant de la formation
	 * @param v_iIdPers Numéro d'identifiant de la personne
	 * @return Cette fonction retourne le numéro d'identifiant du nouveau module.
	 */
	function ajouter ($v_iIdForm=NULL,$v_iIdPers=0)
	{
		$asInfosIntituleModPrecedent = $this->retInfosIntituleModPrecedent($v_iIdForm);
		
		$iNumOrdre = $this->retNombreLignes($v_iIdForm)+1;
		
		$sRequeteSql = "INSERT INTO Module SET"
			." IdMod=NULL"
			.", NomMod='".mysql_escape_string(INTITULE_MODULE." sans nom")."'"
			.", DateDebMod=NOW()"
			.", DateFinMod=NOW()"
			.", StatutMod='".STATUT_OUVERT."'"
			.", IdForm='{$v_iIdForm}'"
			.", OrdreMod='{$iNumOrdre}'"
			.", IdPers='{$v_iIdPers}'"
			.", IdIntitule='".$asInfosIntituleModPrecedent["IdIntitule"]."'"
			.", NumDepartIntitule='".$asInfosIntituleModPrecedent["NumDepartIntitule"]."'";
		$this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId();
		$this->init();
		
		// Associe cette personne comme concepteur de ce nouveau module
		$this->associerConcepteur($v_iIdPers);
		
		return $this->iId;
	}
	
	/**
	 * Cette fonction inscrit la personne en tant que concepteur de ce module.
	 *
	 * @param v_iIdPers Numéro d'identifiant de la personne
	 */
	function associerConcepteur ($v_iIdPers)
	{
		$iIdForm = $this->retIdParent();
		$iIdMod  = $this->retId();
		
		if (is_numeric($v_iIdPers) && $v_iIdPers > 0 &&
			$iIdForm > 0 &&
			$iIdMod > 0)
		{
			$sRequeteSql = "REPLACE INTO Formation_Concepteur"
				." (IdForm,IdPers) VALUES ('{$iIdForm}','{$v_iIdPers}')";
			$this->oBdd->executerRequete($sRequeteSql);
			
			$sRequeteSql = "REPLACE INTO Module_Concepteur"
				." (IdMod,IdPers) VALUES ('{$iIdMod}','{$v_iIdPers}')";
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function effacer ()
	{
		$this->effacerForums();
		$this->effacerEtudiants();
		$this->effacerTuteurs();
		$this->effacerConcepteurs();
		$this->effacerEquipes();
		$this->effacerRubriques();
		$this->effacerModule();
		$this->redistNumsOrdre();
	}
	
	function effacerModule ()
	{
		$sRequeteSql = "DELETE FROM Module"
			." WHERE IdMod='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerForums ()
	{
		$this->initForums();
		foreach ($this->aoForums as $oForum)
			$oForum->effacer();
		$this->aoForums = NULL;
	}
	
	function effacerEquipes ()
	{
		$oEquipe = new CEquipe($this->oBdd);
		$oEquipe->effacerParNiveau(TYPE_MODULE,$this->retId());
	}
	
	function effacerEtudiants ()
	{
		$sRequeteSql = "DELETE FROM Module_Inscrit"
			." WHERE IdMod='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerTuteurs ()
	{
		$sRequeteSql = "DELETE FROM Module_Tuteur"
			." WHERE IdMod='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerConcepteurs ()
	{
		$sRequeteSql = "DELETE FROM Module_Concepteur"
			." WHERE IdMod='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerRubriques ()
	{
		$iNbrRubriques = $this->initRubriques();
		
		for ($idx=0; $idx<$iNbrRubriques; $idx++)
			$this->aoRubriques[$idx]->Effacer();
		
		$this->aoRubriques = NULL;
	}
	
	function initForums ()
	{
		$iIdxForum = 0;
		$this->aoForums = array();
		if (is_object($this->oEnregBdd) && $this->oEnregBdd->IdMod > 0)
		{
			$sRequeteSql = "SELECT * FROM Forum"
				." WHERE IdMod='{$this->oEnregBdd->IdMod}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->aoForums[$iIdxForum] = new CForum($this->oBdd);
				$this->aoForums[$iIdxForum]->init($oEnreg);
				$iIdxForum++;
			}
			$this->oBdd->libererResult($hResult);
		}
		return $iIdxForum;
	}
	
	function initConcepteurs ()
	{
		$iIdxConcepteur = 0;
		$this->aoConcepteurs = array();
		
		$sRequeteSql = "SELECT Personne.*"
			." FROM Module_Concepteur"
			." LEFT JOIN Personne USING(IdPers)"
			." WHERE Module_Concepteur.IdMod='".$this->retId()."'"
			." ORDER BY Personne.Nom, Personne.Prenom ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoConcepteurs[$iIdxConcepteur] = new CPersonne($this->oBdd);
			$this->aoConcepteurs[$iIdxConcepteur]->init($oEnreg);
			$iIdxConcepteur++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxConcepteur;
	}
	
	function initTuteurs ()
	{
		$iIndexTuteur = 0;
		$this->aoTuteurs = array();
		
		$sRequeteSql = "SELECT Personne.*"
			." FROM Module_Tuteur"
			." LEFT JOIN Personne USING(IdPers)"
			." WHERE Module_Tuteur.IdMod='".$this->retId()."'"
			." ORDER BY Personne.Nom ASC, Personne.Prenom ASC";
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
	
	function retTypeNiveau () { return TYPE_MODULE; }
	
	function initInscrits ()
	{
		$iIdxInscrit = 0;
		$this->aoInscrits = array();
		
		$sRequeteSql = "SELECT Personne.*"
			." FROM Module_Inscrit"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Module_Inscrit.IdMod='".$this->retId()."'"
			." ORDER BY Personne.Nom, Personne.Prenom";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoInscrits[$iIdxInscrit] = new CPersonne($this->oBdd);
			$this->aoInscrits[$iIdxInscrit]->init($oEnreg);
			$iIdxInscrit++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxInscrit;
	}
	
	function initRubriqueCourante ($v_iIdRubrique=NULL)
	{
		if ($v_iIdRubrique > 0)
			$this->oRubriqueCourante = new CModule_Rubrique($this->oBdd,$v_iIdRubrique);
	}
	
	function initParIdModule ($v_iIdModule)
	{
		if ($v_iIdModule < 1)
			return FALSE;
		$sRequeteSql = "SELECT * FROM Module WHERE IdMod='{$v_iIdModule}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
		$this->oBdd->libererResult($hResult);
		return ($this->retId() > 0);
	}
	
	function initRubriques ($v_iTypeRubriques=NULL)
	{
		$iIdxRubriques = 0;
		
		$this->aoRubriques = array();
		
		$sRequeteSql = "SELECT * FROM Module_Rubrique"
			." WHERE IdMod='".$this->retId()."'"
			.(isset($v_iTypeRubriques) ? " AND TypeRubrique='$v_iTypeRubriques'" : NULL)
			." ORDER BY OrdreRubrique";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoRubriques[$iIdxRubriques] = new CModule_Rubrique($this->oBdd);
			$this->aoRubriques[$iIdxRubriques]->init($oEnreg);
			$iIdxRubriques++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxRubriques;
	}
	
	function defId ($v_iId,$v_bInit=TRUE)
	{
		$this->iId = $v_iId;
		
		if ($v_bInit)
			$this->init();
	}
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	
	function retNom ($v_bHtmlEntities=FALSE) { return ($v_bHtmlEntities ? htmlentities($this->oEnregBdd->NomMod) : $this->oEnregBdd->NomMod); }
	
	function retNomComplet ($v_bHtmlEntities=FALSE)
	{
		$sIntitule = $this->retTexteIntitule();
		$sNomComplet = (strlen($sIntitule) > 0 ? "{$sIntitule} : " : NULL)
			.$this->oEnregBdd->NomMod;
		return ($v_bHtmlEntities ? htmlentities($sNomComplet) : $sNomComplet);
	}
	
	function retTexteIntitule ($v_bAfficherNumOrdre=TRUE)
	{
		$sNomIntitule = $this->oIntitule->retNom();
		
		return (strlen($sNomIntitule) > 0 ? "{$sNomIntitule}" : NULL)
			.($v_bAfficherNumOrdre && $this->oEnregBdd->NumDepartIntitule > 0
				? "&nbsp;{$this->oEnregBdd->NumDepartIntitule}"
				: NULL);
	}
	
	function retDescr ($v_bHtmlEntities=FALSE) { return ($v_bHtmlEntities ? htmlentities($this->oEnregBdd->DescrMod) : $this->oEnregBdd->DescrMod); }
	function retDateDeb () { return $this->oEnregBdd->DateDebMod; }
	function retDateFin () { return $this->oEnregBdd->DateFinMod; }
	
	function defStatut ($v_iStatut)
	{
		if (is_numeric($v_iStatut))
			$this->mettre_a_jour("StatutMod",$v_iStatut);
	}
	
	function retStatut () { return $this->oEnregBdd->StatutMod; }
	function retInscrSpontEquipe () { return $this->oEnregBdd->InscrSpontEquipeM; }
	function retNbMaxDsEquipe () { return $this->oEnregBdd->NbMaxDsEquipeM; }
	function retIdParent () { return (is_numeric($this->oEnregBdd->IdForm) ? $this->oEnregBdd->IdForm : 0); }
	
	function defNumOrdre ($v_iNumOrdre)
	{
		if (is_numeric($v_iNumOrdre))
			$this->mettre_a_jour("OrdreMod",$v_iNumOrdre);
	}
	
	function defIdIntitule ($v_iIdIntitule) { $this->mettre_a_jour("IdIntitule",$v_iIdIntitule); }
	function retIdIntitule () { return $this->oEnregBdd->IdIntitule; }
	function retNumDepart () { return $this->oEnregBdd->NumDepartIntitule; }
	function retNumOrdre () { return $this->oEnregBdd->OrdreMod; }
	
	function defNumDepart ($v_iNumDepart)
	{
		if ($v_iNumDepart >= 0 && $v_iNumDepart <= 254)
			$this->mettre_a_jour("NumDepartIntitule",$v_iNumDepart);
	}
	
	function mettre_a_jour ($v_sNomChamp,$v_mValeurChamp,$v_iIdMod=0)
	{
		if ($v_iIdMod < 1)
			$v_iIdMod = $this->retId();
		
		if ($v_iIdMod < 1)
			return FALSE;
		
		$sRequeteSql = "UPDATE Module SET"
			." {$v_sNomChamp}='".mysql_escape_string($v_mValeurChamp)."'"
			." WHERE IdMod='{$v_iIdMod}'";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	function defNom ($v_sNom)
	{
		$v_sNom = trim(stripslashes($v_sNom));
		if (empty($v_sNom))
			$v_sNom = INTITULE_MODULE." sans nom";
		$this->mettre_a_jour("NomMod",$v_sNom);
	}
	
	function defDescr ($v_sDescr) { $this->mettre_a_jour("DescrMod",trim(stripslashes($v_sDescr))); }
	
	function initMembres ($v_bAppartenirEquipe=TRUE,$v_bAutoInscrit=TRUE,$v_iSensTri=TRI_CROISSANT)
	{
		$iIdxMembre = 0;
		
		$this->aoMembres = array();
				
		if ($v_bAppartenirEquipe)
			$sRequeteSql = "SELECT Personne.* FROM Equipe"
				." LEFT JOIN Equipe_Membre USING (IdEquipe)"
				." LEFT JOIN Personne USING (IdPers)"
				." WHERE Equipe.IdMod='".$this->retId()."' AND Equipe.IdRubrique='0'"
				." AND Personne.IdPers IS NOT NULL";
		else if ($v_bAutoInscrit)
			$sRequeteSql = "SELECT Personne.* FROM Formation_Inscrit"
				." LEFT JOIN Personne USING (IdPers)"
				." LEFT JOIN Equipe ON Formation_Inscrit.IdForm=Equipe.IdForm"
				." LEFT JOIN Equipe_Membre ON Equipe.IdEquipe=Equipe_Membre.IdEquipe"
				." AND Formation_Inscrit.IdPers=Equipe_Membre.IdPers"
				." WHERE Equipe.IdMod='".$this->retId()."' AND Equipe.IdRubrique='0'"
				." GROUP BY Personne.IdPers HAVING COUNT(Equipe_Membre.IdEquipe)='0'";
		else
			$sRequeteSql = "SELECT Personne.* FROM Module_Inscrit"
				." LEFT JOIN Personne USING (IdPers)"
				." LEFT JOIN Equipe ON Module_Inscrit.IdMod=Equipe.IdMod"
				." LEFT JOIN Equipe_Membre ON Equipe.IdEquipe=Equipe_Membre.IdEquipe"
				." AND Module_Inscrit.IdPers=Equipe_Membre.IdPers"
				." WHERE Equipe.IdMod='".$this->retId()."' AND Equipe.IdRubrique='0'"
				." GROUP BY Personne.IdPers HAVING COUNT(Equipe_Membre.IdEquipe)='0'";

		if ($v_iSensTri <> PAS_TRI)
			$sRequeteSql .= " ORDER BY Personne.Nom".($v_iSensTri == TRI_DECROISSANT ? " DESC" :" ASC");
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoMembres[$iIdxMembre] = new CPersonne($this->oBdd);
			$this->aoMembres[$iIdxMembre]->init($oEnreg);
			$iIdxMembre++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxMembre;
	}
	
	function retListeModules ()
	{
		$sRequeteSql = "SELECT * FROM Module"
			." WHERE IdForm=".$this->retIdParent()
			." ORDER BY OrdreMod ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$i = 0;
		
		while ($this->aoModules[$i++] = $this->oBdd->retEnregSuiv($hResult))
			;
		
		$this->oBdd->libererResult($hResult);
		
		return ($i-1);
	}
	
	function redistNumsOrdre ($v_iNouveauNumOrdre=NULL)
	{
		if ($v_iNouveauNumOrdre == $this->retNumOrdre())
			return FALSE;
		
		if (($cpt = $this->retListeModules()) < 0)
			return FALSE;
		
		// Ajouter dans ce tableau les ids et les numéros d'ordre
		$aoNumsOrdre = array();
		
		for ($i=0; $i<$cpt; $i++)
			$aoNumsOrdre[$i] = array($this->aoModules[$i]->IdMod,$this->aoModules[$i]->OrdreMod);
		
		// Mettre à jour dans la table Module avec les nouveaux numéros d'ordre
		if ($v_iNouveauNumOrdre > 0)
		{
			$aoNumsOrdre = redistNumsOrdre($aoNumsOrdre,$this->retNumOrdre (),$v_iNouveauNumOrdre);
			
			$iIdCourant = $this->retId ();
			
			for ($i=0; $i<$cpt; $i++)
				if ($aoNumsOrdre[$i][0] != $iIdCourant)
					$this->mettre_a_jour("OrdreMod",$aoNumsOrdre[$i][1],$aoNumsOrdre[$i][0]);
			
			$this->defNumOrdre($v_iNouveauNumOrdre);
		}
		else
		{
			for ($i=0; $i<$cpt; $i++)
				$this->mettre_a_jour("OrdreMod",($i+1),$aoNumsOrdre[$i][0]);
		}
		
		return TRUE;
	}
	
	// --------------------------------
	function verifInscrit ($v_sRequeteSql)
	{
		$bOk = FALSE;
		$hResult = $this->oBdd->executerRequete($v_sRequeteSql);
		if ($this->oBdd->retEnregSuiv($hResult))
			$bOk = TRUE;
		$this->oBdd->libererResult($hResult);
		return $bOk;
	}
	
	function verifConcepteur ($v_iIdPers)
	{
		if ($v_iIdPers < 1 || $this->oEnregBdd->IdMod < 1)
			return FALSE;
		
		$sRequeteSql = "SELECT *"
			." FROM Module_Concepteur"
			." WHERE IdMod='{$this->oEnregBdd->IdMod}'"
			." AND IdPers='{$v_iIdPers}'"
			." LIMIT 1";
		return $this->verifInscrit($sRequeteSql);
	}
	
	function verifTuteur ($v_iIdPers)
	{
		if ($v_iIdPers < 1 || $this->oEnregBdd->IdMod < 1)
			return FALSE;
		
		$sRequeteSql = "SELECT *"
			." FROM Module_Tuteur"
			." WHERE IdMod='{$this->oEnregBdd->IdMod}'"
			." AND IdPers='{$v_iIdPers}'"
			." LIMIT 1";
		return $this->verifInscrit($sRequeteSql);
	}
	
	function verifEtudiant ($v_iIdPers)
	{
		$sRequeteSql = "SELECT COUNT(*) FROM Module_Inscrit"
			." WHERE IdMod='".$this->retId()."'"
			." AND IdPers='{$v_iIdPers}'"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$bEtudiant = ($this->oBdd->retEnregPrecis($hResult) == 1);
		$this->oBdd->libererResult($hResult);
		return $bEtudiant;
	}
	
	function verifMembre ($v_iIdPers)
	{
		$sRequeteSql = "SELECT Equipe_Membre.IdEquipe"
			." FROM Equipe_Membre"
			." LEFT JOIN Equipe USING (IdEquipe)"
			." WHERE Equipe_Membre.IdPers='{$v_iIdPers}'"
			." AND Equipe.IdMod='{$this->oEnregBdd->IdMod}'";
		return $this->verifInscrit($sRequeteSql);
	}
	
	// ---------------------
	// Inscrire des personnes en tant qu'étudiant/tuteur
	// ---------------------
	function inscrireEtudiants ($v_aiIdPers) { $this->inscrirePersonnes($v_aiIdPers,STATUT_PERS_ETUDIANT); }
	function inscrireTuteurs ($v_aiIdPers) { $this->inscrirePersonnes($v_aiIdPers,STATUT_PERS_TUTEUR); }
	function inscrirePersonnes ($v_aiIdPers,$v_iIdStatut)
	{
		$sValeursRequete = NULL;
		
		if ($this->iId < 1)
			return;
		
		foreach ($v_aiIdPers as $iIdPers)
			$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
				."('{$this->iId}','{$iIdPers}')";
			
		if (isset($sValeursRequete))
		{
			if ($v_iIdStatut == STATUT_PERS_CONCEPTEUR)
				$sNomTable = "Module_Concepteur";
			else if ($v_iIdStatut == STATUT_PERS_TUTEUR)
				$sNomTable = "Module_Tuteur";
			else if ($v_iIdStatut == STATUT_PERS_ETUDIANT)
				$sNomTable = "Module_Inscrit";
			else
				$sNomTable = NULL;
			
			if (isset($sNomTable))
			{
				$sRequeteSql = "REPLACE INTO {$sNomTable}"
					." (IdMod, IdPers) VALUES {$sValeursRequete}";
				$this->oBdd->executerRequete($sRequeteSql);
			}
		}
	}
	
	/**
	 * Cette fonction retire des personnes inscrites de la table.
	 *
	 * \param v_aiIdPers cette variable contient la liste des personnes à retirer
	 * \param v_iIdStatut numéro d'identifiant du statut de la personne.
	 */
	function retirerPersonnes ($v_aiIdPers,$v_iIdStatut)
	{
		$sValeursRequete = NULL;
		
		if ($this->iId < 1)
			return;
		
		foreach ($v_aiIdPers as $iIdPers)
			if (is_numeric($iIdPers))
				$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
					."'{$iIdPers}'";
		
		if (isset($sValeursRequete))
		{
			if ($v_iIdStatut == STATUT_PERS_CONCEPTEUR)
				$sNomTable = "Module_Concepteur";
			else if ($v_iIdStatut == STATUT_PERS_TUTEUR)
				$sNomTable = "Module_Tuteur";
			else if ($v_iIdStatut == STATUT_PERS_ETUDIANT)
				$sNomTable = "Module_Inscrit";
			else
				$sNomTable = NULL;
			
			if (isset($sNomTable))
			{
				$sRequeteSql = "DELETE FROM {$sNomTable}"
					." WHERE IdMod='{$this->iId}'"
					." AND IdPers IN ({$sValeursRequete})"
					." LIMIT ".count($v_aiIdPers);
				$this->oBdd->executerRequete($sRequeteSql);
			}
		}
	}
	
	function retIdPers () { return $this->oEnregBdd->IdPers; }
	
	// ---------------------
	// Chats
	// ---------------------
	function initChats ()
	{
		$oChat = new CChat($this->oBdd);
		$iNbChats = $oChat->initChats($this);
		$this->aoChats = $oChat->aoChats;
		return $iNbChats;
	}
	
	function retNombreChats ()
	{
		$oChat = new CChat($this->oBdd);
		return $oChat->retNombreChats($this);
	}
	
	function ajouterChat ()
	{
		$oChat = new CChat($this->oBdd);
		return $oChat->ajouter($this);
	}
	
	function effacerChats ()
	{
		$oChat = new CChat($this->oBdd);
		$oChat->effacerChats($this);
	}
	
	// --------------------------------
	function initCollecticiels ($v_iModaliteSousActiv=NULL)
	{
		if (!isset($v_iModaliteSousActiv))
			$v_iModaliteSousActiv = MODALITE_INDIVIDUEL;
		
		$sRequeteSql = "SELECT sa.* FROM SousActiv AS sa"
			." LEFT JOIN Activ AS a ON sa.IdActiv=a.IdActiv"
			." LEFT JOIN Module_Rubrique AS mr ON a.IdRubrique=mr.IdRubrique"
			." WHERE mr.IdMod=".$this->retId()
			.(isset($v_iModaliteSousActiv) ? " AND sa.ModaliteSousActiv='{$v_iModaliteSousActiv}'" : NULL);
		
		$iIndexCollecticiels = 0;
		
		$this->aoCollecticiels = array();
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoCollecticiels = new CSousActiv($oProjet->oBdd);
			$this->aoCollecticiels[$iIndexCollecticiels]->init($oEnreg);
			$iIndexCollecticiels++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIndexCollecticiels;
	}
	
	function peutSupprimerIntitule ($v_iIdIntitule)
	{
		$bSupprimerIntitule = FALSE;
		
		if ($v_iIdIntitule < 1)
			return $bSupprimerIntitule;
		
		$sRequeteSql = "SELECT COUNT(*) FROM Module"
			." WHERE IdIntitule='{$v_iIdIntitule}'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($this->oBdd->retEnregPrecis($hResult) < 1)
			$bSupprimerIntitule = TRUE;
			
		$this->oBdd->libererResult($hResult);
		
		return $bSupprimerIntitule;
	}
	
	function retTypes ()
	{
		return array(array(0,INTITULE_MODULE));
	}
}

?>
