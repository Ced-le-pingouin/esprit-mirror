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
** Fichier ................: sous_activite.tbl.php
** Description ............:
** Date de création .......: 01/06/2001
** Dernière modification ..: 16/11/2005
** Auteurs ................: Cédric FLOQUET <cedric.floquet@umh.ac.be>
**                           Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_database("sous_activite.ressource.tbl.php"));
require_once(dir_database("collecticiel.tbl.php"));
require_once(dir_database("galerie.tbl.php"));
require_once(dir_database("glossaire.tbl.php"));
require_once(dir_database("chat.tbl.php"));

define("INTITULE_SOUS_ACTIV","Action");

class CSousActiv
{
	var $iId;
	
	var $oBdd;
	var $oEnregBdd;
	
	var $aoRessources;
	var $oActivParente = NULL;
	var $aoSousActivs;
	var $oIdsParents;
	
	var $oAuteur;
	var $oEquipe;
	var $aoEquipes;
	
	var $oForum;
	var $oCollecticiel;
	var $oGalerie;
	
	var $aoChats;
	
	var $oGlossaire;
	
	function CSousActiv (&$v_oBdd,$v_iIdSousActiv=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdSousActiv;
		
		if ($this->iId > 0)
			$this->init();
	}
	
	function retTypeNiveau () { return TYPE_SOUS_ACTIVITE; }
	
