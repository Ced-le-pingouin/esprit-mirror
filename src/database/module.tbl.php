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
 * @file	module.tbl.php
 * 
 * Contient la classe de gestion des modules, en rapport avec la DB
 * 
 * @date	2001/06/01
 * 
 * @author	Cédric FLOQUET
 * @author	Filippo PORCO
 */


require_once(dir_database("rubrique.tbl.php"));

define("INTITULE_MODULE","Cours"); /// Titre qui désigne le second niveau de la structure d'une formation 	@enum INTITULE_MODULE

/**
 * Gestion des modules, et encapsulation de la table Module de la DB
 */
class CModule
{
	var $iId;					///< Utilisé dans le constructeur, pour indiquer l'id du module à récupérer dans la DB
	
	var $oBdd;					///< Objet représentant la connexion à la DB
	var $oEnregBdd;				///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	var $aoTuteurs;				///< Tableau rempli par #initTuteurs(), contenant tous les tuteurs de ce module
	var $aoInscrits;			///< Tableau rempli par #initInscrits(), contenant tous les personnes inscrites à cette formation
	
	var $aoModules;				///< Tableau rempli par #retListeModules(), contenant tous les modules d'une formation
	var $oRubriqueCourante;		///< Objet de type CModule_Rubrique contenant une rubrique d'un module
	var $aoRubriques;			///< Tableau rempli par #initRubriques(), contenant une liste des rubriques de ce module
	var $oIntitule;				///< Objet de type CIntitule contenant l'intitulé de ce module
	
