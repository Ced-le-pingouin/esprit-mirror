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
 * @file	chat.tbl.php
 * 
 * Contient la classe de gestion des chats, en rapport avec la DB
 * 
 * @date	2005/10/04
 * 
 * @author	Filippo PORCO
 */

define("CHAT_POUR_TOUS",0);															/// chat pour tout le monde							@enum CHAT_POUR_TOUS
define("CHAT_PAR_EQUIPE",1);														/// chat par équipe									@enum CHAT_PAR_EQUIPE

define("CHAT_NOM_DEFAUT","Chat");													/// nom par défaut d'un chat						@enum CHAT_NOM_DEFAUT
define("CHAT_NOM_COULEUR_DEFAUT","argent");											/// couleur en texte par défaut d'un chat			@enum CHAT_NOM_COULEUR_DEFAUT
define("CHAT_RVB_COULEUR_DEFAUT","206,206,206");									/// couleur en rgb par défaut d'un chat				@enum CHAT_RVB_COULEUR_DEFAUT
define("CHAT_COULEUR_DEFAULT",CHAT_NOM_COULEUR_DEFAUT.";".CHAT_RVB_COULEUR_DEFAUT);	/// couleur en texte + en rgb par défaut d'un chat	@enum CHAT_COULEUR_DEFAULT

/**
 * Gestion des chats, et encapsulation de la table Chat de la DB
 */
class CChat
{
	var $iId;			///< Utilisé dans le constructeur, pour indiquer l'id du chat à récupérer dans la DB

	var $oBdd;			///< Objet représentant la connexion à la DB
	var $oEnregBdd;		///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici

