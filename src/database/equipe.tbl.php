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
 * @file	equipe.tbl.php
 * 
 * Contient les classes de gestion des équipes
 * 
 * @date	2003/01/28
 * 
 * @author	Filippo PORCO
 */

require_once(dir_database("ids.class.php"));

/**
 * Gestion des équipes, et encapsulation de la table Equipe de la DB
 */
class CEquipe
{
	var $iId;			///< Utilisé dans le constructeur, pour indiquer l'id de l'équipe à récupérer dans la DB
	
	var $oBdd;			///< Objet représentant la connexion à la DB
	var $oEnregBdd;		///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	var $aoMembres;		///< Tableau d'objets CPersonne représentant les membres de l'équipe (rempli par #initMembres())
	var $aoEquipes;		///< Tableau d'objets CEquipe rempli par diverses fonctions d'initialisation sur des critères spécifiques
	
	/**
	 * Constructeur
	 * 
	 * @param	v_oBdd			l'objet CBdd qui représente la connexion courante à la DB
	 * @param	v_iId			l'id de l'équipe à récupérer dans la DB. S'il est omis ou si l'équipe demandée n'existe 
	 * 							pas dans la DB, l'objet est créé mais ne contient aucune donnée provenant de la DB
	 * @param	v_bInitMembres	si \c true, les personnes composant l'équipe seront initialisées automatiquement dans 
	 * 							le tableau \c aoMembres, sous forme d'objets CPersonne
	 * 
	 * @see	#init()
	 */
	function CEquipe(&$v_oBdd, $v_iId = NULL, $v_bInitMembres = FALSE)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iId;
		
