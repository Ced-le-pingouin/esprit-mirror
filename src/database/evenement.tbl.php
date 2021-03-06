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

/*
** Fichier .................: evenement.tbl.php
** Description .............:
** Date de création ........: 01/03/2002
** Dernière modification ...: 06/05/2004
** Auteurs .................: Filippo PORCO
** Emails ..................: ute@umh.ac.be
**
*/

define("ERREUR_CONNEXION",1);
define("PERSONNE_CONNECTE",2);
define("PERSONNE_DECONNECTE",3);

class CEvenement
{
	var $oBdd;
	var $iId;
	
	var $aoEvenements;
	var $oConnecte;
	var $oEnregBdd;
	
	var $bParFormations = FALSE;
	
	var $iModeTri, $bTriAscendant;
	
	var $iIdPers;
	
	// Pseudo constant
	var $TRI_NOM = 0;
	var $TRI_PRENOM = 1;
	var $TRI_PSEUDO = 2;
	var $TRI_NBR_CONNEXIONS = 3;
	var $TRI_DERNIERE_CONNEXION = 4;
	var $TRI_DERNIERE_DECONNEXION = 5;
	var $TRI_CONNEXION = 6;
	var $TRI_DECONNEXION = 7;
	var $TRI_TEMPS_CONNEXIONS = 8;
	var $TRI_FORMATION = 9;
	
	var $TRI_ASCENDANT = TRUE;
	var $TRI_DESCENDANT = FALSE;
	
