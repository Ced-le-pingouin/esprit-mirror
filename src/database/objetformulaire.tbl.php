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
 * @file	objetformulaire.tbl.php
 * 
 * Contient la classe de gestion des objets de formulaire, en rapport avec la DB
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 */

/**
* Gestion des objets de formulaire, et encapsulation de la table ObjetFormulaire de la DB
*/
class CObjetFormulaire 
{
	var $oBdd;			///< Objet représentant la connexion à la DB
	var $oEnregBdd;		///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	var $iId;			///< Utilisé dans le constructeur, pour indiquer l'id de la formation à récupérer dans la DB
	
	var $oDetail;
	var $aoReponsesPossibles;
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CObjetFormulaire(&$v_oBdd,$v_iId=0) 
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
			$sRequeteSql = "SELECT * FROM ObjetFormulaire WHERE IdObjFormul='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjFormul;
	}
	
	/**
	 * Initialise l'objet oDetail selon le type de l'objet courant
	 * 
	 * @param	v_bInitValeursParAxe
	 * @param	v_sListeAxesAutorises
	 */
	function initDetail($v_bInitValeursParAxe = FALSE, $v_sListeAxesAutorises = NULL)
	{
		if (!$this->retIdTypeObj() || !$this->retId())
			return;
		
		switch($this->retIdTypeObj())
		{
			case OBJFORM_QTEXTELONG:
				$this->oDetail = new CQTexteLong($this->oBdd, $this->retId());
				break;
			
			case OBJFORM_QTEXTECOURT:
				$this->oDetail = new CQTexteCourt($this->oBdd, $this->retId());
				break;
			
			case OBJFORM_QNOMBRE:
				$this->oDetail = new CQNombre($this->oBdd, $this->retId());
				break;
				
			case OBJFORM_QLISTEDEROUL:
				$this->oDetail = new CQListeDeroul($this->oBdd, $this->retId());
				$this->initReponsesPossibles($v_bInitValeursParAxe, $v_sListeAxesAutorises);
				break;
				
			case OBJFORM_QRADIO:
				$this->oDetail = new CQRadio($this->oBdd, $this->retId());
				$this->initReponsesPossibles($v_bInitValeursParAxe, $v_sListeAxesAutorises);
				break;
				
			case OBJFORM_QCOCHER:
				$this->oDetail = new CQCocher($this->oBdd, $this->retId());
				$this->initReponsesPossibles($v_bInitValeursParAxe, $v_sListeAxesAutorises);
				break;
				
			case OBJFORM_MPTEXTE:
				$this->oDetail = new CMPTexte($this->oBdd, $this->retId());
				break;
				
			case OBJFORM_MPSEPARATEUR:
				$this->oDetail = new CMPSeparateur($this->oBdd, $this->retId());
				break;
		}
	}
	
	function initReponsesPossibles($v_bInitValeursParAxe = FALSE, $v_sListeAxesAutorises = NULL)
	{
		if (isset($this->aoReponsesPossibles))
			return;
		
		$sRequeteSql = "SELECT * FROM PropositionReponse"
					." WHERE IdObjFormul = '{$this->iId}'"
					." ORDER BY OrdrePropRep";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$iIndexReponse = $oEnreg->IdPropRep;
			$this->aoReponsesPossibles[$iIndexReponse] = new CPropositionReponse($this->oBdd);
			$this->aoReponsesPossibles[$iIndexReponse]->init($oEnreg);
			if ($v_bInitValeursParAxe)
				$this->aoReponsesPossibles[$iIndexReponse]->initValeursParAxe($v_sListeAxesAutorises);
		}
		$this->oBdd->libererResult($hResult);
	}
	
	function ajouter()  //Cette fonction ajoute un Objet Formulaire, avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO ObjetFormulaire SET IdObjFormul=NULL;";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	function DeplacerObjet($v_iNouvPos)
	{
		if ($v_iNouvPos==$this->oEnregBdd->OrdreObjFormul)
			return false;
		
		//Verrouillage de la table ObjetFormulaire
		$sRequeteSql = "LOCK TABLES ObjetFormulaire WRITE";
		$this->oBdd->executerRequete($sRequeteSql);
		
		if ($v_iNouvPos > $this->oEnregBdd->OrdreObjFormul)
		{
			$sRequeteSql = "UPDATE ObjetFormulaire SET"
			  			." OrdreObjFormul = OrdreObjFormul - 1"
						." WHERE OrdreObjFormul > '{$this->oEnregBdd->OrdreObjFormul}'"
						." AND OrdreObjFormul <= '$v_iNouvPos'"
						." AND IdFormul = '{$this->oEnregBdd->IdFormul}'";
				  
			$this->oBdd->executerRequete($sRequeteSql);
		} 
		else if ($v_iNouvPos < $this->oEnregBdd->OrdreObjFormul)
		{
			$sRequeteSql = "UPDATE ObjetFormulaire SET"
						." OrdreObjFormul = OrdreObjFormul + 1"
						." WHERE OrdreObjFormul >= '$v_iNouvPos'"
						." AND OrdreObjFormul < '{$this->oEnregBdd->OrdreObjFormul}'"
						." AND IdFormul = '{$this->oEnregBdd->IdFormul}'";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$sRequeteSql = "UPDATE ObjetFormulaire SET"
					." OrdreObjFormul = '$v_iNouvPos'"
					." WHERE IdObjFormul = '{$this->oEnregBdd->IdObjFormul}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		// déverrouillage de la table ObjetFormulaire
		$this->oBdd->executerRequete("UNLOCK TABLES");
		$this->defOrdreObjFormul($v_iNouvPos);
	}
	
	/*
	** Fonction 		: NbObjForm
	** Description		: renvoie le nombre total d'objet que comprend un formulaire
	** Entrée			: 
	**					$v_iNumForm	: numéro du formulaire à traiter
	** Sortie			:
	**					nombre total d'objets pour ce formulaire
	*/
	function NbObjForm($v_iNumForm) 	//$v_iNumForm = {$this->oEnregBdd->IdForm} ne fonctionne pas car la classe n'existe pas ? Mais aurais pu etre pratique
	{
		$sRequeteSql = "SELECT * FROM ObjetFormulaire WHERE IdFormul ='{$v_iNumForm}'";
		$hResult2=$this->oBdd->executerRequete($sRequeteSql);
		$i_NbObjForm = $this->oBdd->retNbEnregsDsResult($hResult2);
		$this->oBdd->libererResult($hResult2);
		return $i_NbObjForm;
	}
	
	/*
	** Fonction 		: OrdreMaxObjForm
	** Description		: renvoie le plus grand numéro d'ordre (objet) que comprend un formulaire
	** Entrée			: 
	**					$v_iNumForm	: numéro du formulaire à traiter
	** Sortie			:
	**					le plus grand numéro d'ordre pour ce formulaire
	*/
	function OrdreMaxObjForm($v_iNumForm) 	//$v_iNumForm = {$this->oEnregBdd->IdForm} ne fonctionne pas car la classe n'existe pas ? Mais aurais pu etre pratique
	{
		$sRequeteSql = "SELECT MAX(OrdreObjFormul) as OrdreMax FROM ObjetFormulaire"
					." WHERE IdFormul ='{$v_iNumForm}'";
		
		$hResult2=$this->oBdd->executerRequete($sRequeteSql);
		$oEnreg = $this->oBdd->retEnregSuiv($hResult2);
		$iMaxOrdreObjForm = $oEnreg->OrdreMax;
		$this->oBdd->libererResult($hResult2);
		
		return $iMaxOrdreObjForm;
	}
	
	function enregistrer()
	{
		$sRequeteSql = ($this->retId() > 0 ? "UPDATE ObjetFormulaire SET" : "INSERT INTO ObjetFormulaire SET")
					." IdObjFormul='{$this->oEnregBdd->IdObjFormul}'"
					.", OrdreObjFormul='{$this->oEnregBdd->OrdreObjFormul}'"
					.", IdTypeObj='{$this->oEnregBdd->IdTypeObj}'"
					.", IdFormul='{$this->oEnregBdd->IdFormul}'"
					.($this->oEnregBdd->IdObjFormul > 0 ? " WHERE IdFormul='{$this->oEnregBdd->IdObjFormul}'" : NULL);
		$this->oBdd->executerRequete($sRequeteSql);
		$this->defIdObjFormul($this->oBdd->retDernierId()); //On place dans l'objet créé son Id
	}
	
	function copier($v_iIdFormParent, $iIdObjParent, $v_iOrdreObjet = NULL)
	{
		if ($iIdObjParent < 1)
			return;
		
		$this->oBdd->executerRequete("LOCK TABLES ObjetFormulaire WRITE");
		
		if (empty($v_iOrdreObjet))
		{
			$sSqlOrdreObjet = " OrdreObjFormul='{$this->oEnregBdd->OrdreObjFormul}'";
		}
		else if ($v_iOrdreObjet == "max")
		{
			$iOrdre = $this->OrdreMaxObjForm($v_iIdFormParent) + 1;
			$sSqlOrdreObjet = " OrdreObjFormul='{$iOrdre}'";
		}
		else
		{
			$sSqlOrdreObjet = " OrdreObjFormul='{$v_iOrdreObjet}'";
		}
		
		$sRequeteSql = "INSERT INTO ObjetFormulaire SET"
					.$sSqlOrdreObjet
					.", IdTypeObj='{$this->oEnregBdd->IdTypeObj}'"
					.", IdFormul='{$v_iIdFormParent}'";
					
		$this->oBdd->executerRequete($sRequeteSql);
		
		$iOrdre = $this->oBdd->retDernierId();
		$this->oBdd->executerRequete("UNLOCK TABLES");
		
		return $iOrdre;
	}
	
	function effacer()
	{
		$sRequeteSql = "DELETE FROM ObjetFormulaire"
					." WHERE IdObjFormul ='{$this->oEnregBdd->IdObjFormul}'";
		$this->oBdd->executerRequete($sRequeteSql);
		$this->reorganiser();
	}

	/*
	** Fonction 		: reorganiser
	** Description		: modifie l'ordre des objets d'un formulaire après suppression d'un de ces objets.
								  Cette fonction s'appelle uniquement après un effacement
	** Entrée			:
	** Sortie			:
	*/	
	function reorganiser()
	{
		$sRequeteSql = "UPDATE ObjetFormulaire"
					." SET OrdreObjFormul = OrdreObjFormul-1"
					." WHERE IdFormul='{$this->oEnregBdd->IdFormul}'"
					." AND OrdreObjFormul>'{$this->oEnregBdd->OrdreObjFormul}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Retourne un tableau contenant la liste des objets de formulaire d'une formation
	 * 
	 * @param	v_iIdFormulaire l'id de la formation
	 * 
	 * @return	un tableau contenant la liste des objets de formulaire d'une formation
	 */
	function retListeObjFormulaire($v_iIdFormulaire)
	{
		$iIdx = 0;
		$aoObjFormul = array();
		$sRequeteSql = "SELECT * FROM ObjetFormulaire WHERE IdFormul='$v_iIdFormulaire' ORDER BY OrdreObjFormul";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$aoObjFormul[$iIdx] = new CObjetFormulaire($this->oBdd);
			$aoObjFormul[$iIdx]->init($oEnreg);
			$iIdx++;
		}
		$this->oBdd->libererResult($hResult);
		return $aoObjFormul;
	}
	
	/** @name Fonctions de définition des champs pour cet objet formulaire */
	//@{
	function defIdObjFormul($v_iIdObjForm) { $this->oEnregBdd->IdObjFormul = $v_iIdObjForm; }
	function defOrdreObjFormul($v_iOrdre) { $this->oEnregBdd->OrdreObjFormul = $v_iOrdre; }
	function defIdTypeObj($v_iTypeObj) { $this->oEnregBdd->IdTypeObj = $v_iTypeObj; }
	function defIdFormul($v_iIdForm) { $this->oEnregBdd->IdFormul = $v_iIdForm; }
	//@}
	
	/** @name Fonctions de lecture des champs pour cet objet formulaire */
	//@{
	function retId() { return $this->oEnregBdd->IdObjFormul; }
	function retOrdreObjFormul() { return $this->oEnregBdd->OrdreObjFormul; }
	function retOrdre() { return $this->retOrdreObjFormul(); }
	function retIdTypeObj() { return $this->oEnregBdd->IdTypeObj; }
	function retIdType() { return $this->retIdTypeObj(); }
	function retIdFormul() { return $this->oEnregBdd->IdFormul; }
	//@}
}
?>
