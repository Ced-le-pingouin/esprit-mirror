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
 * @file	sous_activite.tbl.php
 * 
 * Contient la classe de gestion des sous-activités, en rapport avec la DB
 * 
 * @date	2001/06/01
 * 
 * @author	Cédric FLOQUET
 * @author	Filippo PORCO
 */

require_once(dir_database("sous_activite.ressource.tbl.php"));
require_once(dir_database("collecticiel.tbl.php"));
require_once(dir_database("galerie.tbl.php"));
require_once(dir_database("glossaire.tbl.php"));
require_once(dir_database("chat.tbl.php"));
require_once(dir_lib("std/FichierInfo.php", TRUE));

define("INTITULE_SOUS_ACTIV","Action");	/// Titre qui désigne le cinquième niveau de la structure d'une formation 	@enum INTITULE_SOUS_ACTIV

/**
 * Gestion des sous-activités, et encapsulation de la table SousActiv de la DB
 */
class CSousActiv
{
	var $iId;					///< Utilisé dans le constructeur, pour indiquer l'id de la sous-activité à récupérer dans la DB
	
	var $oBdd;					///< Objet représentant la connexion à la DB
	var $oEnregBdd;				///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	var $oActivParente;			///< Objet de type CActiv contenant l'activité parente de la sous-activité courante
	var $aoRessources;			///< Tableau rempli par #initRessources(), contenant une liste des ressources de cette sous-activité
	var $aoSousActivs;			///< Tableau rempli par #retListeSousActivs(), contenant une liste des sous-activités de l'activité
	var $oIdsParents;			///< Objet contenant l'id de la formation, du module, de la rubrique et de l'activité
	
	var $oAuteur;				///< Objet de type Cpersonne contenant une personne
	var $oEquipe;				///< Objet de type CEquipe contenant une équipe
	var $aoEquipes;				///< Tableau rempli par #initEquipes(), contenant une liste des équipes de cette sous-activité
	
	var $oForum;				///< Objet de type CForum contenant un forum
	var $oCollecticiel;			///< Objet de type CCollecticiel contenant un collecticiel
	var $oGalerie;				///< Objet de type CGalerie contenant un collecticiel
	
	var $aoChats;				///< Tableau rempli par #initChats(), contenant une liste des chats de cette sous-activité
	
	var $oGlossaire;			///< Objet de type CGlossaire contenant un glossaire
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CSousActiv (&$v_oBdd,$v_iIdSousActiv=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdSousActiv;
		
		if ($this->iId > 0)
			$this->init();
	}
	
	/**
	 * Retourne la constante qui définit le niveau "sous-activité", de la structure d'une formation
	 * 
	 * @return	la constante qui définit le niveau "sous-activité", de la structure d'une formation
	 */
	function retTypeNiveau () { return TYPE_SOUS_ACTIVITE; }
	
	/**
	 * Initialise l'objet avec un enregistrement de la DB ou un objet PHP existant représentant un tel enregistrement
	 * Voir CPersonne#init()
	 */
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
	
	/**
	 * Initialise \c oEquipe avec l'équipe dont l'id est passé en paramètre
	 * 
	 * @param	v_iIdEquipe		l'id de l'équipe
	 * @param	v_bInitMembres	si \c true, initialise les membres de l'équipe(\c false par défaut)
	 */
	function initEquipe ($v_iIdEquipe,$v_bInitMembres=FALSE) 
	{
		$this->oEquipe = new CEquipe($this->oBdd,$v_iIdEquipe,$v_bInitMembres);
	}
	
	/**
	 * Initialise un tableau contenant les équipes de la sous-activité
	 * 
	 * @param	v_bInitMembres		si \c true, initialise également les membres de l'équipe (défaut à \c false)
	 * @param	v_iTypeNiveauDepart	le numéro représentant le type d'élément (formation/module/etc) par lequel on veut 
	 * 								débuter la recherche des équipes à initialiser
	 * 
	 * @return	le nombre d'équipes insérés dans le tableau
	 */
	function initEquipes ($v_bInitMembres=FALSE,$v_iTypeNiveauDepart=TYPE_SOUS_ACTIVITE)
	{
		$oListeEquipes = new CEquipe($this->oBdd);
		$oListeEquipes->initEquipesEx($this->retId(),$v_iTypeNiveauDepart,$v_bInitMembres);
		$this->aoEquipes = $oListeEquipes->aoEquipes;
		return count($this->aoEquipes);
	}
	
