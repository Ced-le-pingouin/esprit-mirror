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
 * @file	qtextecourt.tbl.php
 * 
 * Contient la classe de gestion des questions de formulaire de type "texte court", en rapport avec la DB
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 */

/**
* Gestion des questions de type "texte court" des activités en ligne, et encapsulation de la table QTexteCourt de la DB
*/
class CQTexteCourt 
{
	var $oBdd;				///< Objet représentant la connexion à la DB
	var $iId;				///< Utilisé dans le constructeur, pour indiquer l'id du sujet à récupérer dans la DB
	var $oEnregBdd;			///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CQTexteCourt(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;  
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	/**
	 * Initialise l'objet avec un enregistrement de la DB ou un objet PHP existant représentant un tel enregistrement
	 * Voir CPersonne#init()
	 */
	function init($v_oEnregExistant=NULL)  
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM QTexteCourt WHERE IdObjFormul='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjFormul;
	}
	
	function ajouter($v_iIdObjForm) //Cette fonction ajoute une question de type texte court, avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO QTexteCourt SET IdObjFormul='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	//Fonctions de définition
	function defIdObjFormul ($v_iIdObjForm) { $this->oEnregBdd->IdObjFormul = $v_iIdObjForm; }
	function defEnonQTC ($v_sEnonQTC) { $this->oEnregBdd->EnonQTC = $v_sEnonQTC; }
	function defAlignEnonQTC ($v_sAlignEnonQTC) { $this->oEnregBdd->AlignEnonQTC = $v_sAlignEnonQTC; }
	function defAlignRepQTC ($v_sAlignRepQTC) { $this->oEnregBdd->AlignRepQTC = $v_sAlignRepQTC; }
	function defTxtAvQTC ($v_sTxtAvQTC) { $this->oEnregBdd->TxtAvQTC = $v_sTxtAvQTC; }
	function defTxtApQTC ($v_sTxtApQTC) { $this->oEnregBdd->TxtApQTC = $v_sTxtApQTC; }
	function defLargeurQTC ($v_iLargeurQTC) { $this->oEnregBdd->LargeurQTC = trim($v_iLargeurQTC); }
	function defMaxCarQTC ($v_iMaxCarQTC) { $this->oEnregBdd->MaxCarQTC = trim($v_iMaxCarQTC); }
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjFormul; }
	function retEnonQTC () { return $this->oEnregBdd->EnonQTC; }
	function retAlignEnonQTC () { return $this->oEnregBdd->AlignEnonQTC; }
	function retAlignRepQTC () { return $this->oEnregBdd->AlignRepQTC; }
	function retTxtAvQTC () { return $this->oEnregBdd->TxtAvQTC; }
	function retTxtApQTC () { return $this->oEnregBdd->TxtApQTC; }
	function retLargeurQTC () { return $this->oEnregBdd->LargeurQTC; }
	function retMaxCarQTC () { return $this->oEnregBdd->MaxCarQTC; }
	
	function enregistrer()
	{
		if ($this->oEnregBdd->IdObjFormul != NULL)
		{
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			//de les stocker dans la BD sans erreur 
			$sEnonQTC = validerTexte($this->oEnregBdd->EnonQTC);
			$sTxtAvQTC = validerTexte($this->oEnregBdd->TxtAvQTC);
			$sTxtApQTC = validerTexte($this->oEnregBdd->TxtApQTC);
			
			//Valeur par défaut de MaxCar c'est la valeur de LargeurQTC
			if (strlen($this->oEnregBdd->MaxCarQTC) < 1) 
				{$this->oEnregBdd->MaxCarQTC = $this->oEnregBdd->LargeurQTC;}
			
			$sRequeteSql = "REPLACE QTexteCourt SET"									  
						." IdObjFormul='{$this->oEnregBdd->IdObjFormul}'"
						.", EnonQTC='{$sEnonQTC}'"
						.", AlignEnonQTC='{$this->oEnregBdd->AlignEnonQTC}'"
						.", AlignRepQTC='{$this->oEnregBdd->AlignRepQTC}'"
						.", TxtAvQTC='{$sTxtAvQTC}'"
						.", TxtApQTC='{$sTxtApQTC}'"
						.", LargeurQTC='{$this->oEnregBdd->LargeurQTC}'"
						.", MaxCarQTC='{$this->oEnregBdd->MaxCarQTC}'"; 		
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
		else
		{
			echo "Identifiant NULL enregistrement impossible";
		}
	}
	
	function enregistrerRep($v_iIdFC,$v_iIdObjForm,$v_sReponsePersQTC)
	{
		if ($v_iIdObjForm != NULL)
		{	
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			//de les stocker dans la BD sans erreur 
			$sReponsePersQTC = validerTexte($v_sReponsePersQTC);
			
			$sRequeteSql = "REPLACE ReponseCar SET"									  
						." IdFC='{$v_iIdFC}'"
						.", IdObjFormul='{$v_iIdObjForm}'"
						.", Valeur='{$sReponsePersQTC}'";
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
		else
		{
			echo "Identifiant NULL enregistrement impossible";
		}
	}
	
	function copier($v_iIdNvObjForm)
	{
		if ($v_iIdNvObjForm < 1)
			return;
		
		// Les variables contenant du "texte" doivent être formatées, cela permet 
		//de les stocker dans la BD sans erreur 
		$sEnonQTC = validerTexte($this->oEnregBdd->EnonQTC);
		$sTxtAvQTC = validerTexte($this->oEnregBdd->TxtAvQTC);
		$sTxtApQTC = validerTexte($this->oEnregBdd->TxtApQTC);
		
		$sRequeteSql = "INSERT INTO QTexteCourt SET"									  
					." IdObjFormul='{$v_iIdNvObjForm}'"
					.", EnonQTC='{$sEnonQTC}'"
					.", AlignEnonQTC='{$this->oEnregBdd->AlignEnonQTC}'"
					.", AlignRepQTC='{$this->oEnregBdd->AlignRepQTC}'"
					.", TxtAvQTC='{$sTxtAvQTC}'"
					.", TxtApQTC='{$sTxtApQTC}'"
					.", LargeurQTC='{$this->oEnregBdd->LargeurQTC}'"
					.", MaxCarQTC='{$this->oEnregBdd->MaxCarQTC}'"; 
		
		$this->oBdd->executerRequete($sRequeteSql);
		$iIdObjForm = $this->oBdd->retDernierId();
		
		return $iIdObjForm;
	}
	
	function effacer()
	{
		$sRequeteSql = "DELETE FROM QTexteCourt WHERE IdObjFormul ='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
}
?>
