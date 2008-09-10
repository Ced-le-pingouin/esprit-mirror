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

/**
 * @file	rubrique.tbl.php
 * 
 * Contient la classe de gestion des rubriques, en rapport avec la DB
 * 
 * @date	2002/02/18
 * 
 * @author	Cédric FLOQUET
 * @author	Filippo PORCO
 */

require_once(dir_database("module.tbl.php"));
require_once(dir_database("activite.tbl.php"));
require_once(dir_database("equipe.tbl.php"));
require_once(dir_lib("std/FichierInfo.php", TRUE));

define("INTITULE_RUBRIQUE","Unité"); /// Titre qui désigne le troisième niveau de la structure d'une formation 	@enum INTITULE_RUBRIQUE

/**
 * Gestion des rubriques, et encapsulation de la table Module_Rubrique de la DB
 */
class CModule_Rubrique
{
	var $iId;					///< Utilisé dans le constructeur, pour indiquer l'id du module à récupérer dans la DB
	
	var $oBdd;					///< Objet représentant la connexion à la DB
	var $oEnregBdd;				///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	var $iIdForm;				///< Objet initialisé par #initIdForm(), contient l'id de la formation
	
	var $aoRubriques;			///< Tableau rempli par #retListeRubriques(), contenant toutes les rubriques d'un module
	var $aoActivs;				///< Tableau rempli par #initActivs() , contenant tous les activités d'une rubrique
	
	var $aoCollecticiels;		///< Tableau rempli par #initCollecticiels(), contenant tous les collecticiels de la rubrique
	var $aoChats;				///< Tableau rempli par #initChats() ou #initChats2(), contenant tous les chats de la rubrique
	
	var $aoEquipes;				///< Tableau rempli par #initEquipes(), contenant tous les équipes de la rubrique
	var $aoMembres;				///< Tableau rempli par #initMembres(), contenant les personnes rattachées à la rubrique
	
	var $aoFormulaires;			///< Tableau rempli par #initFormulaires(), contenant les formulaires de la rubrique
	var $aoHotpotatoes;			///< Tableau rempli par #initHotpotatoes(), contenant les exercices de la rubrique
	var $oIntitule;				///< Objet de type CIntitule contenant l'intitulé de cette rubrique
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CModule_Rubrique (&$v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iId;
		
		if ($this->iId > 0)
			$this->init();
	}
	
	/**
	 * Initialise l'objet avec un enregistrement de la DB ou un objet PHP existant représentant un tel enregistrement
	 * Voir CPersonne#init()
	 */
	function init ($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdRubrique;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Module_Rubrique"
				." WHERE IdRubrique='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		// Rechercher l'intitulé de la rubrique
		$this->initIntitule();
		
		$this->initIdForm();
	}
	
	/**
	 * Initialise un tableau contenant les équipes de la rubrique
	 * 
	 * @param	v_bInitMembres	si \c true, initialise également les membres de l'équipe (défaut à \c false)
	 * 
	 * @return	le nombre d'équipes insérées dans le tableau
	 */
	function initEquipes ($v_bInitMembres=FALSE)
	{
		$oEquipe = new CEquipe($this->oBdd);
		$iNbrEquipes = $oEquipe->initEquipesEx($this->retId(),TYPE_RUBRIQUE,$v_bInitMembres);
		$this->aoEquipes = $oEquipe->aoEquipes;
		return $iNbrEquipes;
	}
	
	/**
	 * Initialise l'objet \c oIntitule avec l'intitulé de la rubrique 
	 * 
	 * @return	\c true si l'objet a bien été initialisé
	 */
	function initIntitule ()
	{
		$this->oIntitule = NULL;
		if (is_object($this->oEnregBdd))
			$this->oIntitule = new CIntitule($this->oBdd,$this->oEnregBdd->IdIntitule);
		return is_object($this->oIntitule);
	}
	
	/**
	 * @param	v_bAfficherNumOrdre	si \c true, retourne aussi le  n° d'ordre
	 * 								après l'intitulé
	 * @param	v_bPonctuation		si \c true, ajoute deux points après 
	 * 								l'intitulé (et n° d'ordre éventuel)
	 * 
	 * @return	l'intitulé de la rubrique avec éventuellement des infos 
	 * 			supplémentaires
	 */
	function retTexteIntitule($v_bAfficherNumOrdre = TRUE, $v_bPonctuation = FALSE)
	{
		if ($this->retType() != LIEN_UNITE)
			return '';
		
		$sTexteIntitule = $this->oIntitule->retNom();
		
		if ($v_bAfficherNumOrdre && $this->oEnregBdd->NumDepartIntitule > 0)
			$sTexteIntitule .= "&nbsp;{$this->oEnregBdd->NumDepartIntitule}";
		
		if (strlen($sTexteIntitule) && $v_bPonctuation)
			$sTexteIntitule .= ' :';
		
		return $sTexteIntitule;
	}
	
	/**
	 * Permet de connaître le numero d'ordre maximum des rubriques
	 * 
	 * @return	le numéro d'ordre maximum
	 */
	function retNumOrdreMax ($v_iIdMod=NULL)
	{
		if ($v_iIdMod == NULL)
			$v_iIdMod = $this->oEnregBdd->IdMod;
		$sRequeteSql = "SELECT MAX(OrdreRubrique) FROM Module_Rubrique WHERE IdMod='".$v_iIdMod."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iMax = $this->oBdd->retEnregPrecis($hResult,0);
		$this->oBdd->libererResult($hResult);
		return $iMax;
	}
	
