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
 * @file	messageforum.tbl.php
 * 
 * Contient la classe de gestion des messages des forums
 * 
 * @date	2004/05/14
 * 
 * @author	Filippo PORCO
 * @author	Jérôme TOUZE
 */

require_once(dir_database("ressource.tbl.php"));

/**
 * Gestion des messages des forums, et encapsulation de la table MessageForum de la DB
 */
class CMessageForum
{
	var $oBdd;				///< Objet représentant la connexion à la DB
	var $oEnregBdd;			///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	var $iId;				///< Utilisé dans le constructeur, pour indiquer l'id du sujet à récupérer dans la DB
	var $oAuteur;			///< Variable de type CPersonne, contenant l'auteur du message
	
	var $sRepRessources;	///< Variable de type chaîne de caratères, contenant l'adresse du répertoire des ressources
	
	var $aoRessources;		///< Tableau rempli par initRessources(), contenant les ressources attachées au message
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CMessageForum (&$v_oBdd,$v_iId=NULL)
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
			$this->iId = $this->oEnregBdd->IdMessageForum;
		}
		else
		{
			$sRequeteSql = "SELECT *"
				." FROM MessageForum"
				." WHERE IdMessageForum='".$this->retId()."'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	/**
	 * Ajoute un message dans un sujet
	 * 
	 * @param	v_sMessage	le message
	 * @param	v_iIdSujet	l'id du sujet
	 * @param	v_iIdPers	l'id de la personne
	 * @param	v_iIdEquipe	id de l'équipe(optionnel)
	 */
	function ajouter ($v_sMessage,$v_iIdSujet,$v_iIdPers,$v_iIdEquipe=0)
	{
		$sRequeteSql = "INSERT INTO MessageForum SET"
			." IdMessageForum=NULL"
			.", DateMessageForum=NOW()"
			.", TexteMessageForum='".MySQLEscapeString($v_sMessage)."'"
			.", IdSujetForum='{$v_iIdSujet}'"
			.", IdPers='{$v_iIdPers}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId($hResult);
		$this->init();
		
		// Associer le message à l'équipe
		if ($v_iIdEquipe > 0)
			$this->associerMessageEquipe($v_iIdEquipe);
	}
	
	/**
	 * Associe un message à une équipe
	 * 
	 * @param	v_iIdEquipe	l'id de l'équipe
	 */
	function associerMessageEquipe ($v_iIdEquipe)
	{
		$iIdMessageForum = $this->retId();
		if ($iIdMessageForum < 1 || $v_iIdEquipe < 1) return FALSE;
		$this->oBdd->executerRequete("REPLACE INTO MessageForum_Equipe (IdMessageForum,IdEquipe) VALUES ('{$iIdMessageForum}','{$v_iIdEquipe}')");
	}
	
	/**
	 *  Enregistre(update) dans la DB le message courant
	 */
	function enregistrer ()
	{
		$sRequeteSql = "UPDATE MessageForum SET"
			." TexteMessageForum='".MySQLEscapeString($this->oEnregBdd->TexteMessageForum)."'"
			." WHERE IdMessageForum='{$this->iId}'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface le message
	 */
	function effacer () 
	{
		$this->effacerMessage();
	}
	
	/**
	 * Efface le message de la DB ainsi que les ressources et les équipes associées
	 */
	function effacerMessage ()
	{
		$this->effacerEquipesAssocieesMessage();
		$this->effacerRessources();
		
		$sRequeteSql = "DELETE FROM MessageForum"
			." WHERE IdMessageForum='".$this->retId()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface les équipes associés à un message
	 */
	function effacerEquipesAssocieesMessage ()
	{
		$sRequeteSql = "DELETE FROM MessageForum_Equipe"
			." WHERE IdMessageForum='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Optimise les tables MessageForum, Ressource et MessageForum_Ressource
	 */
	function optimiserTables ()
	{
		$this->oBdd->executerRequete("OPTIMIZE TABLE MessageForum, Ressource, MessageForum_Ressource");
	}
	
	/** @name Fonctions de définition des champs pour ce message */
	//@{
	function defMessage ($v_sMessage) { $this->oEnregBdd->TexteMessageForum = $v_sMessage; }
	function defRepRessources ($v_sRepRessources) { $this->sRepRessources = $v_sRepRessources; }
	//@}
	
	/** @name Fonctions de lecture des champs pour ce message */
	//@{
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retIdParent () { return (is_numeric($this->oEnregBdd->IdSujetForum) ? $this->oEnregBdd->IdSujetForum : 0); }
	function retMessage () { return $this->oEnregBdd->TexteMessageForum; }
	function retAuteur (){ return new CPersonne($this->oBdd,$this->oEnregBdd->IdPers); }
	function retRepRessources () { return (empty($this->sRepRessources) ? NULL : $this->sRepRessources); }
	function retDate ($v_sFormatterDate="d/m/y H:i")
	{
		return retDateFormatter($this->oEnregBdd->DateMessageForum,$v_sFormatterDate);
	}
	//@}
	
	/**
	 * Initialise \c oAuteur avec la personne qui a
	 */
	function initAuteur () 
	{
		$this->oAuteur = $this->retAuteur();
	}
	
	/**
	 * Initialise un tableau contenant les ressources attachées au message
	 * 
	 * @return	le nombre de ressources insérées dans le tableau
	 */
	function initRessources ()
	{
		$iIdxRes = 0;
		$this->aoRessources = array();
		$sRequeteSql = "SELECT Ressource.*"
			." FROM MessageForum_Ressource"
			." LEFT JOIN Ressource USING (IdRes)"
			." WHERE MessageForum_Ressource.IdMessageForum='".$this->retId()."'"
			." ORDER BY Ressource.DateRes DESC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($hResult !== FALSE)
		{
			while ($oEnregBdd = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->aoRessources[$iIdxRes] = new CRessource($this->oBdd);
				$this->aoRessources[$iIdxRes]->init($oEnregBdd);
				$iIdxRes++;
			}
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $iIdxRes;
	}
	
	/**
	 * Ajoute un ressource au message
	 * 
	 * @param	v_sNomRes	le nom de la ressource
	 * @param	v_sUrlRes	l'url de la ressource
	 * @param	v_iIdPers	l'id de la personne
	 * 
	 * @return	l'id de la ressource ajoutée
	 */
	function ajouterRessource ($v_sNomRes,$v_sUrlRes,$v_iIdPers)
	{
		$iIdMessageForum = $this->retId();
		
		if ($iIdMessageForum < 1)
			return 0;
		
		$sRequeteSql = "LOCK TABLES"
			." Ressource WRITE"
			.", MessageForum_Ressource WRITE";
		$this->oBdd->executerRequete($sRequeteSql);
		
		// Ajouter une ligne dans la table des ressources
		$sRequeteSql = "INSERT INTO Ressource SET"
			." IdRes=NULL"
			.", NomRes='".MySQLEscapeString($v_sNomRes)."'"
			.", DescrRes=''"
			.", DateRes=NOW()"
			.", AuteurRes=''"
			.", UrlRes='".MySQLEscapeString($v_sUrlRes)."'"
			.", IdPers='{$v_iIdPers}'"
			.", IdDeposeur='{$v_iIdPers}'"
			.", IdFormat='0'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		// Récupérer l'id de la nouvelle ressource
		$iIdRes = $this->oBdd->retDernierId($hResult);
		
		// Faire le lien entre le message du forum et sa ressource
		if ($iIdRes > 0)
		{
			$sRequeteSql = "INSERT INTO MessageForum_Ressource SET"
				." IdMessageForum='{$iIdMessageForum}'"
				.", IdRes='{$iIdRes}'";
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$this->oBdd->executerRequete("UNLOCK TABLES");
		
		return $iIdRes;
	}
	
	/**
	 * Efface les ressources liées au message
	 */
	function effacerRessources ()
	{
		$sRequeteSql = "LOCK TABLES"
			." Ressource WRITE"
			.", MessageForum_Ressource WRITE";
		$this->oBdd->executerRequete($sRequeteSql);
		
		// Rechercher toutes les ressources de ce message
		$this->initRessources();
		
		$sListeRessources = NULL;
		$sRepRessources = $this->retRepRessources();
		
		foreach ($this->aoRessources as $oRessource)
		{
			$sListeRessources .= (isset($sListeRessources) ? ", " : NULL)
				."'".$oRessource->retId()."'";
			
			// Dans la même occassion, supprimons la ressource
			if (isset($sRepRessources))
				@unlink($sRepRessources.$oRessource->retUrl());
		}
		
		if (isset($sListeRessources))
		{
			$sRequeteSql = "DELETE FROM Ressource"
				." WHERE IdRes IN ({$sListeRessources})";
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$sRequeteSql = "DELETE FROM MessageForum_Ressource"
			." WHERE IdMessageForum='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$this->oBdd->executerRequete("UNLOCK TABLES");
	}
	
	/**
	 * Retourne les tables à verrouiller de la DB
	 * 
	 * @return	les tables à verrouiller de la DB
	 */
	function STRING_LOCK_TABLES ()
	{ 
		return "MessageForum WRITE, MessageForum_Equipe WRITE, MessageForum_Ressource WRITE";
	}
}
?>