	var $aoChats;		///< Tableau rempli par #initChats(), contenant une liste de chats
	var $oParent;		///< Objet (de type CModule_Rubrique ou CSousActiv) contenant le parent du chat
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CChat (&$v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iId;
		
		if (isset($this->iId))
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
			$this->iId = $this->oEnregBdd->IdChat;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Chat"
				." WHERE IdChat='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	/**
	 * Retourne soit l'id de la rubrique ou soit celui de la sous-activité parente
	 * 
	 * @return	l'id du parent
	 */
	function retIdNiveau ()
	{
		if ($this->oEnregBdd->IdSousActiv > 0)
			return $this->oEnregBdd->IdSousActiv;
		else if ($this->oEnregBdd->IdRubrique > 0)
			return $this->oEnregBdd->IdRubrique;
		else
			return 0;
	}
	
	/**
	 * Retourne la constante qui définit le niveau actuel, de la structure d'une formation (rubrique ou sous-activité)
	 * 
	 * @return	la constante qui définit le niveau actuel, de la structure d'une formation
	 */
	function retTypeNiveau ()
	{
		if ($this->oEnregBdd->IdSousActiv > 0)
			return TYPE_SOUS_ACTIVITE;
		else if ($this->oEnregBdd->IdRubrique > 0)
			return TYPE_RUBRIQUE;
		else
			return NULL;
	}
	
	/**
	 * Initialise \c oParent avec un objet de type rubrique ou sous-activité
	 * 
	 * @return	\c true si l'objet a bien été initialisé
	 */
	function initParent ()
	{
		switch ($this->retTypeNiveau())
		{
			case TYPE_SOUS_ACTIVITE:
				$this->oParent = new CSousActiv($this->oBdd,$this->retIdNiveau());
				break;
			case TYPE_RUBRIQUE:
				$this->oParent = new CRubrique($this->oBdd,$this->retIdNiveau());
				break;
			default:
				$this->oParent = NULL;
		}
		
		return is_object($this->oParent);
	}
	
	/**
	 * Enlève les trous dans les numéros d'ordre des chats
	 */
	function remettreDelOrdre ()
	{
		// Rechercher tous les salons de cette sous-activité
		$sRequeteSql = "SELECT IdChat FROM Chat"
			." WHERE"
			.($this->retTypeNiveau() == TYPE_SOUS_ACTIVITE ? " IdSousActiv='{$this->oEnregBdd->IdSousActiv}'" : NULL)
			.($this->retTypeNiveau() == TYPE_RUBRIQUE ? " IdRubrique='{$this->oEnregBdd->IdRubrique}'" : NULL)
			." ORDER BY OrdreChat ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$aiIdsChat = array();
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$aiIdsChat[] = $oEnreg->IdChat;
		
		$this->oBdd->libererResult($hResult);
		
		// Remettre de l'ordre
		$this->oBdd->executerRequete("LOCK TABLE Chat Write");
		
		for ($i=0; $i<count($aiIdsChat); $i++)
		{
			$sRequeteSql = "UPDATE Chat SET OrdreChat='".($i+1)."'"
				." WHERE IdChat='".$aiIdsChat[$i]."'"
				." LIMIT 1";
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$this->oBdd->executerRequete("UNLOCK TABLES");
	}
	
	/**
	 * Efface le chat de la DB
	 */
	function effacer ()
	{
		$sRequeteSql = "DELETE FROM Chat"
			." WHERE IdChat='".$this->retId()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
		$this->remettreDelOrdre();
	}
	
	/**
	 * Met à jour l'enregistrement dans la DB
	 */
	function enregistrer ()
	{
		$sRequeteSql = "UPDATE Chat SET"
			." OrdreChat='{$this->oEnregBdd->OrdreChat}'"
			.", NomChat='{$this->oEnregBdd->NomChat}'"
			.", CouleurChat='{$this->oEnregBdd->CouleurChat}'"
			.", ModaliteChat='{$this->oEnregBdd->ModaliteChat}'"
			.", EnregChat='{$this->oEnregBdd->EnregChat}'"
			.", SalonPriveChat='{$this->oEnregBdd->SalonPriveChat}'"
			." WHERE IdChat='{$this->iId}'";
			
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/** @name Fonctions de définition des champs pour ce chat */
	//@{
	function defNom ($v_sNomChat)
	{
		$v_sNomChat = MySQLEscapeString($v_sNomChat);
		
		if (strlen($v_sNomChat) < 1)
			$v_sNomChat = $this->retNomDefaut();
		
		$this->oEnregBdd->NomChat = $v_sNomChat;
	}

	function defNumOrdre ($v_iNouvellePos)
	{
		if ($this->oEnregBdd->OrdreChat != $v_iNouvellePos)
		{
			$this->redistNumsOrdre($this->oEnregBdd->OrdreChat,$v_iNouvellePos);
			$this->oEnregBdd->OrdreChat = $v_iNouvellePos;
		}
	}
	
	function defCouleur ($v_sCouleurChat) { $this->oEnregBdd->CouleurChat = $v_sCouleurChat; }
	function defModalite ($v_bModaliteChat) { $this->oEnregBdd->ModaliteChat = $v_bModaliteChat; }
	function defSalonPrive ($v_bSalonPriveChat) { $this->oEnregBdd->SalonPriveChat = ($v_bSalonPriveChat == "on" ? "1" : "0"); }
	function defEnregConversation ($v_bEnregChat) { $this->oEnregBdd->EnregChat = ($v_bEnregChat == "on" ? "1" : "0"); }
	function defIdSousActiv ($v_iIdSousActiv) { $this->oEnregBdd->IdSousActiv = $v_iIdSousActiv; }

	//@}


	/** @name Fonctions de lecture des champs pour ce chat */
	//@{
	function retId () {	return (is_numeric($this->iId) ? $this->iId : 0); }

	function retNom ($v_sMode=NULL)
	{
		if ($v_sMode == "html")
			return emb_htmlentities($this->oEnregBdd->NomChat);
		else if ($v_sMode == "url")
			return rawurlencode($this->oEnregBdd->NomChat);
		else
			return $this->oEnregBdd->NomChat;
	}

	function retCouleur () { return $this->oEnregBdd->CouleurChat; }

	function retModalite ($v_bTransformer=FALSE)
	{
		if ($v_bTransformer)
			return (CHAT_PAR_EQUIPE == $this->oEnregBdd->ModaliteChat ? MODALITE_PAR_EQUIPE : MODALITE_INDIVIDUEL);
		else
			return $this->oEnregBdd->ModaliteChat;
	}
	
	function retEnregConversation () { return $this->oEnregBdd->EnregChat; }
	function retNumOrdre () { return $this->oEnregBdd->OrdreChat; }
	function retSalonPrive () { return $this->oEnregBdd->SalonPriveChat; }
	function retIdRubrique () { return $this->oEnregBdd->IdRubrique; }
	function retIdSousActiv () { return $this->oEnregBdd->IdSousActiv; }


	function retNomCouleur ()
	{
		list($sNomCouleur) = split(";",$this->oEnregBdd->CouleurChat);
		return $sNomCouleur;
	}

	function retValeurCouleur ()
	{
		list(,$sValeurCouleur) = split(";",$this->oEnregBdd->CouleurChat);
		return $sValeurCouleur;
	}

	//@}

	/**
	 * Retourne le nom par défaut d'un chat(constante CHAT_NOM_DEFAUT)
	 * 
	 * @return	le nom par défaut d'un chat
	 */
	function retNomDefaut ()
	{
		return CHAT_NOM_DEFAUT;
	}
	
	/**
	 * Retourne en français et en RVB la couleur par défaut d'un chat
	 * 
	 * @return	en français et en RVB la couleur par défaut d'un chat
	 */
	function retCouleurDefaut ()
	{
		return $this->retNomCouleurDefaut().";".$this->retValeurCouleurDefaut();
	}
	
	/**
	 * Retourne en français la couleur par défaut(constante CHAT_NOM_COULEUR_DEFAUT)
	 * 
	 * @return	en français la couleur par défaut
	 */
	function retNomCouleurDefaut ()
	{
		return CHAT_NOM_COULEUR_DEFAUT;
	}
	
	/**
	 * Retourne la couleur en RVB par défaut d'un chat(constante CHAT_RVB_COULEUR_DEFAUT)
	 * 
	 * @return	la couleur en RVB par défaut d'un chat
	 */
	function retValeurCouleurDefaut () 
	{
		return CHAT_RVB_COULEUR_DEFAUT;
	}
	
	/**
	 * Redistribue les numéros d'ordre des chats
	 * 
	 * @param	v_iAnciennePos	l'ancien numéro d'ordre du chat
	 * @param	v_iNouvellePos	le nouveau numéro d'ordre du chat
	 */
	function redistNumsOrdre ($v_iAnciennePos,$v_iNouvellePos)
	{
		$v_iAnciennePos--; $v_iNouvellePos--;
		
		$v_sSens = ($v_iAnciennePos > $v_iNouvellePos ? "desc" : "asc");
		
		$sRequeteSql = "SELECT IdChat,OrdreChat FROM Chat"
			." WHERE"
			.($this->retTypeNiveau() == TYPE_SOUS_ACTIVITE ? " IdSousActiv='{$this->oEnregBdd->IdSousActiv}'" : NULL)
			.($this->retTypeNiveau() == TYPE_RUBRIQUE ? " IdRubrique='{$this->oEnregBdd->IdRubrique}'" : NULL)
			." ORDER BY OrdreChat ASC";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$aoEnregs = array();
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$aoEnregs[] = $oEnreg;
		
		$this->oBdd->libererResult($hResult);
		
		$sValeursSql = NULL;
		
		if ($v_sSens == "desc")
		{
			$sAction = "OrdreChat=OrdreChat+1";
			for ($i=$v_iNouvellePos; $i<$v_iAnciennePos; $i++)
				$sValeursSql .= (isset($sValeursSql) ? "," : NULL)."'".$aoEnregs[$i]->IdChat."'";
		}
		else
		{
			$sAction = "OrdreChat=OrdreChat-1";
			for ($i=$v_iNouvellePos; $i>$v_iAnciennePos; $i--)
				$sValeursSql .= (isset($sValeursSql) ? "," : NULL)."'".$aoEnregs[$i]->IdChat."'";
		}
		
		$sRequeteSql = "UPDATE Chat SET {$sAction} WHERE IdChat IN ({$sValeursSql})";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Verifie si le statut donné peut effacer les archives des chats
	 * 
	 * @param	v_iIdStatutUtilisateur l'id du statut de la personne
	 * 
	 * @return	\c true si le statut peut effacer les archives
	 */
	function peutEffacerArchives ($v_iIdStatutUtilisateur)
	{
		return ($v_iIdStatutUtilisateur == STATUT_PERS_TUTEUR ||
				$v_iIdStatutUtilisateur == STATUT_PERS_RESPONSABLE ||
				$v_iIdStatutUtilisateur == STATUT_PERS_ADMIN);
	}
	
	/**
	 * Ajoute un chat dans la DB
	 * 
	 * @param	v_oObjNiveau objet (CModule_Rubrique ou CSousActiv) du niveau auquel le chat appartient
	 * 
	 * @return	l'id du nouveau chat
	 */
	function ajouter (&$v_oObjNiveau)
	{
		// Attribuer un numéro ordre
		$iOrdre = $this->retNombreChats($v_oObjNiveau)+1;
		
		// Ajouter dans la table
		$sRequeteSql = "INSERT INTO Chat SET"
			." IdChat=NULL"
			.", NomChat='".CHAT_NOM_DEFAUT."'"
			.", CouleurChat='".CHAT_COULEUR_DEFAULT."'"
			.", ModaliteChat='0'"
			.", EnregChat='1'"
			.", SalonPriveChat='1'"
			.", OrdreChat='{$iOrdre}'"
			.", IdRubrique='".($v_oObjNiveau->retTypeNiveau() == TYPE_RUBRIQUE ? $v_oObjNiveau->retId() : 0)."'"
			.", IdSousActiv='".($v_oObjNiveau->retTypeNiveau() == TYPE_SOUS_ACTIVITE ? $v_oObjNiveau->retId() : 0)."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		return $this->oBdd->retDernierId($hResult);
	}
	
	/**
	 * Initialise un tableau contenant la liste des chats de la sous-activité ou de la rubrique
	 * 
	 * @param	v_oObjNiveau objet (CModule_Rubrique ou CSousActiv) du niveau auquel le chat appartient
	 * 
	 * @return	le nombre de chats insérés dans le tableau
	 */
	function initChats (&$v_oObjNiveau)
	{
		$this->aoChats = array();
		
		if ($v_oObjNiveau->retTypeNiveau() == TYPE_SOUS_ACTIVITE)
			$sRequeteSql = "SELECT * FROM Chat"
				." WHERE IdSousActiv='".$v_oObjNiveau->retId()."'"
				." ORDER BY OrdreChat ASC";
		else if ($v_oObjNiveau->retTypeNiveau() == TYPE_RUBRIQUE)
			$sRequeteSql = "SELECT * FROM Chat"
				." WHERE IdRubrique='".$v_oObjNiveau->retId()."'"
				." ORDER BY OrdreChat ASC";
		else
			$sRequeteSql = NULL;
		
		if (isset($sRequeteSql))
		{
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($hResult !== FALSE)
			{
				$iIdxChat = 0;
				
				while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
				{
					$this->aoChats[$iIdxChat] = new CChat($this->oBdd);
					$this->aoChats[$iIdxChat]->init($oEnreg);
					$iIdxChat++;
				}
				
				$this->oBdd->libererResult($hResult);
			}
		}
		
		return $iIdxChat;
	}
	
	/**
	 * Efface tous les chats appartenant à un niveau
	 * 
	 * @param	v_oObjNiveau objet (CModule_Rubrique ou CSousActiv) du niveau auquel le chat appartient
	 */
	function effacerChats (&$v_oObjNiveau)
	{
		$iTypeNiveau = $v_oObjNiveau->retTypeNiveau();
		
		if (TYPE_SOUS_ACTIVITE == $iTypeNiveau)
			$sRequeteSql = "DELETE FROM Chat"
				." WHERE IdSousActiv='".$v_oObjNiveau->retId()."'";
		else if (TYPE_RUBRIQUE == $iTypeNiveau)
			$sRequeteSql = "DELETE FROM Chat"
				." WHERE IdRubrique='".$v_oObjNiveau->retId()."'";
		else
			return;
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Retourne le nombre de chats appartenant au niveau de la formation
	 * 
	 * @param	v_oObjNiveau objet (CModule_Rubrique ou CSousActiv) du niveau auquel le chat appartient
	 * 
	 * @return	le nombre de chats
	 */
	function retNombreChats (&$v_oObjNiveau)
	{
		if ($v_oObjNiveau->retTypeNiveau() == TYPE_SOUS_ACTIVITE)
			$sRequeteSql = "SELECT COUNT(*) FROM Chat"
				." WHERE IdSousActiv='".$v_oObjNiveau->retId()."'";
		else if ($v_oObjNiveau->retTypeNiveau() == TYPE_RUBRIQUE)
			$sRequeteSql = "SELECT COUNT(*) FROM Chat"
				." WHERE IdRubrique='".$v_oObjNiveau->retId()."'";
		else
			return 0;
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($hResult === FALSE)
			return 0;
		
		$iNbChats = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		
		return $iNbChats;
	}

	/**
	 * Copie le chat courant d'un niveau vers un autre
	 * 
	 * @param	v_iNouvId l'id du niveau de destination
	 */
	function copier($v_iNouvId)
	{
		$sRequeteSql = "INSERT INTO Chat SET"
			." IdChat=NULL"
			.", NomChat='".$this->retNom()."'"
			.", CouleurChat='".$this->retCouleur()."'"
			.", ModaliteChat='".$this->retModalite()."'"
			.", EnregChat='".$this->retEnregConversation()."'"
			.", SalonPriveChat='".$this->retSalonPrive()."'"
			.", OrdreChat='".$this->retNumOrdre()."'"
			.", IdRubrique='".($this->retIdRubrique() > 0 ? $v_iNouvId : 0)."'"
			.", IdSousActiv='".($this->retIdSousActiv() > 0 ? $v_iNouvId : 0)."'";
			
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
	}
}
?>
