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
** Fichier ................: rubrique.tbl.php
** Description ............: Ouvrir une connexion avec la table des rubriques.
** Date de création .......: 18/02/2002
** Dernière modification ..: 10/10/2005
** Auteurs ................: Cédric FLOQUET <cedric.floquet@umh.ac.be>
**                           Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_database("activite.tbl.php"));
require_once(dir_database("equipe.tbl.php"));

define("INTITULE_RUBRIQUE","Unité");

class CModule_Rubrique
{
	var $iId;
	
	var $oBdd;
	var $oEnregBdd;
	
	var $oIdsParents;
	
	var $aoRubriques;
	var $aoUnites;
	var $aoActivs;
	
	var $aoCollecticiels;
	var $aoChats;
	
	var $aoEquipes;
	var $aoMembres;
	
	var $oIntitule;
	
	function CModule_Rubrique (&$v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iId;
		
		if ($this->iId > 0)
			$this->init();
	}
	
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
		
		$this->initIdsParents();
	}
	
	function initEquipes ($v_bInitMembres=FALSE)
	{
		$oEquipe = new CEquipe($this->oBdd);
		$iNbrEquipes = $oEquipe->initEquipesEx($this->retId(),TYPE_RUBRIQUE,$v_bInitMembres);
		$this->aoEquipes = $oEquipe->aoEquipes;
		return $iNbrEquipes;
	}
	
	function initIntitule ()
	{
		$this->oIntitule = NULL;
		if (is_object($this->oEnregBdd))
			$this->oIntitule = new CIntitule($this->oBdd,$this->oEnregBdd->IdIntitule);
		return is_object($this->oIntitule);
	}
	
	function retTexteIntitule ($v_bAfficherNumOrdre=TRUE)
	{
		$sTexteIntitule = $this->oIntitule->retNom();
		
		if ($v_bAfficherNumOrdre && $this->oEnregBdd->NumDepartIntitule > 0)
				$sTexteIntitule .= "&nbsp;{$this->oEnregBdd->NumDepartIntitule}";
		
		return $sTexteIntitule;
	}
	
	function retNumOrdreMax ()
	{
		$sRequeteSql = "SELECT MAX(OrdreRubrique) FROM Module_Rubrique";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iMax = $this->oBdd->retEnregPrecis($hResult,0);
		$this->oBdd->libererResult($hResult);
		return $iMax;
	}
	
	function copier ($v_iIdMod,$v_bRecursive=TRUE)
	{
		$iIdRubrique = $this->copierRubrique($v_iIdMod);
		
		if ($iIdRubrique < 1)
			return 0;
		
		$this->copierForum($iIdRubrique);
		
		if ($v_bRecursive)
			$this->copierActivites($iIdRubrique);
		
		return $iIdRubrique;
	}
	
	function copierRubrique ($v_iIdMod)
	{
		if ($v_iIdMod < 1)
			return 0;
			
		$sRequeteSql = "INSERT INTO Module_Rubrique SET"
			." IdRubrique=NULL"
			.", NomRubrique='".MySQLEscapeString($this->retNom())."'"
			.", IdMod='{$v_iIdMod}'"
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
		$this->oBdd->executerRequete ($sRequeteSql);
		
		return $this->oBdd->retDernierId();
	}
	
	function copierActivites ($v_iIdRubrique)
	{
		$this->initActivs();
		foreach ($this->aoActivs as $oActiv)
			$oActiv->copier($v_iIdRubrique);
		$this->aoActivs = NULL;
	}
	
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
	
	function ajouterEquipes ()
	{
		// Effacer des équipes qui ont été supprimées de la table "ModeleEquipe"
		$oEquipe = new CEquipe($this->oBdd);
		$oEquipe->nettoyer();
		unset($oEquipe);
		
		// Rechercher toutes les activités de modalité "par équipe" de cette rubrique
		$iNbrActivs = $this->initActivs(NULL,MODALITE_PAR_EQUIPE);
		
		$oModeleEquipe = new CModeleEquipe($this->oBdd);
		
		// Ajouter les équipes qui ne font pas encore parties de cette rubrique
		$sValeursRequete = NULL;
		
		for ($iIdxActiv=0; $iIdxActiv<$iNbrActivs; $iIdxActiv++)
		{
			$iIdActiv = $this->aoActivs[$iIdxActiv]->retId();
			
			$oModeleEquipe->initModeles($this->oIdsParents->IdForm,$iIdActiv);
			
			for ($iIdxModele=0; $iIdxModele<count($oModeleEquipe->aoModeles); $iIdxModele++)
			{
				if ($oModeleEquipe->aoModeles[$iIdxModele]->retIdEquipe() <= 0)
					$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
						." ("
						."\"".$oModeleEquipe->aoModeles[$iIdxModele]->retNom()."\""
						.",'".$oModeleEquipe->aoModeles[$iIdxModele]->retId()."'"
						.",'$iIdActiv'"
						.",'0'"
						.")";
			}
		}
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "REPLACE INTO Equipe"
				." (NomEquipe,IdModeleEquipe,IdActiv,OrdreEquipe)"
				." VALUES"
				.$sValeursRequete;
			$this->oBdd->executerRequete($sRequeteSql);
			$this->oBdd->executerRequete("OPTIMIZE TABLE Equipe");
		}
	}
	
	function retTypeMenu () { return $this->oEnregBdd->TypeMenuUnite; }
	function retNumeroterActiv () { return $this->oEnregBdd->NumeroActivUnite; }
	function retIdParent () { return $this->oEnregBdd->IdMod; }
	function defIdParent ($v_iIdModule) { $this->oEnregBdd->IdMod = $v_iIdModule; }
	function retIdModule () { return $this->retIdParent(); }
	
	function retNombreLignes ()
	{
		$sRequeteSql ="SELECT COUNT(*) FROM Module_Rubrique"
			." WHERE IdMod='{$this->oEnregBdd->IdMod}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbrLignes = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		return $iNbrLignes;
	}
	
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
	
	function retNumDepart ()
	{
		return $this->oEnregBdd->NumDepartIntitule;
	}
	
	function defNumDepart ($v_iNumDepart)
	{
		if ($v_iNumDepart >= 0 && $v_iNumDepart <= 254)
			$this->mettre_a_jour("NumDepartIntitule",$v_iNumDepart);
	}
	
	function initIdsParents ()
	{
		$sRequeteSql = "SELECT Formation.IdForm, Module.IdMod FROM Formation"
			." LEFT JOIN Module USING (IdForm)"
			." WHERE Module.IdMod=".$this->retIdParent();
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->oIdsParents->IdForm = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
	}
	
	/**
	 * Retourner les informations du module précédent.
	 */
	function retInfosIntituleRubPrecedente ()
	{
		$asInfosIntituleRubPrecedente = array("IdIntitule" => "2" // Unité
			,"NumDepartIntitule" => "1");
		
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
	
	function ajouter ()
	{
		$asInfosIntituleRubPrecedente = $this->retInfosIntituleRubPrecedente();
		
		$sRequeteSql = "INSERT INTO Module_Rubrique SET"
			." IdRubrique=NULL"
			.", IdMod='".$this->retIdModule()."'"
			.", NomRubrique='".mysql_escape_string(INTITULE_RUBRIQUE." sans nom")."'"
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
	
	/*function ajouterForum ($v_iIdMod=NULL)
	{
		if (!isset($v_iIdMod))
			$v_iIdMod = $this->retIdParent();

		if ($v_iIdMod <= 0)
			return;
				
		$sRequeteSql = "INSERT INTO Module_Rubrique SET"
			." IdRubrique=NULL"
			.", IdMod={$v_iIdMod}"
			.", TypeRubrique=".LIEN_FORUM
			.", OrdreRubrique=1"
			.", NomRubrique=\"Forum du cours\""
			.", StatutRubrique=".STATUT_OUVERT
			.", DonneesRubrique=NULL"
			.", TypeMenuUnite=0"
			.", NumeroActivUnite=0";
		
		$this->oBdd->executerRequete ($sRequeteSql);
	}*/
	
	function retIdRubrique () { return $this->retId(); }
	
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
	
	function retNbrEquipes ()
	{
		// Vérifier si au moins une activité a été associé à une équipe
		$iNbrEquipes = 0;
		$this->initActivs(NULL,MODALITE_PAR_EQUIPE);
		for ($iIdxActiv=0; $iIdxActiv<count($this->aoActivs); $iIdxActiv++)
			$iNbrEquipes += $this->aoActivs[$iIdxActiv]->retNbrEquipes();
		return $iNbrEquipes;
	}
	
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
	
	function effacerRubrique ($v_iIdRubrique)
	{
		$sRequeteSql = "DELETE FROM Module_Rubrique"
			." WHERE IdRubrique='{$v_iIdRubrique}'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
		$this->redistNumsOrdre();
	}
	
	function effacerEquipes ()
	{
		$oEquipe = new CEquipe($this->oBdd);
		$oEquipe->effacerParNiveau(TYPE_RUBRIQUE,$this->iId);
	}
	
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
	
	function effacerForum ()
	{
		$oForum = new CForum($this->oBdd);
		$oForum->initForumParType(TYPE_RUBRIQUE,$this->retId());
		$oForum->effacer();
		$oForum = NULL;
	}
	
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
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	
	// {{{ Nom de la rubrique
	function defNom ($v_sNom)
	{
		$v_sNom = trim(stripslashes($v_sNom));
		if (empty($v_sNom)) $v_sNom = INTITULE_RUBRIQUE." sans nom";
		$this->mettre_a_jour("NomRubrique",$v_sNom);
	}
	
	function retNom ($v_bHtmlEntities=FALSE)
	{
		$sNomRubrique = trim($this->oEnregBdd->NomRubrique);
		if (empty($sNomRubrique))
			$sNomRubrique = "Rubrique/unité sans nom";
		return ($v_bHtmlEntities
			? htmlentities($sNomRubrique,ENT_COMPAT,"UTF-8")
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
	// }}}
	
	// ---------------------
	// Donnée de la rubrique
	// ---------------------
	function defDonnee ($v_sDonnee)
	{
		$this->mettre_a_jour("DonneesRubrique",trim(stripslashes($v_sDonnee)).":2");
	}
	
	function retDonnee ($v_bHtmlEntities=FALSE)
	{
		return ($v_bHtmlEntities
			? htmlentities($this->oEnregBdd->DonneesRubrique,ENT_COMPAT,"UTF-8")
			: $this->oEnregBdd->DonneesRubrique);
	}
	
	// ---------------------
	// Type de la rubrique
	// ---------------------
	function defType ($v_iType)
	{
		if (is_numeric($v_iType))
			$this->mettre_a_jour("TypeRubrique",$v_iType);
	}
	
	function retType () { return $this->oEnregBdd->TypeRubrique; }
	
	function retTypeNiveau () { return TYPE_RUBRIQUE; }
	
	// ---------------------
	// Numéro d'ordre de la rubrique
	// ---------------------
	function defNumOrdre ($v_iNumOrdre)
	{
		if (is_numeric($v_iNumOrdre))
			$this->mettre_a_jour("OrdreRubrique",$v_iNumOrdre);
	}
	
	function retNumOrdre ()
	{
		return $this->oEnregBdd->OrdreRubrique;
	}
	
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
	
	function defStatut ($v_iStatut)
	{
		if (is_numeric($v_iStatut))
			$this->mettre_a_jour("StatutRubrique",$v_iStatut);
	}
	
	function retStatut () { return $this->oEnregBdd->StatutRubrique; }
	
	function defDescr ($v_sDescr) { $this->mettre_a_jour("DescrRubrique",$v_sDescr); }
	function retDescr () { return $this->oEnregBdd->DescrRubrique; }
	
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
	
	/**
	 * Cette méthode recherche tous les chats attachés aux activités qui sont
	 * attachées à cette rubrique.
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
	
	/**
	 * Ne plus utiliser cette méthode.
	 *
	 * @see retTableauTypes
	 */
	function retTypesUnite ()
	{
		return $this->retListeTypes();
	}
	
	/**
	 * Cette méthode retourne un tableau contenant la liste des différents types
	 * pour la rubrique.
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
			);
		
		return $aoTypes;
	}
	
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
	
	function rafraichir ()
	{
		$this->init();
	}
	
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
	
	function defIdIntitule ($v_iIdIntitule) { $this->mettre_a_jour("IdIntitule",$v_iIdIntitule); }
	function retIdIntitule () { return $this->oEnregBdd->IdIntitule; }
	
	function initActivCourante ($v_iIdActiv=NULL)
	{
		if ($v_iIdActiv < 1)
			return;
		$this->oActivCourante = new CActiv($this->oBdd, $v_iIdActiv);
		if ($this->oActivCourante->retIdParent() != $this->retId())
			unset($this->oActivCourante);
	}
	
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
	
	function retIdEnregPrecedent ()
	{
		if (($cpt = $this->retListeRubriques()) < 0)
			return 0;
		
		$cpt--;
				
		return (($cpt < 0) ?  0 : $this->aoRubriques[$cpt]->IdRubrique);
	}
	
	function retIdPers () { return $this->oEnregBdd->IdPers; }
	
	/*:06/09/2004:function retLien ($v_sRepRubriques=NULL,$v_bStatut=TRUE)
	{
		$sLien = NULL;
		
		$sNomLien = $this->retNom();
		
		list($sHref) = explode(":",$this->retDonnee());
		
		switch ($this->retType())
		{
			case LIEN_SITE_INTERNET:
				if (!empty($sHref) && $v_bStatut)
					$sLien = "<a href=\"http://".$sHref."\""
						." target=\"_blank\""
						." onfocus=\"blur()\""
						.">".htmlentities($sNomLien,ENT_COMPAT,"UTF-8")."</a>";
				else if (!empty ($sNomLien))
					$sLien = "<span class=\"cssLienDesactive\">{$sNomLien}</span>";
				
				break;
				
			case LIEN_PAGE_HTML:
				if (!empty($sHref) && $v_bStatut)
					$sLien = "<a href=\"".$v_sRepRubriques.rawurlencode($sHref)."\""
						." target=\"_blank\""
						." onfocus=\"blur()\""
						.">".htmlentities($sNomLien,ENT_COMPAT,"UTF-8")."</a>";
				else if (!empty($sNomLien))
					$sLien = "<span class=\"cssLienDesactive\">{$sNomLien}</span>";
				
				break;
				
			case LIEN_DOCUMENT_TELECHARGER:
				if (!empty($sHref) && $v_bStatut)
					$sLien = "<a href=\"".dir_lib("download.php?f=".urlencode($v_sRepRubriques.$sHref))."\""
						." onfocus=\"blur()\""
						.">".htmlentities($sNomLien,ENT_COMPAT,"UTF-8")."</a>";
				else if (!empty($sNomLien))
					$sLien = "<span class=\"cssLienDesactive\">{$sNomLien}</span>";
				
				break;
		}
		
		return $sLien;
	}*/
	
	function verifSaRubrique ($v_iIdPers) { return ($v_iIdPers == $this->oEnregBdd->IdPers); }
	
	function peutModifier ($v_iIdPers) { return ($this->verifSaRubrique($v_iIdPers) | $this->verifConcepteur($v_iIdPers)); }
	
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
	
	// {{{ Formulaire
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
	// }}}
}

?>
