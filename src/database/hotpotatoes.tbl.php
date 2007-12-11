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

require_once(dir_database("hotpotatoes_score.tbl.php"));

/**
 * Gestion des exercices Hotpotatoes (table SQL "Hotpotatoes")
 */
class CHotpotatoes
{
	var $oBdd;			///< Objet représentant la connexion à la DB
	var $oEnregBdd;		///< champs SQL (l'objet est rempli à partir de la DB)
	var $iId;			///< Utilisé dans le constructeur, pour indiquer l'id de la formation à récupérer dans la DB
	var $aoScores;      ///< Tableau des objets scores des étudiants

	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 *
	 */
	function CHotpotatoes( &$v_oBdd,$v_iId=0 )
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
			$sRequeteSql = "SELECT * FROM Hotpotatoes"
					." WHERE IdHotpot=".$this->iId;
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdHotpot;
	}

	/**
	 * Récupère un objet score ou un tableau des derniers scores pour un ou plusieurs étudiants
	 *
	 * @param	etudiants IdPers ou tableau d'IdPers
	 *
	 * @return	Objet CHotpotatoesScore ou tableau de ces objets
	 */
	function der_score_par_etudiant( $etudiants )
	{
		if (empty($etudiants)) {
			// TODO !!!!
			return FALSE;
		} elseif (is_array($etudiants)) {
			$ids = join(',',$etudiants);
		} else {
			$ids = $etudiants;
		}
		$sRequeteSql = "SELECT *, MAX(DateModif) AS DateH FROM Hotpotatoes_Score"
			 ." WHERE IdHotpot={$this->oEnregBdd->IdHotpot} AND IdPers IN ($ids)"
			 ." GROUP BY IdHotpot";
		$this->oBdd->executerRequete($sRequeteSql);
		if (is_array($etudiants)) {
			$scores = array();
			$i = 0;
			while ($row = $this->oBdd->retEnregSuiv($hResult)) {
				$score[$i] = new CHotpotatoesScore($this->oBdd);
				$score[$i]->init( $row );
				$i++;
			}
			return $scores;
		} else {
			$score = new CHotpotatoesScore( $this->oBdd );
			$score->init( $this->oBdd->retEnregSuiv($hResult) );
			return $score;
		}

	}

	/**
	 * Récupère un tableau des objets scores pour un étudiant
	 *
	 * @param	IdPers Id SQL de l'étudiant
	 *
	 * @return	Tableau d'objets CHotpotatoesScore
	 */
	function scores_par_etudiant( $IdPers )
	{
		$sRequeteSql = "SELECT * FROM Hotpotatoes_Score"
			 ." WHERE IdHotpot={$this->oEnregBdd->IdHotpot} AND IdPers=".$IdPers
			 ." ORDER BY DateModif ASC";
		$this->oBdd->executerRequete($sRequeteSql);
		$scores = array();
		$i = 0;
		while ($row = $this->oBdd->retEnregSuiv($hResult)) {
			$scores[$i] = new CHotpotatoesScore($this->oBdd);
			$scores[$i]->init( $row );
			$i++;
		}
		return $scores;
	}

	/**
	 * Ajoute un nouvel exercice dans la DB
	 *
	 * @param	iIdPers	l'id de la personne
	 *
	 * @return	l'id du nouvel exercice
	 */
	function ajouter( $iIdPers )
	{
		$sRequeteSql = "INSERT INTO Hotpotatoes SET Titre='Nouvel exercice', IdPers=$iIdPers";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->oEnregBdd->IdHotpot = $this->oBdd->retDernierId());
	}

	/**
	 * Enregistre les données de l'exercice courant dans la DB
	 */
	function enregistrer()
	{
		$sRequeteSql = "UPDATE Hotpotatoes"
			." SET Titre='".mysql_real_escape_string($this->oEnregBdd->Titre)."'"
			.", Fichier='".mysql_real_escape_string($this->oEnregBdd->Fichier)."'"
			.", IdPers=".$this->oEnregBdd->IdPers
			." WHERE IdHotpot=".$this->oEnregBdd->IdHotpot;
		$this->oBdd->executerRequete($sRequeteSql);
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
	function defTitre( $v_sTitre ) { $this->oEnregBdd->Titre = trim($v_sTitre); }
	function defFichier( $v_sFichier ) { $this->oEnregBdd->Fichier = trim($v_sFichier); }
	function defStatut( $v_iStatut ) { $this->oEnregBdd->Statut = $v_iStatut; }
	function defType( $v_sType ) { $this->oEnregBdd->Type = trim($v_sType); }
	function defIdPers( $v_iIdPers ) { $this->oEnregBdd->IdPers = $v_iIdPers; }
	//@}
	
	/** @name Fonctions de lecture des champs pour cet exercice */
	//@{
	function retId() { return $this->oEnregBdd->IdHotpot; }
	function retTitre() { return $this->oEnregBdd->Titre; }
	function retFichier() { return $this->oEnregBdd->Fichier; }
	function retStatut() { return $this->oEnregBdd->Statut; }
	function retType() { return $this->oEnregBdd->Type; }
	function retIdPers() { return $this->oEnregBdd->IdPers; }
	//@}
}
?>