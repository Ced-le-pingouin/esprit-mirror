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
 * @file	sujetforum.tbl.php
 * 
 * Contient la classe de gestion des sujets des forums
 * 
 * @date	2004/05/14
 * 
 * @author	Filippo PORCO
 * @author	Jérôme TOUZE
 */

require_once(dir_database("messageforum.tbl.php"));

/**
 * Gestion des sujets des forums, et encapsulation de la table SujetForum de la DB
 */
class CSujetForum
{
	var $oBdd;				///< Objet représentant la connexion à la DB
	var $oEnregBdd;			///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	var $iId;				///< Utilisé dans le constructeur, pour indiquer l'id du sujet à récupérer dans la DB
	
	var $aoMessages;		///< Tableau rempli par #initMessages(), contenant les mesages du sujet 
	var $oAuteur;			///< Variable de type CPersonne, contenant l'auteur du sujet
	
	var $sRepRessources;	///< Variable de type chaîne de caratères, contenant l'adresse du répertoire des ressources
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CSujetForum (&$v_oBdd,$v_iId=NULL)
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
			$this->iId = $this->oEnregBdd->IdSujetForum;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM SujetForum"
				." WHERE IdSujetForum='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	/**
	 * Initialise la variable \c oAuteur (de type CPersonne) avec l'auteur du sujet
	 */
	function initAuteur ()
	{
		if (is_numeric($this->oEnregBdd->IdPers))
			$this->oAuteur = new CPersonne($this->oBdd,$this->oEnregBdd->IdPers);
		else
			$this->oAuteur = NULL;
	}
	
	/**
	 * Initialise un tableau contenant les messages de ce sujet
	 * 
	 * @param	v_iIdEquipe si fourni, retourne les messages d'un forum dont la modalité est par équipe
	 * 
	 * @return	le nombre de messages insérés dans le tableau
	 */
	function initMessages ($v_iIdEquipe=0)
	{
		$iIdxMessage = 0;
		$this->aoMessages = array();
		
		$sRequeteSql = "SELECT MessageForum.*"
			." FROM MessageForum"
			.($v_iIdEquipe > 0
				? " LEFT JOIN MessageForum_Equipe USING (IdMessageForum)"
				: NULL)
			." WHERE MessageForum.IdSujetForum='".$this->retId()."'"
			.($v_iIdEquipe > 0
				? " AND (MessageForum_Equipe.IdEquipe IS NULL OR MessageForum_Equipe.IdEquipe='{$v_iIdEquipe}' )"
				: NULL)
			." ORDER BY MessageForum.DateMessageForum DESC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnregBdd = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoMessages[$iIdxMessage] = new CMessageForum($this->oBdd);
			$this->aoMessages[$iIdxMessage]->init($oEnregBdd);
			$iIdxMessage++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxMessage;
	}
	
	/**
	 * Retourne le nombre de messages que la personne a déposés dans ce sujet.
	 *
	 * @param v_iIdPers		id de la personne
	 * @param v_iIdEquipe	id de l'équipe(optionnel)
	 * @return	le nombre de messages dans ce sujet de la personne désiré.
	 */
	function retNbMessagesDeposesPersonne ($v_iIdPers,$v_iIdEquipe=0)
	{
		if ($v_iIdEquipe > 0)
			$sRequeteSql = "SELECT COUNT(*)"
				." FROM MessageForum"
				." LEFT JOIN MessageForum_Equipe USING (IdMessageForum)"
				." WHERE MessageForum.IdSujetForum='".$this->retId()."'"
				." AND (MessageForum_Equipe.IdEquipe='{$v_iIdEquipe}'"
				." OR MessageForum_Equipe.IdEquipe IS NULL)"
				." AND MessageForum.IdPers='{$v_iIdPers}'";
		else
			$sRequeteSql = "SELECT COUNT(*)"
				." FROM MessageForum"
				." WHERE IdSujetForum='".$this->retId()."'"
				." AND IdPers='{$v_iIdPers}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbMessagesDeposesPersonne = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		return $iNbMessagesDeposesPersonne;
	}
	
