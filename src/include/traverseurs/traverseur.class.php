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
 * @file	traverseur.class.php
 * 
 * Contient la classe de base pour les "traverseurs" de formations
 */

require_once(dirname(__FILE__).'/../../database/formation.tbl.php');     // pour CFormation
require_once(dirname(__FILE__).'/../../database/module.tbl.php');        // pour CModule
require_once(dirname(__FILE__).'/../../database/rubrique.tbl.php');      // pour CModule_Rubrique
require_once(dirname(__FILE__).'/../../database/activite.tbl.php');      // pour CActiv
require_once(dirname(__FILE__).'/../../database/sous_activite.tbl.php'); // pour CSousActiv
require_once(dirname(__FILE__).'/../plate_forme.class.php');             // pour les constantes TYPE_

/**
 * Classe de base pour les "traverseurs". Ceux-ci permettent de traverser une formation et tous ses descendants, 
 * càd modules, rubriques, etc, et d'effectuer des actions à chaque étape, en disposant des infos sur l'étape
 * courante (id, nom, etc de la formation, module, etc; fichiers associés)
 */
class CTraverseur
{
	var $oBdd;			///< Objet représentant la connexion à la DB
	
	var $oFormation;	///< Objet qui contiendra la formation en cours de traitement
	var $oModule;		///< Objet qui contiendra le module en train d'être traité
	var $oRubrique;		///< Objet qui contiendra la rubrique en cours de traitement
	var $oActiv;		///< Objet qui contiendra l'activité en cours de traitement
	var $oSousActiv;	///< Objet qui contiendra la sous-activité en cours de traitement
	
	var $fnParcourir;	///< Pointeur vers la fonction chargée de démarrer le traitement (dépend du niveau départ : formation, module, etc.)
	
	var $bPremierModule = FALSE;
	var $bDernierModule = FALSE;
	var $bPremiereRubrique = FALSE;
	var $bDerniereRubrique = FALSE;
	var $bPremiereActiv = FALSE;
	var $bDerniereActiv = FALSE;
	var $bPremiereSousActiv = FALSE;
	var $bDerniereSousActiv = FALSE;
	
	/**
	 * Constructeur
	 * 
	 * @param	v_oBdd		l'objet CBdd qui représente la connexion courante à la DB
	 * @param	v_iIdForm	l'id de la formation à initialiser automatiquement en vue de la "traversée". S'il est omis, 
	 * 						il faudra le définir en appelant #defIdDepart() avant de démarrer la traversée
	 */
	function CTraverseur(&$v_oBdd, $v_iIdForm = NULL)
	{
		$this->oBdd = &$v_oBdd;
		if (!empty($v_iIdForm))
			$this->defIdDepart($v_iIdForm);
	}
	
	function defIdDepart($v_iId, $v_iTypeElement = TYPE_FORMATION, $v_bReInit = TRUE)
	{
		if ($v_bReInit)
			$this->reInit();	
		
		switch($v_iTypeElement)
		{
			case TYPE_FORMATION:
				$this->oFormation = new CFormation($this->oBdd, $v_iId);
				$this->fnParcourir = 'parcourirFormation';
				break;
			
			case TYPE_MODULE:
				$this->oModule = new CModule($this->oBdd, $v_iId);
				$this->fnParcourir = 'parcourirModule';
				break;
			
			case TYPE_RUBRIQUE:
				$this->oRubrique = new CModuleRubrique($this->oBdd, $v_iId);
				$this->fnParcourir = 'parcourirRubrique';
				break;
			
			case TYPE_ACTIVITE:
				$this->oActiv = new CActiv($this->oBdd, $v_iId);
				$this->fnParcourir = 'parcourirActiv';
				break;
			
			case TYPE_SOUS_ACTIVITE:
				$this->oSousActiv = new CSousActiv($this->oBdd, $v_iId);
				$this->fnParcourir = 'parcourirSousActiv';
				break;
				
			default:
				die("Erreur: Niveau de départ non reconnu dans CTraverseur::defIdDepart()");
		}
	}
	
	function reInit()
	{
		$this->oFormation = NULL;
		$this->oModule    = NULL;
		$this->oRubrique  = NULL;
		$this->oActiv     = NULL;
		$this->oSousActiv = NULL;
		
		$this->bPremierModule = FALSE;
		$this->bDernierModule = FALSE;
		$this->bPremiereRubrique = FALSE;
		$this->bDerniereRubrique = FALSE;
		$this->bPremiereActiv = FALSE;
		$this->bDerniereActiv = FALSE;
		$this->bPremiereSousActiv = FALSE;
		$this->bDerniereSousActiv = FALSE;
	}
	
	function demarrer()
	{
		$this->debutTraitement();

		$this->{$this->fnParcourir}();
		
		$this->finTraitement();
	}
	
	function parcourirFormation()
	{
		$this->debutFormation();
		
		if ($this->oFormation->initModules())
		{
			$iNbModules = count($this->oFormation->aoModules);
			$iNumModule = 0;
			foreach ($this->oFormation->aoModules as $this->oModule)
			{
				$i++;
				$this->bPremierModule = ($i == 1);
				$this->bDernierModule =  
				$this->parcourirModule();
			}
		}
				
		$this->finFormation();
	}
	
	function parcourirModule()
	{
		$this->debutModule();
		
		if ($this->oModule->initRubriques())
			foreach ($this->oModule->aoRubriques as $this->oRubrique)
				$this->parcourirRubrique();
				
		$this->finModule();
	}
	
	function parcourirRubrique()
	{
		$this->debutRubrique();
		
		if ($this->oRubrique->initActivs())
			foreach ($this->oRubrique->aoActivs as $this->oActiv)
				$this->parcourirActiv();
				
		$this->finRubrique();
	}
	
	function parcourirActiv()
	{
		$this->debutActiv();
		
		if ($this->oActiv->initSousActivs())
			foreach ($this->oActiv->aoSousActivs as $this->oSousActiv)
				$this->parcourirSousActiv();
				
		$this->finActiv();
	}
	
	function parcourirSousActiv()
	{
		$this->debutSousActiv();
		
		$this->finSousActiv();
	}
	
	function debutTraitement() { }
	function finTraitement() { }
	function debutFormation() { }
	function finFormation() { }
	function debutModule() { }
	function finModule() { }
	function debutRubrique() { }
	function finRubrique() { }
	function debutActiv() { }
	function finActiv() { }
	function debutSousActiv() { }
	function finSousActiv() { }
}

?>