		if (isset($this->iId))
		{
			$this->init();
			
			if ($v_bInitMembres)
				$this->initMembres();
		}
	}
	
	/**
	 * Voir CPersonne#init()
	 */
	function init($v_oEnregBdd = NULL)
	{
		if (isset($v_oEnregBdd))
		{
			$this->oEnregBdd = $v_oEnregBdd;
			$this->iId = $this->oEnregBdd->IdEquipe;
		}
		else
		{
			$sRequeteSql =
				 " SELECT * FROM Equipe"
				." WHERE IdEquipe='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	/**
	 * Verrouille ou retourne les noms des tables généralement utilisées lors des requêtes relatives aux équipes
	 * 
	 * @param	v_bExecuterRequete	si \c true, la requête LOCK TABLES sur les tables utilisées pendant les requêtes 
	 * 								concernant les équipes est réellement exécutée. Si \c false, les noms de ces tables 
	 * 								nécessaires à une requête LOCK TABLES seront retournés, mais le lock ne sera pas 
	 * 								exécuté
	 * 
	 * @return	les noms des tables utilisées pendant les requêtes concernant les équipes, suivi de WRITE et séparés par 
	 * 			des virgules. La chaîne retournée pourra donc être utilisée pour composer une requête LOCK TABLES
	 */
	function verrouillerTables($v_bExecuterRequete = TRUE)
	{
		$sListeTables = "Equipe WRITE, Equipe_Membre WRITE";
		if ($v_bExecuterRequete) $this->oBdd->executerRequete("LOCK TABLES {$sListeTables}");
		return $sListeTables;
	}
	
	/**
	 * Initialise l'équipe d'un utilisateur dans un contexte/niveau/élément précis (formation, module, etc)
	 * 
	 * @param	v_iIdPers			l'id de l'utilisateur dont on veut connaître l'équipe
	 * @param	v_iIdNiveau			l'id de l'élément pour lequel on veut récupérer les équipes. Sa signification dépend
	 *								du paramètre \p v_iTypeNiveau
	 * @param	v_iTypeNiveau		le numéro représentant le type d'élément pour lequel on veut récupérer les équipes, 
	 * 								càd formation, module, rubrique, activité, sous-activité (voir les constantes TYPE_)
	 * @param	v_bInitMembres		si \c true, initialise également les membres de l'équipe
	 * @param	v_iIdNiveauDernier	le niveau maximum jusqu'auquel il faut "remonter" pour chercher l'équipe de 
	 * 								l'utilisateur. Par défaut, la rechercher s'effectue jusqu'à la "racine", càd 
	 * 								les équipes créées au niveau de la formation, en passant éventuellement par 
	 * 								sous-activité, activité, rubrique, et module, car le niveau de départ (minimum) de 
	 * 								la recherche dépend du paramètre \p v_iTypeNiveau
	 * 
	 * @return	\c true si l'utilisateur fait bien partie d'une équipe
	 */
	function initEquipe($v_iIdPers, $v_iIdNiveau, $v_iTypeNiveau, $v_bInitMembres = FALSE, $v_iIdNiveauDernier = TYPE_FORMATION)
	{
		$oIds = new CIds($this->oBdd, $v_iTypeNiveau, $v_iIdNiveau);
		$aiIds = $oIds->retTableIds();
		
		$bRemonter = TRUE;
		$asChampsNiveaux = array(NULL, "IdForm", "IdMod", "IdRubrique", "IdActiv", "IdSousActiv");
		
		for ($iIdxNiveau = $v_iTypeNiveau; $iIdxNiveau >= $v_iIdNiveauDernier; $iIdxNiveau--)
		{
			if ($aiIds[$iIdxNiveau] > 0 && isset($asChampsNiveaux[$iIdxNiveau]))
			{
				if ($bRemonter)
				{
					$sRequeteSql = 
						 " SELECT COUNT(*)"
						." FROM Equipe"
						." WHERE Equipe.".$asChampsNiveaux[$iIdxNiveau]."='".$aiIds[$iIdxNiveau]."'"
						.(isset($asChampsNiveaux[$iIdxNiveau+1]) ? " AND Equipe.".$asChampsNiveaux[$iIdxNiveau+1]."='0'" : NULL)
						." LIMIT 1";
					$hResult = $this->oBdd->executerRequete($sRequeteSql);
					$bRemonter = ($this->oBdd->retEnregPrecis($hResult) == 0);
					$this->oBdd->libererResult($hResult);
				}
				
				if ($bRemonter)
					continue;
				
				$sRequeteSql =
					 " SELECT Equipe.*"
					." FROM Equipe_Membre"
					."  LEFT JOIN Equipe USING (IdEquipe)"
					." WHERE Equipe_Membre.IdPers='{$v_iIdPers}'"
					."  AND Equipe.".$asChampsNiveaux[$iIdxNiveau]."='".$aiIds[$iIdxNiveau]."'"
					.(isset($asChampsNiveaux[$iIdxNiveau+1]) ? " AND Equipe.".$asChampsNiveaux[$iIdxNiveau+1]."='0'" : NULL)
					." LIMIT 1";
				$hResult = $this->oBdd->executerRequete($sRequeteSql);
				
				if ($hResult !== FALSE && ($oEnreg = $this->oBdd->retEnregSuiv($hResult)))
				{
					$this->oBdd->libererResult($hResult);
					
					$this->init($oEnreg);
					
					if ($v_bInitMembres)
						$this->initMembres();
					
					return TRUE;
				}
				
				break;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * Initialise un tableau d'équipes (\c aoEquipes) selon le niveau (formation/module/etc) où elles sont définies. 
	 * Plusieurs niveaux peuvent être spécifiés en paramètres en même temps
	 * 
	 * @param	v_iIdForm		l'id de la formation dans laquelle il faut chercher les équipes à récupérer.
	 * 							Si \c null (défaut), la recherche ne porte pas sur l'appartenance à une formation
	 * @param	v_iIdMod		l'id du module dans lequel il faut chercher les équipes à récupérer.
	 * 							Si \c null (défaut), la recherche ne porte pas sur l'appartenance à un module
	 * @param	v_iIdRubrique	l'id de la rubrique dans laquelle il faut chercher les équipes à récupérer.
	 * 							Si \c null (défaut), la recherche ne porte pas sur l'appartenance à une rubrique
	 * @param	v_iIdActiv		l'id de l'activité dans laquelle il faut chercher les équipes à récupérer
	 * 							Si \c null (défaut), la recherche ne porte pas sur l'appartenance à une activité
	 * @param	v_iIdSousActiv	l'id de la sous-activité dans laquelle il faut chercher les équipes à récupérer.
	 * 							Si \c null (défaut), la recherche ne porte pas sur l'appartenance à une sous-activité
	 * @param	v_bInitMembres	si \c true, initialise également les membres de l'équipe (défaut à \c false)
	 * 
	 * @return	le nombre d'équipes trouvées
	 */
	function initEquipes($v_iIdForm = NULL, $v_iIdMod = NULL, $v_iIdRubrique = NULL, $v_iIdActiv = NULL, $v_iIdSousActiv = NULL, $v_bInitMembres = FALSE)
	{
		$iIdxEquipe = 0;
		
		$this->aoEquipes = array();
		
		$sRequeteSql = 
			 " SELECT * FROM Equipe WHERE (1=1)"
			.(empty($v_iIdForm) ? NULL : " AND IdForm='{$v_iIdForm}'")
			.(empty($v_iIdMod) ? NULL : " AND IdMod='{$v_iIdMod}'")
			.(empty($v_iIdRubrique) ? NULL : " AND IdRubrique='{$v_iIdRubrique}'")
			.(empty($v_iIdActiv) ? NULL : " AND IdActiv='{$v_iIdActiv}'")
			.(empty($v_iIdSousActiv) ? NULL : " AND IdSousActiv='{$v_iIdSousActiv}'")
			." ORDER BY NomEquipe";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoEquipes[$iIdxEquipe] = new CEquipe($this->oBdd);
			$this->aoEquipes[$iIdxEquipe]->init($oEnreg);
			
			if ($v_bInitMembres)
				$this->aoEquipes[$iIdxEquipe]->initMembres();
			
			$iIdxEquipe++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxEquipe;
	}
	
	/**
	 * Initialise un tableau d'équipes (\c aoEquipes) selon le niveau (formation/module/etc) où elles sont définies.
	 * Un seul niveau est passé en paramètre, contrairement à #initEquipes(). Permet aussi de restreindre aux équipes 
	 * qui sont explicitement définies au niveau spécifié (qui ne sont pas des équipes définie à un niveau plus élevé 
	 * et "héritées"
	 * 
	 * @param	v_iTypeNiveau			le numéro représentant le type d'élément pour lequel on veut récupérer les 
	 * 									équipes, càd formation, module, rubrique, activité, sous-activité (voir les 
	 *									constantes TYPE_)
	 * @param	v_iIdNiveau				l'id de l'élément pour lequel on veut récupérer les équipes. Sa signification 
	 * 									dépend du paramètre \p v_iTypeNiveau
	 * @param	v_bInitMembres			si \c true, initialise également les membres de chaque équipe (défaut à \c false)
	 * @param	v_bNonEquipesEnfants	si \c true, la recherche des équipes à initialiser se restreint aux équipes 
	 * 									explicitement définies au niveau spécifié par \p v_iTypeNiveau, càd pas des 
	 * 									équipes définies à un niveau plus élevé et héritée à ce niveau
	 * 
	 * @return	le nombre d'équipe trouvées, ou 0 si v_iTypeNiveau ou v_iIdNiveau sont < 1 ou invalides
	 */
	function initEquipesNiveau($v_iTypeNiveau, $v_iIdNiveau, $v_bInitMembres = FALSE, $v_bNonEquipesEnfants = TRUE)
	{
		$iIdxEquipe = 0;
		$this->aoEquipes = array();
		
		if ($v_iTypeNiveau < 1 || $v_iIdNiveau < 1)
			return $iIdxEquipe;
		
		$asRecherche = array(NULL, "IdForm", "IdMod", "IdRubrique", NULL, "IdActiv", "IdSousActiv", NULL);
		
		if (!isset($asRecherche[$v_iTypeNiveau]))
			return $iIdxEquipe;
		
		$sRequeteSql = 
			 " SELECT * FROM Equipe"
			." WHERE ".$asRecherche[$v_iTypeNiveau]."='{$v_iIdNiveau}'"
			.($v_bNonEquipesEnfants && isset($asRecherche[$v_iTypeNiveau+1])
				? " AND ".$asRecherche[$v_iTypeNiveau+1]."='0'"
				: NULL)
			." ORDER BY NomEquipe";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($hResult !== FALSE)
		{
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->aoEquipes[$iIdxEquipe] = new CEquipe($this->oBdd);
				$this->aoEquipes[$iIdxEquipe]->init($oEnreg);
				
				if ($v_bInitMembres)
					$this->aoEquipes[$iIdxEquipe]->initMembres();
				
				$iIdxEquipe++;
			}
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $iIdxEquipe;
	}
	
	/**
	 * Initialise un tableau d'équipes (\c aoEquipes) en les cherchant à partir d'un certain niveau de la structure 
	 * (formation/module/etc), tout en passant au niveau supérieur (parent) à chaque fois qu'aucune équipe n'est 
	 * trouvée au niveau courant, et cela jusqu'au "dernier" niveau, càd TYPE_FORMATION. Si une ou plusieurs équipe 
	 * sont trouvées à un niveau, la recherche s'arrête
	 * 
	 * @param	v_iIdNiveauDepart	l'id de l'élément de départ pour lequel on veut récupérer les équipes.
	 * 								Sa signification dépend du paramètre \p v_iTypeNiveau
	 * @param	v_iTypeNiveauDepart	le numéro représentant le type d'élément (formation/module/etc) par lequel on veut 
	 * 								débuter la recherche des équipes à initialiser
	 * @param	v_bInitMembres		si \c true, initialise également les membres de l'équipe (défaut à \c false)
	 * 
	 * @return	le nombre d'équipes trouvées
	 * 
	 * @see		#initEquipesNiveau()
	 */
	function initEquipesEx($v_iIdNiveauDepart, $v_iTypeNiveauDepart, $v_bInitMembres = FALSE)
	{
		$oIds = new CIds($this->oBdd, $v_iTypeNiveauDepart, $v_iIdNiveauDepart);
		$aiIds = $oIds->retListeIds();
		
		// Rechercher les équipes par niveau
		for ($iIdxTypeNiveau = $v_iTypeNiveauDepart; $iIdxTypeNiveau >= TYPE_FORMATION; $iIdxTypeNiveau--)
		{
			if ($aiIds[$iIdxTypeNiveau] < 1)
				continue;
			
			if ($this->initEquipesNiveau($iIdxTypeNiveau, $aiIds[$iIdxTypeNiveau], $v_bInitMembres) > 0)
				break;
		}
		
		return count($this->aoEquipes);
	}
	
	// {{{ Membres
	/**
	 * Initialise les membres de l'équipe (dans le tableau \c aoMembres - objets CPersonne)
	 * 
	 * @return	le nombre de membres de l'équipe
	 */
	function initMembres()
	{
		$iIdxPers = 0;
		$this->aoMembres = array();
		
		$sRequeteSql = 
			 " SELECT Personne.*"
			." FROM Equipe_Membre"
			."  LEFT JOIN Personne USING (IdPers)"
			." WHERE Equipe_Membre.IdEquipe='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($hResult !== FALSE)
		{
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->aoMembres[$iIdxPers] = new CPersonne($this->oBdd);
				$this->aoMembres[$iIdxPers]->init($oEnreg);
				$iIdxPers++;
			}
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $iIdxPers;
	}
	
	/**
	 * Retourne le nombre de personnes/membres composant l'équipe
	 * 
	 * @return	le nombre de membres de l'équipe, ou 0 si celle-ci n'a pas encore été initialisée par #initMembres()
	 * 
	 * @see	#initMembres()
	 */
	function retNbMembres() { return (isset($this->aoMembres) && is_array($this->aoMembres) ? count($this->aoMembres) : 0); }
	
	/**
	 * Vérifier qu'une personne est membre de l'équipe (ou d'une équipe spécifique)
	 * 
	 * @param	v_iIdPers	l'id de la personne dont on veut vérifier l'appartenance à une équipe
	 * @param	v_iIdEquipe	l'id de l'équipe dont on veut vérifier si elle comprend la personne spécifiée par v_iIdPers. 
	 * 						Si \c null, c'est l'équipe actuellement initialisée qui est prise pour la vérification
	 * 
	 * @return	\c true si la personne est bien membre de l'équipe (courante ou spécifiée en paramètre)
	 */
	function verifMembre($v_iIdPers, $v_iIdEquipe = NULL)
	{
		$bVerifMembre = FALSE;
		
		if (empty($v_iIdEquipe))
			$v_iIdEquipe = $this->retId();
		
		if ($v_iIdPers > 0 && $v_iIdEquipe > 0)
		{
			$sRequeteSql = 
				 " SELECT IdPers FROM Equipe_Membre"
				." WHERE IdEquipe='{$v_iIdEquipe}'"
				."  AND IdPers='{$v_iIdPers}'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($hResult !== FALSE && $this->oBdd->retEnregSuiv($hResult))
				$bVerifMembre = TRUE;
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $bVerifMembre;
	}
	// }}}
	
	/**
	 * Initialise un tableau d'équipes (\c aoEquipes) en se basant sur une série d'ids d'équipes
	 * 
	 * @param	v_aiIdEquipes	un tableau contenant les ids des équipes qu'on veut initialiser et qui rempliront le 
	 * 							tableau \c aoEquipes
	 * 
	 * @return	le nombre d'équipes initialisées
	 */
	function initGraceIdEquipes($v_aiIdEquipes)
	{
		$iIdxEquipe = 0;
		$this->aoEquipes = array();
		
		foreach ($v_aiIdEquipes as $iIdEquipe)
			$this->aoEquipes[$iIdxEquipe++] = new CEquipe($this->oBdd, $iIdEquipe);
		
		return $iIdxEquipe;
	}
	
	// --------------------------------
	/**
	 * Insère une nouvelle équipe dans la DB, en utilisant les champs Nom, IdForm, IdMod, IdRubrique, IdActiv, 
	 * IdSousActiv, et Ordre actuellement définis dans l'objet CEquipe (par les fonctions def...()). La fonction 
	 * #init() est ensuite rappelée pour initialiser complètement l'objet courant
	 */
	function ajouter()
	{
		$sRequeteSql = 
			 " INSERT INTO Equipe SET"
			."  IdEquipe=NULL"
			.", NomEquipe=\"".$this->retNom()."\""
			.", IdForm=".$this->retIdFormation()
			.", IdMod=".$this->retIdModule()
			.", IdRubrique=".$this->retIdRubrique()
			.", IdActiv=".$this->retIdActivite()
			.", IdSousActiv=".$this->retIdSousActivite()
			.", OrdreEquipe=".$this->retNumOrdre();
		$this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId();
		$this->init();
	}
	
	/**
	 * Efface de la DB l'équipe actuellement initialisée dans l'objet
	 */
	function effacer()
	{
		$iIdEquipe = $this->retId();
		
		$this->oBdd->executerRequete("LOCK TABLES Equipe_Membre WRITE, Equipe WRITE");
		
		$sRequeteSql = "DELETE FROM Equipe_Membre WHERE IdEquipe='{$iIdEquipe}'";
		$this->oBdd->executerRequete($sRequeteSql);
		$sRequeteSql = "DELETE FROM Equipe WHERE IdEquipe='{$iIdEquipe}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$this->oBdd->executerRequete("UNLOCK TABLES");
	}
	
	/**
	 * Efface les équipes appartenant à un niveau spécifique (formation, module, etc)
	 * 
	 * @param	v_iNiveau	le numéro représentant le type d'élément pour lequel on veut effacer les équipes, 
	 * 						càd formation, module, rubrique, activité, sous-activité (voir les constantes TYPE_)
	 * @param	v_iIdNiveau	l'id de l'élément pour lequel on veut effacer les équipes. Sa signification dépend
	 * 						du paramètre \p v_iNiveau
	 */
	function effacerParNiveau($v_iNiveau, $v_iIdNiveau)
	{
		if ($v_iIdNiveau < 1)
			return;
		
		$sNomChamp = NULL;
		
		switch ($v_iNiveau)
		{
			case TYPE_RUBRIQUE: $sNomChamp = "IdRubrique"; break;
			case TYPE_MODULE: $sNomChamp = "IdMod"; break;
			case TYPE_FORMATION: $sNomChamp = "IdForm"; break;
		}
		
		if ($sNomChamp == NULL)
			return;
		
		$sValeursRequete = NULL;
		
		// Rechercher les équipes à effacer
		$sRequeteSql = 
			 " SELECT IdEquipe FROM Equipe"
			." WHERE {$sNomChamp}='{$v_iIdNiveau}'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
				."'{$oEnreg->IdEquipe}'";
		
		$this->oBdd->libererResult($hResult);
		
		if (isset($sValeursRequete))
		{
			$this->oBdd->executerRequete("LOCK TABLES Equipe_Membre WRITE, Equipe WRITE");
			
			// Effacer les enregistrements de la table "Equipe_Membre"
			$sRequeteSql = 
				 " DELETE FROM Equipe_Membre"
				." WHERE IdEquipe IN ({$sValeursRequete})";
			
			$this->oBdd->executerRequete($sRequeteSql);
			
			// Effacer les enregistrements de la table "Equipe"
			$sRequeteSql = 
				 " DELETE FROM Equipe"
				." WHERE IdEquipe IN ({$sValeursRequete})";
			
			$this->oBdd->executerRequete($sRequeteSql);
			
			$this->oBdd->executerRequete("UNLOCK TABLES");
		}
	}
	
	/**
	 * Met à jour l'équipe courante dans la DB, en utilisant les champs Nom, IdForm, IdMod, IdRubrique, IdActiv, 
	 * IdSousActiv, et Ordre actuellement définis dans l'objet CEquipe (par les fonctions def...())
	 */
	function sauvegarder()
	{
		$sRequeteSql = 
			 " UPDATE Equipe SET"
			."  NomEquipe=\"".$this->retNom()."\""
			.", IdForm=".$this->retIdFormation()
			.", IdMod=".$this->retIdModule()
			.", IdRubrique=".$this->retIdRubrique()
			.", IdActiv=".$this->retIdActivite()
			.", IdSousActiv=".$this->retIdSousActivite()
			.", OrdreEquipe=".$this->retNumOrdre()
			." WHERE IdEquipe='".$this->retId()."'";
		
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Ajoute des nouveaux membres à l'équipe dans la DB
	 * 
	 * @param	v_aiIdPers	le tableau contenant les id des personnes à ajouter à l'équipe
	 * 
	 * @see	CEquipe_Membre#ajouterMembres()
	 */
	function ajouterMembres($v_aiIdPers)
	{
		$oMembre = new CEquipe_Membre($this->oBdd, $this->retId());
		$oMembre->ajouterMembres($v_aiIdPers);
	}
	
	/** @name Fonctions de définition des champs pour cette équipe */
	//@{
	function defNom($v_sNomEquipe) { $this->oEnregBdd->NomEquipe = $v_sNomEquipe; }
	function defIdFormation($v_iIdFormation) { $this->oEnregBdd->IdForm = $v_iIdFormation; }
	function defIdModule($v_iIdModule) { $this->oEnregBdd->IdMod = $v_iIdModule; }
	function defIdRubrique($v_iIdRubrique) { $this->oEnregBdd->IdRubrique = $v_iIdRubrique; }
	function defIdActivite($v_iIdActiv) { $this->oEnregBdd->IdActiv = $v_iIdActiv; }
	function defIdSousActivite($v_iIdSousActiv) { $this->oEnregBdd->IdSousActiv = $v_iIdSousActiv; }
	//@}
	
	/** @name Fonctions de lecture des champs pour cette équipe */
	//@{
	function retId() { return (is_numeric($this->iId) ? $this->iId : 0); }
	/**
	 * Retourne le nom de l'équipe, avec possibilité d'encodages en html ou en "url"
	 * 
	 * @param	v_sMode	si \c "html", les caractères spéciaux sont remplacés par des entités HTML. Si \c "url", le 
	 * 					nom sera encodé en URL. Si autre valeur (ou \c null), le nom de l'équipe est retourné tel 
	 * 					quel
	 * 
	 * @return	le nom de l'équipe dans l'encodage choisi par \p v_sMode
	 */
	function retNom($v_sMode = NULL)
	{
		if ($v_sMode == "html")
			return emb_htmlentities($this->oEnregBdd->NomEquipe);
		else if ($v_sMode == "url")
			return rawurlencode($this->oEnregBdd->NomEquipe);
		else
			return $this->oEnregBdd->NomEquipe;
	}
	function retIdFormation() { return (empty($this->oEnregBdd->IdForm) ? 0 : $this->oEnregBdd->IdForm); }
	function retIdModule() { return (empty($this->oEnregBdd->IdMod) ? 0 : $this->oEnregBdd->IdMod); }
	function retIdRubrique() { return (empty($this->oEnregBdd->IdRubrique) ? 0 : $this->oEnregBdd->IdRubrique); }
	function retIdActivite() { return (empty($this->oEnregBdd->IdActiv) ? 0 : $this->oEnregBdd->IdActiv); }
	function retIdSousActivite() { return (empty($this->oEnregBdd->IdSousActiv) ? 0 : $this->oEnregBdd->IdSousActiv); }
	function retNumOrdre() { return (empty($this->oEnregBdd->OrdreEquipe) ? 0 : $this->oEnregBdd->OrdreEquipe); }
	//@}
	
	// --------------------------------
	
	/**
	 * Retourne le code HTML nécessaire à la création d'un lien qui ouvrira une popup avec renseignements sur l'équipe
	 * 
	 * @return	le code HTML du lien
	 */
	function retLien()
	{
		return "<a href=\"javascript: open('"
			.dir_admin("equipe","liste_equipes-index.php")
			."?idEquipe=".$this->retId()."'"
			.",'WIN_INFO_EQUIPE','resizable=1,width=600,height=450,status=0'); void(0);\""
			." title=\"Cliquer ici pour voir les membres de cette équipe\""
			." onfocus=\"blur()\""
			.">".$this->retNom()."</a>";
	}
}

/**
 * Aide à la gestion et manipulation des membres d'équipes
 */
class CEquipe_Membre
{
	var $oBdd;		///< Objet représentant la connexion à la DB
	var $iId;		///< Utilisé dans le constructeur, pour indiquer l'id de l'équipe à récupérer dans la DB
	
	var $aoMembres;	///< Tableau d'objets CPersonne représentant les membres de l'équipe (rempli par #init() et #initMembres())
	var $asNiveau;	///< Tableau contenant les chaînes correspondant aux noms des champs "de niveau" de la table Equipe (IdForm, IdMod, etc), rempli dans le constructeur
	
	/**
	 * Constructeur. L'objet peut être initialisé avec un id d'équipe. Pour plus de détails sur les constructeurs qui
	 * initialisent des classes représentant des enregistrements de DB, voir CPersonne#CPersonne()
	 * 
	 * @param	v_oBdd		l'objet CBdd qui représente la connexion courante à la DB
	 * @param	v_iIdEquipe	l'id de l'équipe dont les membres seront récupérés dans la DB. S'il est omis ou si l'équipe 
	 * 						demandée n'existe pas dans la DB, l'objet est créé mais ne contient aucune donnée provenant 
	 * 						de la DB
	 */
	function CEquipe_Membre(&$v_oBdd, $v_iIdEquipe = NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdEquipe;
		
		$this->asNiveau = array(NULL, "IdForm", "IdMod", NULL, "IdRubrique", "IdActiv", "IdSousActiv");
		
		if (isset($this->iId))
			$this->init();
	}
	
	/**
	 * Initialise un tableau de personnes (\c aoMembres) sous forme d'objets CPersonne, qui sont les membres de l'équipe 
	 * actuellement représentée par \c iId (qui peut être initialisé par le constructeur #CEquipe_Membre()). 
	 * Les membres sont initialisés par ordre alphabétique sur le nom de famille
	 */
	function init()
	{
		$iIdxMembre = 0;
		
		$this->aoMembres = array();
		
		$sRequeteSql = 
			 " SELECT Personne.*"
			." FROM Equipe_Membre"
			."  LEFT JOIN Personne USING (IdPers)"
			." WHERE Equipe_Membre.IdEquipe='".$this->retId()."'"
			." ORDER BY Personne.Nom ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoMembres[$iIdxMembre] = new CPersonne($this->oBdd);
			$this->aoMembres[$iIdxMembre]->init($oEnreg);
			$iIdxMembre++;
		}
		
		$this->oBdd->libererResult($hResult);
	}
	
	/**
	 * Initialise un tableau de personne (\c aoMembres) avec les membres des équipes répondant à certains critères 
	 * (équipe rattachée à une formation, un cours, etc. particuliers), ou avec les personnes *non*-membres d'équipes 
	 * spécifiques
	 * 
	 * @param	v_iNiveau			le numéro représentant le type d'élément pour lequel on veut récupérer les
	 * 								(non-)membres d'équipes, càd formation, module, rubrique, activité, sous-activité 
	 * 								(voir les constantes TYPE_)
	 * @param	v_iIdNiveau			l'id de l'élément pour lequel on veut récupérer les (non-)membres d'équipes. Sa 
	 * 								signification dépend du paramètre \p v_iTypeNiveau
	 * @param	v_bAppartenirEquipe	si \c true, ce sont les membres des équipes répondant aux critères passés en 
	 * 								paramètres \p v_iNiveau et \p v_iIdNiveau qui sont retournés. Si \c false, ce sont 
	 * 								les personnes n'appartenant *pas* à ces équipes
	 * 
	 * @return	le nombre de membres trouvés
	 */
	function initMembresDe($v_iNiveau, $v_iIdNiveau, $v_bAppartenirEquipe = TRUE)
	{
		$iIdxMembre = 0;
		
		$this->aoMembres = array();
		
		$asIdParent = array(NULL, "IdForm", "IdMod", "IdRubrique", "IdActiv", "IdSousActiv", NULL);
		
		$sRequeteSql = 
			 " SELECT Personne.*"
			." FROM Formation_Inscrit"
			."  LEFT JOIN Equipe_Membre ON Formation_Inscrit.IdPers=Equipe_Membre.IdPers"
			."  LEFT JOIN Equipe ON Formation_Inscrit.IdForm=Equipe.IdForm"
			."  LEFT JOIN Personne ON Formation_Inscrit.IdPers=Personne.IdPers"
			." WHERE Equipe.".$asIdParent[$v_iNiveau]."='".$v_iIdNiveau."'"
			.(isset($asIdParent[$v_iNiveau+1]) ? " AND Equipe.".$asIdParent[$v_iNiveau+1]."='0'" : NULL)
			."  AND Equipe_Membre.IdEquipe IS".($v_bAppartenirEquipe ? " NOT" : NULL)." NULL"
			." GROUP BY Formation_Inscrit.IdPers"
			." ORDER BY Personne.Nom";
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
	 * Vérifie qu'un personne est membre d'équipes spécifiques. Ces équipes (et donc les membres) ont été déterminées 
	 * lors d'appels au constructeur #CEquipe_Membre(), à #init(), ou à #initMembresDe(), qui auront donc initialisés 
	 * au préalable le tableau \c aoMembres. Il est à noter que dans le cas de la dernière fonction, il est possible 
	 * de rechercher les *non*-membres d'équipes spécifiées
	 * 
	 * @param	v_iIdPers	l'id de la personne dont on veut vérifier l'appartenance (ou la non-appartenance) à des 
	 * 						équipes spécifiques
	 * 
	 * @return	\c true si la personne spécifiée est "membre" (existe dans le tableau \c aoMembres); \c false dans le 
	 * 			cas contraire ou dans le cas ou l'id de la personne n'est pas valable ou le tableau \c aoMembres n'est 
	 * 			pas correctement initialisé
	 */
	function verifMembre($v_iIdPers)
	{
		if ($v_iIdPers > 0 && is_array($this->aoMembres))
			foreach ($this->aoMembres as $oMembre)
				if ($oMembre->retId() == $v_iIdPers)
					return TRUE;
		return FALSE;
	}
	
	// --------------------------------
	
	/**
	 * Ajoute (dans la DB) une ou plusieurs personnes à l'équipe qui a servi à initialiser l'objet
	 * 
	 * @param	v_aiIdPers	le tableau contenant les ids des personnes à ajouter à l'équipe. S'il s'agit d'une personne 
	 * 						seule, il n'est pas nécessaire que le paramètre soit un tableau, juste un nombre
	 */
	function ajouterMembres($v_aiIdPers)
	{
		settype($v_aiIdPers, "array");
		
		if (count($v_aiIdPers) < 1 || $this->iId < 1)
			return;
		
		$sValeursRequete = NULL;
		
		foreach ($v_aiIdPers as $iIdPers)
			$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
				." ('{$this->iId}','{$iIdPers}','0')";
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = 
				 " REPLACE INTO Equipe_Membre"
				." (IdEquipe, IdPers, OrdreEquipeMembre) VALUES {$sValeursRequete}";
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	/**
	 * Retourne l'id de l'objet courant, càd l'id de l'équipe qui a servi à initialiser l'objet
	 * 
	 * @return	l'id de l'équipe qui a servi à initialiser l'objet, ou \c 0 si non applicable
	 */
	function retId() { return (is_numeric($this->iId) ? $this->iId : 0); }
	
	/**
	 * Optimise la table Equipe_Membre de la DB, souvent utilisée dans cette classe (l'optimisation est réalisée après 
	 * des effacements)
	 */
	function optimiserTable()
	{
		$this->oBdd->executerRequete("OPTIMIZE TABLE Equipe_Membre");
	}
	
	/**
	 * Enlève une personne de toutes les équipes rattachées à un élément d'un niveau spécifié (formation, module, etc), 
	 * MAIS aussi des équipes rattachées aux éléments enfants de celui spécifié
	 * 
	 * @param	v_iIdPers	l'id de la personne à effacer des équipes
	 * @param	v_iNiveau	le numéro représentant le type d'élément auquel doivent être rattachées les équipes dont on 
	 * 						veut enlever la personne (voir les constantes TYPE_)
	 * @param	v_iIdNiveau	l'id de l'élément pour lequel on veut trouver les équipes attachées afin d'en effacer la 
	 * 						personne passée en \p v_iIdPers. La signification de cet id dépend du paramètre \p v_iTypeNiveau
	 * 
	 * @return	\c true si l'id de la personne passé en paramètre est valide, \c false dans le cas contraire
	 */
	function effacerMembre($v_iIdPers, $v_iNiveau, $v_iIdNiveau)
	{
		if ($v_iIdPers < 1)
			return FALSE;
		
		$this->oBdd->executerRequete("LOCK TABLES ".$this->STRING_LOCK_TABLES());
		
		$sRequeteSql = 
			 " SELECT Equipe.IdEquipe"
			." FROM Equipe"
			."  LEFT JOIN Equipe_Membre USING (IdEquipe)"
			." WHERE Equipe_Membre.IdPers='{$v_iIdPers}'"
			."  AND Equipe.".$this->asNiveau[$v_iNiveau]."='".$v_iIdNiveau."'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$sValeursRequete = NULL;
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
				."'{$oEnreg->IdEquipe}'";
		
		$this->oBdd->libererResult($hResult);
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = 
				 " DELETE FROM Equipe_Membre"
				." WHERE IdEquipe IN ({$sValeursRequete}) AND IdPers='{$v_iIdPers}'";
			$this->oBdd->executerRequete($sRequeteSql);
			
			$this->optimiserTable();
		}
		
		$this->oBdd->executerRequete("UNLOCK TABLES");
		
		return TRUE;
	}
	
	/**
	 * Enlève une ou plusieurs personnes de l'équipe dont l'id a servi à initialiser l'objet courant
	 * 
	 * @param	v_aiIdPers	le tableau contenant les ids des personnes à enlever de l'équipe. S'il s'agit d'une personne 
	 * 						seule, il n'est pas nécessaire que le paramètre soit un tableau, juste un nombre
	 * 
	 * @return	\c true si un ou plusieurs id ont bien été passés en paramètre et si une équipe est bien initialisée 
	 * 			dans l'objet courant, ce qui rend l'effacement possible; \c false dans le cas contraire
	 */
	function effacerMembres($v_aiIdPers)
	{
		settype($v_aiIdPers,"array");
		
		if (count($v_aiIdPers) < 1 || $this->iId < 1)
			return FALSE;
		
		$sValeursRequete = NULL;
		
		foreach ($v_aiIdPers as $iIdPers)
			$sValeursRequete .= (isset($sValeursRequete) ? ", " : NULL)
				."'$iIdPers'";
		
		if (isset($sValeursRequete))
		{
			$sRequeteSql = "DELETE FROM Equipe_Membre WHERE IdEquipe='{$this->iId}' AND IdPers IN ({$sValeursRequete})";
			$this->oBdd->executerRequete($sRequeteSql);
			$this->optimiserTable();
		}
		
		return TRUE;
	}
	
	/**
	 * Fonction utilitaire pour les LOCK TABLES éventuels de cette classe
	 * 
	 * @return	une chaîne de caractères contenant la liste des tables à "locker", qui devra être précédée d'un 
	 * 			"LOCK TABLES " pour l'exécution en SQL
	 */
	function STRING_LOCK_TABLES() { return "Equipe WRITE, Equipe_Membre WRITE"; }
}

?>