	/**
	 * Ajoute un sujet dans la DB
	 * 
	 * @param	v_sTitreSujet			le titre du sujet
	 * @param	v_iModaliteSujet		la modalité du sujet {MODALITE_IDEM_PARENT | MODALITE_POUR_TOUS | MODALITE_PAR_EQUIPE}
	 * @param	v_iStatutSujet			statut du sujet {STATUT_OUVERT | STATUT_LECTURE_SEULE | STATUT_FERME | STATUT_INVISIBLE}
	 * @param	v_bAccessibleVisiteur	boolean, ce sujet est-il accessible aux visiteurs ?
	 * @param	v_iIdForum				l'id du forum parent
	 * @param	v_iIdPers				l'id de la personne
	 * 
	 * @return	le nouveau numéro d'identifiant du sujet
	 */
	function ajouter ($v_sTitreSujet,$v_iModaliteSujet,$v_iStatutSujet,$v_bAccessibleVisiteur,$v_iIdForum,$v_iIdPers)
	{
		$sRequeteSql = "INSERT INTO SujetForum SET"
			." IdSujetForum=NULL"
			.", TitreSujetForum='".MySQLEscapeString($v_sTitreSujet)."'"
			.", DateSujetForum=NOW()"
			.", ModaliteSujetForum=".(isset($v_iModaliteSujet) ? "'{$v_iModaliteSujet}'" : "'0'")
			.", StatutSujetForum=".(isset($v_iStatutSujet) ? "'{$v_iStatutSujet}'" : "'0'")
			.", AccessibleVisiteursSujetForum=".(isset($v_bAccessibleVisiteur) ? "'{$v_bAccessibleVisiteur}'" : "'1'")
			.", IdForum='{$v_iIdForum}'"
			.", IdPers='{$v_iIdPers}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId($hResult);
		$this->init();
		return $this->iId;
	}
	
