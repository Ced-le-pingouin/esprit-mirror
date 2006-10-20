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
			$sRequeteSql = "SELECT * FROM MPSeparateur WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjForm;
	}
	
	function ajouter($v_iIdObjForm) //Cette fonction ajoute une ligne de type séparateur, avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO MPSeparateur (IdObjForm,TypeLargMPS,LargeurMPS) VALUES ('$v_iIdObjForm','P','100');";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	//Fonctions de définition
	function defIdObjForm ($v_iIdObjForm) { $this->oEnregBdd->IdObjForm = $v_iIdObjForm; }
	function defLargeurMPS ($v_iLargeurMPS) { $this->oEnregBdd->LargeurMPS = $v_iLargeurMPS; }
	function defTypeLargMPS ($v_sTypeLargMPS) { $this->oEnregBdd->TypeLargMPS = $v_sTypeLargMPS; }
	function defAlignMPS ($v_sAlignMPS) { $this->oEnregBdd->AlignMPS = $v_sAlignMPS; }
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjForm; }
	function retLargeurMPS () { return $this->oEnregBdd->LargeurMPS; }
	function retTypeLargMPS () { return $this->oEnregBdd->TypeLargMPS; }
	function retAlignMPS () { return $this->oEnregBdd->AlignMPS; }
	
	function cHtmlMPSeparateur()
	{
		if ($this->oEnregBdd->TypeLargMPS=="P")					//ajoute % ou px a la largeur pour ainsi créer une chaine de car
			$sLargeur=$this->oEnregBdd->LargeurMPS."%";
		else												//se test est peut etre à deplacer car il a l'air a l'origine d'un certain ralentissement
			$sLargeur=$this->oEnregBdd->LargeurMPS."px";
		//Genération du code html représentant l'objet
		$sCodeHtml = "<hr width=\"$sLargeur\" size=\"2\" align=\"".$this->retAlignMPS()."\" />";
		return $sCodeHtml;
	}
	
	function enregistrer()
	{
		if ($this->oEnregBdd->IdObjForm != NULL)
		{	
			$sRequeteSql = "REPLACE MPSeparateur SET"
						." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
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
					." IdObjForm='{$v_iIdNvObjForm}'"
					.", LargeurMPS='{$this->oEnregBdd->LargeurMPS}'"
					.", TypeLargMPS='{$this->oEnregBdd->TypeLargMPS}'"
					.", AlignMPS='{$this->oEnregBdd->AlignMPS}'";
		$this->oBdd->executerRequete($sRequeteSql);
		$iIdObjForm = $this->oBdd->retDernierId();
		return $iIdObjForm;
	}
	
	function effacer()
	{
		$sRequeteSql = "DELETE FROM MPSeparateur WHERE IdObjForm ='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
}
?>
