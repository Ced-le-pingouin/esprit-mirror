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
 * @file	propositionreponse.tbl.php
 * 
 * Contient la classe de gestion des propositions de réponse des activités en ligne, en rapport avec la DB
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 * @author	Jérôme TOUZE
 */

/**
 * Gestion des proposition de réponse des activés en ligne, et encapsulation de la table PropositionReponse de la DB
 */
class CPropositionReponse 
{
	var $oBdd;				///< Objet représentant la connexion à la DB
	var $oEnregBdd;			///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	var $iId;				///< Utilisé dans le constructeur, pour indiquer l'id du sujet à récupérer dans la DB
	
	var $aiValeurAxe;
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CPropositionReponse(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;  
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	/**
	 * Initialise l'objet avec un enregistrement de la DB ou un objet PHP existant représentant un tel enregistrement
	 * Voir CPersonne#init()
	 */
	function init ($v_oEnregExistant = NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM PropositionReponse WHERE IdPropRep='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdPropRep;
	}
	
	function initValeursParAxe($v_sListeAxesAutorises = NULL)
	{
		if (isset($this->aiValeurAxe))
			return;
		
		if (isset($v_sListeAxesAutorises))
			$sSqlAxes = "  AND IdAxe IN ($v_sListeAxesAutorises)";
		else
			$sSqlAxes = "";
		
		$sRequeteSql = 	"SELECT * FROM Reponse_Axe WHERE IdPropRep = '{$this->iId}'".$sSqlAxes
						." ORDER BY IdAxe";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$this->aiValeurAxe[$oEnreg->IdAxe] = $oEnreg->Poids;
		
		$this->oBdd->libererResult($hResult);
	}
	
	/**
	 * Ajoute une proposition de réponse avec tous ses champs vides
	 * 
	 * @return	l'id de la nouvelle proposition de réponse
	 */
	function ajouter()
	{
		$sRequeteSql = "INSERT INTO PropositionReponse SET IdPropRep=NULL;";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	function enregistrer($v_bModifOrdre = TRUE)
	{
		$sTexteReponse = validerTexte($this->oEnregBdd->TextePropRep);
		
		//Quand on envoie false cela veut dire que l'on ne modifie ni l'ordre, ni l'Id de l'objet auquel se rapporte la réponse
		if ($v_bModifOrdre)
		{
			$sModifOrdre = ", OrdrePropRep='{$this->oEnregBdd->OrdrePropRep}'";
			$sModifIdObjForm =	", IdObjFormul='{$this->oEnregBdd->IdObjFormul}'";
		}
		
		$sRequeteSql = ($this->retId() > 0 ? "UPDATE PropositionReponse SET":"INSERT INTO PropositionReponse SET")
					." IdPropRep='{$this->oEnregBdd->IdPropRep}'"
					." , TextePropRep='{$sTexteReponse}'"
					." , ScorePropRep='{$this->oEnregBdd->ScorePropRep}'"
					." , FeedbackPropRep='".validerTexte($this->oEnregBdd->FeedbackPropRep)."'"
					.$sModifOrdre
					.$sModifIdObjForm
					.($this->oEnregBdd->IdPropRep > 0 ? " WHERE IdPropRep='{$this->oEnregBdd->IdPropRep}'" : NULL);
		
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function copier($v_iIdObjFormul)
	{
		if ($v_iIdObjFormul < 1)
			return;
		
		$sRequeteSql = "INSERT INTO PropositionReponse SET"
					." TextePropRep='".validerTexte($this->oEnregBdd->TextePropRep)."'"
					." , OrdrePropRep='{$this->oEnregBdd->OrdrePropRep}'"
					." , ScorePropRep='{$this->oEnregBdd->ScorePropRep}'"
					." , FeedbackPropRep='".validerTexte($this->oEnregBdd->FeedbackPropRep)."'"
					." , IdObjFormul='{$v_iIdObjFormul}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return  $this->oBdd->retDernierId();
	}
	
	function effacer()
	{
		$this->oBdd->executerRequete("LOCK TABLES PropositionReponse WRITE, Reponse_Axe WRITE");
		
		$sRequeteSql = "UPDATE PropositionReponse SET OrdrePropRep=(OrdrePropRep-1) WHERE OrdrePropRep>".$this->retOrdrePropRep()." AND IdObjFormul=".$this->retIdObjFormul()."";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSql = "DELETE FROM PropositionReponse WHERE IdPropRep ='{$this->oEnregBdd->IdPropRep}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSql = "DELETE FROM Reponse_Axe WHERE IdPropRep ='{$this->oEnregBdd->IdPropRep}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$this->oBdd->executerRequete("UNLOCK TABLES");
	}
	
	/*
	** Fonction 		: effacerRepObj
	** Description		: supprime TOUTES les réponses qui se rapportent à un objet formulaire 
	** Entrée			:
					$v_iIdObjForm : Id de l'objet formulaire à traiter
	** Sortie			: 
	*/
	function effacerRepObj($v_iIdObjFormul)
	{
		$sRequeteSql = "DELETE FROM PropositionReponse WHERE IdObjFormul ='$v_iIdObjFormul'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/*
	** Fonction 		: effacerRepPoidsObj
	** Description		: supprime TOUTES les réponses et leurs poids qui se rapportent à un objet formulaire 
	** Entrée			:
					$v_iIdObjForm : Id de l'objet formulaire à traiter
	** Sortie			: 
	*/
	function effacerRepPoidsObj($v_iIdObjFormul)
	{	
		$sRequeteSql = "LOCK TABLES PropositionReponse WRITE, Reponse_Axe WRITE";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSqlSelect = "SELECT IdPropRep FROM PropositionReponse"
							." WHERE IdObjFormul = '{$v_iIdObjFormul}'";
							
		$hResult = $this->oBdd->executerRequete($sRequeteSqlSelect);
		$iNbEnreg = $this->oBdd->retNbEnregsDsResult();
		
		if ($iNbEnreg > 0)
		{
			$sListeIdReponse="";
			
			while ($oEnregSelect = $this->oBdd->retEnregSuiv($hResult))
			{
				$sListeIdReponse.="{$oEnregSelect->IdPropRep}".",";
			}
			$this->oBdd->libererResult($hResult);
			
			//Ci-dessous : suppression de la virgule de trop a la fin de la chaîne de caractères
			$sListeIdReponse = substr($sListeIdReponse,0,strlen($sListeIdReponse)-1);
			
			//Suppression des poids
			$sRequeteSql = "DELETE FROM Reponse_Axe WHERE IdPropRep IN ($sListeIdReponse)";
			$this->oBdd->executerRequete($sRequeteSql);
			
			//Suppression des réponses
			$sRequeteSql = "DELETE FROM PropositionReponse WHERE IdObjFormul ='$v_iIdObjFormul'";
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$sRequeteSql = "UNLOCK TABLES";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Retourne le numéro d'ordre maximum des propositions de réponse d'un objet formulaire
	 * 
	 * @param	iIdObjFormul l'id de l'objet formulaire
	 * 
	 * @return	le numéro d'ordre maximum des réponses d'un objet formulaire
	 */
	function retMaxOrdre($v_iIdObjFormul)
	{
		$hResult = $this->oBdd->executerRequete("SELECT MAX(OrdrePropRep) AS OrdreMax FROM PropositionReponse WHERE IdObjFormul = '$v_iIdObjFormul'");
		$oEnreg = $this->oBdd->retEnregSuiv($hResult);
		return $oEnreg->OrdreMax;
	}
	
	/**
	 * Retourne une liste d'objet de type CPropositionReponse
	 * 
	 * @param	v_iIdObjFormul l'id de l'objet de formulaire
	 * 
	 * @return une liste d'objet de type CPropositionReponse
	 */
	function retListePropRep($v_iIdObjFormul)
	{
		$aoListePropRep = array();
		$iIdxPropRep = 0;
		$sRequeteSql = "SELECT * FROM PropositionReponse WHERE IdObjFormul='$v_iIdObjFormul' ORDER BY OrdrePropRep";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		while($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$aoListePropRep[$iIdxPropRep] = new CPropositionReponse($this->oBdd);
			$aoListePropRep[$iIdxPropRep]->init($oEnreg);
			$iIdxPropRep++;
		}
		$this->oBdd->libererResult($hResult);
		return $aoListePropRep;
	}
	
	/** @name Fonctions de définition des champs pour cette proposition de réponse */
	//@{
	function defId ($v_iIdPropRep) { $this->oEnregBdd->IdPropRep = $v_iIdPropRep; } //Ne pas confondre IdObfForm[Multi] et IdReponse[Unique] - Fonction pas utile car auto_increment ?
	function defTextePropRep ($v_sTextePropRep) { $this->oEnregBdd->TextePropRep = $v_sTextePropRep; }
	function defOrdrePropRep ($v_iOrdrePropRep) { $this->oEnregBdd->OrdrePropRep = $v_iOrdrePropRep; }
	function defScorePropRep ($v_iScorePropRep) { $this->oEnregBdd->ScorePropRep = $v_iScorePropRep; }
	function defFeedbackPropRep ($v_sFeedbackPropRep) { $this->oEnregBdd->FeedbackPropRep = $v_sFeedbackPropRep; }
	function defIdObjFormul ($v_iIdObjFormul) { $this->oEnregBdd->IdObjFormul = $v_iIdObjFormul; } //Ne pas confondre IdObfForm[Multi] et IdReponse[Unique] 
	//@}
	
	/** @name Fonctions de lecture des champs pour cette proposition de réponse */
	//@{
	function retId () { return $this->oEnregBdd->IdPropRep; }
	function retTextePropRep () { return $this->oEnregBdd->TextePropRep; }
	function retTexte() { return $this->retTextePropRep(); }
	function retOrdrePropRep () { return $this->oEnregBdd->OrdrePropRep; }
	function retOrdre() { return $this->retOrdrePropRep(); }
	function retScorePropRep () { return $this->oEnregBdd->ScorePropRep; }
	function retFeedbackPropRep () { return $this->oEnregBdd->FeedbackPropRep; }
	function retIdObjFormul () { return $this->oEnregBdd->IdObjFormul; }
	//@}
}
?>
