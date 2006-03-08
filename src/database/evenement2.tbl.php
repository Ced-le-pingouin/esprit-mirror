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

class CEvenement
{
	var $oBdd;
	var $iId;
	
	var $oEnregBdd;
	var $oConnecte;
	
	var $aoEvenements;
	
	// Pseudo constant
	var $TRI_NOM = 0;
	var $TRI_PRENOM = 1;
	var $TRI_PSEUDO = 2;
	var $TRI_NBR_CONNEXIONS = 3;
	var $TRI_CONNEXION = $TRI_DERNIERE_CONNEXION = 4;
	var $TRI_DECONNEXION = $TRI_DERNIERE_DECONNEXION = 5;
	var $TRI_DUREE_CONNEXIONS = 6;
	
	var $TRI_ASCENDANT = TRUE;
	var $TRI_DESCENDANT = FALSE;
	
	function CEvenement ($v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iId;
	}
	
	function init($v_oEnregBddExistant=NULL)
	{
		if (isset($v_oEnregBddExistant))
		{
			$this->oEnregBdd = $v_oEnregBddExistant;
			$this->oConnecte = new CPersonne($this->oBdd,$this->oEnregBdd->IdPers);
		}
	}
	
	function defTriAscendant ($v_bTriAscendant=TRUE)
	{
		$this->bTriAscendant = $v_bTriAscendant;
	}
	
	function retTexteModeTri ()
	{
		$asModeTri = array("Personne.Nom"
			,"Personne.Prenom"
			,"Personne.Pseudo"
			,"NbrConnexion"
			,"MomentEven"
			,"SortiMomentEven"
			,"DureeEven"
			,"Formation.OrdreForm");
		
		return ($asModeTri[$this->iModeTri]." ".($this->bTriAscendant ? "ASC" : "DESC"));
	}
	
	function rechParFormation ($v_iIdForm)
	{
		return "SELECT Personne.IdPers"
			.", COUNT(Personne.IdPers) AS NbrConnexions"
			.", MAX(Evenement_Detail.MomentEven) AS MomentEven"
			.", MAX(Evenement_Detail.SortiMomentEven) AS SortiMomentEven"
			.", SEC_TO_TIME(SUM(UNIX_TIMESTAMP(Evenement_Detail.SortiMomentEven) - UNIX_TIMESTAMP(Evenement_Detail.MomentEven))) AS DureeEven"
			." FROM Evenement_Detail"
			." LEFT JOIN Evenement USING (IdEven)"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Evenement_Detail.IdForm='{$v_iIdForm}'"
			." AND Evenement_Detail.IdEven IS NOT NULL"
			." GROUP BY Personne.IdPers;"
			." ORDER BY ".$this->retTexteModeTri();
	}
	
	function rechParFormationInscrit ($v_iIdForm)
	{
		return "SELECT Personne.IdPers"
			.", COUNT(Personne.IdPers) AS NbrConnexions"
			.", MAX(Evenement_Detail.MomentEven) AS MomentEven"
			.", MAX(Evenement_Detail.SortiMomentEven) AS SortiMomentEven"
			.", SEC_TO_TIME(SUM(UNIX_TIMESTAMP(Evenement_Detail.SortiMomentEven) - UNIX_TIMESTAMP(Evenement_Detail.MomentEven))) AS DureeEven"
			." FROM Evenement_Detail"
			." LEFT JOIN Evenement USING (IdEven)"
			." LEFT JOIN Formation_Inscrit"
			." ON Evenement.IdPers = Formation_Inscrit.IdPers"
			." AND Evenement_Detail.IdForm = Formation_Inscrit.IdForm"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Evenement_Detail.IdForm='{$v_iIdForm}'"
			." AND Evenement_Detail.IdEven IS NOT NULL"
			." GROUP BY Personne.IdPers;"
			." ORDER BY ".$this->retTexteModeTri();
	}
	
	function initEvenements ($v_iIdForm=NULL,$v_iIdStatutPers=NULL)
	{
		$sRequeteSql = NULL;
		
		if (isset($v_iIdForm) && $v_iIdForm > 0)
			if ($v_iIdStatutPers >= STATUT_PERS_ETUDIANT)
				$sRequeteSql = $this->rechParFormationInscrit($v_iIdForm);
			else 
				$sRequeteSql = $this->rechParFormation($v_iIdForm);
		
		$iIdxEven = 0;
		$this->aoEvenements = array();
		
		if (isset($sRequeteSql))
		{
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->aoEvenements[$iIdxEven] = new CEvenement($this->oBdd);
				$this->aoEvenements[$iIdxEven]->init($oEnreg);
				$iIdxEven++;
			}
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $iIdxEven;
	}
	
	
	function retNbrConnexions ()
	{
		return $this->oEnregBdd->NbrConnexions;
	}
	
	function retDerniereConnexion ()
	{
		return $this->oEnregBdd->MomentEven;
	}
	
	function retDerniereDeconnexion ()
	{
		return $this->oEnregBdd->SortiMomentEven;
	}
	
	function retDureeConnexions ($bFormatCours=FALSE)
	{
		$sDuree = $this->oEnregBdd->DureeEven;
		
		if (empty($sDuree) || ereg("^-",$sDuree)) // "-00:00:15" => "-"
			$sDuree = "&#8211;";
		else if (ereg("^00:00\$",$sDuree)) // "00:00:20" => "< 1min"
			$sDuree = "&#8249;&nbsp;1min";
		else if ($bFormatCours)
			$sDuree = substring($sDuree,0,5);
		
		return $sDuree;
	}
}

?>