	function init ($v_oEnregExistant=NULL)
	{
		if (is_object($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdSousActiv;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM SousActiv"
				." WHERE IdSousActiv='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function initEquipe ($v_iIdEquipe,$v_bInitMembres=FALSE) { $this->oEquipe = new CEquipe($this->oBdd,$v_iIdEquipe,$v_bInitMembres); }
	
	function initEquipes ($v_bInitMembres=FALSE,$iDernierNiveau=TYPE_FORMATION)
	{
		$oListeEquipes = new CEquipe($this->oBdd);
		$oListeEquipes->initEquipesEx($this->retId(),TYPE_SOUS_ACTIVITE,$v_bInitMembres);
		$this->aoEquipes = $oListeEquipes->aoEquipes;
		return count($this->aoEquipes);
	}
	
	// {{{ Formulaire
	function initFormulairesCompletes ($v_iIdPers=NULL,$v_mStatutFC=NULL)
	{
		$iIdxFC = 0;
		$sListeStatutsFC = NULL;
		$this->aoFormulairesCompletes = array();
		
		if (is_array($v_mStatutFC))
			foreach ($v_mStatutFC as $iStatutFC)
				$sListeStatutsFC .= (isset($sListeStatutsFC) ? "," : NULL)
					."'{$iStatutFC}'";
		else
			$sListeStatutsFC = "'{$v_mStatutFC}'";
		
		$sRequeteSql = "SELECT FormulaireComplete_SousActiv.IdFCSousActiv"
			.", FormulaireComplete_SousActiv.StatutFormSousActiv"
			.", FormulaireComplete.*"
			." FROM FormulaireComplete_SousActiv"
			." LEFT JOIN FormulaireComplete USING (IdFC)"
			." WHERE FormulaireComplete_SousActiv.IdSousActiv='".$this->retId()."'"
			.($v_iIdPers > 0 ? " AND FormulaireComplete.IdPers='{$v_iIdPers}'" : NULL)
			.(isset($v_mStatutFC) ? " AND FormulaireComplete_SousActiv.StatutFormSousActiv IN ({$sListeStatutsFC})" : NULL)
			." ORDER BY FormulaireComplete.DateFC ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($hResult !== FALSE)
		{
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->aoFormulairesCompletes[$iIdxFC] = new CFormulaireComplete_SousActiv($this->oBdd);
				$this->aoFormulairesCompletes[$iIdxFC]->init($oEnreg);
				$iIdxFC++;
			}
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $iIdxFC;
	}
	
	function retStatutFormulairePlusHaut ($v_miIdPers=NULL)
	{
		$sListePers = NULL;
		$iStatutResPlusHaut = 0;
		
		if (is_array($v_miIdPers))
			foreach ($v_miIdPers as $iIdPers)
				$sListePers .= (isset($sListePers) ? ", " : NULL)
					."'{$iIdPers}'";
		else
			$sListePers = "'{$v_miIdPers}'";
		
		if (isset($sListePers))
		{
			$sRequeteSql = "SELECT StatutFormSousActiv"
				.", MAX(StatutFormSousActiv) AS MaxStatutFormSousActiv"
				.", COUNT(*) AS CountStatutFormSousActiv"
				." FROM FormulaireComplete_SousActiv"
				." LEFT JOIN FormulaireComplete USING (IdFC)"
				." WHERE FormulaireComplete_SousActiv.IdSousActiv='".$this->retId()."'"
				." AND FormulaireComplete.IdPers IN ({$sListePers})"
				." GROUP BY FormulaireComplete_SousActiv.StatutFormSousActiv"
				." ORDER BY FormulaireComplete_SousActiv.StatutFormSousActiv DESC";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$oEnreg = $this->oBdd->retEnregSuiv($hResult);
			$iStatutResPlusHaut = $oEnreg->MaxStatutFormSousActiv;
			$iNbStatutResPlusHaut = $oEnreg->CountStatutFormSousActiv;
			$this->oBdd->libererResult($hResult);
		}
		
		return array($iStatutResPlusHaut,$iNbStatutResPlusHaut);
	}
	// }}}
	
	function retNumOrdreMax ()
	{
		$sRequeteSql = "SELECT MAX(OrdreSousActiv) FROM SousActiv"
			." WHERE IdActiv='".$this->retIdParent()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNumOrdreMax = $this->oBdd->retEnregPrecis($hResult,0);
		$this->oBdd->libererResult($hResult);
		return $iNumOrdreMax;
	}
	
	function copier ($v_iIdActiv)
	{
		$iIdSousActiv = $this->copierSousActiv($v_iIdActiv);
		
		if ($iIdSousActiv < 1)
			return 0;
		
		switch ($this->retId())
		{
			case LIEN_FORUM: $this->copierForum($iIdSousActiv); break;
			case LIEN_CHAT: $this->copierChats($iIdSousActiv); break;
		}
		
		return $iIdSousActiv;
	}
	
	function copierSousActiv ($v_iIdActiv)
	{
		if ($v_iIdActiv < 1)
			return 0;
		
		$sRequeteSql = "INSERT INTO SousActiv SET"
			." IdSousActiv=NULL"
			.", NomSousActiv='".MySQLEscapeString($this->oEnregBdd->NomSousActiv)."'"
			.", DonneesSousActiv='".MySQLEscapeString($this->oEnregBdd->DonneesSousActiv)."'"
			.", DescrSousActiv='".MySQLEscapeString($this->oEnregBdd->DescrSousActiv)."'"
			.", DateDebSousActiv=NOW()"
			.", DateFinSousActiv=NOW()"
			.", StatutSousActiv='{$this->oEnregBdd->StatutSousActiv}'"
			.", VotesMinSousActiv='{$this->oEnregBdd->VotesMinSousActiv}'"
			.", IdTypeSousActiv='{$this->oEnregBdd->IdTypeSousActiv}'"
			.", PremierePageSousActiv='".($this->oEnregBdd->PremierePageSousActiv ? "1" : "0")."'" // ENUM ('0','1')
			.", IdActiv='{$v_iIdActiv}'"
			.", OrdreSousActiv='{$this->oEnregBdd->OrdreSousActiv}'"
			.", ModaliteSousActiv='{$this->oEnregBdd->ModaliteSousActiv}'"
			.", IdPers='".$this->retIdPers()."'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return $this->oBdd->retDernierId();
	}
	
	function copierChats ($v_iIdSousActiv)
	{
		if ($this->retType() != LIEN_CHAT && $v_iIdSousActiv < 1)
			return;
		
		$this->initChats();
		
		foreach ($this->aoChats as $oChat)
			$oChat->copier($v_iIdSousActiv);
		
		$this->aoChats = NULL;
	}
	
	function rafraichir () { if ($this->retId() > 0) $this->init(); }
	
	function initGlossaire ($v_bInitElements=FALSE)
	{
		$this->oGlossaire = NULL;
		
		if ($this->retType() == LIEN_GLOSSAIRE)
		{
			$sRequeteSql = "SELECT Glossaire.*"
				." FROM Glossaire"
				." LEFT JOIN SousActiv_Glossaire USING (IdGlossaire)"
				." WHERE SousActiv_Glossaire.IdSousActiv='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->oGlossaire = new CGlossaire($this->oBdd);
				$this->oGlossaire->init($oEnreg);
				
				if ($v_bInitElements)
					$this->oGlossaire->initElements();
			}
			
			$this->oBdd->libererResult($hResult);
		}
	}
	
	/**
	 * Associer un glossaire à la sous-activité
	 *
	 * @param v_iIdGlossaire Numéro d'identifiant du glossaire
	 */
	function associerGlossaire ($v_iIdGlossaire)
	{
		$iIdSousActiv = $this->retId();
		
		if (is_numeric($v_iIdGlossaire) &&
			$v_iIdGlossaire > 0 &&
			$iIdSousActiv > 0)
		{
			$sRequeteSql = "REPLACE INTO SousActiv_Glossaire SET"
				." IdSousActiv='{$iIdSousActiv}'"
				.", IdGlossaire='{$v_iIdGlossaire}'";
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function effacerGlossaire ()
	{
		$sRequeteSql = "DELETE FROM SousActiv_Glossaire"
			." WHERE IdSousActiv='".$this->retId()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	// {{{ Ressources
	function initRessources ($v_sTri="date",$v_iTypeTri=NULL,$v_iModalite=0,$v_iIdPers=0,$v_iStatut=0,$v_sDate=0)
	{
		$sTablesSupplementaire = NULL;
		
		// Sur quel champ trier ?
		switch ($v_sTri)
		{
			case "titre": $sTrier = "Ressource.NomRes"; break;
			case "auteur": $sTrier = "Personne.Nom"; $sTablesSupplementaire = " LEFT JOIN Personne USING(IdPers)"; break;
			case "etat":
			case "statut": $sTrier = "Ressource_SousActiv.StatutResSousActiv"; break;
			default: $sTrier = "Ressource.DateRes";
		}
		
		$sStatutSql = NULL;
		
		switch ($v_iStatut)
		{
			case STATUT_RES_ORIGINAL:
			case STATUT_RES_APPROF:
			case STATUT_RES_EN_COURS:
			case STATUT_RES_SOUMISE:
			case STATUT_RES_ACCEPTEE:
			case STATUT_RES_TRANSFERE: $sStatutSql = " AND Ressource_SousActiv.StatutResSousActiv='{$v_iStatut}'"; break;
			case TRANSFERT_FICHIERS: $sStatutSql = " AND Ressource_SousActiv.StatutResSousActiv IN ('".STATUT_RES_ACCEPTEE."', '".STATUT_RES_TRANSFERE."')"; break;
			case STATUT_RES_EVALUEE: $sStatutSql = " AND Ressource_SousActiv.StatutResSousActiv IN ('".STATUT_RES_ACCEPTEE."', '".STATUT_RES_APPROF."')"; break;
			case STATUT_RES_ORIGINAL: $sStatutSql = " AND Ressource_SousActiv.StatutResSousActiv='".STATUT_RES_ORIGINAL."'"; break;
		}
		
		$sDateSql = NULL;
		
		if ($v_sDate > 0)
			$sDateSql = " AND Ressource.DateRes >= '{$v_sDate}%'";
		
		$sModalite = NULL;
		
		if (MODALITE_INDIVIDUEL == $v_iModalite)
			$sModalite = " AND Ressource.IdPers='{$v_iIdPers}'";
		else if (MODALITE_PAR_EQUIPE == $v_iModalite)
		{
			$sTablesSupplementaire .= " LEFT JOIN Equipe_Membre ON Equipe_Membre.IdPers=Ressource.IdPers";
			$sModalite = " AND Equipe_Membre.IdEquipe='{$v_iIdPers}'";
		}
		
		$sRequeteSql = "SELECT Ressource_SousActiv.*, Ressource.*"
			." FROM Ressource_SousActiv"
			." LEFT JOIN Ressource USING (IdRes)"
			.$sTablesSupplementaire
			." WHERE Ressource_SousActiv.IdSousActiv='".$this->retId()."'"
			.$sModalite
			.$sStatutSql
			.$sDateSql
			." ORDER BY ".$sTrier
			.($v_iTypeTri == TRI_DECROISSANT ? " DESC" : " ASC");
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$this->aoRessources = array();
		
		$iIdxRes = 0;
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoRessources[$iIdxRes] = new CRessourceSousActiv($this->oBdd);
			$this->aoRessources[$iIdxRes]->init($oEnreg);
			$this->aoRessources[$iIdxRes]->initExpediteur();
			$iIdxRes++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxRes;
	}
	// }}}
	
	function ajouter ($v_iIdActiv)
	{
		$sRequeteSql = "INSERT INTO SousActiv SET"
			." IdSousActiv=NULL"
			.", NomSousActiv='".mysql_escape_string(INTITULE_SOUS_ACTIV." sans nom")."'"
			.", DateDebSousActiv=NOW()"
			.", DateFinSousActiv=NOW()"
			.", IdTypeSousActiv='0'"
			.", StatutSousActiv='".STATUT_IDEM_PARENT."'"
			.", VotesMinSousActiv='100'"
			.", PremierePageSousActiv='0'"
			.", IdActiv='{$v_iIdActiv}'"
			.", OrdreSousActiv='".($this->retNombreLignes($v_iIdActiv)+1)."'"
			.", ModaliteSousActiv='".MODALITE_IDEM_PARENT."'";
		$this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId();
		$this->init();
		return $this->retId();
	}
	
	function retNombreLignes ($v_iNumParent=NULL)
	{
		if ($v_iNumParent == NULL)
			$v_iNumParent = $this->retIdParent();
		
		if ($v_iNumParent == NULL)
			return FALSE;
		
		$sRequeteSql = "SELECT COUNT(*) FROM SousActiv"
			." WHERE IdActiv='{$v_iNumParent}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbrLignes = $this->oBdd->retEnregPrecis($hResult,0);
		$this->oBdd->libererResult($hResult);
		
		return $iNbrLignes;
	}
	
	function initIdsParents ()
	{
		$sRequeteSql = "SELECT Module.IdForm"
			.", Module.IdMod"
			.", Module_Rubrique.IdRubrique"
			.", Activ.IdActiv"
			." FROM SousActiv"
			." LEFT JOIN Activ USING (IdActiv)"
			." LEFT JOIN Module_Rubrique USING (IdRubrique)"
			." LEFT JOIN Module USING (IdMod)"
			." WHERE SousActiv.IdSousActiv='".$this->retId()."'"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->oIdsParents = $this->oBdd->retEnregSuiv($hResult);
		$this->oBdd->libererResult($hResult);
		return $this->oIdsParents;
	}
	
	function effacer ()
	{
		switch ($this->retType())
		{
			case LIEN_CHAT: $this->effacerChats(); break;
			case LIEN_FORUM : $this->effacerForum(); break;
			case LIEN_COLLECTICIEL: $this->effacerCollecticiel(); break;
			case LIEN_GALERIE: $this->effacerGalerie(); break;
				
			default :
				// Effacer le fichier
				$this->initIdsParents();
				list($sNomFichier) = explode(";",$this->retDonnees());
				$sFichierASupprimer = dir_cours($this->oIdsParents->IdActiv,$this->oIdsParents->IdForm,$sNomFichier,TRUE);
				@unlink($sFichierASupprimer);
				unset($oIdsParents,$sFichierASupprimer,$sNomFichier);
		}
		
		// Effacer les équipes
		$this->effacerEquipes();
		
		// Effacer cette sous-activité
		$this->effacerSousActiv();
		
		$this->redistNumsOrdre();
	}
	
	function effacerSousActiv ()
	{
		$sRequeteSql = "DELETE FROM SousActiv"
			." WHERE IdSousActiv='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerCollecticiel ()
	{
		$this->initCollecticiel();
		$this->oCollecticiel->effacer();
		$this->oCollecticiel = NULL;
	}
	
	function effacerGalerie ()
	{
		$this->initGalerie();
		$this->oGalerie->effacer();
		$this->oGalerie = NULL;
	}
	
	function effacerEquipes ()
	{
		$oEquipe = new CEquipe($this->oBdd);
		$oEquipe->effacerParNiveau(TYPE_SOUS_ACTIVITE,$this->iId);
		$oEquipe = NULL;
	}
	
	function enregistrerEvaluation ($v_iIdResSousActiv,$v_iIdPers,$v_sApprec,$v_sComment,$v_iStatutRes)
	{
		$v_sApprec  = MySQLEscapeString($v_sApprec);
		$v_sComment = MySQLEscapeString($v_sComment);
		
		$sRequeteSql = "LOCK TABLES"
			." Ressource_SousActiv_Evaluation WRITE"
			.", Ressource_SousActiv WRITE";
		$this->oBdd->executerRequete($sRequeteSql);
		
		/*if (empty($v_sApprec) && empty($v_sComment))
		{
			// Effacer le commentaire de ce document du tuteur actuel
			$sRequeteSql = "DELETE FROM Ressource_SousActiv_Evaluation"
				." WHERE IdResSousActiv='{$v_iIdResSousActiv}'"
				." AND IdPers='{$v_iIdPers}'";
			$this->oBdd->executerRequete($sRequeteSql);
			
			// Vérifier que d'autres tuteurs n'ont pas déposés eux aussi un commentaire
			$sRequeteSql = "SELECT COUNT(*)"
				." FROM Ressource_SousActiv_Evaluation"
				." WHERE IdResSousActiv='{$v_iIdResSousActiv}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$iNbEvals = $this->oBdd->retEnregPrecis($hResult,0);
			$this->oBdd->libererResult($hResult);
			
			if ($iNbEvals < 1)
			{
				// Dans le cas où il n'y a plus de commentaire
				// modifié l'état du document en "Soumis au tuteur"
				$sRequeteSql = "UPDATE Ressource_SousActiv"
					." SET StatutResSousActiv='".STATUT_RES_SOUMISE."'"
					." WHERE IdResSousActiv='{$v_iIdResSousActiv}'";
				$this->oBdd->executerRequete($sRequeteSql);
			}
		}
		else
		{*/
			// Retourner le nombre d'évaluations
			$sRequeteSql = "SELECT COUNT(*)"
				." FROM Ressource_SousActiv_Evaluation"
				." WHERE IdResSousActiv='{$v_iIdResSousActiv}'"
				." AND IdPers='{$v_iIdPers}'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$iNbEvals = $this->oBdd->retEnregPrecis($hResult, 0);
			$this->oBdd->libererResult($hResult);
			
			if ($iNbEvals == 1)
				$sRequeteSql = "UPDATE Ressource_SousActiv_Evaluation"
					." SET DateEval=NOW()"
					.", AppreciationEval='{$v_sApprec}'"
					.", CommentaireEval='{$v_sComment}'"
					." WHERE IdResSousActiv='{$v_iIdResSousActiv}'"
					." AND IdPers='{$v_iIdPers}'";
			else
				$sRequeteSql = "INSERT INTO Ressource_SousActiv_Evaluation"
					." (IdResSousActiv, IdPers, DateEval, AppreciationEval, CommentaireEval)"
					." VALUES"
					." ('{$v_iIdResSousActiv}','{$v_iIdPers}',NOW(),'{$v_sApprec}','{$v_sComment}')";
			
			$this->oBdd->executerRequete($sRequeteSql);
			
			$sRequeteSql = "UPDATE Ressource_SousActiv"
				." SET StatutResSousActiv='{$v_iStatutRes}'"
				." WHERE IdResSousActiv='{$v_iIdResSousActiv}'"
				." AND StatutResSousActiv<>'{$v_iStatutRes}'";
			$this->oBdd->executerRequete($sRequeteSql);
		/*}*/
		
		$this->oBdd->executerRequete("UNLOCK TABLES");
	}
	
	// {{{ Forum
	function ajouterForum ()
	{
		$oForum = new CForum($this->oBdd);
		$oForum->ajouter(
			$this->retNom()
			,$this->retModalite()
			,$this->retStatut()
			,'1'
			,0
			,0
			,$this->retId()
			,0
			,$this->retIdPers()
		);
	}
	
	function initForum ()
	{
		$this->oForum = new CForum($this->oBdd);
		$this->oForum->initForumParType(TYPE_SOUS_ACTIVITE,$this->retId());
	}
	
	function copierForum ($v_iIdSousActiv)
	{
		if ($this->retType() != LIEN_FORUM && $v_iIdSousActiv < 1)
			return;
		
		$oForum = new CForum($this->oBdd);
		$oForum->initForumParType(TYPE_SOUS_ACTIVITE,$this->iId);
		$oForum->ajouter(
			$oForum->retNom()
			, $oForum->retModalite()
			, $oForum->retStatut()
			, $oForum->retAccessibleVisiteurs()
			, 0
			, 0
			, $v_iIdSousActiv
			, 0
			, $this->retIdPers()
		);
	}
	
	function effacerForum ()
	{
		$this->initForum();
		if (is_object($this->oForum))
			$this->oForum->effacerForum();
		$this->oForum = NULL;
	}
	// }}}
	
	// {{{ Chats
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
	// }}}
	
	// {{{ Collecticiel
	function initCollecticiel () { $this->oCollecticiel = new CCollecticiel($this->oBdd,$this->retId()); }
	
	function retStatutResPlusHaut ($v_miIdPers=NULL)
	{
		$sListePers = NULL;
		$iStatutResPlusHaut = 0;
		
		if (is_array($v_miIdPers))
			foreach ($v_miIdPers as $iIdPers)
				$sListePers .= (isset($sListePers) ? ", " : NULL)
					."'{$iIdPers}'";
		else
			$sListePers = "'{$v_miIdPers}'";
		
		if (isset($sListePers))
		{
			$sRequeteSql = "SELECT StatutResSousActiv"
				.", MAX(StatutResSousActiv) AS MaxStatutResSousActiv"
				.", COUNT(*) AS CountStatutResSousActiv"
				.", Ressource.IdPers AS IdPersStatutResSousActiv"
				." FROM Ressource_SousActiv"
				." LEFT JOIN Ressource USING (IdRes)"
				." WHERE Ressource_SousActiv.IdSousActiv='".$this->retId()."'"
				." AND Ressource.IdPers IN ({$sListePers})"
				." AND Ressource_SousActiv.StatutResSousActiv<>'".STATUT_RES_TRANSFERE."'"
				." GROUP BY Ressource_SousActiv.StatutResSousActiv"
				." ORDER BY Ressource_SousActiv.StatutResSousActiv DESC";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$oEnreg = $this->oBdd->retEnregSuiv($hResult);
			$iStatutResPlusHaut = $oEnreg->MaxStatutResSousActiv;
			$iNbStatutResPlusHaut = $oEnreg->CountStatutResSousActiv;
			$iIdPersStatutResSousActiv = $oEnreg->IdPersStatutResSousActiv;
			$this->oBdd->libererResult($hResult);
		}
		
		return array(
				"StatutResPlusHaut" => $iStatutResPlusHaut
				,"StatutResPlusHautNb" => $iNbStatutResPlusHaut
				,"StatutResPlusHautIdPers" => $iIdPersStatutResSousActiv
			);
	}
	// }}}
	
	function initGalerie () { $this->oGalerie = new CGalerie($this->oBdd,$this->retId()); }
	
	function retStatutReel ()
	{
		if (STATUT_IDEM_PARENT == ($iStatut = $this->retStatut()))
			return $this->oActivParente->retStatut();
		else
			return $iStatut;
	}
	
	function voterPourRessource ($v_iIdResSousActiv,$v_iIdVotant)
	{
		if ($v_iIdResSousActiv < 1)
			return;
		
		// le vote se passe en plusieurs étapes et on utilise plusieurs tables
		// pendant l'opération, donc on les locke (on est obligé de locker TOUS les
		// alias d'une même table)
		$sRequeteSql = "LOCK TABLES"
			." Ressource_SousActiv READ"
			.", Ressource_SousActiv_Vote WRITE";
		$this->oBdd->executerRequete($sRequeteSql);
		
		// on efface tous éventuels votes de cette personne pour des documents
		// de cette sous-activité, car il n'y a qu'un vote par personne par sous-activité
		$sRequeteSql = "SELECT Ressource_SousActiv.IdResSousActiv"
			." FROM Ressource_SousActiv"
			." LEFT JOIN Ressource_SousActiv_Vote USING (IdResSousActiv)"
			." WHERE Ressource_SousActiv.IdSousActiv=\"".$this->retId()."\""
			." AND Ressource_SousActiv_Vote.IdPers='{$v_iIdVotant}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($this->oBdd->retNbEnregsDsResult($hResult))
		{
			$sListeIds = NULL;
			
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
				$sListeIds .= (isset($sListeIds) ? ", " : NULL)
					."'".$oEnreg->IdResSousActiv."'";
			
			$this->oBdd->libererResult($hResult);
			
			$sRequeteSql = "DELETE FROM Ressource_SousActiv_Vote"
				." WHERE IdResSousActiv IN ({$sListeIds})"
				." AND IdPers='{$v_iIdVotant}'";
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		// quand il n'y a plus de votes de cette personne pour un document de ce
		// collecticiel, on insère le nouveau vote
		$sRequeteSql = "INSERT INTO Ressource_SousActiv_Vote"
			." (IdResSousActiv, IdPers)"
			." VALUES"
			." ('{$v_iIdResSousActiv}', '{$v_iIdVotant}')";
		$this->oBdd->executerRequete($sRequeteSql);
		
		// quand on a fini ces multiples opération, on unlocke
		$this->oBdd->executerRequete("UNLOCK TABLES");
		
		// ensuite, on va modifier le statut du document en fonction de
		// la modalite de travail et du nombre de votes requis
		return $this->majStatutRessource($v_iIdResSousActiv);
	}
	
	function majStatutRessource ($v_iIdResSousActiv)
	{
		// la fonction retourna true si le document est soumis (votes suffisants)
		$bSoumis   = FALSE;
		$iModalite = $this->retModalite(TRUE);
		
		if (MODALITE_INDIVIDUEL == $iModalite)
		{
			$iNbVotesRequis = 1;
			
			$sRequeteSql = "LOCK TABLES"
				." Ressource READ"
				.", Ressource_SousActiv WRITE"
				.", Ressource_SousActiv_Vote READ";
			$this->oBdd->executerRequete($sRequeteSql);
			
			$sRequeteSql = "SELECT COUNT(*)"
				." FROM Ressource_SousActiv_Vote"
				." LEFT JOIN Ressource_SousActiv USING (IdResSousActiv)"
				." LEFT JOIN Ressource USING (IdRes)"
				." WHERE Ressource_SousActiv_Vote.IdResSousActiv='{$v_iIdResSousActiv}'";
		}
		else if (MODALITE_PAR_EQUIPE == $iModalite)
		{
			// {{{ Composer la liste des membres
			$sListeMembres = NULL;
			
			foreach ($this->oEquipe->aoMembres as $oMembre)
				$sListeMembres .= (isset($sListeMembres) ? ", " : NULL)
					."'".$oMembre->retId()."'";
			// }}}
			
			$iNbVotesRequis = $this->retVotesMinReels();
			
			$sRequeteSql = "LOCK TABLES"
				." Ressource_SousActiv_Vote READ"
				.", Ressource_SousActiv WRITE";
			$this->oBdd->executerRequete($sRequeteSql);
			
			$sRequeteSql = "SELECT COUNT(*)"
				." FROM Ressource_SousActiv_Vote"
				." WHERE IdResSousActiv='{$v_iIdResSousActiv}'"
				." AND IdPers IN ({$sListeMembres})";
		}
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$iNbVotes = $this->oBdd->retEnregPrecis($hResult,0);
		
		$this->oBdd->libererResult($hResult);
		
		if ($iNbVotes >= $iNbVotesRequis)
		{
			$bSoumis = TRUE;
			
			$sRequeteSql = "UPDATE Ressource_SousActiv"
				." SET StatutResSousActiv='".STATUT_RES_SOUMISE."'"
				." WHERE IdResSousActiv='{$v_iIdResSousActiv}'"
				." AND StatutResSousActiv='".STATUT_RES_EN_COURS."'";
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$this->oBdd->executerRequete("UNLOCK TABLES");
		
		return $bSoumis;
	}
	
	function retVotesMinReels ()
	{
		// Combien de votes nécessaires pour soumettre un document ?
		// = [nb de membres de l'équipe de l'utilisateur connecté] * ([%tage requis] / 100)
		// si on obtient des décimales, on arrondit à 1 vote supplémentaire
		$iNbMembres = count($this->oEquipe->aoMembres);
		$iNbVotes = ceil($iNbMembres * ($this->retVotesMin() / 100));
		return ($iNbVotes > 0 ? $iNbVotes : 1);
	}
	
	function mettre_a_jour ($v_sNomChamp,$v_mValeurChamp,$v_iIdSousActiv=0)
	{
		if ($v_iIdSousActiv < 1)
			$v_iIdSousActiv = $this->retId();
		
		if ($v_iIdSousActiv < 1)
			return FALSE;
		
		$sRequeteSql = "UPDATE SousActiv SET"
			." {$v_sNomChamp}='".MySQLEscapeString($v_mValeurChamp)."'"
			." WHERE IdSousActiv='{$v_iIdSousActiv}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	
	function retIdPers () { return (is_numeric($this->oEnregBdd->IdPers) ? $this->oEnregBdd->IdPers : 0); }
	function setIdPers ($v_iIdPers) { $this->oEnregBdd->IdPers=$v_iIdPers; }
	
	function retDonnees ($v_bHtmlEntities=FALSE)
	{
		return ($v_bHtmlEntities
			? htmlentities($this->oEnregBdd->DonneesSousActiv)
			: "{$this->oEnregBdd->DonneesSousActiv};;;");
	}
	
	function defDonnees ($v_sDonnees) {	$this->mettre_a_jour("DonneesSousActiv",$v_sDonnees); }
	function retVotesMin () { return $this->oEnregBdd->VotesMinSousActiv; }
	
	function defStatut ($v_iStatut)
	{
		if (is_numeric($v_iStatut))
			$this->mettre_a_jour("StatutSousActiv",$v_iStatut);
	}
	
	function retStatut () { return $this->oEnregBdd->StatutSousActiv; }
	function retType () { return $this->oEnregBdd->IdTypeSousActiv; }
	
	function defType ($v_iIdType)
	{
		if (!is_numeric($v_iIdType))
			return;
		
		$iIdType = $this->retType();
		
		if ($iIdType != $v_iIdType)
		{
			if ($iIdType == LIEN_FORUM)
				$this->effacerForum();
			else if ($iIdType == LIEN_GALERIE)
				$this->effacerGalerie();
			else if ($iIdType == LIEN_CHAT)
				$this->effacerChats();
		}
		$this->mettre_a_jour("IdTypeSousActiv",$v_iIdType);
	}
	
	function retDateDeb ()
	{
		$sDateDeb = substr($this->oEnregBdd->DateDebSousActiv,0,10);
		return ereg_replace('^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})$','\\3-\\2-\\1',$sDateDeb);
	}
	
	function defDateDeb ($v_dDateDeb)
	{
		if (isset($v_dDateDeb))
		{
			$v_dDateDeb = ereg_replace('^([0-9]{1,2})-([0-9]{1,2})-([0-9]{2,4})$','\\3-\\2-\\1', $v_dDateDeb);
			$this->mettre_a_jour("DateDebSousActiv",$v_dDateDeb);
		}
	}
	
	function retDateFin ()
	{
		$sDateFin = substr($this->oEnregBdd->DateFinSousActiv,0,10);
		return ereg_replace('^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})$','\\3-\\2-\\1',$sDateFin);
	}
	
	function defDateFin ($v_dDateFin)
	{
		if (isset($v_dDateFin))
		{
			$v_dDateFin = ereg_replace('^([0-9]{1,2})-([0-9]{1,2})-([0-9]{2,4})$','\\3-\\2-\\1', $v_dDateFin);
			$this->mettre_a_jour("DateFinSousActiv",$v_dDateFin);
		}
	}
	
	function retNom ($v_bHtmlEntities=FALSE) { return ($v_bHtmlEntities ? htmlentities($this->oEnregBdd->NomSousActiv) : $this->oEnregBdd->NomSousActiv); }
	function retNumOrdre () { return $this->oEnregBdd->OrdreSousActiv; }
	function retDescr ($v_bHtmlEntities=FALSE) { return ($v_bHtmlEntities ? htmlentities($this->oEnregBdd->DescrSousActiv) : $this->oEnregBdd->DescrSousActiv); }
	function retIdParent () { return $this->oEnregBdd->IdActiv; }
	
	function defNom ($v_sNomSousActiv)
	{
		$v_sNomSousActiv = trim(stripslashes($v_sNomSousActiv));
		
		if (empty($v_sNomSousActiv))
			$v_sNomSousActiv = INTITULE_SOUS_ACTIV." sans nom";
		
		if (isset($v_sNomSousActiv))
			$this->mettre_a_jour("NomSousActiv",$v_sNomSousActiv);
	}
	
	function defDescr ($v_sDescrSousActiv) { $this->mettre_a_jour("DescrSousActiv",trim(stripslashes($v_sDescrSousActiv))); }
	function defNumOrdre ($v_iOrdre) { $this->mettre_a_jour("OrdreSousActiv",$v_iOrdre); }
	
	function defPremierePage ($v_bPremierePage,$v_iIdRubrique)
	{
		if ($v_bPremierePage == $this->retPremierePage() || $v_iIdRubrique < 1)
			return;
		
		$sRequeteSql = "SELECT Activ.IdActiv FROM Module_Rubrique, Activ"
			." WHERE Module_Rubrique.IdRubrique=Activ.IdRubrique"
			." AND Module_Rubrique.IdRubrique='{$v_iIdRubrique}'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSql = NULL;
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			if (!empty($sRequeteSql))
				$sRequeteSql .= " OR";
			
			$sRequeteSql .= " IdActiv=".$oEnreg->IdActiv;
		}
		
		if (!empty($sRequeteSql))
		{
			$sRequeteSql = "UPDATE SousActiv"
				." SET PremierePageSousActiv='0'"
				." WHERE".$sRequeteSql;
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$this->oBdd->libererResult ($hResult);
		
		if ($v_bPremierePage > 0)
			$this->mettre_a_jour("PremierePageSousActiv",1);
	}
	
	function retPremierePage () { return (bool)$this->oEnregBdd->PremierePageSousActiv; }
	
	function initActiv () { $this->oActivParente = new CActiv($this->oBdd,$this->retIdParent()); }
	
	function retListeSousActivs ($v_iIdActiv=NULL,$v_iTypeSousActiv=NULL)
	{
		if ($v_iIdActiv == NULL)
			$v_iIdActiv = $this->retIdParent();
		
		if (!isset($v_iIdActiv))
			return 0;
		
		$sRequeteSql = "SELECT * FROM SousActiv"
			." WHERE IdActiv='{$v_iIdActiv}'"
			.($v_iTypeSousActiv == NULL ? NULL : " AND IdTypeSousActiv='{$v_iTypeSousActiv}'")
			." ORDER BY OrdreSousActiv ASC";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$this->aoSousActivs = array();
		
		$i = 0;
		
		while ($this->aoSousActivs[$i++] = $this->oBdd->retEnregSuiv($hResult))
			;
		
		return ($i-1);
	}
	
	function retIdEnregPrecedent ()
	{
		if (($cpt = $this->retListeSousActivs()) < 0)
			return 0;
		$cpt--;
		return (($cpt < 0) ?  0 : $this->aoSousActivs[$cpt]->IdSousActiv);
	}
	
	function redistNumsOrdre ($v_iNouveauNumOrdre=NULL)
	{
		if ($v_iNouveauNumOrdre == $this->retNumOrdre())
			return FALSE;
		
		if (($cpt = $this->retListeSousActivs()) < 0)
			return FALSE;
		
		// *************************************
		// Ajouter dans ce tableau les ids et les numéros d'ordre
		// *************************************
		
		$aoNumsOrdre = array();
		
		for ($i=0; $i<$cpt; $i++)
			$aoNumsOrdre[$i] = array($this->aoSousActivs[$i]->IdSousActiv,$this->aoSousActivs[$i]->OrdreSousActiv);
		
		// *************************************
		// Mettre à jour dans la table
		// *************************************
		
		if ($v_iNouveauNumOrdre > 0)
		{
			$aoNumsOrdre = redistNumsOrdre($aoNumsOrdre,$this->retNumOrdre(),$v_iNouveauNumOrdre);
			for ($i=0; $i<$cpt; $i++)
				$this->mettre_a_jour("OrdreSousActiv",$aoNumsOrdre[$i][1],$aoNumsOrdre[$i][0]);
			$this->defNumOrdre($v_iNouveauNumOrdre);
		}
		else
			for ($i=0; $i<$cpt; $i++)
				$this->mettre_a_jour("OrdreSousActiv",($i+1),$aoNumsOrdre[$i][0]);
		
		return TRUE;
	}
	
	function retInfoBulle ($v_bHtmlEntities=FALSE) { return ($v_bHtmlEntities ? htmlentities($this->oEnregBdd->InfoBulleSousActiv) : $this->oEnregBdd->InfoBulleSousActiv); }
	
	function defInfoBulle ($v_sInfoBulle=NULL) { $this->mettre_a_jour("InfoBulleSousActiv",$v_sInfoBulle); }
	function defModalite ($v_iModalite) { $this->mettre_a_jour("ModaliteSousActiv",$v_iModalite); }
	
	function retModalite ($v_bModaliteParente=FALSE)
	{
		$iIdModalite = $this->oEnregBdd->ModaliteSousActiv;
		
		if ($v_bModaliteParente && MODALITE_IDEM_PARENT == $iIdModalite)
		{
			$oActiv = new CActiv($this->oBdd,$this->retIdParent());
			$iIdModalite = $oActiv->retModalite();
		}
		
		return $iIdModalite;
	}
	
	
	function initAuteur () { $this->oAuteur = new CPersonne($this->oBdd,$this->retIdPers()); }
	
	// {{{ Inscrits non autorisés
	function ajouterInscritsNonAutorises ($v_aiIdInscritsNonAutorises)
	{
		$iIdSousActiv = $this->retId();
		
		// Vider la table
		$sRequeteSql = "DELETE FROM SousActivInvisible"
			." WHERE IdSousActiv='{$iIdSousActiv}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		// Inscrire les personnes qui n'ont pas le droit de cliquer sur cette sous-activité
		$sValeursRequete = NULL;
		
		if (isset($v_aiIdInscritsNonAutorises))
			foreach ($v_aiIdInscritsNonAutorises as $iIdInscritNonAutorise)
				$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
					."('{$iIdSousActiv}','{$iIdInscritNonAutorise}')";
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "INSERT INTO SousActivInvisible"
				." (IdSousActiv,IdPers)"
				." VALUES {$sValeursRequete}";
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function initInscritsNonAutorises ()
	{
		$iIdxInscritNonAutorise = 0;
		$this->aoInscritsNonAutorises = array();
		
		$sRequeteSql = "SELECT Personne.*"
			." FROM SousActivInvisible"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE SousActivInvisible.IdSousActiv='".$this->retId()."'"
			." ORDER BY Personne.Nom ASC, Personne.Prenom ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoInscritsNonAutorises[$iIdxInscritNonAutorise] = new CPersonne($this->oBdd);
			$this->aoInscritsNonAutorises[$iIdxInscritNonAutorise]->init($oEnreg);
			$iIdxInscritNonAutorise++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxInscritNonAutorise;
	}
	// }}}
	
	function retListeModalites ()
	{
		return array(
			array(MODALITE_IDEM_PARENT,"même modalité que le ".strtolower(INTITULE_ACTIV))
			/*, array(MODALITE_INDIVIDUEL,"individuel")
			, array(MODALITE_PAR_EQUIPE,"par &eacute;quipe")*/
		);
	}
	
	function retTexteType ($v_iType=NULL)
	{
		if (empty($v_iType))
			$v_iType = $this->oEnregBdd->IdTypeSousActiv;
		
		$aaListeTypes = $this->retListeTypes();
		
		foreach ($aaListeTypes as $amTypes)
			if ($amTypes[0] == $v_iType)
				return $amTypes[1];
	}
	
	function retListeTypes ($v_iIdRubrique=NULL)
	{
		return array(
			  array(LIEN_PAGE_HTML,"Choisissez un type pour cette ".strtolower(INTITULE_SOUS_ACTIV))
			, array(LIEN_PAGE_HTML,"Affichage d'un document déposé sur le serveur")
			, array(LIEN_TEXTE_FORMATTE,"Texte formaté")
			, array(LIEN_DOCUMENT_TELECHARGER,"Document à télécharger")
			, array(LIEN_SITE_INTERNET,"Lien vers un site Internet")
			, array(LIEN_COLLECTICIEL,"Collecticiel")
			, array(LIEN_GALERIE,"Galerie")
			, array(LIEN_CHAT,"Chat")
			, array(LIEN_FORUM,"Forum")
			, array(LIEN_FORMULAIRE,"Activités en ligne")
			/*, array(LIEN_GLOSSAIRE,"Glossaire")*/
			, array(LIEN_TABLEAU_DE_BORD,"Tableau de bord")
		);
	}
	
	function retListeStatuts ()
	{
		return array(
			  array(STATUT_FERME,"Fermé")
			, array(STATUT_OUVERT,"Ouvert")
			, array(STATUT_INVISIBLE,"Invisible")
			, array(STATUT_IDEM_PARENT,"Même statut que le ".strtolower(INTITULE_ACTIV))
		);
	}
	
	function retListeModes ()
	{
		return array(
			  array(FRAME_CENTRALE_DIRECT,"Zone de cours (1 temps)",0)
			, array(FRAME_CENTRALE_INDIRECT,"Zone de cours (2 temps)",1)
			, array(NOUVELLE_FENETRE_DIRECT,"Nouvelle fenêtre (1 temps)",0)
			, array(NOUVELLE_FENETRE_INDIRECT,"Nouvelle fenêtre (2 temps)",1)
		);
	}
	
	function retListeDeroulements ()
	{
		list(,$bSoumissionManuelle) = explode(";",$this->oEnregBdd->DonneesSousActiv);
		return array(
			array(SOUMISSION_AUTOMATIQUE,"en un seul temps",($bSoumissionManuelle == SOUMISSION_AUTOMATIQUE))
			/*, array(SOUMISSION_MANUELLE,"en deux temps (par défaut)",(empty($bSoumissionManuelle) || $bSoumissionManuelle == SOUMISSION_MANUELLE))*/
		);
	}
}

?>
