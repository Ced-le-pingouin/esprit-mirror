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
** Fichier ................: intitule.tbl.php
** Description ............: 
** Date de création .......: 12/04/2003
** Dernière modification ..: 16/06/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

class CIntitule
{
	var $oBdd;
	var $oEnregBdd;
	var $iId;
	
	var $aoIntitules;
	
	function CIntitule (&$v_oBdd,$v_iIdIntitule=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdIntitule;
		
		if (isset($this->iId))
			$this->init();
	}
	
	function detruire ()
	{
		$this->iId = NULL;
		$this->oBdd = NULL;
		$this->oEnregBdd = NULL;
		$this->aoIntitules = NULL;
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		$this->oEnregBdd = NULL;
		
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdIntitule;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Intitule"
				." WHERE IdIntitule='{$this->iId}'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function initParNom ($v_sNomIntitule,$v_iTypeIntitule)
	{
		$this->iId = NULL;
		$this->oEnregBdd = NULL;
		
		if ($v_sNomIntitule == NULL || $v_iTypeIntitule == NULL)
			return;
		
		$sRequeteSql = "SELECT * FROM Intitule"
			." WHERE NomIntitule='{$v_sNomIntitule}'"
			." AND TypeIntitule='{$v_iTypeIntitule}'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
		$this->iId = $this->oEnregBdd->IdIntitule;
		$this->oBdd->libererResult($hResult);
	}
	
	function ajouter ()
	{
		if (empty($this->oEnregBdd->NomIntitule))
			return FALSE;
		
		$sRequeteSql = "INSERT INTO Intitule"
			." (IdIntitule,NomIntitule,TypeIntitule)"
			." VALUES"
			." (NULL"
			.",'".mysql_escape_string($this->oEnregBdd->NomIntitule)."'"
			.",'{$this->oEnregBdd->TypeIntitule}')";
		$this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId();
		$this->init();
		return TRUE;
	}
	
	function enregistrer ()
	{
		if (!isset($this->iId) || $this->iId < 1)
			return FALSE;
		
		$sRequeteSql = "UPDATE Intitule SET"
			." NomIntitule='".mysql_escape_string($this->oEnregBdd->NomIntitule)."'"
			." WHERE IdIntitule='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	function supprimer ()
	{
		if (!isset($this->iId) || $this->iId < 1)
			return FALSE;
		
		$sRequeteSql = "DELETE FROM Intitule"
			." WHERE IdIntitule='{$this->iId}'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	
	function retNom ($v_bHtmlEntities=TRUE) { return ($v_bHtmlEntities ? emb_htmlentities($this->oEnregBdd->NomIntitule) : $this->oEnregBdd->NomIntitule); }
	
	function defNom ($v_sNomIntitule)
	{
		$v_sNomIntitule = trim(stripslashes($v_sNomIntitule));
		
		if (!empty($v_sNomIntitule))
			$this->oEnregBdd->NomIntitule = $v_sNomIntitule;
	}
	
	function defType ($v_iType) { $this->oEnregBdd->TypeIntitule = $v_iType; }
	
	function retType () { return $this->oEnregBdd->TypeIntitule; }
		
	function initIntitules ($v_iTypeIntitule=NULL)
	{
		$iIdxIntitules = 0;
		
		$this->aoIntitules = array();
		
		$sRequeteSql = "SELECT * FROM Intitule"
			.(isset($v_iTypeIntitule) ? " WHERE TypeIntitule='{$v_iTypeIntitule}'" : NULL)
			." ORDER BY NomIntitule ASC";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
		while ($oEnregBdd = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoIntitules[$iIdxIntitules] = new CIntitule($this->oBdd);
			$this->aoIntitules[$iIdxIntitules]->init($oEnregBdd);
			$iIdxIntitules++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxIntitules;
	}
}

?>
