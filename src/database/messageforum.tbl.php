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
** Fichier ................: messageforum.tbl.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 12/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Jérôme TOUZE
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_database("ressource.tbl.php"));

class CMessageForum
{
	var $oBdd;
	var $oEnregBdd;
	
	var $iId;
	var $oAuteur;
	
	var $sRepRessources;
	
	var $aoRessources;
	
	function CMessageForum (&$v_oBdd,$v_iId=NULL)
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
	
	function associerMessageEquipe ($v_iIdEquipe)
	{
		$iIdMessageForum = $this->retId();
		if ($iIdMessageForum < 1 || $v_iIdEquipe < 1) return FALSE;
		$this->oBdd->executerRequete("REPLACE INTO MessageForum_Equipe (IdMessageForum,IdEquipe) VALUES ('{$iIdMessageForum}','{$v_iIdEquipe}')");
	}
	
	function enregistrer ()
	{
		$sRequeteSql = "UPDATE MessageForum SET"
			." TexteMessageForum='".MySQLEscapeString($this->oEnregBdd->TexteMessageForum)."'"
			." WHERE IdMessageForum='{$this->iId}'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacer () { $this->effacerMessage(); }
	
	function effacerMessage ()
	{
		$this->effacerEquipesAssocieesMessage();
		$this->effacerRessources();
		
		$sRequeteSql = "DELETE FROM MessageForum"
			." WHERE IdMessageForum='".$this->retId()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacerEquipesAssocieesMessage ()
	{
		$sRequeteSql = "DELETE FROM MessageForum_Equipe"
			." WHERE IdMessageForum='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function optimiserTables ()
	{
		$this->oBdd->executerRequete("LOCK TABLES MessageForum WRITE, Ressource WRITE, MessageForum_Ressource WRITE");
		$this->oBdd->executerRequete("OPTIMIZE TABLE MessageForum, Ressource, MessageForum_Ressource");
		$this->oBdd->executerRequete("UNLOCK TABLES");
	}
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retIdParent () { return (is_numeric($this->oEnregBdd->IdSujetForum) ? $this->oEnregBdd->IdSujetForum : 0); }
	function defMessage ($v_sMessage) { $this->oEnregBdd->TexteMessageForum = $v_sMessage; }
	function retMessage () { return $this->oEnregBdd->TexteMessageForum; }
	function initAuteur () { $this->oAuteur = $this->retAuteur(); }
	function retAuteur (){ return new CPersonne($this->oBdd,$this->oEnregBdd->IdPers); }
	
	function retDate ($v_sFormatterDate="d/m/y H:i")
	{
		return retDateFormatter($this->oEnregBdd->DateMessageForum,$v_sFormatterDate);
	}
	
	/**
	 * Rechercher les fichiers attachés à ce message
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
	
	function retRepRessources () { return (empty($this->sRepRessources) ? NULL : $this->sRepRessources); }
	function defRepRessources ($v_sRepRessources) { $this->sRepRessources = $v_sRepRessources; }
	
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
	
	function STRING_LOCK_TABLES () { return "MessageForum WRITE, MessageForum_Equipe WRITE, MessageForum_Ressource WRITE"; }
}

?>
