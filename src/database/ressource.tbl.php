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
** Fichier ................: ressource.tbl.php
** Description ............: 
** Date de création .......: 01/06/2001
** Dernière modification ..: 16/09/2005
** Auteurs ................: Cédric FLOQUET <cedric.floquet@umh.ac.be>
**                           Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_database("ressource.def.php"));

class CRessource
{
	var $oBdd;
	var $iId;
	var $oEnregBdd;
	var $oExpediteur;
	var $bEstSelectionne;
	
	function CRessource (&$v_oBdd,$v_iIdRes=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdRes;
		
		if (isset($this->iId))
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdRes;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Ressource"
				." WHERE IdRes='".$this->retId()."'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		return $this->iId;
	}
	
	function ajouter ()
	{
		$sRequeteSql = "INSERT INTO Ressource SET"
			." IdRes=NULL"
			.", NomRes='".MySQLEscapeString($this->retNom())."'"
			.", DescrRes='".MySQLEscapeString($this->retDescr())."'"
			.", DateRes=NOW()"
			.", AuteurRes='".MySQLEscapeString($this->retAuteur())."'"
			.", UrlRes='".MySQLEscapeString($this->retUrl())."'"
			.", IdPers='".$this->retIdExped()."'"
			.", IdDeposeur='".$this->retIdExped()."'"
			.", IdFormat='".$this->retIdFormat()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$this->iId = $this->oBdd->retDernierId($hResult);
		return $this->iId;
	}
	
	function enregistrer ()
	{
		$sRequeteSql = "UPDATE Ressource SET"
			." NomRes='".MySQLEscapeString($this->retNom())."'"
			.", DescrRes='".MySQLEscapeString($this->retDescr())."'"
			.", DateRes=NOW()"
			.", UrlRes='".MySQLEscapeString($this->retUrl())."'"
			." WHERE IdRes='".$this->retId()."'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacer ($v_sNomRepRessources)
	{
		$sRequeteSql = "DELETE FROM Ressource"
			." WHERE IdRes='".$this->retId()."'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		// Effacer le fichier
		@unlink($v_sNomRepRessources.$this->retUrl());
	}
	
	function initExpediteur () { $this->oExpediteur = new CPersonne($this->oBdd,$this->retIdExped()); }
	
	function retId () { return (is_numeric($this->iId) ? $this->iId : 0);  }
	function retNom () { return $this->oEnregBdd->NomRes; }
	function retDescr () { return $this->oEnregBdd->DescrRes; }
	function retDate () { return $this->oEnregBdd->DateRes; }
	function retAuteur () { return $this->oEnregBdd->AuteurRes; }
	
	function retUrl ($v_bVraiNomFichier=FALSE,$bAjouterAntiSlashes=FALSE)
	{
		$r_sUrl = $this->oEnregBdd->UrlRes;
		if ($v_bVraiNomFichier)
			$r_sUrl = ereg_replace("-([0-9]){4}\.",".",$r_sUrl);
		return ($bAjouterAntiSlashes ? addslashes($r_sUrl) : $r_sUrl);
	}
	
	function retIdExped () { return $this->oEnregBdd->IdPers; }
	function retIdFormat () { return $this->oEnregBdd->IdFormat; }
	function retLabel () { return $this->oEnregBdd->LabelResUnite; }
	function retModeLien () { return $this->oEnregBdd->ModeLienResUnite; }
	
	function defIdExped ($v_iIdPers) { $this->oEnregBdd->IdPers = $v_iIdPers; }
	function defNom ($v_sNom) { $this->oEnregBdd->NomRes = $v_sNom; }
	function defUrl ($v_sUrl=NULL) { $this->oEnregBdd->UrlRes = $v_sUrl; }
}

?>
