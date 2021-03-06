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
 * @file	qtextelong.tbl.php
 * 
 * Contient la classe de gestion des questions de formulaire de type "texte long", en rapport avec la DB
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 */

/**
* Gestion des questions de type "texte long" des activités en ligne, et encapsulation de la table QTexteLong de la DB
*/
class CQTexteLong 
{
	var $oBdd;				///< Objet représentant la connexion à la DB
	var $iId;				///< Utilisé dans le constructeur, pour indiquer l'id du sujet à récupérer dans la DB
	var $oEnregBdd;			///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CQTexteLong(&$v_oBdd,$v_iId=0) 
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
			$sRequeteSql = "SELECT * FROM QTexteLong WHERE IdObjFormul='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjFormul;
	}
	
	function ajouter($v_iIdObjForm) //Cette fonction ajoute une question de type texte long, avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO QTexteLong SET IdObjFormul='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	//Fonctions de définition
	function defIdObjFormul ($v_iIdObjForm) { $this->oEnregBdd->IdObjFormul = $v_iIdObjForm; }
	function defEnonQTL ($v_sEnonQTL) { $this->oEnregBdd->EnonQTL = $v_sEnonQTL; }
	function defAlignEnonQTL ($v_sAlignEnonQTL) { $this->oEnregBdd->AlignEnonQTL = $v_sAlignEnonQTL; }
	function defAlignRepQTL ($v_sAlignRepQTL) { $this->oEnregBdd->AlignRepQTL = $v_sAlignRepQTL; }
	function defLargeurQTL ($v_iLargeurQTL) { $this->oEnregBdd->LargeurQTL = trim($v_iLargeurQTL); }
	function defHauteurQTL ($v_iHauteurQTL) { $this->oEnregBdd->HauteurQTL = trim($v_iHauteurQTL); }
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjFormul; }
	function retEnonQTL () { return $this->oEnregBdd->EnonQTL; }
	function retAlignEnonQTL () { return $this->oEnregBdd->AlignEnonQTL; }
	function retAlignRepQTL () { return $this->oEnregBdd->AlignRepQTL; }
	function retLargeurQTL () { return $this->oEnregBdd->LargeurQTL; }
	function retHauteurQTL () { return $this->oEnregBdd->HauteurQTL; }
	
 	function enregistrer()
	{
		if ($this->oEnregBdd->IdObjFormul != NULL)
		{
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			//de les stocker dans la BD sans erreur 
			$sEnonQTL = validerTexte($this->oEnregBdd->EnonQTL);
			
			$sRequeteSql = "REPLACE QTexteLong SET"
						." IdObjFormul='{$this->oEnregBdd->IdObjFormul}'"
						.", EnonQTL='{$sEnonQTL}'"
						.", AlignEnonQTL='{$this->oEnregBdd->AlignEnonQTL}'"
						.", AlignRepQTL='{$this->oEnregBdd->AlignRepQTL}'"
						.", LargeurQTL='{$this->oEnregBdd->LargeurQTL}'"
						.", HauteurQTL='{$this->oEnregBdd->HauteurQTL}'";
			$this->oBdd->executerRequete($sRequeteSql);
		}
		else
		{
			echo "Identifiant NULL: enregistrement impossible";
		}
	}
	
	function enregistrerRep($v_iIdFC, $v_iIdObjForm, $v_sReponsePersQTL)
	{
		if ($v_iIdObjForm != NULL)
		{
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			// de les stocker dans la BD sans erreur 
			$sReponsePersQTL = validerTexte($v_sReponsePersQTL);
			
			$sRequeteSql = "REPLACE ReponseTexte SET"
						." IdFC='{$v_iIdFC}'"
						.", IdObjFormul='{$v_iIdObjForm}'"
						.", Valeur='{$sReponsePersQTL}'";
				
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
		$sEnonQTL = validerTexte($this->oEnregBdd->EnonQTL);
		
		$sRequeteSql = "INSERT INTO QTexteLong SET"
					." IdObjFormul='{$v_iIdNvObjForm}'"
					.", EnonQTL='{$sEnonQTL}'"
					.", AlignEnonQTL='{$this->oEnregBdd->AlignEnonQTL}'"
					.", AlignRepQTL='{$this->oEnregBdd->AlignRepQTL}'"
					.", LargeurQTL='{$this->oEnregBdd->LargeurQTL}'"
					.", HauteurQTL='{$this->oEnregBdd->HauteurQTL}'"; 
			
		$this->oBdd->executerRequete($sRequeteSql);
		$iIdObjForm = $this->oBdd->retDernierId();
		return $iIdObjForm;
	}
	
	function effacer()
	{
		$sRequeteSql = "DELETE FROM QTexteLong WHERE IdObjFormul ='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
}
?>
