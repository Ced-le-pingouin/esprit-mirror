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
			// LOCK TABLES ici ??? pourquoi en avoir besoin alors qu'ailleurs �a marche sans �a?
			//$hResult = $this->oBdd->executerRequete("LOCK TABLES Hotpotatoes WRITE");
			$sRequeteSql = "SELECT * FROM Hotpotatoes"
					." WHERE IdHotpot=".$this->iId;
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
			//$hResult = $this->oBdd->executerRequete("UNLOCK TABLES");
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
		$sRequeteSql = "SELECT HS.* FROM Hotpotatoes_Score HS"
			." JOIN "
				."(SELECT IdHotpot, IdPers, MAX(DateModif) AS MDM FROM Hotpotatoes_Score GROUP BY IdHotpot,IdPers ) T1"
				." ON (HS.DateModif=T1.MDM AND T1.IdHotpot=HS.IdHotpot)"
			." WHERE HS.IdHotpot={$this->oEnregBdd->IdHotpot} AND HS.IdPers IN ($ids)"
			." ORDER BY IdHotpot, IdPers";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
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
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
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
	 * Récupère la liste des étudiants ayant répondu
	 *
	 * @return	Tableau d'objets CPersonne
	 */
	function etudiants( )
	{
		$sRequeteSql = "SELECT DISTINCT Hotpotatoes_Score.IdPers FROM Hotpotatoes_Score"
			 ." JOIN Personne USING (IdPers)"
			 ." WHERE IdHotpot={$this->oEnregBdd->IdHotpot}"
		     ." ORDER BY Personne.Nom ASC, Personne.Prenom ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$etudiants = array();
		while ($row = $this->oBdd->retEnregSuiv($hResult)) {
			$etudiants[] = new CPersonne($this->oBdd,$row->IdPers);
		}
		return $etudiants;
	}

	/**
	 * Récupère le nombre max d'essais pour un exo
	 *
	 * @return	Entier
	 */
	function retMaxEssais( )
	{
		$sRequeteSql = "SELECT count(*) AS num FROM Hotpotatoes_Score"
			 ." WHERE IdHotpot={$this->oEnregBdd->IdHotpot}"
		     ." GROUP BY IdPers";
		$max = 0;
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		while ($row = $this->oBdd->retEnregSuiv($hResult)) {
			if ($row->num > $max)
				$max = $row->num;
		}
		return $max;
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
	 * Copie l'exercice actuel vers un nouvel exercice (mêmes données)
	 *
	 * @return int l'id du nouvel exercice
	 */
	function copier()
	{
		$sRequeteSql = 
			 "INSERT INTO Hotpotatoes "
			."SET Titre='".mysql_real_escape_string($this->oEnregBdd->Titre)."'"
			."  , Fichier='".mysql_real_escape_string($this->oEnregBdd->Fichier)."'"
			."  , Statut='".mysql_real_escape_string($this->oEnregBdd->Statut)."'"
			."  , Type='".mysql_real_escape_string($this->oEnregBdd->Type)."'"
			."  , IdPers={$this->oEnregBdd->IdPers}"
		;
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
			"DELETE FROM Hotpotatoes WHERE IdHotpot='" . $this->oEnregBdd->IdHotpot."'"
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