	function CEvenement ($v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iModeTri = $this->TRI_NOM;
		$this->bTriAscendant = $this->TRI_ASCENDANT;
		$this->iIdPers = NULL;
		
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	function init ($v_oEnregBddExistant=NULL)
	{
		if (isset($v_oEnregBddExistant))
		{
			$this->oEnregBdd = $v_oEnregBddExistant;
			
			if (isset($v_oEnregBddExistant->IdPers))
			{
				$this->oConnecte = new CPersonne($this->oBdd);
				$this->oConnecte->init($v_oEnregBddExistant);
			}
		}
		else
		{
			$sRequeteSql = "SELECT Evenement.* FROM Evenement"
				." WHERE Evenement.IdEven='{$this->iId}'";
			
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function effacer ()
	{
		if (isset($this->iIdForm) && $this->iIdForm > 0)
		{
			$oEvenDetail = new CEvenement_Detail($this->oBdd,NULL,$this->iIdForm);
			$aiIdsEvens = $oEvenDetail->retIdsEvensParFormation();
			$oEvenDetail->effacerParFormation();
			
			$sValeursSql = NULL;
			
			foreach ($aiIdsEvens as $iId)
				$sValeursSql .= (isset($sValeursSql) ? "," : NULL)
					."'{$iId}'";
			
			if (isset($sValeursSql))
			{
				$sRequeteSql = "DELETE FROM Evenement"
					." WHERE IdEven IN ({$sValeursSql})"
					." AND MomentEven IS NOT NULL"
					." AND SortiMomentEven IS NOT NULL";
				
				$this->oBdd->executerRequete($sRequeteSql);
			}
		}
	}
	
	function defIdFormation ($v_iIdForm)
	{
		$this->iIdForm = $v_iIdForm;
	}
	
	function defParFormations ($v_bParFormations=FALSE)
	{
		$this->bParFormations = $v_bParFormations;
	}
	
	function retListeConnectes ()
	{
		$sRequeteSql = "select CURDATE()";
		
		$this->oBdd->executerRequete ($sRequeteSql);
		
		$date = $this->oBdd->retEnregPrecis();
		
		$sRequeteSql = "SELECT * FROM Evenement"
			." WHERE IdPers>'0'"
			." AND IdTypeEven=".PERSONNE_CONNECTE
			." AND MomentEven LIKE \"$date%\" AND SortiMomentEven IS NULL";
		
		$asNomConnectes = array();
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($ligne=$this->oBdd->retEnregSuiv($hResult))
		{
			$oPers = new CPersonne($this->oBdd,$ligne->IdPers);
			$asNomConnectes[] = $oPers->retNomComplet();
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $asNomConnectes;
	}
	
	function defPersonne ($v_iIdPers=NULL)
	{
		$this->iIdPers = $v_iIdPers;
	}
	
	function retPersonne ()
	{
		return $this->iIdPers;
	}
	
	function defModeTri ($v_iModeTri)
	{
		$this->iModeTri = $v_iModeTri;
	}
	
	function retModeTri ()
	{
		$asModeTri = array("Personne.Nom"
			,"Personne.Prenom"
			,"Personne.Pseudo"
			,"NbrConnexion"
			,"MaxMomentEven"
			,"MaxSortiMomentEven"
			,($this->bParFormations ? "Evenement_Detail" : "Evenement").".MomentEven"
			,($this->bParFormations ? "Evenement_Detail" : "Evenement").".SortiMomentEven"
			,"TempsEven"
			,"Formation.OrdreForm");
		
		return ($asModeTri[$this->iModeTri]." ".($this->bTriAscendant ? "ASC" : "DESC"));
	}
	
	function defTriAscendant ($v_bTriAscendant=TRUE)
	{
		$this->bTriAscendant = $v_bTriAscendant;
	}
	
	function requeteParFormations ()
	{
		return "SELECT Evenement_Detail.*"
			.", Evenement_Detail.MomentEven AS MomentEven"
			.", Evenement_Detail.SortiMomentEven AS SortiMomentEven"
			.", SEC_TO_TIME("
			."UNIX_TIMESTAMP(Evenement_Detail.SortiMomentEven) - UNIX_TIMESTAMP(Evenement_Detail.MomentEven)"
			.") AS TempsEven"
			.", Formation.NomForm AS NomForm"
			." FROM Evenement_Detail"
			." LEFT JOIN Evenement USING (IdEven)"
			." LEFT JOIN Formation ON Evenement_Detail.IdForm=Formation.IdForm"
			." LEFT JOIN Personne ON Evenement.IdPers=Personne.IdPers"
			." WHERE Evenement.IdPers='{$this->iIdPers}'"
			." ORDER BY ".$this->retModeTri();
	}
	
	function requeteParPersonne ()
	{
		$this->bParFormations = TRUE;
		
		return "SELECT Evenement.*"
			.", Evenement_Detail.MomentEven"
			.", Evenement_Detail.SortiMomentEven"
			.", SEC_TO_TIME(UNIX_TIMESTAMP(Evenement_Detail.SortiMomentEven) - UNIX_TIMESTAMP(Evenement_Detail.MomentEven)) AS TempsEven"
			." FROM Evenement_Detail"
			." LEFT JOIN Evenement USING (IdEven)"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Evenement_Detail.IdForm='{$this->iIdForm}'"
			." AND Evenement.IdPers='{$this->iIdPers}'"
			." ORDER BY ".$this->retModeTri();
	}
	
	function requeteParProjet ()
	{
		return "SELECT *, UPPER(Nom) AS Nom"
			.", COUNT(Evenement.IdPers) AS NbrConnexion"
			.", MAX(Evenement.MomentEven) AS MomentEven"
			.", MAX(Evenement.SortiMomentEven) AS SortiMomentEven"
			.", SEC_TO_TIME(UNIX_TIMESTAMP(MAX(SortiMomentEven)) - UNIX_TIMESTAMP(MAX(MomentEven))) AS TempsEven"
			.", SEC_TO_TIME(SUM(UNIX_TIMESTAMP(SortiMomentEven) - UNIX_TIMESTAMP(MomentEven))) AS DureeTotaleConnexions"
			." FROM Evenement"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Evenement.IdPers IS NOT NULL"
			." GROUP BY Evenement.IdPers"
			." ORDER BY ".$this->retModeTri();
	}
	
	function requeteParEvenForm ()
	{
		return "SELECT *, UPPER(Nom) AS Nom"
			.", COUNT(Evenement.IdPers) AS NbrConnexion"
			.", MAX(Evenement_Detail.MomentEven) AS MaxMomentEven"
			.", MAX(Evenement_Detail.SortiMomentEven) AS MaxSortiMomentEven"
			.", SEC_TO_TIME(UNIX_TIMESTAMP(MAX(Evenement_Detail.SortiMomentEven)) - UNIX_TIMESTAMP(MAX(Evenement_Detail.MomentEven))) AS TempsEven"
			.", SEC_TO_TIME(SUM(UNIX_TIMESTAMP(Evenement_Detail.SortiMomentEven) - UNIX_TIMESTAMP(Evenement_Detail.MomentEven))) AS DureeTotaleConnexions"
			." FROM Evenement_Detail"
			." LEFT JOIN Evenement USING (IdEven)"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Evenement_Detail.IdForm='{$this->iIdForm}'"
			." GROUP BY Personne.IdPers"
			." ORDER BY ".$this->retModeTri();
	}
	
	function initEvenements ()
	{
		$iIdxEven = 0;
		
		$this->aoEvenements = array();
		
		if ($this->bParFormations)
			$sRequeteSql = $this->requeteParFormations();
		else if ($this->iIdPers > 0)
			$sRequeteSql = $this->requeteParPersonne();
		else
			$sRequeteSql =  $this->requeteParEvenForm();
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoEvenements[$iIdxEven] = new CEvenement($this->oBdd);
			$this->aoEvenements[$iIdxEven]->init($oEnreg);
			$iIdxEven++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxEven;
	}
	
	function retNbrConnexion ()
	{
		return $this->oEnregBdd->NbrConnexion;
	}
	
	function retConnexion ($v_bFormatDateCours=TRUE,$v_bFormatHeureCours=FALSE)
	{
		return retDateLocale($this->oEnregBdd->MomentEven,$v_bFormatDateCours,$v_bFormatHeureCours);
	}
	
	function retDeconnexion ($v_bFormatDateCours=TRUE,$v_bFormatHeureCours=FALSE)
	{
		$sDateDeconnexion = $this->oEnregBdd->SortiMomentEven;
		
		if ($sDateDeconnexion < $this->oEnregBdd->MomentEven)
			$sDateDeconnexion = NULL;
			
		return retDateLocale($sDateDeconnexion,$v_bFormatDateCours,$v_bFormatHeureCours);
	}
	
	function retTempsConnexion ($bFormatCours=FALSE)
	{
		$sDureeConnexion = "&#8211;";
		
		if (!empty($this->oEnregBdd->TempsEven))
		{
			$sDureeConnexion = $this->oEnregBdd->TempsEven;
			
			if ($bFormatCours)
				$sDureeConnexion = ereg_replace("([0-9]{2}):([0-9]{2}):([0-9]{2})","\\1:\\2",$sDureeConnexion);
			
			if (ereg("^-",$sDureeConnexion)) // "-00:00:15" => "-"
				$sDureeConnexion = "&#8211;";
			else if (ereg("^00:00\$",$sDureeConnexion)) // "00:00:20" => "< 1min"
				$sDureeConnexion = "&#8249;&nbsp;1min";
		}
		
		return $sDureeConnexion;
	}
	
	function retNavigateur ()
	{
		return $this->oEnregBdd->DonneesEven;
	}
	
	function retDateDerniereConnexion ($v_bFormatDateCours=TRUE,$v_bFormatHeureCours=TRUE)
	{
		return retDateLocale($this->oEnregBdd->MaxMomentEven,$v_bFormatDateCours,$v_bFormatHeureCours);
	}
	
	function retDateDerniereDeconnexion ($v_bFormatDateCours=TRUE,$v_bFormatHeureCours=TRUE)
	{
		$sDateDeconnexion = $this->oEnregBdd->MaxSortiMomentEven;
		
		if ($sDateDeconnexion < $this->oEnregBdd->MaxMomentEven)
			$sDateDeconnexion = NULL;
		
		return retDateLocale($sDateDeconnexion,$v_bFormatDateCours,$v_bFormatHeureCours);
	}
	
	function retDureeTotaleConnexions ($v_iIdForm=NULL)
	{
		if (!isset($v_iIdForm))
			$v_iIdForm = $this->iIdForm;
		
		$sDureeTotaleConnexions = "&#8211;";
		
		if ($this->iIdPers > 0)
		{
			if ($this->bParFormations)
				$sRequeteSql = "SELECT SEC_TO_TIME(SUM(UNIX_TIMESTAMP(Evenement_Detail.SortiMomentEven) - UNIX_TIMESTAMP(Evenement_Detail.MomentEven)))"
					." AS DureeTotaleConnexions"
					." FROM Evenement_Detail"
					." LEFT JOIN Evenement USING (IdEven)"
					." WHERE Evenement.IdPers='{$this->iIdPers}'"
					." AND Evenement_Detail.IdForm='{$v_iIdForm}'";
			else
				$sRequeteSql = "SELECT SEC_TO_TIME(SUM(UNIX_TIMESTAMP(SortiMomentEven) - UNIX_TIMESTAMP(MomentEven)))"
					." AS DureeTotaleConnexions"
					." FROM Evenement"
					." WHERE IdPers='{$this->iIdPers}'";
			
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			
			$this->oBdd->libererResult($hResult);
		}
		
		return (empty($this->oEnregBdd->DureeTotaleConnexions) ? "&#8211;" : $this->oEnregBdd->DureeTotaleConnexions);
	}
	
	/*function retIdFormation () { return $this->oEnregBdd->IdForm; }*/
	/*function retNomFormation () { return $this->oEnregBdd->NomForm; }*/
	
	function initFichierExporter ($v_sNomFichierCSV=NULL,$v_iIdForm=NULL)
	{
		if (empty($v_sNomFichierCSV))
		{
			$aDate = getDate();
			
			$v_sNomFichierCSV = "even-"
				.$aDate["mday"].$aDate["mon"].$aDate["year"]
				."_"
				.$aDate["hours"].$aDate["minutes"].$aDate["seconds"]
				.".csv";
		}
		
		$v_sNomFichierCSV = dir_tmp($v_sNomFichierCSV,TRUE);
		
		$sRequeteSql = "SELECT Personne.Nom"
			.", Personne.Prenom"
			.", Personne.Pseudo"
			.", Personne.Sexe"
			.", DATE_FORMAT(Evenement.MomentEven,\"%d/%m/%y\") AS DateConnexion"
			.", DATE_FORMAT(Evenement.MomentEven,\"%H:%i:%s\") AS HeureConnexion"
			.", DATE_FORMAT(Evenement.SortiMomentEven,\"%H:%i:%s\") AS HeureDeconnexion"
			.", SEC_TO_TIME(UNIX_TIMESTAMP(Evenement.SortiMomentEven) - UNIX_TIMESTAMP(Evenement.MomentEven)) AS DureeConnexion"
			.", Evenement.DonneesEven";
		
		if (isset($v_iIdForm))
			$sRequeteSql .= " FROM Evenement_Detail"
				." LEFT JOIN Evenement USING (IdEven)"
				." LEFT JOIN Personne USING (IdPers)"
				." WHERE Evenement_Detail.IdForm='{$v_iIdForm}'"
					." AND Evenement.IdPers IS NOT NULL";
		else
			$sRequeteSql .= " FROM Evenement"
				." LEFT JOIN Personne USING (IdPers)"
				." WHERE Evenement.IdPers IS NOT NULL";
		
		$sRequeteSql .= " ORDER BY Personne.Nom, Evenement.MomentEven DESC";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$fp = fopen($v_sNomFichierCSV,"w");
		
		$sDonnees = "\"Nom\""
			.";\"Prenom\""
			.";\"Pseudo\""
			.";\"Sexe\""
			.";\"Connexion (date)\""
			.";\"Connexion (heure)\""
			.";\"Déconnexion (heure)\""
			.";\"Durée\""
			.";\"Navigateur\""
			."\r\n";
		
		fputs($fp,$sDonnees);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$sDonnees = "\"{$oEnreg->Nom}\""
				.";\"{$oEnreg->Prenom}\""
				.";\"{$oEnreg->Pseudo}\""
				.";\"{$oEnreg->Sexe}\""
				.";\"{$oEnreg->DateConnexion}\""
				.";\"{$oEnreg->HeureConnexion}\""
				.";\"{$oEnreg->HeureDeconnexion}\""
				.";\"{$oEnreg->DureeConnexion}\""
				.";\"{$oEnreg->DonneesEven}\""
				."\r\n";
			fputs($fp,$sDonnees);
		}
		
		fclose($fp);
		
		$this->oBdd->libererResult($hResult);
		
		return $v_sNomFichierCSV;
	}
}

function retDateLocale ($v_sDate,$v_bFormatDateCours=FALSE,$v_bFormatHeureCours=FALSE)
{
	if (empty($v_sDate))
		$sDateLocale = "&#8211;";
	else
	{
		ereg("([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})",$v_sDate,$a);
		
		$sDateLocale = "{$a[3]}/{$a[2]}/"
			.($v_bFormatDateCours ? substr("{$a[1]}",-2) : "{$a[1]}")
			." {$a[4]}:{$a[5]}"
			.($v_bFormatHeureCours ? NULL :":{$a[6]}");
		
		unset($a);
	}
	
	return $sDateLocale;
}

class CEvenement_Detail
{
	var $oBdd;
	var $iId;
	
	var $iIdForm;
	
	var $sErreur;
	
	function CEvenement_Detail ($v_oBdd,$v_iId,$v_iIdForm=NULL)
	{
		$this->oBdd = &$v_oBdd;
		
		$this->sErreur = NULL;
		$this->iId = $v_iId;
		$this->iIdForm = $v_iIdForm;
	}
	
	function optimiserTable()
	{
		$this->oBdd->executerRequete("OPTIMIZE TABLE Evenement_Detail");
	}
	
	function effacerParFormation ($v_bOptimiserTable=TRUE)
	{
		$sRequeteSql = "DELETE FROM Evenement_Detail"
			." WHERE IdForm='{$this->iIdForm}'";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		if ($v_bOptimiserTable)
			$this->optimiserTable();
	}
	
	function retIdsEvensParFormation ()
	{
		$aiIdsEvens = array();
		
		$sRequeteSql = "SELECT IdEven FROM Evenement_Detail"
			." WHERE IdForm='{$this->iIdForm}'"
			." AND MomentEven IS NOT NULL"
			." AND SortiMomentEven IS NOT NULL"
			." GROUP BY IdEven";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$aiIdsEvens[] = $oEnreg->IdEven;
		
		$this->oBdd->libererResult($hResult);
		
		return $aiIdsEvens;
	}
	
	function dejaEntrerFormation ()
	{
		$sRequeteSql = "SELECT Evenement_Detail.IdEven FROM Evenement_Detail"
			." WHERE Evenement_Detail.IdEven='{$this->iId}'"
			." AND Evenement_Detail.IdForm='{$this->iIdForm}'"
			." AND Evenement_Detail.SortiMomentEven IS NULL";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$bDejaEntrerFormation = ($this->oBdd->retEnregSuiv($hResult) != NULL);
		$this->oBdd->libererResult($hResult);
		
		return $bDejaEntrerFormation;
	}
	
	function entrerFormation ($v_iIdForm=NULL)
	{
		$this->sErreur = NULL;
		
		if ($v_iIdForm > 0)
			$this->iIdForm = $v_iIdForm;
		
		if ($this->iId < 1 || $this->iIdForm < 1)
		{
			$this->sErreur = "CEvenement_Detail.entrerFormation: IdEven < 1 OR IdForm < 1";
			return FALSE;
		}
		
		if ($this->dejaEntrerFormation())
			return TRUE;
		
		$sRequeteSql = "INSERT INTO Evenement_Detail"
			." (IdEven,MomentEven,SortiMomentEven,IdForm)"
			." VALUES"
			." ('{$this->iId}',NOW(),NULL,'{$this->iIdForm}')";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	function sortirFormation ($v_iIdForm=NULL)
	{
		$this->sErreur = NULL;
		
		if ($v_iIdForm > 0)
			$this->iIdForm = $v_iIdForm;
		
		if ($this->iId < 1 || $this->iIdForm < 1)
		{
			$this->sErreur = "CEvenement_Detail.sortirFormation: IdEven < 1 OR IdForm < 1";
			return FALSE;
		}
		
		$sRequeteSql = "UPDATE Evenement_Detail"
			." SET SortiMomentEven=NOW()"
			." WHERE IdEven='{$this->iId}'"
			." AND IdForm='{$this->iIdForm}'"
			." AND SortiMomentEven IS NULL";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	function retErreur ()
	{
		return $this->sErreur;
	}
}

?>
