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

/**
 * @file	mpseparateur.tbl.php
 * 
 * Contient la classe de gestion de ligne de séparation pour la mise en page des activités en ligne, en rapport avec la DB
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 */

class CMPSeparateur
{
	var $oBdd;
	var $iId;
	var $oEnregBdd;
	
	function CMPSeparateur(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	function init($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM MPSeparateur WHERE IdObjFormul='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjFormul;
	}
	
	function ajouter($v_iIdObjForm) //Cette fonction ajoute une ligne de type séparateur, avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO MPSeparateur (IdObjFormul,TypeLargMPS,LargeurMPS) VALUES ('$v_iIdObjForm','P','100');";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	//Fonctions de définition
	function defIdObjFormul ($v_iIdObjForm) { $this->oEnregBdd->IdObjFormul = $v_iIdObjForm; }
	function defLargeurMPS ($v_iLargeurMPS) { $this->oEnregBdd->LargeurMPS = $v_iLargeurMPS; }
	function defTypeLargMPS ($v_sTypeLargMPS) { $this->oEnregBdd->TypeLargMPS = $v_sTypeLargMPS; }
	function defAlignMPS ($v_sAlignMPS) { $this->oEnregBdd->AlignMPS = $v_sAlignMPS; }
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjFormul; }
	function retLargeurMPS () { return $this->oEnregBdd->LargeurMPS; }
	function retTypeLargMPS () { return $this->oEnregBdd->TypeLargMPS; }
	function retAlignMPS () { return $this->oEnregBdd->AlignMPS; }
	function retLargeurCompleteMPS ()
	{
		if($this->retTypeLargMPS() == "P")
			return $this->retLargeurMPS()."%";
		else
			return $this->retLargeurMPS()."px";
	}
	
	function enregistrer()
	{
		if ($this->oEnregBdd->IdObjFormul != NULL)
		{	
			$sRequeteSql = "REPLACE MPSeparateur SET"
						." IdObjFormul='{$this->oEnregBdd->IdObjFormul}'"
						.", LargeurMPS='{$this->oEnregBdd->LargeurMPS}'"
						.", TypeLargMPS='{$this->oEnregBdd->TypeLargMPS}'"
						.", AlignMPS='{$this->oEnregBdd->AlignMPS}'";
			$this->oBdd->executerRequete($sRequeteSql);
		}
		else
		{
			echo "Identifiant NULL enregistrement impossible";
		}
	}
	
	function copier($v_iIdNvObjForm)
	{
		if ($v_iIdNvObjForm < 1)
			return;
		
		$sRequeteSql = "INSERT INTO MPSeparateur SET"
					." IdObjFormul='{$v_iIdNvObjForm}'"
					.", LargeurMPS='{$this->oEnregBdd->LargeurMPS}'"
					.", TypeLargMPS='{$this->oEnregBdd->TypeLargMPS}'"
					.", AlignMPS='{$this->oEnregBdd->AlignMPS}'";
		$this->oBdd->executerRequete($sRequeteSql);
		$iIdObjForm = $this->oBdd->retDernierId();
		return $iIdObjForm;
	}
	
	function effacer()
	{
		$sRequeteSql = "DELETE FROM MPSeparateur WHERE IdObjFormul ='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
}
?>
