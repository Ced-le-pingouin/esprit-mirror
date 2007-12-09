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
// Copyright (C) 2001-2007  Unite de Technologie de l'Education,
//                          Universite de Mons-Hainaut, Belgium,
//                          Grenoble Universités.

/**
 * @file	hotpotatoes.tbl.php
 *
 * Contient la classe de gestion des exercices Hotpot, en rapport avec la DB
 */

/**
 * Gestion des exercices Hotpotatoes (table SQL "Hotpotatoes")
 */
class CHotpotatoes
{
	var $oBdd;			///< Objet représentant la connexion à la DB
	var $oEnregBdd;		///< champs SQL (l'objet est rempli à partir de la DB)
	var $iId;			///< Utilisé dans le constructeur, pour indiquer l'id de la formation à récupérer dans la DB

	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 *
	 */
	function CHotpotatoes(&$v_oBdd,$v_iId=0)
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
			$sRequeteSql = "SELECT * FROM Hotpotatoes"
					." WHERE IdHotpot='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdFormul;
	}

	/**
	 * Ajoute un nouvel exercice dans la DB
	 *
	 * @param	iIdPers	l'id de la personne
	 *
	 * @return	l'id du nouvel exercice
	 */
	function ajouter($iIdPers)
	{
		$sRequeteSql = "INSERT INTO Hotpotatoes SET ...";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}

	/**
	 * Enregistre les données de l'exercice courant dans la DB
	 */
	function enregistrer()
	{
	}

	/**
	 * Efface l'exercice courant
	 */
	function effacer()
	{
		$this->oBdd->executerRequete(
			"DELETE FROM Hotpotatoes WHERE IdHotpot=" . $this->oEnregBdd->IdHotpot
			);
	}

	/** @name Fonctions de définition des champs pour cet exercice */
	//@{
	function defTitre ($v_sTitre) { $this->oEnregBdd->Titre = trim($v_sTitre); }
	function defStatut ($v_iStatut) { $this->oEnregBdd->Statut = $v_iStatut; }
	function defType ($v_sType) { $this->oEnregBdd->Type = trim($v_sType); }
	function defIdPers ($v_iIdPers) { $this->oEnregBdd->IdPers = $v_iIdPers; }
	//@}
	
	/** @name Fonctions de lecture des champs pour ce formulaire */
	//@{
	function retId () { return $this->oEnregBdd->IdFormul; }
	function retTitre () { return $this->oEnregBdd->Titre; }
	function retStatut () { return $this->oEnregBdd->Statut; }
	function retType () { return $this->oEnregBdd->Type; }
	function retIdPers () { return $this->oEnregBdd->IdPers; }
	//@}
}
?>