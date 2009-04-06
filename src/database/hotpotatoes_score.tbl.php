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
//                          Grenoble UniversitÃ©s.

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
	var $oBdd;			///< Objet reprÃ©sentant la connexion Ã  la DB
	var $oEnregBdd;		///< champs SQL (l'objet est rempli Ã  partir de la DB)
	var $iId;			///< UtilisÃ© dans le constructeur, pour indiquer l'id dans la DB

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
	 * Initialise l'objet avec un enregistrement de la DB ou un objet PHP existant reprÃ©sentant un tel enregistrement
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
	 * Enregistre les donnÃ©es de l'exercice courant dans la DB
	 * 
	 * Ajout d'un nombre aléatoire et du nom de fichier dans la DB afin de vérifier si un exercice comporte plusieurs pages
	 * Le nombre aléatoire est généré seulement au moment du clic sur la partie gauche (menu),
	 * il restera le même tout au long de l'exercice.
	 * 
	 */
	function enregistrer()
	{
		$sRequeteSql = "INSERT INTO Hotpotatoes_Score"
				." SET IdHotpot={$this->oEnregBdd->IdHotpot}"
				.", IdPers={$this->oEnregBdd->IdPers}"
				.", Score={$this->oEnregBdd->Score}"
				.", DateDebut='{$this->oEnregBdd->DateDebut}'"
				.", DateFin='{$this->oEnregBdd->DateFin}'"
				.", IdSessionExercice='{$this->oEnregBdd->IdSessionExercice}'"
				.", NombreExercice='{$this->oEnregBdd->NombreExercice}'"
				.", NumeroPage='{$this->oEnregBdd->NumeroPage}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}

	/**
	 * Efface le rÃ©sultat de l'exercice courant
	 */
	function effacer()
	{
		$this->oBdd->executerRequete(
			"DELETE FROM HotpotatoesScore WHERE IdHotpotScore=" . $this->oEnregBdd->IdHotpotScore
			);
	}

	/**
	 * Calcule la durÃ©e de rÃ©alisation du score
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
	 * Calcule l'heure du dÃ©but de l'exercice (sans utiliser la date utilisateur, peu fiable)
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

	/**
	 * Vérifie si l'exercice à déjà été fait sur la session en cours.
	 * @return	\c score
	 */
	function ExerciceFait($v_iIdPersonne, $v_iIdHotpot, $v_sIdExercice, $v_iNumeroPage) {
		$iScoreExercice = NULL;

		$sRequeteSql = "SELECT IdHotpotScore,IdHotpot,IdPers,Score,IdSessionExercice,NumeroPage FROM Hotpotatoes_Score"
				." WHERE"
				." IdHotpot={$v_iIdHotpot}"
				." AND IdPers = {$v_iIdPersonne}"
				." AND IdSessionExercice='{$v_sIdExercice}'"
				." AND NumeroPage='{$v_iNumeroPage}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);

		if ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$iScoreExercice = $oEnreg->Score;

		$this->oBdd->libererResult($hResult);

		return $iScoreExercice;
	}

	/**
	 * Calcul la moyenne des scores selon leur Id de session et si l'Id est différent de 0 (ce qui a été inscrit dans la DB avant cette modification).
	 * @return	\c la moyenne par Id.
	 */
	function CalculMoyenne($iIdExercice=0) {
		$iMoyenne = $iScoreTotal = 0;

		if ($iIdExercice!=0) $iIdSessionExercice = $iIdExercice;
		else if (isset($this->oEnregBdd->IdSessionExercice)) $iIdSessionExercice = $this->oEnregBdd->IdSessionExercice;
		else $iIdSessionExercice = 0;

		if ($iIdSessionExercice != 0) {
			$sRequeteSql	= "SELECT Score FROM Hotpotatoes_Score"
							." WHERE IdSessionExercice='$iIdSessionExercice'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$iNombreDeScore = $this->oBdd->retNbEnregsDsResult($hResult);
			if ($iNombreDeScore == 0) return;
			while ($row = $this->oBdd->retEnregSuiv($hResult)) {
				$iScoreTotal += $row->Score;
			}
			$iMoyenne = round($iScoreTotal / $iNombreDeScore, 2);
			return $iMoyenne;
		}
		return $this->retScore();
	}

	/**
	 * Calcul le nombre de score par ID d'exercice si le numéro de session existe.
	 * @return	\c nombre de scores
	 */
	function NbScoreParId() {
		$iCompte = $iNombreDeScore = 0;

		if (isset($this->oEnregBdd->IdSessionExercice) && ($this->oEnregBdd->IdSessionExercice != 0)) {
			$sRequeteSql	= "SELECT Score, NombreExercice FROM Hotpotatoes_Score"
							." WHERE IdSessionExercice='{$this->oEnregBdd->IdSessionExercice}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$iNombreDeScore = $this->oBdd->retNbEnregsDsResult($hResult);
			return $iNombreDeScore;
		}
		return 1;
	}

	/**
	 * Calcul le nombre réel d'exercice fait.
	 * @return	\c nombre d'exercice
	 */
	function NbReelScoresParId() {
		$iNombreDeScoreAllIn = 0;
		if (isset($this->oEnregBdd->IdSessionExercice) && ($this->oEnregBdd->IdSessionExercice != 0)) {
			$sRequeteSql	= "SELECT NombreExercice FROM Hotpotatoes_Score"
							." WHERE IdSessionExercice='{$this->oEnregBdd->IdSessionExercice}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$iNombreDeScore = $this->oBdd->retNbEnregsDsResult($hResult);

			if (isset($this->oEnregBdd->NombreExercice) && ($this->oEnregBdd->NombreExercice > 0)) {
				$iNombreDeScoreAllIn = $this->oEnregBdd->NombreExercice - 1;
				$iNombreDeScore += $iNombreDeScoreAllIn;
			}

			return $iNombreDeScore;
		}
		return 1;
	}

	/** @name Fonctions de dÃ©finition des champs pour cet exercice Hotpot */
	//@{
	function defIdHotpot( $v_iIdHotpot ) { $this->oEnregBdd->IdHotpot = $v_iIdHotpot; }
	function defIdPers( $v_iIdPers ) { $this->oEnregBdd->IdPers = $v_iIdPers; }
	function defScore( $v_iScore ) { $this->oEnregBdd->Score = $v_iScore; }
	function defDateDebut( $arg ) { $this->oEnregBdd->DateDebut = $arg; }
	function defDateFin( $arg ) { $this->oEnregBdd->DateFin = $arg; }
	
	function defIdSessionExercice($v_sIdExercice) { $this->oEnregBdd->IdSessionExercice = $v_sIdExercice; }
	function defNombreQuestion($v_iNbQuestion) { $this->oEnregBdd->NombreExercice = $v_iNbQuestion; }
	function defNumeroPage($v_iNumeroPage) { $this->oEnregBdd->NumeroPage = $v_iNumeroPage; }
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

	function retIdSessionExercice() { return $this->oEnregBdd->IdSessionExercice; }
	function retNombreQuestion() { return $this->oEnregBdd->NombreExercice; }
	function retNumeroPage() { return ($this->oEnregBdd->NumeroPage); }
	//@}
}
?>