	/**
	 * Enregistre(update) dans la DB le sujet courant
	 */
	function enregistrer ()
	{
		$sRequeteSql = "UPDATE SujetForum SET"
			." TitreSujetForum='".MySQLEscapeString($this->oEnregBdd->TitreSujetForum)."'"
			.", ModaliteSujetForum='".$this->oEnregBdd->ModaliteSujetForum."'"
			.", StatutSujetForum='".$this->oEnregBdd->StatutSujetForum."'"
			.", AccessibleVisiteursSujetForum='".$this->oEnregBdd->AccessibleVisiteursSujetForum."'"
			.", StatutSujetForum='".$this->oEnregBdd->StatutSujetForum."'"
			." WHERE IdSujetForum='{$this->iId}'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface le sujet
	 */
	function effacer ()
	{
		$this->verrouillerTables();
		$this->effacerSujet();
		$this->verrouillerTables(FALSE);
	}
	
	/**
	 * Efface le sujet courant dans la DB
	 */
	function effacerSujet ()
	{
		$this->effacerEquipesAssocieesSujet();
		$this->effacerMessages();
		
		$sRequeteSql = "DELETE FROM SujetForum"
			." WHERE IdSujetForum='".$this->retId()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Efface les messages contenus dans le sujet
	 */
	function effacerMessages ()
	{
		$this->initMessages();
		$sRepRessources = $this->retRepRessources();
		foreach ($this->aoMessages as $oMessage)
		{
			$oMessage->defRepRessources($sRepRessources);
			$oMessage->effacer();
		}
	}
	
	/**
	 * Efface les liens qui relient les équipes au sujet courant
	 */
	function effacerEquipesAssocieesSujet ()
	{
		$sRequeteSql = "DELETE FROM SujetForum_Equipe"
			." WHERE IdSujetForum='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Retourne l'adresse du répertoire des ressources
	 * 
	 * @return	l'adresse du répertoire des ressources
	 */
	function retRepRessources () 
	{ 
		return (empty($this->sRepRessources) ? NULL : $this->sRepRessources);
	}
	
	/**
	 * Définie l'adresse du répertoire des ressources
	 * 
	 * @param	v_sRepRessources l'adresse du répertoire des ressources
	 */
	function defRepRessources ($v_sRepRessources) 
	{ 
		$this->sRepRessources = $v_sRepRessources;
	}
	
	/**
	 *  Verrouille les tables en relation avec la table SujetForum
	 */
	function verrouillerTables ($v_bVerrouillerTables=TRUE)
	{
		if ($v_bVerrouillerTables)
			$sRequeteSql = "LOCK TABLES ".$this->STRING_LOCK_TABLES();
		else
			$sRequeteSql = "UNLOCK TABLES";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/** @name Fonctions de définition des champs pour ce sujet */
	//@{
	function defTitre ($v_sTitre) { $this->oEnregBdd->TitreSujetForum = $v_sTitre; }
	function defModalite ($v_iIdModalite) { $this->oEnregBdd->ModaliteSujetForum = $v_iIdModalite; }
	function defStatut ($v_iIdStatut) { $this->oEnregBdd->StatutSujetForum = $v_iIdStatut; }
	function defAccessibleVisiteurs ($v_iAccessibleVisiteurs) { $this->oEnregBdd->AccessibleVisiteursSujetForum = $v_iAccessibleVisiteurs; }
	function defNumOrdre ($v_iNumOrdre) { $this->oEnregBdd->OrdreSujetForum = $v_iNumOrdre; }
	//@}
	
	/** @name Fonctions de lecture des champs pour ce sujet */
	//@{
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retIdParent () { return (is_numeric($this->oEnregBdd->IdForum) ? $this->oEnregBdd->IdForum : 0); }
	function retIdPers () { return (is_numeric($this->oEnregBdd->IdPers) ? $this->oEnregBdd->IdPers : 0); }
	function retTitre () { return $this->oEnregBdd->TitreSujetForum; }
	function retNom () { return $this->oEnregBdd->TitreSujetForum; }
	function retModalite () { return $this->oEnregBdd->ModaliteSujetForum; }
	function retStatut () { return $this->oEnregBdd->StatutSujetForum; }
	function retDate ($v_sFormatterDate="d/m/y H:i") { return retDateFormatter($this->oEnregBdd->DateSujetForum,$v_sFormatterDate); }
	function retAccessibleVisiteurs () { return $this->oEnregBdd->AccessibleVisiteursSujetForum; }
	function retNumOrdre () { return $this->oEnregBdd->OrdreSujetForum; }
	//@}
	
	/**
	 * Retourne en français la modalité courante du sujet
	 * 
	 * @return	en français la modalité courante du sujet
	 */
	function retTexteModalite ()
	{
		$iModalite = $this->oEnregBdd->ModaliteSujetForum;
		
		if ($iModalite == MODALITE_IDEM_PARENT)
		{
			$oForum = new CForum($this->oBdd,$this->oEnregBdd->IdForum);
			$iModalite = $oForum->retModalite();
			$oForum = NULL;
		}
		
		return ($iModalite == MODALITE_POUR_TOUS
			? "Pour&nbsp;tous"
			: "Par&nbsp;&eacute;quipe");
	}
	
	/**
	 * Retourne le nombre de messages contenus dans le sujet
	 * 
	 * @param	v_iIdEquipe si fourni, le nombre de messages de l'équipe
	 * 
	 * @return	le nombre de messages contenus dans le sujet
	 */
	function retNombreMessages ($v_iIdEquipe=0)
	{
		if ($v_iIdEquipe > 0)
			$sRequeteSql = "SELECT COUNT(*) FROM MessageForum"
				." LEFT JOIN MessageForum_Equipe USING (IdMessageForum)"
				." WHERE MessageForum.IdSujetForum='".$this->retId()."'"
				." AND (MessageForum_Equipe.IdEquipe='{$v_iIdEquipe}'"
				." OR MessageForum_Equipe.IdEquipe IS NULL)";
		else
			$sRequeteSql = "SELECT COUNT(*) FROM MessageForum"
				." WHERE IdSujetForum='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbMessages = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		
		return $iNbMessages;
	}
	
	/**
	 * Retourne le forum parent (de type CForum)
	 * 
	 * @return	le forum parent
	 */
	function retForum ()
	{ 
		return new CForum($this->oBdd,$this->retIdParent());
	}
	
	/**
	 * Vérifie si le sujet est pour toute les équipes
	 * 
	 * @return	/c true si le sujet est pour toute les équipes
	 */
	function estPourTous ()
	{
		$bEstPourTous = FALSE;
		
		$sRequeteSql = "SELECT SujetForum.IdSujetForum"
			.", SujetForum_Equipe.IdSujetForum AS IdSujetForumEquipe"
			." FROM Forum"
			." LEFT JOIN SujetForum USING (IdForum)"
			." LEFT JOIN SujetForum_Equipe USING (IdSujetForum)"
			." WHERE Forum.IdForum='".$this->retIdParent()."'"
			." AND Forum.ModaliteForum<>'".MODALITE_POUR_TOUS."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($hResult !== FALSE)
		{
			$iIdSujetForum = $this->retId();
			
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				if ($oEnreg->IdSujetForum == $iIdSujetForum && empty($oEnreg->IdSujetForumEquipe))
				{
					$bEstPourTous = TRUE;
					break;
				}
			}
			$this->oBdd->libererResult($hResult);
		}
		
		return $bEstPourTous;
	}
	
	/**
	 * Retourne la date du dernier message posté dans ce sujet
	 * 
	 * @param	v_sFormatterDate	format de la date
	 * @param	v_iIdEquipe			id de l'équipe(optionnel)
	 * 
	 * @return	la date du dernier message posté dans ce sujet
	 */
	function retDateDernierMessagePoster ($v_sFormatterDate="d/m/y H:i",$v_iIdEquipe=0)
	{
		if ($v_iIdEquipe > 0)
			$sRequeteSql = "SELECT"
				." MAX(MessageForum.DateMessageForum) AS DateDernierMessagePoster"
				." FROM MessageForum"
				." LEFT JOIN MessageForum_Equipe USING (IdMessageForum)"
				." WHERE MessageForum.IdSujetForum='".$this->retId()."'"
				." AND (MessageForum_Equipe.IdEquipe='{$v_iIdEquipe}'"
				." OR MessageForum_Equipe.IdEquipe IS NULL)";
		else
			$sRequeteSql = "SELECT"
				." MAX(DateMessageForum) AS DateDernierMessagePoster"
				." FROM MessageForum"
				." WHERE IdSujetForum='".$this->retId()."'";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
		$this->oBdd->libererResult($hResult);
		
		if (isset($oEnregBdd->DateDernierMessagePoster))
			return retDateFormatter($oEnregBdd->DateDernierMessagePoster,$v_sFormatterDate);
		else
			return "&#8212;";
	}
	
	/**
	 * Associe un sujet à une équipe
	 * 
	 * @param	v_iIdEquipe	id équipe
	 */
	function associerEquipe ($v_iIdEquipe)
	{
		$iIdSujetForum = $this->retId();
		if ($iIdSujetForum < 1 || $v_iIdEquipe < 1) return FALSE;
		$this->oBdd->executerRequete("REPLACE INTO SujetForum_Equipe (IdSujetForum,IdEquipe) VALUES ('$iIdSujetForum','$v_iIdEquipe')");
	}
	
	/**
	 * Retourne les tables à verrouiller de la DB
	 * 
	 * @return	les tables à verrouiller de la DB
	 */
	function STRING_LOCK_TABLES ()
	{
		return "SujetForum WRITE, SujetForum_Equipe WRITE"
			.", ".CMessageForum::STRING_LOCK_TABLES();
	}
}
?>
