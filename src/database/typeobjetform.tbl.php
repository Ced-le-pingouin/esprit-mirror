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
 * @file	typeobjetform.tbl.php
 * 
 * Contient la classe de gestion des types d'objet de formulaire, en rapport avec la DB
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 */

/**
 * Gestion des types d'objet de formulaire, et encapsulation de la table TypeObjetForm de la DB
 */
class CTypeObjetForm 
{
	var $iId;					///< Utilisé dans le constructeur, pour indiquer l'id de l'objet de formulaire à récupérer dans la DB
	var $oBdd;					///< Objet représentant la connexion à la DB
	var $oEnregBdd;				///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici
	
	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CTypeObjetForm(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;  
		if (($this->iId = $v_iId) > 0)
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
		}
		else
		{
			$sRequeteSql = "SELECT * FROM TypeObjetForm WHERE IdTypeObj='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdTypeObj;
	}
	
	/**
	 * Ajoute un nouveau type d'objet de formulaire
	 * 
	 * @return	l'id du nouveau type d'objet de formulaire
	 */
	function ajouter()
	{
		$sRequeteSql = "INSERT INTO TypeObjetForm SET IdTypeObj=NULL;";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	/**
	 * Retourne un tableau contenant la liste des types d'objet de formulaire
	 * 
	 * @return	la liste des types d'objet de formulaire
	 */
	function retListeTypeObjet()
	{
		$iIdx = 0;
		$aoTypeObj = array();
		$sRequeteSql = "SELECT * FROM TypeObjetForm ORDER BY IdTypeObj";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$aoTypeObj[$iIdx] = new CTypeObjetForm($this->oBdd);
			$aoTypeObj[$iIdx]->init($oEnreg);
			$iIdx++;
		}
		$this->oBdd->libererResult($hResult);
		return $aoTypeObj;
	}
	
	/** @name Fonctions de définition des champs pour ce type d'objet de formulaire */
	//@{
	function defIdTypeObj ($v_iIdTypeObj) { $this->oEnregBdd->IdTypeObj = $v_iIdTypeObj; }
	function defNomTypeObj ($v_sNomTypeObj) { $this->oEnregBdd->NomTypeObj = $v_sNomTypeObj; }
	function defDescTypeObj ($v_sDescTypeObj) { $this->oEnregBdd->DescTypeObj = $v_sDescTypeObj; }
	//@}
	
	/** @name Fonctions de lecture des champs pour ce type d'objet de formulaire */
	//@{
	function retId () { return $this->oEnregBdd->IdTypeObj; }
	function retNomTypeObj () { return $this->oEnregBdd->NomTypeObj; }
	function retDescTypeObj () { return $this->oEnregBdd->DescTypeObj; }
	//@}
}
?>