	/**
	 * Copie la rubrique courante vers un module, en indiquant un n° d'ordre 
	 * pour la rubrique copiée
	 * 
	 * @param	v_iIdDest	l'id du module de destination
	 * @param	v_iNumOrdre	le n° d'ordre de la copie de la rubrique. Si \c 0
	 * 						(défaut), elle sera dernière dans le module 
	 * 						destination
	 * 
	 * @return	l'id de la copie de la rubrique
	 */
	function copierAvecNumOrdre($v_iIdDest, $v_iNumOrdre = 0)
	{
		// lock tables Formation, Module, Module_Rubrique, Forum, Activ, SousActiv, Chat, Intitule
		$sRequeteSql = "LOCK TABLES Formation WRITE, Module WRITE, Module_Rubrique WRITE, Forum WRITE,"
						." Activ WRITE, SousActiv WRITE, Hotpotatoes WRITE, Chat WRITE, Intitule WRITE";
		$this->oBdd->executerRequete($sRequeteSql);
		
		if (empty($v_iNumOrdre) || !is_int($v_iNumOrdre) || $v_iNumOrdre < 0)
			$iNumOrdre = $this->retNumOrdreMax($v_iIdDest) + 1;
		else
			$iNumOrdre = $v_iNumOrdre;
		
		$iIdNouv = $this->copier($v_iIdDest, TRUE);
		$oNouv = ElementFormation::retElementFormation($this->oBdd, $this->retTypeNiveau(), $iIdNouv);
		ElementFormation::defNumOrdre($oNouv, $iNumOrdre);
		
		$this->oBdd->executerRequete("UNLOCK TABLES");
		
		return $iIdNouv;
	}
	
	/**
	 * Copie la rubrique courante vers un module spécifique
	 * 
	 * @param	v_iIdMod 		l'id du module
	 * @param	v_bRecursive	si \c true, copie aussi les activités associées à la rubrique
	 * 
	 * @return	l'id de la nouvelle rubrique
	 */
	function copier($v_iIdMod, $v_bRecursive = TRUE, $v_sExportation = NULL)
	{
		$iIdRubrique = $this->copierRubrique($v_iIdMod, $v_sExportation);
		
		if ($iIdRubrique < 1 && !$v_sExportation)
			return 0;
		
		if (!$v_sExportation)
		{
			switch ($this->retType())
			{
				case LIEN_FORUM:	$this->copierForum($iIdRubrique);
									break;
				case LIEN_CHAT:		$this->copierChats($iIdRubrique);
									break;
			}
		}
		
		if ($v_bRecursive)
			$this->copierActivites($iIdRubrique, $v_sExportation);
		
		return $iIdRubrique;
	}
	
	/**
	 * Insère une copie d'une rubrique dans la DB
	 * 
	 * @param	v_iIdMod l'id du module
	 * 
	 * @return	l'id de la nouvelle rubrique
	 */
	function copierRubrique($v_iIdMod, $v_sExportation = NULL)
	{
		global $sSqlExportForm;
		
		if ($v_iIdMod < 1 && !$v_sExportation)
			return 0;
			
		$sRequeteSql = "INSERT INTO Module_Rubrique SET"
			." IdRubrique=".(!$v_sExportation?"NULL":"'".$this->retId()."'")
			.", NomRubrique='".MySQLEscapeString($this->retNom())."'"
			//.", IdMod=".(!$v_sExportation?"'{$v_iIdMod}'":"@iIdModuleCourant")
			.", IdMod=".(!$v_sExportation?"'{$v_iIdMod}'":"'".$this->retIdParent()."'")
			.", TypeRubrique='{$this->oEnregBdd->TypeRubrique}'"
			.", DescrRubrique='".MySQLEscapeString($this->oEnregBdd->DescrRubrique)."'"
			.", DonneesRubrique='".MySQLEscapeString($this->oEnregBdd->DonneesRubrique)."'"
			.", StatutRubrique='{$this->oEnregBdd->StatutRubrique}'"
			.", TypeMenuUnite='{$this->oEnregBdd->TypeMenuUnite}'"
			.", NumeroActivUnite='{$this->oEnregBdd->NumeroActivUnite}'"
			.", OrdreRubrique='{$this->oEnregBdd->OrdreRubrique}'"
			.", IdIntitule='{$this->oEnregBdd->IdIntitule}'"
			.", NumDepartIntitule='{$this->oEnregBdd->NumDepartIntitule}'"
			.", IdPers='{$this->oEnregBdd->IdPers}'";
		
		if ($v_sExportation)
		{
			$sSqlExportForm .= $sRequeteSql . ";\n\n";
			$sSqlExportForm .= "SET @iIdRubriqueCourante := LAST_INSERT_ID();\n\n";
			
			return -1;
		}
		else
		{
			$this->oBdd->executerRequete ($sRequeteSql);
			
			return $this->oBdd->retDernierId();
		}
	}
	
	/**
	 * Copie toutes les activités de la rubrique vers une autre
	 * 
	 * @param	v_iIdRubrique l'id de la rubrique de destination
	 */
	function copierActivites($v_iIdRubrique, $v_sExportation = NULL)
	{
		$this->initActivs();
		foreach ($this->aoActivs as $oActiv)
			$oActiv->copier($v_iIdRubrique, TRUE, $v_sExportation);
		$this->aoActivs = NULL;
	}
	
	/**
	 * Copie tout les chats de la rubrique courante vers une autre
	 * 
	 * @param	v_iIdRubrique l'id de la rubrique de destination
	 */
	function copierChats($v_iIdRubrique)
	{
		if ( ($v_iIdRubrique < 1) || ($this->retType() != LIEN_CHAT) )
			return;
		
		$this->initChats();
		
		foreach ($this->aoChats as $oChat)
			$oChat->copier($v_iIdRubrique);
		
		$this->aoChats = NULL;
	}
	
	/**
	 * Copie le forum(= rubrique) vers une autre rubrique
	 * 
	 * @param	v_iIdRubrique l'id de la rubrique de destination
	 */
	function copierForum ($v_iIdRubrique)
	{
		if ($this->retType() != LIEN_FORUM && $v_iIdRubrique > 0)
			return;
		
		$oForum = new CForum($this->oBdd);
		$oForum->initForumParType(TYPE_RUBRIQUE,$this->iId);
		$oForum->ajouter($oForum->retNom()
				, $oForum->retModalite()
				, $oForum->retStatut()
				, $oForum->retAccessibleVisiteurs()
				, 0
				, $v_iIdRubrique
				, 0
				, 0
				, $this->retIdPers()
			);
		$oForum = NULL;
	}
	
