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
require_once(dirname(__FILE__).'/../../lib/std/IterateurTableau.php');   // pour les itérateurs
require_once(dirname(__FILE__).'/../../lib/std/Erreur.php');             // pour la gestion des erreurs

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
	
	var $bPremierModule     = FALSE; ///< Indication que le module en cours de traitement est le premier de sa formation parente
	var $bDernierModule     = FALSE; ///< Indication que le module en cours de traitement est le dernier de sa formation parente
	var $bPremiereRubrique  = FALSE; ///< Indication que la rubrique en cours de traitement est la première de son module parent
	var $bDerniereRubrique  = FALSE; ///< Indication que la rubrique en cours de traitement est la dernière de son module parent
	var $bPremiereActiv     = FALSE; ///< Indication que l'activité en cours de traitement est la première de sa rubrique parente
	var $bDerniereActiv     = FALSE; ///< Indication que l'activité en cours de traitement est la dernière de sa rubrique parente
	var $bPremiereSousActiv = FALSE; ///< Indication que la sous-activité en cours de traitement est la première de son activité parente
	var $bDerniereSousActiv = FALSE; ///< Indication que la sous-activité en cours de traitement est la dernière de son activité parente

	/**
	 * Constructeur
	 * 
	 * @param	v_oBdd		l'objet CBdd qui représente la connexion courante à la DB
	 * @param	v_iIdForm	l'id de la formation à initialiser automatiquement en vue de la "traversée". S'il est omis, 
	 * 						il faudra le définir en appelant #defElementATraverser() avant de démarrer la traversée
	 */
	function CTraverseur(&$v_oBdd, $v_iIdForm = NULL)
	{
		$this->oBdd = &$v_oBdd;
		if (!empty($v_iIdForm))
			$this->defElementATraverser($v_iIdForm);
	}
	
	/**
	 * Définit l'élément à partir duquel on va débuter la traversée
	 * 
	 * @param	v_iId			l'id de l'élément de départ à traverser
	 * @param	v_iTypeElement	le type de l'élément de départ (formation, module, etc), conformément aux constantes 
	 * 							\c TYPE_ définies dans le fichier plate_forme.class.php
	 * @param	v_bReInit		si \c true (défaut), toutes les variables internes concernant les traitements en cours 
	 * 							sont au préalable réinitialisées
	 */
	function defElementATraverser($v_iId, $v_iTypeElement = TYPE_FORMATION, $v_bReInit = TRUE)
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
				Erreur::provoquer("Niveau de départ non reconnu");
		}
	}
	
	/**
	 * Remet à zéro les variables internes concernant les éléments (formation, module, etc) en cours de traitement
	 */
	function reInit()
	{
		$this->oFormation = NULL;
		$this->oModule    = NULL;
		$this->oRubrique  = NULL;
		$this->oActiv     = NULL;
		$this->oSousActiv = NULL;
		
		$this->bPremierModule     = FALSE;
		$this->bDernierModule     = FALSE;
		$this->bPremiereRubrique  = FALSE;
		$this->bDerniereRubrique  = FALSE;
		$this->bPremiereActiv     = FALSE;
		$this->bDerniereActiv     = FALSE;
		$this->bPremiereSousActiv = FALSE;
		$this->bDerniereSousActiv = FALSE;
	}
	
	/**
	 * Débute la traversée d'une formation, ou d'un élément de formation, en appelant des fonctions de notifications 
	 * prédéfinies en chemin (ces fonctions de notifications peuvent être redéfinies par des sous-classes)
	 */
	function demarrer()
	{
		$this->debutTraitement();

		$this->{$this->fnParcourir}();
		
		$this->finTraitement();
	}
	
	/**
	 * Initialise les modules enfants de la formation actuellement traitée, parcourt récursivement les modules 
	 * trouvés s'il y en a, et appelle des fonctions de notification en début et en fin pour d'éventuels traitements 
	 * supplémentaires (dans des sous-classes de celle-ci)
	 */
	function parcourirFormation()
	{
		$this->oFormation->initModules();
		
		$this->debutFormation();
		
		$itr = new IterateurTableau($this->oFormation->aoModules);
		for (; $itr->estValide(); $itr->suiv())
		{
			$this->bPremierModule = $itr->estPremier();
			$this->bDernierModule = $itr->estDernier();
			
			$this->oModule = $itr->courant();
			$this->parcourirModule();
		}
				
		$this->finFormation();
	}
	
	/**
	 * Initialise les rubriques enfants du module actuellement traité, parcourt récursivement les rubriques trouvées 
	 * s'il y en a, et appelle des fonctions de notification en début et en fin pour d'éventuels traitements 
	 * supplémentaires (dans des sous-classes de celle-ci)
	 */
	function parcourirModule()
	{
		$this->oModule->initRubriques();
		
		$this->debutModule();
		
		$itr = new IterateurTableau($this->oModule->aoRubriques);
		for (; $itr->estValide(); $itr->suiv())
		{
			$this->bPremiereRubrique = $itr->estPremier();
			$this->bDerniereRubrique = $itr->estDernier();
			
			$this->oRubrique = $itr->courant();
			$this->parcourirRubrique();
		}
				
		$this->finModule();
	}
	
	/**
	 * Initialise les activités enfants de la rubrique actuellement traitée, parcourt récursivement les activités 
	 * trouvées s'il y en a, et appelle des fonctions de notification en début et en fin pour d'éventuels traitements 
	 * supplémentaires (dans des sous-classes de celle-ci)
	 */
	function parcourirRubrique()
	{
		$this->oRubrique->initActivs();
		
		$this->debutRubrique();
		
		$itr = new IterateurTableau($this->oRubrique->aoActivs);
		for (; $itr->estValide(); $itr->suiv())
		{
			$this->bPremiereActiv = $itr->estPremier();
			$this->bDerniereActiv = $itr->estDernier();
			
			$this->oActiv = $itr->courant();
			$this->parcourirActiv();
		}
				
		$this->finRubrique();
	}
	
	/**
	 * Initialise les sous-activités enfants de l'activité actuellement traitée, parcourt récursivement les 
	 * sous-activités trouvées s'il y en a, et appelle des fonctions de notification en début et en fin pour d'éventuels 
	 * traitements supplémentaires (dans des sous-classes de celle-ci)
	 */	
	function parcourirActiv()
	{
		$this->oActiv->initSousActivs();
		
		$this->debutActiv();
		
		$itr = new IterateurTableau($this->oActiv->aoSousActivs);
		for (; $itr->estValide(); $itr->suiv())
		{
			$this->bPremiereSousActiv = $itr->estPremier();
			$this->bDerniereSousActiv = $itr->estDernier();
			
			$this->oSousActiv = $itr->courant();
			$this->parcourirSousActiv();
		}
				
		$this->finActiv();
	}
	
	/**
	 * Pour l'instant, aucune action spécifique n'est exécutée dans une sous-activité parcourue; seules les deux 
	 * fonction de notification avant/après, qui peuvent être redéfinies dans des sous-classes, sont appelées
	 */
	function parcourirSousActiv()
	{
		$this->debutSousActiv();
		
		$this->finSousActiv();
	}
	

	/**
	 * Indique si le module actuellement traité est le premier de sa formation parente
	 */
	function estPremierModule()
	{
		return $this->bPremierModule;
	}
	
	/**
	 * Indique si le module actuellement traité est le dernier de sa formation parente
	 */
	function estDernierModule()
	{
		return $this->bDernierModule;
	}
	
	/**
	 * Indique si la rubrique actuellement traitée est la première de son module parent
	 */
	function estPremiereRubrique()
	{
		return $this->bPremiereRubrique;
	}
	
	/**
	 * Indique si la rubrique actuellement traitée est la dernière de son module parent
	 */
	function estDerniereRubrique()
	{
		return $this->bDerniereRubrique;
	}
	
	/**
	 * Indique si l'activité actuellement traitée est la première de sa rubrique parente
	 */
	function estPremiereActiv()
	{
		return $this->bPremiereActiv;
	}

	/**
	 * Indique si l'activité actuellement traitée est la dernière de sa rubrique parente
	 */
	function estDerniereActiv()
	{
		return $this->bDerniereActiv;
	}
	
	/**
	 * Indique si la sous-activité actuellement traitée est la première de son activité parente
	 */
	function estPremiereSousActiv()
	{
		return $this->bPremiereSousActiv;
	}
	
	/**
	 * Indique si la sous-activité actuellement traitée est la dernière de son activité parente
	 */
	function estDerniereSousActiv()
	{
		return $this->bDerniereSousActiv;
	}
	
	/**
	 * @name	Fonctions appelées automatiquement durant la traversée des éléments de formation, qui permettent, si 
	 * 			elles sont implémentées dans des sous-classes, d'effectuer des traitements spécifiques à chaque étape 
	 * 			de la traversée
	 */
	//@{
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
	//@}
}

?>