	/**
	 * Initialise un tableau contenant les formulaires complétés de la sous-activité
	 * 
	 * @param	v_iIdPers	l'id de la personne(optionnel)
	 * @param	v_mStatutFC	statut(optionnel) de la ressource(constantes STATUT_RES_)
	 * 
	 * @return	Le nombre de formulaires insérés dans le tableau
	 */
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
	
	/**
	 * Retourne des informations concernant les formulaires complétés de cette sous-activité ayant le statut le plus élevé,
	 * et déposés par les personnes spécifiées
	 * 
	 * @param	v_miIdPers la liste des id des personnes ou l'id d'une seule personne
	 * 
	 * @return	un tableau contenant les informations suivantes: le statut le plus élevé des formulaires complétés 
	 * (voir constantes STATUT_RES_), le nombre de formulaires ayant ce statut, et la date de dépôt du dernier de ces formulaires
	 */
	function retStatutPlusHautFormulaire ($v_miIdPers=NULL)
	{
		$sListePers = NULL;
		
		$oEnreg->MaxStatutFormSousActiv = NULL;
		$oEnreg->CountStatutFormSousActiv = NULL;
		$oEnreg->DateDernierFormulaireDepose = NULL;
		
		if (is_array($v_miIdPers))
			foreach ($v_miIdPers as $iIdPers)
				$sListePers .= (isset($sListePers) ? ", " : NULL)
					."'{$iIdPers}'";
		else
			$sListePers = "'{$v_miIdPers}'";
		
		if (isset($sListePers))
		{
			$sRequeteSql = "SELECT MAX(StatutFormSousActiv) AS MaxStatutFormSousActiv"
					.", COUNT(*) AS CountStatutFormSousActiv"
					.", MAX(FormulaireComplete.DateFC) AS DateDernierFormulaireDepose"
				." FROM FormulaireComplete_SousActiv"
				." LEFT JOIN FormulaireComplete USING (IdFC)"
				." WHERE FormulaireComplete_SousActiv.IdSousActiv='".$this->retId()."'"
					." AND FormulaireComplete.IdPers IN ({$sListePers})";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$oEnreg = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		return array($oEnreg->MaxStatutFormSousActiv,$oEnreg->CountStatutFormSousActiv,$oEnreg->DateDernierFormulaireDepose);
	}

	/**
	 * Permet de connaître le numero d'ordre maximum des sous-activités
	 * 
	 * @return	le numéro d'ordre maximum
	 */
	function retNumOrdreMax ($v_iIdActiv=NULL)
	{
		if ($v_iIdActiv == NULL)
			$v_iIdActiv = $this->retIdParent();		
		$sRequeteSql = "SELECT MAX(OrdreSousActiv) FROM SousActiv"
			." WHERE IdActiv='".$v_iIdActiv."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNumOrdreMax = $this->oBdd->retEnregPrecis($hResult,0);
		$this->oBdd->libererResult($hResult);
		return $iNumOrdreMax;
	}
	
	/**
	 * Copie la sous-activité courante dans une activité spécifiée
	 * 
	 * @param	v_iIdActiv l'id de l'activité de destination
	 * 
	 * @return	l'id de la nouvelle sous-activité
	 */
	function copier($v_iIdActiv, $v_sExportation = NULL)
	{
		$iIdSousActiv = $this->copierSousActiv($v_iIdActiv, $v_sExportation);
		
		if ($iIdSousActiv < 1 && !$v_sExportation)
			return 0;
		
		if (!$v_sExportation)
		{
			switch ($this->retType())
			{
				case LIEN_FORUM: $this->copierForum($iIdSousActiv); break;
				case LIEN_CHAT: $this->copierChats($iIdSousActiv); break;
			}
		}
		
		return $iIdSousActiv;
	}
	
	/**
	 * Insère une copie d'une sous-activité dans la DB
	 * 
	 * @param	v_iIdActiv l'id de l'activité
	 * 
	 * @return	l'id de la nouvelle sous-activité
	 */
	function copierSousActiv($v_iIdActiv, $v_sExportation = NULL)
	{
		global $sSqlExportForm;
		
		if ($v_iIdActiv < 1 && !$v_sExportation)
			return 0;
		
		$sRequeteSql = "INSERT INTO SousActiv SET"
			." IdSousActiv=".(!$v_sExportation?"NULL":"'".$this->retId()."'")
			.", NomSousActiv='".MySQLEscapeString($this->oEnregBdd->NomSousActiv)."'"
			.", DonneesSousActiv='".MySQLEscapeString($this->oEnregBdd->DonneesSousActiv)."'"
			.", DescrSousActiv='".MySQLEscapeString($this->oEnregBdd->DescrSousActiv)."'"
			.", DateDebSousActiv=NOW()"
			.", DateFinSousActiv=NOW()"
			.", StatutSousActiv='{$this->oEnregBdd->StatutSousActiv}'"
			.", VotesMinSousActiv='{$this->oEnregBdd->VotesMinSousActiv}'"
			.", IdTypeSousActiv='{$this->oEnregBdd->IdTypeSousActiv}'"
			.", PremierePageSousActiv='".($this->oEnregBdd->PremierePageSousActiv ? "1" : "0")."'" // ENUM ('0','1')
			//.", IdActiv=".(!$v_sExportation?"'{$v_iIdActiv}'":"@iIdActiviteCourante")
			.", IdActiv=".(!$v_sExportation?"'{$v_iIdActiv}'":"'".$this->retIdParent()."'")
			.", OrdreSousActiv='{$this->oEnregBdd->OrdreSousActiv}'"
			.", ModaliteSousActiv='{$this->oEnregBdd->ModaliteSousActiv}'"
			.", IdPers='".$this->retIdPers()."'";
		
		if ($v_sExportation)
		{
			$sSqlExportForm .= $sRequeteSql . ";\n\n";
			$sSqlExportForm .= "SET @iIdSousActiviteCourante := LAST_INSERT_ID();\n\n";
			
			return -1;
		}
		else
		{
			$this->oBdd->executerRequete($sRequeteSql);
			
			return $this->oBdd->retDernierId();
		}
	}
	
	/**
	 * Copie tout les chats de la sous-activité courante vers une autre
	 * 
	 * @param	v_iIdSousActiv l'id de la sous-activité de destination
	 */
	function copierChats ($v_iIdSousActiv)
	{
		if ($this->retType() != LIEN_CHAT && $v_iIdSousActiv < 1)
			return;
		
		$this->initChats();
		
		foreach ($this->aoChats as $oChat)
			$oChat->copier($v_iIdSousActiv);
		
		$this->aoChats = NULL;
	}
	
	/**
	 * Réinitialise l'objet \c oEnregBdd avec l'activité courante
	 */
	function rafraichir () { if ($this->retId() > 0) $this->init(); }
	
	/**
	 * Initialise une sous-activité de type glossaire
	 * 
	 * @param	v_bInitElements si \c true, inialise les éléments du glosaire (\c false par défaut)
	 */
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
	 * Associe un glossaire à la sous-activité
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
	
	/**
	 * Efface un glossaire de la table SousActiv_Glossaire
	 */
	function effacerGlossaire ()
	{
		$sRequeteSql = "DELETE FROM SousActiv_Glossaire"
			." WHERE IdSousActiv='".$this->retId()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Initialise un tableau contenant les ressources de la sous-activité
	 * 
	 * @param	v_sTri			le tri sera effectué sur ce champ
	 * @param	v_iTypeTri		le le sens du tri (constante TRI_)
	 * @param	v_iModalite		la modalité de la ressource
	 * @param	v_iIdPers		l'id de la personne
	 * @param	v_iStatut		le satut de la ressource (constante STATUT_RES_ et TRANSFERT_FICHIERS)
	 * @param	v_sDate			la date(optionnelle) à partie de laquelle on recherche les ressources
	 * 
	 * @return	le nombre de ressources insérés dans le tableau
	 */
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

	/**
	 * Ajoute une sous-activité dans une activité
	 * 
	 * @param	v_iIdActiv	l'id de l'activité
	 * 
	 * @return	l'id de la nouvelle sous-activité
	 */
	function ajouter ($v_iIdActiv)
	{
		$sRequeteSql = "INSERT INTO SousActiv SET"
			." IdSousActiv=NULL"
			.", NomSousActiv='".MySQLEscapeString(INTITULE_SOUS_ACTIV." sans nom")."'"
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
	
	/**
	 * Retourne le nombre de sous-activités de cette activité
	 * 
	 * @param	v_iNumParent	l'id de l'activité
	 * 
	 * @return	le nombre de sous-activités de cette activité
	 */
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
	
	/**
	 * Retourne un objet contenant les id des niveaux supérieurs de la sous-activité, dans la structure d'une formation 
	 * 
	 * @return	un objet qui contient: l'id de la formation, du module, de la rubrique, et de l'activité
	 */
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
	
	/**
	 * Efface la sous-activité courante
	 */
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
				unset($this->oIdsParents,$sFichierASupprimer,$sNomFichier);
		}
		
		// Effacer les équipes
		$this->effacerEquipes();
		
		// Effacer cette sous-activité
		$this->effacerSousActiv();
		
		$this->redistNumsOrdre();
	}
	
	/**
	 * Efface dans la DB la sous-activité courante
	 */
	function effacerSousActiv ()
	{
		$sRequeteSql = "DELETE FROM SousActiv"
			." WHERE IdSousActiv='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface le collecticiel
	 */
	function effacerCollecticiel ()
	{
		$this->initCollecticiel();
		$this->oCollecticiel->effacer();
		$this->oCollecticiel = NULL;
	}
	
	/**
	 * Efface la galerie
	 */
	function effacerGalerie ()
	{
		$this->initGalerie();
		$this->oGalerie->effacer();
		$this->oGalerie = NULL;
	}
	
	/**
	 * Efface les équipes de la sous-activités
	 */
	function effacerEquipes ()
	{
		$oEquipe = new CEquipe($this->oBdd);
		$oEquipe->effacerParNiveau(TYPE_SOUS_ACTIVITE,$this->iId);
		$oEquipe = NULL;
	}
	
	/**
	 * Enregistre une évaluation d'une ressource
	 * 
	 * @param	v_iIdResSousActiv	l'id dela ressource de la sous-activité(table Ressource_Sous_activ)
	 * @param	v_iIdPers			l'id de la personne
	 * @param	v_sApprec			l'appréciation de la ressource
	 * @param	v_sComment			le commentaire de l'évaluation
	 * @param	v_iStatutRes 		le statut de la ressource(constante STATUT_RES_)
	 */
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
	
	/**
	 * Ajoute un forum de type sous-activité
	 */
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
	
	/**
	 * Initialise le forum de type sous-activité
	 */
	function initForum ()
	{
		$this->oForum = new CForum($this->oBdd);
		$this->oForum->initForumParType(TYPE_SOUS_ACTIVITE,$this->retId());
	}
	
	/**
	 * Copie le forum courant de type sous-activité dans une autre sous-activité
	 * 
	 * @param	v_iIdSousActiv	l'id de la sous-activité de destination
	 */
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
	
	/**
	 * Efface le forum de type sous-activité
	 */
	function effacerForum ()
	{
		$this->initForum();
		if (is_object($this->oForum))
			$this->oForum->effacerForum();
		$this->oForum = NULL;
	}

	/**
	 * Initialise un tableau contenant les chats de type sous-activité
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
	 * Retourne le nombre de chats de la sous-activité
	 * 
	 * @return	le nombre de chats de la sous-activité
	 */
	function retNombreChats ()
	{
		$oChat = new CChat($this->oBdd);
		return $oChat->retNombreChats($this);
	}
	
	/**
	 * Ajoute un chat à la sous-activité
	 * 
	 * @return	l'id du nouveau chat
	 */
	function ajouterChat ()
	{
		$oChat = new CChat($this->oBdd);
		return $oChat->ajouter($this);
	}
	
	/**
	 * Efface tout les chats de la sous-activité
	 */
	function effacerChats ()
	{
		$oChat = new CChat($this->oBdd);
		$oChat->effacerChats($this);
	}

	/**
	 * Initialise le collecticiel
	 */
	function initCollecticiel () { $this->oCollecticiel = new CCollecticiel($this->oBdd,$this->retId()); }
	
	/**
	 * Retourne des informations concernant les ressources de cette sous-activité ayant le statut le plus élévé, et 
	 * déposés par les personnes spécifiées
	 * 
	 * @param	v_miIdPers	la liste des id des personnes ou l'id d'une seule personne
	 * 
	 * @return	un tableau contenant les informations suivantes: le statut le plus élévé des ressources, 
	 * le nombre de ressources ayant ce statut, la date du dépot du fichier le plus récent qui a ce statut là, la personne
	 * qui a déposé ce fichier
	 */
	function retStatutPlusHautRes ($v_miIdPers=NULL)
	{
		$sListePers = NULL;
		
		$oEnreg->MaxStatutResSousActiv = NULL;
		$oEnreg->CountStatutResSousActiv = NULL;
		$oEnreg->IdPersStatutResSousActiv = NULL;
		$oEnreg->MaxRessourceDateRes = NULL;
		
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
					.", MAX(Ressource.DateRes) AS MaxRessourceDateRes"
					.", COUNT(*) AS CountStatutResSousActiv"
					.", Ressource.IdPers AS IdPersStatutResSousActiv"
				." FROM Ressource_SousActiv"
				." LEFT JOIN Ressource USING (IdRes)"
				." WHERE Ressource_SousActiv.IdSousActiv='".$this->retId()."'"
					." AND Ressource.IdPers IN ({$sListePers})"
					." AND Ressource_SousActiv.StatutResSousActiv<>'".STATUT_RES_TRANSFERE."'"
				." GROUP BY Ressource_SousActiv.StatutResSousActiv"
				." ORDER BY Ressource_SousActiv.StatutResSousActiv DESC LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$oEnreg = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		return array(
				"StatutResPlusHaut" => $oEnreg->MaxStatutResSousActiv,
				"StatutResPlusHautNb" => $oEnreg->CountStatutResSousActiv,
				"StatutResPlusHautIdPers" => $oEnreg->IdPersStatutResSousActiv,
				"StatutResDateRecente" => $oEnreg->MaxRessourceDateRes
			);
	}

	/**
	 * Initalise \c oGalerie avec la galerie(de type sous-activité)
	 */
	function initGalerie () { $this->oGalerie = new CGalerie($this->oBdd,$this->retId()); }
	
	/**
	 * Retourne le statut du parent(activité)
	 * 
	 * @return	le statut du parent(activité)
	 */
	function retStatutReel ()
	{
		if (STATUT_IDEM_PARENT == ($iStatut = $this->retStatut()))
			return $this->oActivParente->retStatut();
		else
			return $iStatut;
	}
	
	/**
	 * Insère un vote pour une ressource de la sous-activité
	 * 
	 * @param	v_iIdResSousActiv	l'id de la ressource de la sous-activité
	 * @param	v_iIdVotant			l'id dela personne qui vote
	 * 
	 * @return	\c true si le document est soumis (votes suffisants)
	 */
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
	
	/**
	 * Met à jour le statut de la ressource
	 * 
	 * @param	v_iIdResSousActiv	l'id de la ressource de la sous-activité
	 * 
	 * @return	\c true si le document est soumis (votes suffisants)
	 */
	function majStatutRessource ($v_iIdResSousActiv)
	{
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
					.", DateModifStatut=NOW()"
				." WHERE IdResSousActiv='{$v_iIdResSousActiv}'"
				." AND StatutResSousActiv='".STATUT_RES_EN_COURS."'";
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$this->oBdd->executerRequete("UNLOCK TABLES");
		
		return $bSoumis;
	}
	
	/**
	 * Retourne le nombre de vote minimum en nombre de personnes minimum votantes
	 * 
	 * @return	le nombre de vote minimum en nombre de personnes minimum votantes
	 */
	function retVotesMinReels ()
	{
		// Combien de votes nécessaires pour soumettre un document ?
		// = [nb de membres de l'équipe de l'utilisateur connecté] * ([%tage requis] / 100)
		// si on obtient des décimales, on arrondit à 1 vote supplémentaire
		$iNbMembres = count($this->oEquipe->aoMembres);
		$iNbVotes = ceil($iNbMembres * ($this->retVotesMin() / 100));
		return ($iNbVotes > 0 ? $iNbVotes : 1);
	}
	
	/**
	 * Met à jour un champ de la table Activ
	 * 
	 * @param	v_sNomChamp		le nom du champ à mettre à jour
	 * @param	v_mValeurChamp	la nouvelle valeur du champ
	 * @param	v_iIdSousActiv	l'id de la sous-activité
	 * 
	 * @return	\c true si il a mis à jour le champ dans la DB
	 */
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
	
	/** @name Fonctions de lecture des champs pour cette sous-activité */
	//@{
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retIdPers () { return (is_numeric($this->oEnregBdd->IdPers) ? $this->oEnregBdd->IdPers : 0); }
	function retVotesMin () { return $this->oEnregBdd->VotesMinSousActiv; }
	function retStatut () { return $this->oEnregBdd->StatutSousActiv; }
	function retType () { return $this->oEnregBdd->IdTypeSousActiv; }
	function retNom ($v_bHtmlEntities=FALSE) { return ($v_bHtmlEntities ? emb_htmlentities($this->oEnregBdd->NomSousActiv) : $this->oEnregBdd->NomSousActiv); }
	function retNumOrdre () { return $this->oEnregBdd->OrdreSousActiv; }
	function retDescr ($v_bHtmlEntities=FALSE) { return ($v_bHtmlEntities ? emb_htmlentities($this->oEnregBdd->DescrSousActiv) : $this->oEnregBdd->DescrSousActiv); }
	function retIdParent () { return $this->oEnregBdd->IdActiv; }
	function retPremierePage () { return (bool)$this->oEnregBdd->PremierePageSousActiv; }
	function retInfoBulle ($v_bHtmlEntities=FALSE) { return ($v_bHtmlEntities ? emb_htmlentities($this->oEnregBdd->InfoBulleSousActiv) : $this->oEnregBdd->InfoBulleSousActiv); }
	
	function retDonnees($v_bHtmlEntities = FALSE)
	{
		return ($v_bHtmlEntities
			? emb_htmlentities($this->oEnregBdd->DonneesSousActiv)
			: "{$this->oEnregBdd->DonneesSousActiv};;;");
	}
	
	function retDonnee($v_iPartie)
	{
		$d = explode(';', $this->retDonnees());
		return $d[$v_iPartie];
	}
	
	function retDateDeb ()
	{
		$sDateDeb = substr($this->oEnregBdd->DateDebSousActiv,0,10);
		return ereg_replace('^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})$','\\3-\\2-\\1',$sDateDeb);
	}
	
	function retDateFin ()
	{
		$sDateFin = substr($this->oEnregBdd->DateFinSousActiv,0,10);
		return ereg_replace('^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})$','\\3-\\2-\\1',$sDateFin);
	}

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
	//@}

	/** @name Fonctions de définition des champs pour cette sous-activité */
	//@{
	function setIdPers ($v_iIdPers) { $this->oEnregBdd->IdPers=$v_iIdPers; }
	function defDonnees ($v_sDonnees) {	$this->mettre_a_jour("DonneesSousActiv",$v_sDonnees); }
	function defDescr ($v_sDescrSousActiv) { $this->mettre_a_jour("DescrSousActiv",$v_sDescrSousActiv); }
	function defNumOrdre ($v_iOrdre) { $this->mettre_a_jour("OrdreSousActiv",$v_iOrdre); }
	function defInfoBulle ($v_sInfoBulle=NULL) { $this->mettre_a_jour("InfoBulleSousActiv",$v_sInfoBulle); }
	function defModalite ($v_iModalite) { $this->mettre_a_jour("ModaliteSousActiv",$v_iModalite); }

	function defStatut ($v_iStatut)
	{
		if (is_numeric($v_iStatut))
			$this->mettre_a_jour("StatutSousActiv",$v_iStatut);
	}
	
	function defDateDeb ($v_dDateDeb)
	{
		if (isset($v_dDateDeb))
		{
			$v_dDateDeb = ereg_replace('^([0-9]{1,2})-([0-9]{1,2})-([0-9]{2,4})$','\\3-\\2-\\1', $v_dDateDeb);
			$this->mettre_a_jour("DateDebSousActiv",$v_dDateDeb);
		}
	}
	
	function defDateFin ($v_dDateFin)
	{
		if (isset($v_dDateFin))
		{
			$v_dDateFin = ereg_replace('^([0-9]{1,2})-([0-9]{1,2})-([0-9]{2,4})$','\\3-\\2-\\1', $v_dDateFin);
			$this->mettre_a_jour("DateFinSousActiv",$v_dDateFin);
		}
	}

	function defNom ($v_sNomSousActiv)
	{
		$v_sNomSousActiv = MySQLEscapeString($v_sNomSousActiv);
		
		if (empty($v_sNomSousActiv))
			$v_sNomSousActiv = INTITULE_SOUS_ACTIV." sans nom";
		
		if (isset($v_sNomSousActiv))
			$this->mettre_a_jour("NomSousActiv",$v_sNomSousActiv);
	}

	/**
	 * Définit le type de la sous-activité et efface l'ancien type s'il était initialisé
	 * 
	 * @param	v_iIdType le type de la sous-activité
	 */
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
	//@}
	
	/**
	 * Initialise l'objet \c oActiveParente avec l'activité parente de la sous-activité
	 */
	function initActiv()
	{
		if (is_null($this->oActivParente))
			$this->oActivParente = new CActiv($this->oBdd,$this->retIdParent());
	}
	
	/**
	 * Initialise un tableau des sous-activités de l'activité
	 * 
	 * @param	v_iIdActiv			l'id de l'activité, si pas fourni, utilise l'id de l'activité parente
	 * @param	v_iTypeSousActiv	le type de sous-activité(optionnel)
	 * 
	 * @return	le nombre de sous-activités insérés dans le tableau
	 */
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
	
	/**
	 * Retourne l'id de la sous-activité précédent celle-ci
	 * 
	 * @return	l'id de la sous-activité précédent celle-ci
	 */
	function retIdEnregPrecedent ()
	{
		if (($cpt = $this->retListeSousActivs()) < 0)
			return 0;
		$cpt--;
		return (($cpt < 0) ?  0 : $this->aoSousActivs[$cpt]->IdSousActiv);
	}
	
	/**
	 * Redistribue les numéros d'ordre des sous-activités
	 * 
	 * @param	v_iNouveauNumOrdre	le nouveau numéro d'ordre de la sous-activité courante
	 * 
	 * @return	\c true si les numéros ont bien été modifiés
	 */
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
	
	/**
	 * Initialise l'objet \c oAuteur de type CPersonne contenant la personne qui a déposé la ressource
	 */
	function initAuteur () { $this->oAuteur = new CPersonne($this->oBdd,$this->retIdPers()); }
	
	/**
	 * Définit les personnes passées en paramètre comme ne pouvant pas voir cette sous-activité
	 * 
	 * @param	v_aiIdInscritsNonAutorises liste des id des personnes
	 */
	function ajouterInscritsNonAutorises ($v_aiIdInscritsNonAutorises)
	{
		$iIdSousActiv = $this->retId();

		$sRequeteSql = "LOCK TABLES SousActivInvisible WRITE";
		$this->oBdd->executerRequete($sRequeteSql);
		
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
		$sRequeteSql = "UNLOCK TABLES";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Initialise le tableau contenant la liste des personnes ne pouvant pas voir cette sous-activité
	 * 
	 * @return	le nombre de personnes insérés dans le tableau
	 */
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
	
	/**
	 * Retourne un tableau contenant les différents modalités d'une sous-activité. Il ne contient qu'un type de modalité,
	 * car elle indique que la sous-activité à la modalité de l'activité parente.
	 * 
	 * @return Retourne un tableau contenant les différents modalités d'une sous activité
	 */
	function retListeModalites ()
	{
		return array(
			array(MODALITE_IDEM_PARENT,"même modalité que le ".mb_strtolower(INTITULE_ACTIV,"UTF-8"))
			/*, array(MODALITE_INDIVIDUEL,"individuel")
			, array(MODALITE_PAR_EQUIPE,"par &eacute;quipe")*/
		);
	}
	
	/**
	 * Retourne le texte en français du type de sous-activité
	 * 
	 * @param	v_iType le type(optionnel) d'une sous-activité
	 * 
	 * @return	le texte du type de sous-activité
	 */
	function retTexteType ($v_iType=NULL)
	{
		if (empty($v_iType))
			$v_iType = $this->oEnregBdd->IdTypeSousActiv;
		
		$aaListeTypes = $this->retListeTypes();
		
		foreach ($aaListeTypes as $amTypes)
			if ($amTypes[0] == $v_iType)
				return $amTypes[1];
	}
	
	/**
	 * Retourne la liste des types de sous-activités
	 * 
	 * @return	la liste des types de sous-activités
	 */
	function retListeTypes ()
	{
		return array(
			  array(LIEN_PAGE_HTML,"Choisissez un type pour cette ".mb_strtolower(INTITULE_SOUS_ACTIV,"UTF-8"))
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
	
	/**
	 * Retourne la liste des statuts possibles d'un sous-activité
	 * 
	 * @return	la liste des statuts possibles d'un sous-activité
	 */
	function retListeStatuts ()
	{
		return array(
			  array(STATUT_FERME,"Fermé")
			, array(STATUT_OUVERT,"Ouvert")
			, array(STATUT_INVISIBLE,"Invisible")
			, array(STATUT_IDEM_PARENT,"Même statut que le ".mb_strtolower(INTITULE_ACTIV,"UTF-8"))
		);
	}
	
	/**
	 * Retourne la liste des modes d'ouverture d'une sous-activité
	 * 
	 * @return	la liste des modes d'ouverture d'une sous-activité
	 */
	function retListeModes ()
	{
		return array(
			  array(FRAME_CENTRALE_DIRECT,"Zone de cours (1 temps)",0)
			, array(FRAME_CENTRALE_INDIRECT,"Zone de cours (2 temps)",1)
			, array(NOUVELLE_FENETRE_DIRECT,"Nouvelle fenêtre (1 temps)",0)
			, array(NOUVELLE_FENETRE_INDIRECT,"Nouvelle fenêtre (2 temps)",1)
		);
	}
	
	/**
	 * Retourne la liste des possibilités de soumission d'un document (constante SOUMISSION_)
	 * 
	 * @return	la liste des possibilités de soumission d'un document
	 */
	function retListeDeroulements ()
	{
		list(,$bSoumissionManuelle) = explode(";",$this->oEnregBdd->DonneesSousActiv);
		return array(
			array(SOUMISSION_AUTOMATIQUE,"en un seul temps",($bSoumissionManuelle == SOUMISSION_AUTOMATIQUE))
			/*, array(SOUMISSION_MANUELLE,"en deux temps (par défaut)",(empty($bSoumissionManuelle) || $bSoumissionManuelle == SOUMISSION_MANUELLE))*/
		);
	}
	
	/**
	 * @return	le dossier associé à cette sous-activité, donc celui où se trouvent ses fichiers associés
	 */
	function retDossier()
	{
		$iIds = $this->initIdsParents();
		$f = new FichierInfo(dir_cours($iIds->IdActiv, $iIds->IdForm));
		return $f->retChemin();
	}
}

?>