	/**
	 * Retourne le nombre de rubriques de ce module 
	 * 
	 * @return	le nombre de rubriques de ce module
	 */
	function retNombreLignes ()
	{
		$sRequeteSql ="SELECT COUNT(*) FROM Module_Rubrique"
			." WHERE IdMod='{$this->oEnregBdd->IdMod}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbrLignes = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		return $iNbrLignes;
	}
	
	/**
	 * Retourne le nombre de rubrique de type lien(LIEN_UNITE)
	 * 
	 * @return	le nombre de rubrique de type lien
	 */
	function retNbrUnites ()
	{
		$sRequeteSql ="SELECT COUNT(*) FROM Module_Rubrique"
			." WHERE IdMod='{$this->oEnregBdd->IdMod}'"
			." AND TypeRubrique='".LIEN_UNITE."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbrUnites = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		return $iNbrUnites;
	}
	
	/**
	 * Initialise la variable \c iIdForm avec l'id de la formation
	 */
	function initIdForm ()
	{
		if (!$this->retIdParent())
			return;
		
		$sRequeteSql = "SELECT Formation.IdForm FROM Formation"
			." LEFT JOIN Module USING (IdForm)"
			." WHERE Module.IdMod=".$this->retIdParent();
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->iIdForm = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
	}
	
	/**
	 * @return	un tableau contenant l'intitulé de la rubrique précédente
	 */
	function retInfosIntituleRubPrecedente ()
	{
		$asInfosIntituleRubPrecedente = array("IdIntitule" => "2" // Unité
			,"NumDepartIntitule" => "1");
		
		// en fait sur une nouvelle install, le 1er intitulé "unité" n'aura pas
		// forcément l'id 2 (???)
		$hResult = $this->oBdd->executerRequete(
			 " SELECT IdIntitule FROM Intitule WHERE TypeIntitule=".TYPE_RUBRIQUE
			." ORDER BY IdIntitule LIMIT 1"
		);
		if ($this->oBdd->retNbEnregsDsResult($hResult))
			$asInfosIntituleRubPrecedente["IdIntitule"] = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
			
		$sRequeteSql = "SELECT IdIntitule, NumDepartIntitule"
			." FROM Module_Rubrique"
			." WHERE IdMod='".$this->retIdModule()."'"
			." AND TypeRubrique='".LIEN_UNITE."'"
			." ORDER BY OrdreRubrique DESC"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($oEnregBdd = $this->oBdd->retEnregSuiv($hResult))
		{
			$iIdIntitule = $oEnregBdd->IdIntitule;
			$iNumDepartIntitule = ($oEnregBdd->NumDepartIntitule > 0
				? $oEnregBdd->NumDepartIntitule + 1
				: 0);
			
			$asInfosIntituleRubPrecedente = array("IdIntitule" => $iIdIntitule
				,"NumDepartIntitule" => $iNumDepartIntitule);
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $asInfosIntituleRubPrecedente;
	}
	
	/**
	 * Insère une nouvelle rubrique
	 * 
	 * @return	l'id de la nouvelle rubrique
	 */
	function ajouter ()
	{
		$asInfosIntituleRubPrecedente = $this->retInfosIntituleRubPrecedente();
		
		$sRequeteSql = "INSERT INTO Module_Rubrique SET"
			." IdRubrique=NULL"
			.", IdMod='".$this->retIdModule()."'"
			.", NomRubrique='".MySQLEscapeString(INTITULE_RUBRIQUE." sans nom")."'"
			.", TypeRubrique='".LIEN_UNITE."'"
			.", OrdreRubrique='".($this->retNombreLignes()+1)."'"
			.", StatutRubrique='".STATUT_OUVERT."'"
			.", DonneesRubrique=''"
			.", TypeMenuUnite='0'"
			.", NumeroActivUnite='0'"
			.", IdIntitule='".$asInfosIntituleRubPrecedente["IdIntitule"]."'"
			.", NumDepartIntitule='".$asInfosIntituleRubPrecedente["NumDepartIntitule"]."'";
		$this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId();
		$this->init();
		return $this->retId();
	}
	
	/**
	 * Initialise un tableau contenant les activités de la rubrique
	 * 
	 * @param	v_iStatut	statut de la rubrique(optionnel)
	 * @param	v_iModalite	modalité(optionnel)
	 * 
	 * @return	le nombre d'activités insérées dans le tableau
	 */
	function initActivs ($v_iStatut=NULL,$v_iModalite=NULL)
	{
		$iIndexActiv = 0;
		$this->aoActivs = array();
		
		$sRequeteSql = "SELECT Formation.IdForm"
			.", Module.IdMod"
			.", Activ.*"
			." FROM Activ"
			." LEFT JOIN Module_Rubrique USING (IdRubrique)"
			." LEFT JOIN Module USING (IdMod)"
			." LEFT JOIN Formation USING (IdForm)"
			." WHERE Module_Rubrique.IdRubrique='{$this->iId}'"
			.(isset($v_iStatut) ? " AND Activ.StatutActiv='$v_iStatut'" : NULL)
			.(isset($v_iModalite) ? " AND Activ.ModaliteActiv='$v_iModalite'" : NULL)
			." ORDER BY Activ.OrdreActiv";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoActivs[$iIndexActiv] = new CActiv($this->oBdd);
			$this->aoActivs[$iIndexActiv]->init($oEnreg);
			$iIndexActiv++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIndexActiv;
	}
	
	/**
	 * Retourne le nombre d'équipes attachées à une rubrique
	 * 
	 * @return	le nombre d'équipes attachées à une rubrique
	 */
	function retNbrEquipes ()
	{
		// Vérifier si au moins une activité a été associé à une équipe
		$iNbrEquipes = 0;
		$this->initActivs(NULL,MODALITE_PAR_EQUIPE);
		for ($iIdxActiv=0; $iIdxActiv<count($this->aoActivs); $iIdxActiv++)
			$iNbrEquipes += $this->aoActivs[$iIdxActiv]->retNbrEquipes();
		return $iNbrEquipes;
	}
	
	/**
	 * Initialise un tableau contenant tous les collecticiels de la rubrique
	 * 
	 * @param	v_iModalite le numéro représentant le type de modalité pour l'activité (voir les constantes MODALITE_)
	 * 
	 * @return	le nombre de collecticiels insérés dans le tableau
	 */
	function initCollecticiels ($v_iModalite=NULL)
	{
		$iIdxCollect = 0;
		$this->aoCollecticiels = array();
		
		$sRequeteSql = "SELECT SousActiv.*"
			." FROM Activ"
			." LEFT JOIN SousActiv USING (IdActiv)"
			." WHERE Activ.IdRubrique='".$this->retId()."'"
			." AND SousActiv.IdTypeSousActiv='".LIEN_COLLECTICIEL."'"
			.(isset($v_iModalite)
				? " AND (SousActiv.ModaliteSousActiv='{$v_iModalite}'"
					." OR (SousActiv.ModaliteSousActiv='".MODALITE_IDEM_PARENT."' AND Activ.ModaliteActiv='{$v_iModalite}'))"
				: NULL)
			." ORDER BY Activ.OrdreActiv ASC, SousActiv.OrdreSousActiv ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoCollecticiels[$iIdxCollect] = new CSousActiv($this->oBdd);
			$this->aoCollecticiels[$iIdxCollect]->init($oEnreg);
			$iIdxCollect++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxCollect;
	}
	
	/**
	 * Initialise un tableau contenant tous les forums de cette rubriqe
	 * 
	 * @param	v_miIdModalite liste des modalités(optionnel), il peut etre de type tableau ou entier
	 * 
	 * @return	le nombre de forums insérés dans le tableau
	 */
	function initForums ($v_miIdModalite=NULL)
	{
		$iIdxForum = 0;
		$iListeIdModalite = NULL;
		$this->aoForums = array();
		
		if (is_array($v_miIdModalite))
			foreach ($v_miIdModalite as $iIdModalite)
				$iListeIdModalite .= (isset($iListeIdModalite) ? ", " : NULL)
					."'{$iIdModalite}'";
		else if (isset($v_miIdModalite))
			$iListeIdModalite = "'{$v_miIdModalite}'";
		
		$sRequeteSql = NULL;
		
		$sRequeteSql = "SELECT Forum.*"
			." FROM Activ"
			." LEFT JOIN SousActiv USING (IdActiv)"
			." LEFT JOIN Forum USING (IdSousActiv)"
			." WHERE Activ.IdRubrique='".$this->retId()."'"
			.(isset($v_miIdModalite) ? " AND Forum.ModaliteForum IN ({$iListeIdModalite})" : NULL)
			." AND Forum.IdSousActiv IS NOT NULL"
			." ORDER BY Activ.OrdreActiv ASC, SousActiv.OrdreSousActiv ASC";
		
		if (isset($sRequeteSql))
		{
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
	
	/**
	 * Efface la totalité d'une rubrique
	 * 
	 * @param	v_iIdRubrique
	 * 
	 * @return	\c true si la rubrique a bien été effacée
	 */
	function effacer ($v_iIdRubrique=NULL)
	{
		if ($v_iIdRubrique == NULL)
			$v_iIdRubrique = $this->retId();
		
		if ($v_iIdRubrique == NULL)
			return FALSE;
		
		// Effacer les équipes
		$this->effacerEquipes();
		
		// Effacer les activités de la rubrique
		$this->effacerActivs();
		
		// Effacer le forum de la rubrique
		$this->effacerForum();
		
		// Effacer la rubrique
		$this->effacerRubrique($v_iIdRubrique);
		
		return TRUE;
	}
	
	/**
	 * Efface une rubrique dans la DB	
	 * 
	 * @param	v_iIdRubrique l'id de la rubrique à effacer
	 */
	function effacerRubrique ($v_iIdRubrique)
	{
		$sRequeteSql = "DELETE FROM Module_Rubrique"
			." WHERE IdRubrique='{$v_iIdRubrique}'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
		$this->redistNumsOrdre();
	}
	
	/**
	 * Efface les équipes associées à cette rubrique
	 */
	function effacerEquipes ()
	{
		$oEquipe = new CEquipe($this->oBdd);
		$oEquipe->effacerParNiveau(TYPE_RUBRIQUE,$this->iId);
	}
	
	/**
	 * Efface les activités de la rubrique
	 */
	function effacerActivs ()
	{
		if ($this->retType() == LIEN_UNITE)
		{
			$iNbrActivs = $this->initActivs();
			for ($idx=0; $idx<$iNbrActivs; $idx++)
			{
				$this->aoActivs[$idx]->defRemettreDeOrdre(FALSE);
				$this->aoActivs[$idx]->effacer();
			}
			$this->aoActivs = NULL;
		}
	}
	
	/**
	 * Efface les forums de la rubrique
	 */
	function effacerForum ()
	{
		$oForum = new CForum($this->oBdd);
		$oForum->initForumParType(TYPE_RUBRIQUE,$this->retId());
		$oForum->effacer();
		$oForum = NULL;
	}
	
	/**
	 * Met à jour un champ de la table Module_Rubrique
	 * 
	 * @param	v_sNomChamp		le nom du champ à mettre à jour
	 * @param	v_mValeurChamp	la nouvelle valeur du champ
	 * @param	v_iIdRubrique	l'id de la rubrique
	 * 
	 * @return	\c true si il a mis à jour le champ dans la DB
	 */
	function mettre_a_jour ($v_sNomChamp,$v_mValeurChamp,$v_iIdRubrique=0)
	{
		if ($v_iIdRubrique < 1)
			$v_iIdRubrique = $this->retId();
		
		if ($v_iIdRubrique < 1)
			return FALSE;
		
		$sRequeteSql = "UPDATE Module_Rubrique SET"
			." {$v_sNomChamp}='".MySQLEscapeString($v_mValeurChamp)."'"
			." WHERE IdRubrique='{$v_iIdRubrique}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	/** @name Fonctions de lecture des champs pour cette rubrique */
	//@{
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retIdRubrique () { return $this->retId(); }
	function retType () { return $this->oEnregBdd->TypeRubrique; }
	function retStatut () { return $this->oEnregBdd->StatutRubrique; }
	function retDescr () { return $this->oEnregBdd->DescrRubrique; }
	function retIdPers () { return $this->oEnregBdd->IdPers; }
	function retIdIntitule () { return $this->oEnregBdd->IdIntitule; }
	function retTypeMenu () { return $this->oEnregBdd->TypeMenuUnite; }
	function retNumeroterActiv () { return $this->oEnregBdd->NumeroActivUnite; }
	function retIdParent () { return $this->oEnregBdd->IdMod; }
	function retIdModule () { return $this->retIdParent(); }

	function retNom ($v_bHtmlEntities=FALSE)
	{
		$sNomRubrique = trim($this->oEnregBdd->NomRubrique);
		if (empty($sNomRubrique))
			$sNomRubrique = "Rubrique/unité sans nom";
		return ($v_bHtmlEntities
			? emb_htmlentities($sNomRubrique)
			: $sNomRubrique);
	}
	
	function retNomComplet ()
	{
		$sIntitule = $this->oIntitule->retNom(FALSE);
		$iNumDepartIntitule = $this->retNumDepart();
		
		return (strlen($sIntitule) > 0 ? "{$sIntitule} " : NULL)
			.($iNumDepartIntitule > 0 ? "$iNumDepartIntitule : " : NULL)
			.$this->oEnregBdd->NomRubrique;
	}

	function retDonnees($v_bHtmlEntities=FALSE)
	{
		return ($v_bHtmlEntities
			? emb_htmlentities($this->oEnregBdd->DonneesRubrique)
			: $this->oEnregBdd->DonneesRubrique);
	}

	function retDonnee($v_iPartie)
	{
		$d = explode(';', $this->retDonnees());
		return $d[$v_iPartie];
	}

	function retNumOrdre ()
	{
		return $this->oEnregBdd->OrdreRubrique;
	}

	function retNumDepart ()
	{
		return $this->oEnregBdd->NumDepartIntitule;
	}
	//@}

	
	/** @name Fonctions de définition des champs pour cette rubrique */
	//@{
	function defNom ($v_sNom)
	{
		$v_sNom = MySQLEscapeString($v_sNom);
		if (empty($v_sNom)) $v_sNom = INTITULE_RUBRIQUE." sans nom";
		$this->mettre_a_jour("NomRubrique",$v_sNom);
	}

	function defDonnees($v_sDonnees)
	{
		$this->mettre_a_jour("DonneesRubrique",$v_sDonnees.":2");
	}

	function defType ($v_iType)
	{
		if (is_numeric($v_iType))
			$this->mettre_a_jour("TypeRubrique",$v_iType);
	}

	function defNumOrdre ($v_iNumOrdre)
	{
		if (is_numeric($v_iNumOrdre))
			$this->mettre_a_jour("OrdreRubrique",$v_iNumOrdre);
	}

	function defStatut ($v_iStatut)
	{
		if (is_numeric($v_iStatut))
			$this->mettre_a_jour("StatutRubrique",$v_iStatut);
	}

	function defNumDepart ($v_iNumDepart)
	{
		if ($v_iNumDepart >= 0 && $v_iNumDepart <= 254)
			$this->mettre_a_jour("NumDepartIntitule",$v_iNumDepart);
	}
	
	function defDescr ($v_sDescr) { $this->mettre_a_jour("DescrRubrique",$v_sDescr); }
	function defIdParent ($v_iIdModule) { $this->oEnregBdd->IdMod = $v_iIdModule; }
	function defIdIntitule ($v_iIdIntitule) { $this->mettre_a_jour("IdIntitule",$v_iIdIntitule); }
	//@}
	
	/**
	 * Retourne la constante qui définit le niveau "rubrique", de la structure d'une formation
	 * 
	 * @return	la constante qui définit le niveau "rubrique", de la structure d'une formation
	 */
	function retTypeNiveau () { return TYPE_RUBRIQUE; }
		
	/**
	 * Retourne le numéro d'ordre des unités de type lien
	 * 
	 * @return	le numéro d'ordre des unités de type lien
	 */
	function retNumOrdreReel ()
	{
		$sRequeteSql = "SELECT * FROM Module_Rubrique WHERE"
			." TypeRubrique='".LIEN_UNITE."'"
			." AND IdMod='".$this->retIdParent()."'"
			." ORDER BY OrdreRubrique ASC";
		
		$iNumOrdreReel = 0;
		
		$iId = $this->retId();
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$iNumOrdreReel++;
			if ($oEnreg->IdRubrique == $iId)
				break;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iNumOrdreReel;
	}
	
	/**
	 * Initialise un tableau contenant la liste des chats de la rubrique
	 * 
	 * @return	le nombre de chats insérés dans le tableau
	 */
	function initChats ()
	{
		$oChat = new CChat($this->oBdd);
		$iNbChats = $oChat->initChats($this);
		$this->aoChats = $oChat->aoChats;
		return $iNbChats;
	}
	
	 
	 /**
	  * Initialise un tableau contenant la liste des chats de la rubrique et ceux des sous-activités
	  * 
	  * @param	v_iIdModalite le numéro(optionnel) représentant le type de modalité pour l'activité (voir les constantes MODALITE_)
	  * 
	  * @return	le nombre de chats insérés dans le tableau
	  */
	function initChats2 ($v_iIdModalite=NULL)
	{
		$iIdxChat = 0;
		$this->aoChats = array();
		
		$sRequeteSql = "SELECT Chat.*"
			." FROM Activ"
			." LEFT JOIN SousActiv USING (IdActiv)"
			." LEFT JOIN Chat USING (IdSousActiv)"
			." WHERE Activ.IdRubrique='".$this->retId()."'"
			." AND SousActiv.IdTypeSousActiv='".LIEN_CHAT."'"
			." AND Chat.IdChat IS NOT NULL"
			.(isset($v_iIdModalite) ? " AND Chat.ModaliteChat='{$v_iIdModalite}'" : NULL)
			." ORDER BY Activ.OrdreActiv ASC, SousActiv.OrdreSousActiv ASC, Chat.OrdreChat ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoChats[$iIdxChat] = new CChat($this->oBdd);
			$this->aoChats[$iIdxChat]->init($oEnreg);
			$iIdxChat++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxChat;
	}
	
	/**
	 * Retourne le nombre de chats de la rubrique
	 * 
	 * @return	le nombre de chats de la rubrique
	 */
	function retNombreChats ()
	{
		$oChat = new CChat($this->oBdd);
		return $oChat->retNombreChats($this);
	}
	
	/**
	 * Ajoute un chat à la rubrique
	 * 
	 * @return	l'id du nouveau chat
	 */
	function ajouterChat ()
	{
		$oChat = new CChat($this->oBdd);
		return $oChat->ajouter($this);
	}
	
	/**
	 * Efface tous les chats de la rubrique
	 */
	function effacerChats ()
	{
		$oChat = new CChat($this->oBdd);
		$oChat->effacerChats($this);
	}
	
	/**
	 * Retourne le texte en français du type de rubrique
	 * 
	 * @param	v_iType le type(optionnel) d'une rubrique
	 * 
	 * @return	le texte du type de rubrique
	 */
	function retTexteType($v_iType=NULL)
	{
		if (empty($v_iType))
			$v_iType = $this->retType();
		
		$aaListeTypes = $this->retListeTypes();
		
		foreach ($aaListeTypes as $amTypes)
			if ($amTypes[0] == $v_iType)
				return $amTypes[1];
	}
	
	 /**
	  * Retourne un tableau contenant la liste des différents types de rubrique. En fait, cette liste se présente elle-même
	  * sous forme de tableaux, contenant chacun le type de rubrique(constante LIEN_), l'intitulé de ce type, et une
	  * valeur booléenne indiquant si la rubrique courante est de ce type
	  * 
	  * @return	le tableau contenant la liste des différents types de rubrique
	  */
	function retListeTypes ()
	{
		$iTypeRubrique = $this->oEnregBdd->TypeRubrique;
		
		$aoTypes = array(
				array(LIEN_UNITE,INTITULE_RUBRIQUE,($iTypeRubrique == LIEN_UNITE))
				, array(LIEN_PAGE_HTML,"Affichage d'une page HTML (du serveur)",($iTypeRubrique == LIEN_PAGE_HTML))
				, array(LIEN_SITE_INTERNET,"Lien vers un site Internet",($iTypeRubrique == LIEN_SITE_INTERNET))
				, array(LIEN_DOCUMENT_TELECHARGER,"Document &agrave; t&eacute;l&eacute;charger",($iTypeRubrique == LIEN_DOCUMENT_TELECHARGER))
				, array(LIEN_FORUM,"Forum",($iTypeRubrique == LIEN_FORUM))
				, array(LIEN_CHAT,"Chat",($iTypeRubrique == LIEN_CHAT))
				, array(LIEN_TEXTE_FORMATTE,"Texte format&eacute;")
				, array(LIEN_NON_ACTIVABLE,"Intitul&eacute; non activable",($iTypeRubrique == LIEN_NON_ACTIVABLE))
			);
		
		return $aoTypes;
	}
	
	/**
	 * Retourne un tableau contenant la liste des statuts possibles d'une rubrique. Cette liste se présente elle-même
	 * sous forme de tableaux, contenant chacun un statut possible de la rubrique (constante STATUT_), l'intitulé de ce 
	 * statut, et une valeur booléenne indiquant si la rubrique courante possède ce statut actuellement
	 * 
	 * @return	le tableau contenant la liste des statuts possibles d'une rubrique
	 */
	function retListeStatuts ()
	{
		$iStatutRubrique = $this->oEnregBdd->StatutRubrique;
		
		$aaStatuts = array(
				// array(identifiant,nom,statut séléctionné)
				array(STATUT_OUVERT,"Ouvert",($iStatutRubrique == STATUT_OUVERT))
				, array(STATUT_LECTURE_SEULE,"Consultable",($iStatutRubrique == STATUT_LECTURE_SEULE))
				, array(STATUT_FERME,"Fermé",($iStatutRubrique == STATUT_FERME))
				, array(STATUT_INVISIBLE,"Invisible",($iStatutRubrique == STATUT_INVISIBLE))
			);
		
		return $aaStatuts;
	}
	
	/**
	 * Initialise un tableau avec la liste des rubriques d'un module
	 * 
	 * @param	v_iIdMod		l'id du module
	 * @param	v_iTypeRubrique	type des rubriques que l'on veut retourner(optionnel)
	 * 
	 * @return	le nombre de rubriques insérées dans le tableau
	 */
	function retListeRubriques ($v_iIdMod=NULL,$v_iTypeRubrique=NULL)
	{
		if ($v_iIdMod == NULL)
			 $v_iIdMod = $this->retIdParent();
		
		if (!isset($v_iIdMod))
			return 0;
		
		$sRequeteSql = "SELECT * FROM Module_Rubrique"
			." WHERE IdMod='{$v_iIdMod}'"
			.($v_iTypeRubrique == NULL ? NULL : " AND TypeRubrique='{$v_iTypeRubrique}'")
			." ORDER BY OrdreRubrique ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$i = 0;
		
		$this->aoRubriques = NULL;
		
		while ($this->aoRubriques[$i++] = $this->oBdd->retEnregSuiv($hResult))
			;
		
		$this->oBdd->libererResult($hResult);
		
		return ($i-1);
	}
	
	/**
	 * @return	le texte (nom) qui désigne ce niveau de la formation (formation, module, rubrique, etc)
	 */
	function retTexteNiveau()
	{
		return INTITULE_RUBRIQUE;
	}
	
	/**
	 * @param	v_bDifferencierActions	si \c true, le symbole retourné tiendra
	 * 									compte de la nature de la rubrique, càd
	 * 									qu'une rubrique "non conteneur", qui 
	 * 									contient une action, renverra un symbole
	 * 									différent d'une rubrique/unité classique
	 * 
	 * @return	le symbole qui représente ce niveau de formation (pour l'instant
	 * 			une simple abréviation)
	 */
	function retSymbole($v_bDifferencierActions = FALSE)
	{
		if ($v_bDifferencierActions && !$this->estConteneur())
			return 'a';
		else
			return 'u';
	}
	
	/**
	 * Réinitialise l'objet \c oEnregBdd avec la rubrique courante
	 */
	function rafraichir ()
	{
		$this->init();
	}
	
	/**
	 * Redistribue les numéros d'ordre des rubriques
	 * 
	 * @param	v_iNouveauNumOrdre le nouveau numéro d'ordre de la rubrique courante
	 * 
	 * @return	\c true si les numéros ont bien été modifiés
	 */
	function redistNumsOrdre ($v_iNouveauNumOrdre=NULL)
	{
		if ($v_iNouveauNumOrdre == $this->retNumOrdre())
			return FALSE;
		
		if (($cpt = $this->retListeRubriques()) < 0)
			return FALSE;
		
		$iNumOrdre = $this->retNumOrdre();
		
		// *************************************
		// Ajouter dans ce tableau les ids et les numéros d'ordre
		// *************************************
		
		$aoNumsOrdre = array();
		
		for ($i=0; $i<$cpt; $i++)
			$aoNumsOrdre[$i] = array($this->aoRubriques[$i]->IdRubrique,$this->aoRubriques[$i]->OrdreRubrique);
		
		// *************************************
		// Mettre à jour dans la table Module_Rubrique
		// *************************************
		
		if ($v_iNouveauNumOrdre > 0)
		{
			$aoNumsOrdre = redistNumsOrdre($aoNumsOrdre,$this->retNumOrdre(),$v_iNouveauNumOrdre);
			
			$iIdCourant = $this->retId();
			
			for ($i=0; $i<count ($aoNumsOrdre); $i++)
				if ($aoNumsOrdre[$i][0] != $iIdCourant)
					$this->mettre_a_jour("OrdreRubrique",$aoNumsOrdre[$i][1],$aoNumsOrdre[$i][0]);
			
			$this->defNumOrdre($v_iNouveauNumOrdre);
		}
		else
			for ($i=0; $i<count($aoNumsOrdre); $i++)
				$this->mettre_a_jour("OrdreRubrique",($i+1),$aoNumsOrdre[$i][0]);
		
		return TRUE;
	}
	
	/**
	 * Initialise l'activité courante
	 * 
	 * @param	v_iIdActiv l'id de l'activité. Si on essaie d'initialiser une activité qui n'appartient pas à cette 
	 * rubrique, l'activité ne sera pas initialisée
	 */
	function initActivCourante ($v_iIdActiv=NULL)
	{
		if ($v_iIdActiv < 1)
			return;
		$this->oActivCourante = new CActiv($this->oBdd, $v_iIdActiv);
		if ($this->oActivCourante->retIdParent() != $this->retId())
			unset($this->oActivCourante);
	}
	
	/**
	 * Vérifie si l'intitulé est utilisé par plusieurs rubriques
	 * 
	 * @param	v_iIdIntitule l'id de l'intitulé
	 * 
	 * @return	si \c true on peut supprimer l'intitulé
	 */
	function peutSupprimerIntitule ($v_iIdIntitule)
	{
		$bSupprimerIntitule = FALSE;
		
		if ($v_iIdIntitule < 1)
			return $bSupprimerIntitule;
		
		$sRequeteSql = "SELECT COUNT(*) FROM Module_Rubrique"
			." WHERE IdIntitule='{$v_iIdIntitule}'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($this->oBdd->retEnregPrecis($hResult) < 1)
			$bSupprimerIntitule = TRUE;
		
		$this->oBdd->libererResult($hResult);
		
		return $bSupprimerIntitule;
	}
	
	/**
	 * Retourne l'id de la rubrique précedent la courante
	 * 
	 * @return	l'id de la rubrique précedent la courante
	 */
	function retIdEnregPrecedent ()
	{
		if (($cpt = $this->retListeRubriques()) < 0)
			return 0;
		
		$cpt--;
				
		return (($cpt < 0) ?  0 : $this->aoRubriques[$cpt]->IdRubrique);
	}
	
	/**
	 * Vérifie si la personne qui a crée la rubrique est la même que celle passé en paramètre
	 * 
	 * @param	v_iIdPers l'id de la personne
	 * 
	 * @return	\c true si la personne qui a crée la rubrique est la même que celle passé en paramètre
	 */
	function verifSaRubrique ($v_iIdPers)
	{
		return ($v_iIdPers == $this->oEnregBdd->IdPers);
	}
	
	/**
	 * Vérifie si la personne peut modifier la rubrique (concepteur(statut) ou créateur de la rubrique)
	 * 
	 * @param	v_iIdPers l'id de la personne
	 * 
	 * @return	\c true si la personne peut modifier la rubrique
	 */
	function peutModifier ($v_iIdPers)
	{
		return ($this->verifSaRubrique($v_iIdPers) | $this->verifConcepteur($v_iIdPers));
	}
	
	/**
	 * @deprecated Ne semble pas/plus utilisé ???
	 */
	function retPremierePageUnite ()
	{
		$aiIdPremierePage = array();
		
		$sRequeteSql = "SELECT SousActiv.IdSousActiv"
			.", Activ.IdActiv"
			." FROM Module_Rubrique"
			." LEFT JOIN Activ USING (IdRubrique)"
			." LEFT JOIN SousActiv USING (IdActiv)"
			." WHERE Module_Rubrique.IdRubrique='{$this->iId}'"
			." AND SousActiv.PremierePageSousActiv='1'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($oEnreg = $this->oBdd->retEnregSuiv())
		{
			$aiIdPremierePage["Activ"] = $oEnreg->IdActiv;
			$aiIdPremierePage["SousActiv"] = $oEnreg->IdSousActiv;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $aiIdPremierePage;
	}
	
	/**
	 * Initialise un tableau avec les étudiants inscrits à la formation
	 * 
	 * @param	v_bAppartenirEquipe	si \c true (par défaut) le tableau est rempli par les personnes qui appartiennent à 
	 *								une équipe de cette rubrique, si \c false voir paramètre \p v_bAutoInscrit
	 * @param	v_bAutoInscrit		si \c true (par défaut) le tableau est rempli par les personnes qui sont inscrites à
	 * 								la formation et qui n'appartiennent pas à une équipe de cette rubrique. Utilisé lorsque
	 * 								les personnes sont automatiquement inscrites aux rubriques de la formation.
	 * 								Si \c false il est rempli par les personnes qui sont inscrites à cette rubrique et qui
	 * 								n'appartiennent pas à une équipe de cette rubrique
	 * @param	v_iSensTri			indique si un tri doit être effectué ainsi que son sens (croissant par défaut)
	 * 
	 * @return	le nombre de personnes(étudiants) insérées dans le tableau
	 */
	function initMembres ($v_bAppartenirEquipe=TRUE,$v_bAutoInscrit=TRUE,$v_iSensTri=TRI_CROISSANT)
	{
		$iIdxMembre = 0;
		$this->aoMembres = array();
				
		if ($v_bAppartenirEquipe)
			$sRequeteSql = "SELECT Personne.* FROM Equipe"
				." LEFT JOIN Equipe_Membre USING (IdEquipe)"
				." LEFT JOIN Personne USING (IdPers)"
				." WHERE Equipe.IdRubrique='".$this->retId()."'"
				." AND Equipe.IdActiv='0'"
				." AND Personne.IdPers IS NOT NULL";
		else if ($v_bAutoInscrit)
			$sRequeteSql = "SELECT Personne.* FROM Formation_Inscrit"
				." LEFT JOIN Personne USING (IdPers)"
				." LEFT JOIN Equipe ON Formation_Inscrit.IdForm=Equipe.IdForm"
				." LEFT JOIN Equipe_Membre ON Equipe.IdEquipe=Equipe_Membre.IdEquipe"
				." AND Formation_Inscrit.IdPers=Equipe_Membre.IdPers"
				." WHERE Equipe.IdRubrique='".$this->retId()."'"
				." AND Equipe.IdActiv='0'"
				." GROUP BY Personne.IdPers"
				." HAVING COUNT(Equipe_Membre.IdEquipe)='0'";
		else
			$sRequeteSql = "SELECT Personne.* FROM Module_Inscrit"
				." LEFT JOIN Personne USING (IdPers)"
				." LEFT JOIN Equipe ON Module_Inscrit.IdMod=Equipe.IdMod"
				." LEFT JOIN Equipe_Membre ON Equipe.IdEquipe=Equipe_Membre.IdEquipe"
				." AND Module_Inscrit.IdPers=Equipe_Membre.IdPers"
				." WHERE Equipe.IdRubrique='".$this->retId()."' AND Equipe.IdActiv='0'"
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
	
	/**
	 * Initialise un tableau contenant tous les formulaires
	 * 
	 * @param	v_iModalite le numéro représentant le type de modalité pour l'activité (voir les constantes MODALITE_)
	 * 
	 * @return	le nombre de formulaires insérés dans le tableau
	 */
	function initFormulaires ($v_iModalite=NULL)
	{
		$iIdxFormulaire = 0;
		$this->aoFormulaires = array();
		
		$sRequeteSql = "SELECT SousActiv.*"
			." FROM Activ"
			." LEFT JOIN SousActiv USING (IdActiv)"
			." WHERE Activ.IdRubrique='".$this->retId()."'"
			." AND SousActiv.IdTypeSousActiv='".LIEN_FORMULAIRE."'"
			.(isset($v_iModalite)
				? " AND (SousActiv.ModaliteSousActiv='{$v_iModalite}'"
					." OR (SousActiv.ModaliteSousActiv='".MODALITE_IDEM_PARENT."' AND Activ.ModaliteActiv='{$v_iModalite}'))"
				: NULL)
			." ORDER BY Activ.OrdreActiv ASC, SousActiv.OrdreSousActiv ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoFormulaires[$iIdxFormulaire] = new CSousActiv($this->oBdd);
			$this->aoFormulaires[$iIdxFormulaire]->init($oEnreg);
			$iIdxFormulaire++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxFormulaire;
	}

	/**
	 * Initialise un tableau contenant tous les exercices Hotpotatoes
	 * 
	 * @param	v_iModalite le numéro représentant le type de modalité pour l'activité (voir les constantes MODALITE_)
	 * 
	 * @return	le nombre d'exercices trouvés
	 */
	function initHotpotatoes( $v_iModalite=NULL )
	{
		$iIdxHotpotatoes = 0;
		$this->aoHotpotatoes = array();

		$sRequeteSql = "SELECT SousActiv.*"
			." FROM Activ"
			." LEFT JOIN SousActiv USING (IdActiv)"
			." WHERE Activ.IdRubrique='".$this->retId()."'"
			." AND SousActiv.IdTypeSousActiv='".LIEN_HOTPOTATOES."'"
			.(isset($v_iModalite)
				? " AND (SousActiv.ModaliteSousActiv='{$v_iModalite}'"
					." OR (SousActiv.ModaliteSousActiv='".MODALITE_IDEM_PARENT."' AND Activ.ModaliteActiv='{$v_iModalite}'))"
				: NULL)
			." ORDER BY Activ.OrdreActiv ASC, SousActiv.OrdreSousActiv ASC";

		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoHotpotatoes[$iIdxHotpotatoes] = new CSousActiv($this->oBdd);
			$this->aoHotpotatoes[$iIdxHotpotatoes]->init($oEnreg);
			$iIdxHotpotatoes++;
		}
		$this->oBdd->libererResult($hResult);

		return $iIdxHotpotatoes;
	}

	/**
	 * @return	le dossier associé à cette rubrique, donc celui où se trouvent ses fichiers associés
	 */
	function retDossier()
	{
		$f = new FichierInfo(dir_rubriques($this->iIdForm));
		return $f->retChemin();
	}
	
	/**
	 * Indique si cet élément est susceptible de contenir d'autre éléments
	 * 
	 * @return	\c true si l'élément est un conteneur (sont rôle est uniquement 
	 * 			de contenir des éléments de niveau inférieur), \c false sinon
	 * 			(dans ce cas il s'agit d'une "activité", par ex. forum, chat...)
	 */
	function estConteneur()	{ return ($this->retType() == LIEN_UNITE); }
	
	/**
	 * Retourne (après les avoir initialisés si nécessaire) les éléments enfants
	 * de la rubrique, càd les activités
	 */	
	function &retElementsEnfants()
	{
		if (!isset($this->aoActivs))
			$this->initActivs();
			
		return $this->aoActivs;
	}
}

?>