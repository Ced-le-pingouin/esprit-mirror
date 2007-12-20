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
 * @file	hotpotatoes_score.tbl.php
 *
 * Contient la classe de gestion des scores des exercices Hotpot, en rapport avec la DB
 */

/**
 * Gestion des scores des exercices Hotpotatoes (table SQL "Hotpotatoes_Score")
 */
class CHotpotatoesScore
{
	var $oBdd;			///< Objet représentant la connexion à la DB
	var $oEnregBdd;		///< champs SQL (l'objet est rempli à partir de la DB)
	var $iId;			///< Utilisé dans le constructeur, pour indiquer l'id dans la DB

	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 *
	 */
	function CHotpotatoesScore(&$v_oBdd,$v_iId=0)
	{
		$this->oBdd = &$v_oBdd;
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}

	/**
	 * Initialise l'objet avec un enregistrement de la DB ou un objet PHP existant représentant un tel enregistrement
	 * Voir CPersonne#init()
	 */
	function init( $v_oEnregExistant=NULL )
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Hotpotatoes_Score"
					." WHERE IdHotpotScore='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdHotpotScore;
	}

	/**
	 * Enregistre les données de l'exercice courant dans la DB
	 */
	function enregistrer()
	{
		if ($this->oEnregBdd->Fini) {
			$sRequeteSql = "UPDATE Hotpotatoes_Score"
				." SET Fini=1"
				." WHERE IdHotpot={$this->oEnregBdd->IdHotpot}"
				."  AND IdPers={$this->oEnregBdd->IdPers}"
				."  AND DateDebut='{$this->oEnregBdd->DateDebut}'"
				." ORDER BY DateModif DESC LIMIT 1";
		} else {
			$sRequeteSql = "INSERT INTO Hotpotatoes_Score"
				." SET IdHotpot={$this->oEnregBdd->IdHotpot}"
				.", IdPers={$this->oEnregBdd->IdPers}"
				.", Fini=0"
				.", Score={$this->oEnregBdd->Score}"
				.", DateDebut='{$this->oEnregBdd->DateDebut}'";
		}
		$this->oBdd->executerRequete($sRequeteSql);
	}

	/**
	 * Efface le résultat de l'exercice courant
	 */
	function effacer()
	{
		$this->oBdd->executerRequete(
			"DELETE FROM HotpotatoesScore WHERE IdHotpotScore=" . $this->oEnregBdd->IdHotpotScore
			);
	}

	/** @name Fonctions de définition des champs pour cet exercice */
	//@{
	function defIdHotpot( $v_iIdHotpot ) { $this->oEnregBdd->IdHotpot = $v_iIdHotpot; }
	function defIdPers( $v_iIdPers ) { $this->oEnregBdd->IdPers = $v_iIdPers; }
	function defFini( $arg ) { $this->oEnregBdd->Fini = $arg; }
	function defScore( $v_iScore ) { $this->oEnregBdd->Score = $v_iScore; }
	function defDateDebut( $arg ) { $this->oEnregBdd->DateDebut = $arg; }
	//@}
	
	/** @name Fonctions de lecture des champs pour ce formulaire */
	//@{
	function retId() { return $this->oEnregBdd->IdHotpotScore; }
	function retIdHotpot() { return $this->oEnregBdd->IdHotpot; }
	function retIdPers() { return $this->oEnregBdd->IdPers; }
	function retFini() { return $this->oEnregBdd->Fini; }
	function retScore() { return (isset($this->oEnregBdd->Score) ? $this->oEnregBdd->Score : NULL); }
	function retDateDebut() { return $this->oEnregBdd->DateDebut; }
	function retDateModif() { return $this->oEnregBdd->DateModif; }
	//@}
}
?>