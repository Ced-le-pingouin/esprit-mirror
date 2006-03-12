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
** Classe .................: chat.tbl.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 04/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

define("CHAT_POUR_TOUS",0);
define("CHAT_PAR_EQUIPE",1);

define("CHAT_NOM_DEFAUT","Chat");
define("CHAT_NOM_COULEUR_DEFAUT","argent");
define("CHAT_RVB_COULEUR_DEFAUT","206,206,206");
define("CHAT_COULEUR_DEFAULT",CHAT_NOM_COULEUR_DEFAUT.";".CHAT_RVB_COULEUR_DEFAUT);

class CChat
{
	var $iId;
	
	var $oBdd;
	var $oEnregBdd;
	
	var $aoChats;
	
	function CChat (&$v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iId;
		
		if (isset($this->iId))
			$this->init();
	}
	
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
	
	function retIdNiveau ()
	{
		if ($this->oEnregBdd->IdSousActiv > 0)
			return $this->oEnregBdd->IdSousActiv;
		else if ($this->oEnregBdd->IdRubrique > 0)
			return $this->oEnregBdd->IdRubrique;
		else
			return 0;
	}
	
	function retTypeNiveau ()
	{
		if ($this->oEnregBdd->IdSousActiv > 0)
			return TYPE_SOUS_ACTIVITE;
		else if ($this->oEnregBdd->IdRubrique > 0)
			return TYPE_RUBRIQUE;
		else
			return NULL;
	}
	
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
	
	function effacer ()
	{
		$sRequeteSql = "DELETE FROM Chat"
			." WHERE IdChat='".$this->retId()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
		$this->remettreDelOrdre();
	}
	
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
	
	function retId () {	return (is_numeric($this->iId) ? $this->iId : 0); }
	
	function defNom ($v_sNomChat)
	{
		$v_sNomChat = MySQLEscapeString($v_sNomChat);
		
		if (strlen($v_sNomChat) < 1)
			$v_sNomChat = $this->retNomDefaut();
		
		$this->oEnregBdd->NomChat = $v_sNomChat;
	}
	
	function retNom ($v_sMode=NULL)
	{
		if ($v_sMode == "html")
			return htmlentities($this->oEnregBdd->NomChat);
		else if ($v_sMode == "url")
			return rawurlencode($this->oEnregBdd->NomChat);
		else
			return $this->oEnregBdd->NomChat;
	}
	
	function retNomDefaut () { return CHAT_NOM_DEFAUT; }
	function defCouleur ($v_sCouleurChat) { $this->oEnregBdd->CouleurChat = $v_sCouleurChat; }
	function retCouleur () { return $this->oEnregBdd->CouleurChat; }
	function retCouleurDefaut () { return $this->retNomCouleurDefaut().";".$this->retValeurCouleurDefaut(); }
	
	function retNomCouleur ()
	{
		list($sNomCouleur) = split(";",$this->oEnregBdd->CouleurChat);
		return $sNomCouleur;
	}
	
	function retNomCouleurDefaut () { return CHAT_NOM_COULEUR_DEFAUT; }
	
	function retValeurCouleur ()
	{
		list(,$sValeurCouleur) = split(";",$this->oEnregBdd->CouleurChat);
		return $sValeurCouleur;
	}
	
	function retValeurCouleurDefaut () { return CHAT_RVB_COULEUR_DEFAUT; }
	
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
	
	function defNumOrdre ($v_iNouvellePos)
	{
		if ($this->oEnregBdd->OrdreChat != $v_iNouvellePos)
		{
			$this->redistNumsOrdre($this->oEnregBdd->OrdreChat,$v_iNouvellePos);
			$this->oEnregBdd->OrdreChat = $v_iNouvellePos;
		}
	}
	
	function retNumOrdre () { return $this->oEnregBdd->OrdreChat; }
	function defModalite ($v_bModaliteChat) { $this->oEnregBdd->ModaliteChat = $v_bModaliteChat; }
	
	function retModalite ($v_bTransformer=FALSE)
	{
		if ($v_bTransformer)
			return (CHAT_PAR_EQUIPE == $this->oEnregBdd->ModaliteChat ? MODALITE_PAR_EQUIPE : MODALITE_INDIVIDUEL);
		else
			return $this->oEnregBdd->ModaliteChat;
	}
	
	function defSalonPrive ($v_bSalonPriveChat) { $this->oEnregBdd->SalonPriveChat = ($v_bSalonPriveChat == "on" ? "1" : "0"); }
	function retSalonPrive () { return $this->oEnregBdd->SalonPriveChat; }
	function defEnregConversation ($v_bEnregChat) { $this->oEnregBdd->EnregChat = ($v_bEnregChat == "on" ? "1" : "0"); }
	function retEnregConversation () { return $this->oEnregBdd->EnregChat; }
	function defIdSousActiv ($v_iIdSousActiv) { $this->oEnregBdd->IdSousActiv = $v_iIdSousActiv; }
	function retIdSousActiv () { return $this->oEnregBdd->IdSousActiv; }
	
	function peutEffacerArchives ($v_iIdStatutUtilisateur)
	{
		return ($v_iIdStatutUtilisateur == STATUT_PERS_TUTEUR ||
				$v_iIdStatutUtilisateur == STATUT_PERS_RESPONSABLE ||
				$v_iIdStatutUtilisateur == STATUT_PERS_ADMIN);
	}
	
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
}

?>
