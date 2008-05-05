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
** Fichier ................: glossaire.tbl.php
** Description ............:
** Date de création .......: 24/07/2004
** Dernière modification ..: 31/07/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
**
*/

class CGlossaire
{
	var $iId;
	
	var $oBdd;
	var $oEnregBdd;
	
	var $oAuteur;
	var $aoElements;
	
	function CGlossaire (&$v_oBdd,$v_iIdGlossaire=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdGlossaire;
		
		if (isset($this->iId))
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (is_object($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdGlossaire;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Glossaire"
				." WHERE IdGlossaire='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function initElements ()
	{
		$iIdxElems = 0;
		$this->aoElements = array();
		
		$sRequeteSql = "SELECT GlossaireElement.*"
			." FROM Glossaire"
			." LEFT JOIN Glossaire_GlossaireElement USING (IdGlossaire)"
			." LEFT JOIN GlossaireElement USING (IdGlossaireElement)"
			." WHERE Glossaire.IdGlossaire='".$this->retId()."'"
			." ORDER BY GlossaireElement.TitreGlossaireElement ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoElements[$iIdxElems] = new CGlossaireElement($this->oBdd);
			$this->aoElements[$iIdxElems]->init($oEnreg);
			$iIdxElems++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxElems;
	}
	
	function ajouter ($v_sTitreGlossaire,$v_iIdForm,$v_iIdPers)
	{
		$v_sTitreGlossaire = MySQLEscapeString($v_sTitreGlossaire);
		
		$sRequeteSql = "INSERT INTO Glossaire"
			." (IdGlossaire,TitreGlossaire,IdForm,IdPers)"
			." VALUES"
			." (NULL,'{$v_sTitreGlossaire}','{$v_iIdForm}','{$v_iIdPers}')";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function associerSousActiv ($v_iIdSousActiv)
	{
		$bAssocierSousActiv = FALSE;
		
		$sRequeteSql = "SELECT IdSousActiv"
			." FROM SousActiv_Glossaire"
			." WHERE IdGlossaire='".$this->retId()."'"
			." AND IdSousActiv='{$v_iIdSousActiv}'"
			." LIMIT 1";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		if ($this->oBdd->retEnregSuiv($hResult))
			$bAssocierSousActiv = TRUE;
		
		$this->oBdd->libererResult($hResult);
		
		return $bAssocierSousActiv;
	}
	
	function initAuteur ()
	{
		$this->oAuteur = new CPersonne($this->oBdd,$this->oEnregBdd->IdPers);
	}
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retTitre () { return $this->oEnregBdd->TitreGlossaire; }
	function retTexte () { return $this->oEnregBdd->TexteGlossaire; }
}

class CGlossaireElement
{
	var $iId;
	
	var $oBdd;
	var $oEnregBdd;
	
	var $oAuteur;
	
	function CGlossaireElement (&$v_oBdd,$v_iIdGlossaireElement=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdGlossaireElement;
		
		if (isset($this->iId))
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (is_object($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdGlossaireElement;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM GlossaireElement"
				." WHERE IdGlossaireElement='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function initAuteur ()
	{
		$this->oAuteur = new CPersonne($this->oBdd,$this->oEnregBdd->IdPers);
	}
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retTitre () { return $this->oEnregBdd->TitreGlossaireElement; }
	function retTexte () { return $this->oEnregBdd->TexteGlossaireElement; }
	function estSelectionne () { return (is_numeric($this->oEnregBdd->estSelectionne) && $this->oEnregBdd->estSelectionne > 0); }
}

?>