	var $aoForums;				///< Tableau rempli par #initForums(), contenant tous les forums du module
	var $aoCollecticiels;		///< Tableau rempli par #initCollecticiels(), contenant tous les collecticiels du module
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CModule (&$v_oBdd,$v_iIdMod=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdMod;
		
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
	
	/**
	 * Retourne le numero d'ordre maximum des modules
	 * 
	 * @return	le numéro d'ordre maximum
	 */
	function retNumOrdreMax ($v_iIdForm=NULL)
	{
		if ($v_iIdForm == NULL)
			$v_iIdForm = $this->retIdParent();
		$sRequeteSql = "SELECT MAX(OrdreMod) FROM Module"
			." WHERE IdForm='".$v_iIdForm."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNumMax = $this->oBdd->retEnregPrecis($hResult,0);
		$this->oBdd->libererResult($hResult);
		return $iNumMax;
	}
	
	/**
	 * Copie le module courant vers une formation spécifique
	 * 
	 * @param	v_iIdForm		l'id de la formation
	 * @param	v_bRecursive	si \c true, copie aussi les rubriques associées au module
	 * 
	 * @return	l'id du nouveau module
	 */
	function copier($v_iIdForm, $v_bRecursive = TRUE, $v_sExportation = NULL)
	{
		$iIdMod = $this->copierModule($v_iIdForm, $v_sExportation);
		
		if ($v_bRecursive && ($iIdMod > 0 || $v_sExportation))
			$this->copierRubriques($iIdMod, $v_sExportation);
		
		return $iIdMod;
	}
	
	/**
	 * Insère une copie d'un module dans la DB
	 * 
	 * @param	v_iIdForm l'id de la formation
	 * 
	 * @return	l'id du nouveau module
	 */
	function copierModule($v_iIdForm, $v_sExportation = NULL)
	{
		global $sSqlExportForm;
		
		if ($v_iIdForm < 1 && !$v_sExportation)
			return 0;
		
		$sRequeteSql = "INSERT INTO Module SET"
			." IdMod=".(!$v_sExportation?"NULL":"'".$this->retId()."'")
			.", NomMod='".MySQLEscapeString($this->oEnregBdd->NomMod)."'"
			.", DescrMod='".MySQLEscapeString($this->oEnregBdd->DescrMod)."'"
			.", StatutMod='".$this->retStatut()."'"
			//.", IdForm=".(!$v_sExportation?"'{$v_iIdForm}'":"@iIdFormationCourante")
			.", IdForm=".(!$v_sExportation?"'{$v_iIdForm}'":"'".$this->retIdParent()."'")
			.", DateDebMod=NOW()"
			.", DateFinMod=NOW()"
			.", InscrSpontEquipeM='0'"
			.", NbMaxDsEquipeM='3'"
			.", IdPers='0'"
			.", OrdreMod='{$this->oEnregBdd->OrdreMod}'"
			.", IdIntitule='{$this->oEnregBdd->IdIntitule}'"
			.", NumDepartIntitule='{$this->oEnregBdd->NumDepartIntitule}'";
		
		if ($v_sExportation)
		{
			$sSqlExportForm .= $sRequeteSql . ";\n\n";
			$sSqlExportForm .= "SET @iIdModuleCourant := LAST_INSERT_ID();\n\n";
			
			return -1;
		}
		else
		{
			$this->oBdd->executerRequete($sRequeteSql);
			
			return $this->oBdd->retDernierId();
		}
	}
	
	/**
	 * Copie les rubriques du module courant dans un autre
	 * 
	 * @param	v_iIdMod l'id du module de destination
	 */
	function copierRubriques($v_iIdMod, $v_sExportation = NULL)
	{
		$this->initRubriques();
		foreach ($this->aoRubriques as $oRubrique)
			$oRubrique->copier($v_iIdMod, TRUE, $v_sExportation);
		$this->aoRubriques = NULL;
	}
	
	/**
	 * Réinitialise l'objet \c oEnregBdd avec le module courant
	 */
	function rafraichir ()
	{
		if ($this->retId() > 0) $this->init();
	}
	
	/**
	 * Retourne le nombre de modules d'une formation
	 * 
	 * @param	v_iIdForm l'id de la formation
	 * 
	 * @return	le nombre de modules d'une formation
	 */
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
	 * Retourne l'intitulé du module précédent.
	 * 
	 * @param	v_iIdForm l'id de la formation
	 * 
	 * @return	un tableau avec l'intitulé du module précédent ou s'il n'existe pas, un intitulé de base.
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
	 * Ajoute un nouveau module à la formation
	 * 
	 * @param	v_iIdForm	l'id de la formation
	 * @param	v_iIdPers	l'id de la personne conceptrice du module
	 * 
	 * @return	l'id du nouveau module
	 */
	 function ajouter ($v_iIdForm=NULL,$v_iIdPers=0)
	{
		$asInfosIntituleModPrecedent = $this->retInfosIntituleModPrecedent($v_iIdForm);
		
		$iNumOrdre = $this->retNombreLignes($v_iIdForm)+1;
		
		$sRequeteSql = "INSERT INTO Module SET"
			." IdMod=NULL"
			.", NomMod='".MySQLEscapeString(INTITULE_MODULE." sans nom")."'"
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
	 * Inscrit la personne en tant que concepteur de ce module
	 * 
	 * @param	v_iIdPers l'id de la personne
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
	
	/**
	 * Efface la totalité d'un module
	 */
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
	
	/**
	 * Efface un module dans la DB
	 */
	function effacerModule ()
	{
		$sRequeteSql = "DELETE FROM Module"
			." WHERE IdMod='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface tous les forums associés à ce module
	 */
	function effacerForums ()
	{
		$this->initForums();
		foreach ($this->aoForums as $oForum)
			$oForum->effacer();
		$this->aoForums = NULL;
	}
	
	/**
	 * Efface les équipes associées à ce module
	 */
	function effacerEquipes ()
	{
		$oEquipe = new CEquipe($this->oBdd);
		$oEquipe->effacerParNiveau(TYPE_MODULE,$this->retId());
	}
	
	/**
	 * Efface les étudiants associés à ce module
	 */
	function effacerEtudiants ()
	{
		$sRequeteSql = "DELETE FROM Module_Inscrit"
			." WHERE IdMod='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface les tuteurs associés à ce module
	 */
	function effacerTuteurs ()
	{
		$sRequeteSql = "DELETE FROM Module_Tuteur"
			." WHERE IdMod='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface les concepteurs associés à ce module
	 */
	function effacerConcepteurs ()
	{
		$sRequeteSql = "DELETE FROM Module_Concepteur"
			." WHERE IdMod='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface les rubriques associés à ce module
	 */
	function effacerRubriques ()
	{
		$iNbrRubriques = $this->initRubriques();
		
		for ($idx=0; $idx<$iNbrRubriques; $idx++)
			$this->aoRubriques[$idx]->Effacer();
		
		$this->aoRubriques = NULL;
	}
	
	/**
	 * Initialise un tableau contenant tous les forums de ce module
	 * 
	 * @return	le nombre de forums de ce module
	 */
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
	
	/**
	 * Initialise un tableau avec les concepteurs (table Module_Concepteur) du module
	 * 
	 * @return	le nombre de personnes(concepteurs) insérées dans le tableau
	 */
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
	
	/**
	 * Initialise un tableau avec les tuteurs (table Module_Tuteur) du module
	 * 
	 * @return	le nombre de personnes(tuteurs) insérées dans le tableau
	 */
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
	
	/**
	 * Retourne la constante qui définit le niveau "module", de la structure d'une formation
	 * 
	 * @return	la constante qui définit le niveau "module", de la structure d'une formation
	 */
	function retTypeNiveau () { return TYPE_MODULE; }
	
	
	/**
	 * Initialise un tableau avec les étudiants inscrits à ce module
	 * 
	 * @return	le nombre de personnes(étudiants) insérées dans le tableau
	 */
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
	
	/**
	 * Initialise la rubrique courant (\c oRubriqueCourante) du module
	 * 
	 * @param	v_iIdRubrique l'id de la rubrique
	 */
	function initRubriqueCourante ($v_iIdRubrique=NULL)
	{
		if ($v_iIdRubrique > 0)
			$this->oRubriqueCourante = new CModule_Rubrique($this->oBdd,$v_iIdRubrique);
	}
	
	
	/**
	 * Initialise un tableau contenant la liste des rubriques appartenant à ce module
	 * 
	 * @param	v_iTypeRubriques sélection sur le type de rubrique(optionnel)
	 * 
	 * @return	le nombre de rubrique insérés dans le tableau
	 */
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
	
	/**
	 * Définit l'id du module et initialise l'objet(optionnel)
	 * 
	 * @param	v_iId	l'id du module
	 * @param	v_bInit	si \c true(par défaut), initialise l'objet
	 */
	function defId ($v_iId,$v_bInit=TRUE)
	{
		$this->iId = $v_iId;
		
		if ($v_bInit)
			$this->init();
	}
	
	/** @name Fonctions de lecture des champs pour ce module */
	//@{
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	
	function retNom ($v_bHtmlEntities=FALSE) { return ($v_bHtmlEntities ? htmlentities($this->oEnregBdd->NomMod,ENT_COMPAT,"UTF-8") : $this->oEnregBdd->NomMod); }
	
	function retNomComplet ($v_bHtmlEntities=FALSE)
	{
		$sIntitule = $this->retTexteIntitule();
		$sNomComplet = (strlen($sIntitule) > 0 ? "{$sIntitule} : " : NULL)
			.$this->oEnregBdd->NomMod;
		return ($v_bHtmlEntities ? htmlentities($sNomComplet,ENT_COMPAT,"UTF-8") : $sNomComplet);
	}
	
	function retTexteIntitule ($v_bAfficherNumOrdre=TRUE)
	{
		$sNomIntitule = $this->oIntitule->retNom();
		
		return (strlen($sNomIntitule) > 0 ? "{$sNomIntitule}" : NULL)
			.($v_bAfficherNumOrdre && $this->oEnregBdd->NumDepartIntitule > 0
				? "&nbsp;{$this->oEnregBdd->NumDepartIntitule}"
				: NULL);
	}
	
	function retDescr ($v_bHtmlEntities=FALSE) { return ($v_bHtmlEntities ? htmlentities($this->oEnregBdd->DescrMod,ENT_COMPAT,"UTF-8") : $this->oEnregBdd->DescrMod); }
	function retDateDeb () { return $this->oEnregBdd->DateDebMod; }
	function retDateFin () { return $this->oEnregBdd->DateFinMod; }
	function retStatut () { return $this->oEnregBdd->StatutMod; }
	function retInscrSpontEquipe () { return $this->oEnregBdd->InscrSpontEquipeM; }
	function retNbMaxDsEquipe () { return $this->oEnregBdd->NbMaxDsEquipeM; }
	function retIdParent () { return (is_numeric($this->oEnregBdd->IdForm) ? $this->oEnregBdd->IdForm : 0); }
	function retIdIntitule () { return $this->oEnregBdd->IdIntitule; }
	function retNumDepart () { return $this->oEnregBdd->NumDepartIntitule; }
	function retNumOrdre () { return $this->oEnregBdd->OrdreMod; }
	function retIdPers () { return $this->oEnregBdd->IdPers; }
	//@}
	
	/** @name Fonctions de définition des champs pour ce module */
	//@{
	function defStatut ($v_iStatut)
	{
		if (is_numeric($v_iStatut))
			$this->mettre_a_jour("StatutMod",$v_iStatut);
	}
	
	
	function defNumOrdre ($v_iNumOrdre)
	{
		if (is_numeric($v_iNumOrdre))
			$this->mettre_a_jour("OrdreMod",$v_iNumOrdre);
	}
	
	function defIdIntitule ($v_iIdIntitule) { $this->mettre_a_jour("IdIntitule",$v_iIdIntitule); }
	
	function defNumDepart ($v_iNumDepart)
	{
		if ($v_iNumDepart >= 0 && $v_iNumDepart <= 254)
			$this->mettre_a_jour("NumDepartIntitule",$v_iNumDepart);
	}

	function defNom ($v_sNom)
	{
		$v_sNom = MySQLEscapeString($v_sNom);
		if (empty($v_sNom))
			$v_sNom = INTITULE_MODULE." sans nom";
		$this->mettre_a_jour("NomMod",$v_sNom);
	}
	
	function defDescr ($v_sDescr) { $this->mettre_a_jour("DescrMod",$v_sDescr); }
	//@}
	
	/**
	 * Met à jour un champ de la table Module
	 * 
	 * @param	v_sNomChamp		le nom du champ à mettre à jour
	 * @param	v_mValeurChamp	la nouvelle valeur du champ
	 * @param	v_iIdMod		l'id du module
	 * 
	 * @return	\c true si il a mis à jour le champ dans la DB
	 */
	function mettre_a_jour ($v_sNomChamp,$v_mValeurChamp,$v_iIdMod=0)
	{
		if ($v_iIdMod < 1)
			$v_iIdMod = $this->retId();
		
		if ($v_iIdMod < 1)
			return FALSE;
		
		$sRequeteSql = "UPDATE Module SET"
			." {$v_sNomChamp}='".MySQLEscapeString($v_mValeurChamp)."'"
			." WHERE IdMod='{$v_iIdMod}'";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	/**
	 * Initialise un tableau avec les étudiants inscrits à la formation
	 * 
	 * @param	v_bAppartenirEquipe	si \c true (par défaut) le tableau est rempli par les personnes qui appartiennent à 
	 *								une équipe de ce module, si \c false voir paramètre \p v_bAutoInscrit
	 * 
	 * @param	v_bAutoInscrit		si \c true (par défaut) le tableau est rempli par les personnes qui sont inscrites à
	 * 								la formation et qui n'appartiennent pas à une équipe de ce module. Utilisé lorsque
	 * 								les personnes sont automatiquement inscrites aux modules de la formation.
	 * 								Si \c false il est rempli par les personnes qui sont inscrites au module et qui n'
	 * 								appartiennent pas à une équipe de ce module
	 * 
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
	
	/**
	 * Initialise un tableau contenant la liste des modules de la formation
	 * 
	 * @return	le nombre de modules insérés dans le tableau
	 */
	function retListeModules ()
	{
		$this->aoModules = array();
		
		$sRequeteSql = "SELECT * FROM Module"
			." WHERE IdForm=".$this->retIdParent()
			." ORDER BY OrdreMod ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$i = 0;
		
		while ($this->aoModules[$i++] = $this->oBdd->retEnregSuiv($hResult))
			;
		
		$this->oBdd->libererResult($hResult);
		
		return (count($this->aoModules));
	}
	
	/**
	 * Redistribue les numéros d'ordre des modules
	 * 
	 * @param	v_iNouveauNumOrdre le nouveau numéro d'ordre du module courant
	 * 
	 * @return	\c true si les numéros ont bien été modifiés
	 */
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
	
	/**
	 * Execute une requête sql et vérifie s'il elle retourne un enregistrement
	 * 
	 * @param	v_sRequeteSql la requête sql
	 * 
	 * @return	\c true s'il existe un enregistrement
	 */
	function verifInscrit ($v_sRequeteSql)
	{
		$bOk = FALSE;
		$hResult = $this->oBdd->executerRequete($v_sRequeteSql);
		if ($this->oBdd->retEnregSuiv($hResult))
			$bOk = TRUE;
		$this->oBdd->libererResult($hResult);
		return $bOk;
	}
	
	/**
	 * Vérifie si la personne est "concepteur" du module
	 * 
	 * @param	v_iIdPers l'id de la personne
	 * 
	 * @return	\c true si la personne est "concepteur"
	 */
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
	
	/**
	 * Vérifie si la personne est "tuteur" du module
	 * 
	 * @param	v_iIdPers l'id de la personne
	 * 
	 * @return	\c true si la personne est "tuteur"
	 */
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
	
	/**
	 * Vérifie si la personne est étudiante du module
	 * 
	 * @param	v_iIdPers l'id de la personne
	 * 
	 * @return	\c true si la personne est étudiante
	 */
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
	
	/**
	 * Vérifie si la personne est membre d'une équipe rattachée au module
	 * 
	 * @param	v_iIdPers l'id de la personne
	 * 
	 * @return	\c true si la personne est membre d'un équipe rattachée au module
	 */
	function verifMembre ($v_iIdPers)
	{
		$sRequeteSql = "SELECT Equipe_Membre.IdEquipe"
			." FROM Equipe_Membre"
			." LEFT JOIN Equipe USING (IdEquipe)"
			." WHERE Equipe_Membre.IdPers='{$v_iIdPers}'"
			." AND Equipe.IdMod='{$this->oEnregBdd->IdMod}'";
		return $this->verifInscrit($sRequeteSql);
	}
	
	/**
	 * Rajoute les personnes comme étudiantes rattachées au module
	 * 
	 * @param	v_aiIdPers tableau contenant les id des personnes
	 */
	function inscrireEtudiants ($v_aiIdPers) { $this->inscrirePersonnes($v_aiIdPers,STATUT_PERS_ETUDIANT); }
	
	/**
	 * Rajoute les personnes comme tuteurs du module
	 * 
	 * @param	v_aiIdPers
	 */
	function inscrireTuteurs ($v_aiIdPers) { $this->inscrirePersonnes($v_aiIdPers,STATUT_PERS_TUTEUR); }
	
	/**
	 * Inscrit les personnes comme soit étudiant, soit tuteur, ou soit concepteur d'un module
	 * 
	 * @param	v_aiIdPers	tableau contenat les id des personnes
	 * @param	v_iIdStatut	le statut des personnes à inséré
	 * 
	 * @return	\c false si l'objet \c oEnregBdd n'a pas été initialisé
	 */
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
	 * Retire des personnes inscrites comme soit étudiant, soit tuteur, ou soit concepteur d'un module
	 * 
	 * @param	v_aiIdPers	tableau contenat les id des personnes
	 * @param	v_iIdStatut	le statut des personnes à effacé
	 * 
	 * @return	\c false si l'objet \c oEnregBdd n'a pas été initialisé
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
	
	/**
	 * Initialise un tableau contenant la liste des chats du module
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
	 * Retourne le nombre de chats du module
	 * 
	 * @return	le nombre de chats du module
	 */
	function retNombreChats ()
	{
		$oChat = new CChat($this->oBdd);
		return $oChat->retNombreChats($this);
	}
	
	/**
	 * Ajoute un chat au module
	 * 
	 * @return	l'id du nouveau chat
	 */
	function ajouterChat ()
	{
		$oChat = new CChat($this->oBdd);
		return $oChat->ajouter($this);
	}
	
	/**
	 * Efface tous les chats du module
	 */
	function effacerChats ()
	{
		$oChat = new CChat($this->oBdd);
		$oChat->effacerChats($this);
	}
	
	/**
	 * Initialise un tableau contenant tous les collecticiels du module
	 * 
	 * @param	v_iModaliteSousActiv le numéro représentant le type de modalité pour l'activité (voir les constantes MODALITE_)
	 * 
	 * @return	le nombre de collecticiels insérés dans le tableau
	 */
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
			$this->aoCollecticiels = new CSousActiv($this->oBdd);
			$this->aoCollecticiels[$iIndexCollecticiels]->init($oEnreg);
			$iIndexCollecticiels++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIndexCollecticiels;
	}
	
	/**
	 * Vérifie si l'intitulé est utilisé par plusieurs modules
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
		
		$sRequeteSql = "SELECT COUNT(*) FROM Module"
			." WHERE IdIntitule='{$v_iIdIntitule}'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($this->oBdd->retEnregPrecis($hResult) < 1)
			$bSupprimerIntitule = TRUE;
			
		$this->oBdd->libererResult($hResult);
		
		return $bSupprimerIntitule;
	}
	
	/**
	 * Retourne un tableau à 2 dimensions contenant l'intitulé d'un module
	 * @todo ce système sera modifié pour l'internationalisation de la plate-forme
	 * 
	 * @return	le mot français utilisé pour désigner un module
	 */
	function retTypes ()
	{
		return array(array(0,INTITULE_MODULE));
	}
}

?>
