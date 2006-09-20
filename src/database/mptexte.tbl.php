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
 * @file	mptexte.tbl.php
 * 
 * Contient la classe de gestion de texte pour la mise en page des activités en ligne, en rapport avec la DB
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 */

class CMPTexte 
{
	var $oBdd;
	var $iId;
	var $oEnregBdd;
	
	function CMPTexte(&$v_oBdd,$v_iId=0) 
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
			$sRequeteSql = "SELECT * FROM MPTexte WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjForm;
	}
	
	function ajouter($v_iIdObjForm) //Cette fonction ajoute une mise en page de type texte, avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO MPTexte SET IdObjForm='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	//Fonctions de définition
	function defIdObjForm ($v_iIdObjForm) { $this->oEnregBdd->IdObjForm = $v_iIdObjForm; }
	function defTexteMPT ($v_sTexteMPT) { $this->oEnregBdd->TexteMPT = $v_sTexteMPT; }
	function defAlignMPT ($v_sAlignMPT) { $this->oEnregBdd->AlignMPT = $v_sAlignMPT; }
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjForm; }
	function retTexteMPT () { return $this->oEnregBdd->TexteMPT; }
	function retAlignMPT () { return $this->oEnregBdd->AlignMPT; }
	
	function cHtmlMPTexte()
	{
		//Mise en page du texte
		$this->oEnregBdd->TexteMPT = convertBaliseMetaVersHtml($this->oEnregBdd->TexteMPT);
		
		//Genération du code html représentant l'objet
		$sCodeHtml="<div align=\"{$this->oEnregBdd->AlignMPT}\">{$this->oEnregBdd->TexteMPT}</div>";
		return $sCodeHtml;	
	}
	
	function enregistrer()
	{
		if ($this->oEnregBdd->IdObjForm != NULL)
		{	
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			//de les stocker dans la BD sans erreur 
			$sTexteMPT = validerTexte($this->oEnregBdd->TexteMPT);
			
			$sRequeteSql = "REPLACE MPTexte SET"									  
						." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
						.", TexteMPT='{$sTexteMPT}'"
						.", AlignMPT='{$this->oEnregBdd->AlignMPT}'";
			
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
		
		// Les variables contenant du "texte" doivent être formatées, cela permet 
		//de les stocker dans la BD sans erreur 
		$sTexteMPT = validerTexte($this->oEnregBdd->TexteMPT);
		
		$sRequeteSql = "INSERT INTO MPTexte SET"									  
					." IdObjForm='{$v_iIdNvObjForm}'"
					.", TexteMPT='{$sTexteMPT}'"
					.", AlignMPT='{$this->oEnregBdd->AlignMPT}'"; 
		
		$this->oBdd->executerRequete($sRequeteSql);
		$iIdObjForm = $this->oBdd->retDernierId();
		
		return $iIdObjForm;
	}
	
	function effacer()
	{
		$sRequeteSql = "DELETE FROM MPTexte WHERE IdObjForm ='{$this->iId}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
}
?>
