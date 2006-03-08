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
** Fichier ................: typestatutpers.tbl.php
** Description ............: 
** Date de création .......: 22/02/2005
** Dernière modification ..: 22/02/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

class CTypeStatutPers
{
	var $oBdd;
	var $oEnregBdd;
	
	var $iId;
	
	var $aoTypesStatutsPers;
	
	function CTypeStatutPers (&$v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iId;
		
		if ($this->iId > 0)
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (is_object($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdStatut;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM TypeStatutPers"
				." WHERE IdStatut='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	// {{{ Méthodes de retour
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0); }
	function retNomStatut () { return $this->oEnregBdd->TxtStatut; }
	function retNomStatutMasculin () { return $this->oEnregBdd->NomMasculinStatut; }
	function retNomStatutFeminin () { return $this->oEnregBdd->NomFemininStatut; }
	// }}}
	
	function initTypesStatutsPers ()
	{
		$iIdxTypeStatutPers = 0;
		$this->aoTypesStatutsPers = array();
		
		$sRequeteSql = "SELECT * FROM TypeStatutPers"
			." ORDER BY IdStatut ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoTypesStatutsPers[$iIdxTypeStatutPers] = new CTypeStatutPers($this->oBdd);
			$this->aoTypesStatutsPers[$iIdxTypeStatutPers]->init($oEnreg);
			$iIdxTypeStatutPers++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxTypeStatutPers;
	}
}

?>
