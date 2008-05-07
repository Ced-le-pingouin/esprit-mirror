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
		$sRequeteSql = "INSERT INTO Hotpotatoes_Score"
				." SET IdHotpot={$this->oEnregBdd->IdHotpot}"
				.", IdPers={$this->oEnregBdd->IdPers}"
				.", Score={$this->oEnregBdd->Score}"
				.", DateDebut='{$this->oEnregBdd->DateDebut}'"
				.", DateFin='{$this->oEnregBdd->DateFin}'";
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

	/**
	 * Calcule la durée de réalisation du score
	 */
	function retDuree( $fr=TRUE )
	{
		if (empty($this->oEnregBdd->DateDebut) || empty($this->oEnregBdd->DateFin))
			return FALSE;
		list($sDate,$sTime) = explode(" ",$this->oEnregBdd->DateDebut);
        $asDate = explode("-",$sDate);
        $asTime = explode(":",$sTime);
		$time1 = mktime($asTime[0],$asTime[1],$asTime[2],$asDate[1],$asDate[2],$asDate[0]);
		list($sDate,$sTime) = explode(" ",$this->oEnregBdd->DateFin);
        $asDate = explode("-",$sDate);
        $asTime = explode(":",$sTime);
		$time2 = mktime($asTime[0],$asTime[1],$asTime[2],$asDate[1],$asDate[2],$asDate[0]);
		if ($fr)
			return strftime("%M min. %S s.",$time2-$time1);
		else
			return ($time2-$time1);
	}

	/**
	 * Calcule l'heure du début de l'exercice (sans utiliser la date utilisateur, peu fiable)
	 */
	function retDateInitiale() {
		if (empty($this->oEnregBdd->DateModif) || ($this->retDuree===FALSE))
			return FALSE;
		list($sDate,$sTime) = explode(" ",$this->oEnregBdd->DateModif);
        $asDate = explode("-",$sDate);
        $asTime = explode(":",$sTime);
		$time1 = mktime($asTime[0],$asTime[1],$asTime[2],$asDate[1],$asDate[2],$asDate[0]);
		return ($time1 - $this->retDuree(FALSE));
	}

	/** @name Fonctions de définition des champs pour cet exercice Hotpot */
	//@{
	function defIdHotpot( $v_iIdHotpot ) { $this->oEnregBdd->IdHotpot = $v_iIdHotpot; }
	function defIdPers( $v_iIdPers ) { $this->oEnregBdd->IdPers = $v_iIdPers; }
	function defScore( $v_iScore ) { $this->oEnregBdd->Score = $v_iScore; }
	function defDateDebut( $arg ) { $this->oEnregBdd->DateDebut = $arg; }
	function defDateFin( $arg ) { $this->oEnregBdd->DateFin = $arg; }
	//@}
	
	/** @name Fonctions de lecture des champs pour cet exercice Hotpot */
	//@{
	function retId() { return $this->oEnregBdd->IdHotpotScore; }
	function retIdHotpot() { return $this->oEnregBdd->IdHotpot; }
	function retIdPers() { return $this->oEnregBdd->IdPers; }
	function retScore() { return (isset($this->oEnregBdd->Score) ? $this->oEnregBdd->Score : NULL); }
	function retDateDebut() { return $this->oEnregBdd->DateDebut; }
	function retDateFin() { return $this->oEnregBdd->DateFin; }
	function retDateModif() { return $this->oEnregBdd->DateModif; }
	//@}
}
?>