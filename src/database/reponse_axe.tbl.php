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
 * @file	reponse_axe.tbl.php
 * 
 * Contient la classe de gestion du poids des réponses par rapport aux axes des activités en ligne, en rapport avec la DB
 * 
 * @date	2004/06/22
 * 
 * @author	Ludovic FLAMME
 */

class CReponse_Axe
{
	var $oBdd;
	var $oEnregBdd;
	
	function CReponse_Axe(&$v_oBdd, $v_iIdProRep = 0, $v_iIdAxe = 0) 
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdPropRep = $v_iIdProRep;
		$this->iIdAxe = $v_iIdAxe;
	}
	
	function init ($v_oEnregExistant=NULL)  
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Reponse_Axe"
						." WHERE IdPropRep='{$this->iIdPropRep}'"
						." AND IdAxe='{$this->iIdAxe}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iIdPropRep = $this->oEnregBdd->IdPropRep;
		$this->iIdAxe = $this->oEnregBdd->iIdAxe;
	}
	
	/*
	** Fonction 		: VerifierValidite
	** Description		: supprime le(s) poids des réponses d'un formulaire pour lesquels 1(ou plusieurs) axe a été supprimé
	** Entrée			:
							$v_iIdFormulaire : Id du formulaire à traiter
							$v_sListeAxes : liste des Id des Axes présent dans le formulaire( cette liste se présente comme ceci ex : 1,5,8)
	** Sortie			: 
	*/
	function VerifierValidite($v_iIdFormulaire,$v_sListeAxes)
	{
		$sRequeteSql = "LOCK TABLES ObjetFormulaire READ, PropositionReponse READ, Reponse_Axe WRITE";
		$this->oBdd->executerRequete($sRequeteSql);
		
		//Cette requête donne les enregistrements de la table réponse_axe qui ne sont plus valables 
		//car le/les axes ont été supprimé du formulaire
		$sRequeteSql = "SELECT Reponse_Axe.*"
				   ." FROM ObjetFormulaire, PropositionReponse, Reponse_Axe"
				   ." WHERE ObjetFormulaire.IdForm = '$v_iIdFormulaire' AND PropositionReponse.IdObjFormul = ObjetFormulaire.IdObjForm"
				   ." AND Reponse_Axe.IdPropRep = PropositionReponse.IdPropRep"
				   ." AND Reponse_Axe.IdAxe NOT IN($v_sListeAxes)";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNbEnreg = $this->oBdd->retNbEnregsDsResult();
		
		if ($iNbEnreg > 0)
		{
			$sListeEffacer="";
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$sListeEffacer.="(IdPropRep='$oEnreg->IdPropRep' AND IdAxe='$oEnreg->IdAxe') OR "; 
			}
			$sListeEffacer = subStr($sListeEffacer,0,strlen($sListeEffacer)-4);
			$sRequeteSql = "DELETE FROM Reponse_Axe WHERE $sListeEffacer ";
			$this->oBdd->executerRequete($sRequeteSql);
		}
		$this->oBdd->executerRequete("UNLOCK TABLES");
	}
	
	function enregistrer()
	{
		$sRequeteSql = "REPLACE Reponse_Axe SET" 
					." IdPropRep='{$this->oEnregBdd->IdPropRep}'"
					." , IdAxe='{$this->oEnregBdd->IdAxe}'"
					." , Poids='{$this->oEnregBdd->Poids}'";
		
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function copier($v_iIdNvReponse)
	{
		if ($v_iIdNvReponse < 1)
			return;
		
		$sRequeteSql = "INSERT INTO Reponse_Axe SET"
					." IdPropRep='{$v_iIdNvReponse}'"
					." , IdAxe='{$this->oEnregBdd->IdAxe}'"
					." , Poids='{$this->oEnregBdd->Poids}'";
									
		$this->oBdd->executerRequete($sRequeteSql);
		$iIdNvReponse = $this->oBdd->retDernierId();
		
		return $iIdNvReponse;
	}
	
	//Fonctions de définitions
	function defIdPropRep($v_iIdProRep) { $this->oEnregBdd->IdPropRep = $v_iIdProRep; }
	function defIdAxe($v_iIdAxe) { $this->oEnregBdd->IdAxe = $v_iIdAxe; }
	function defPoids($v_iPoids) { $this->oEnregBdd->Poids = $v_iPoids; }
	
	// Fonctions de retour
	function retIdPropRep() { return $this->oEnregBdd->IdPropRep; }
	function retIdAxe() { return $this->oEnregBdd->IdAxe; }
	function retPoids() { return $this->oEnregBdd->Poids; }
}
?>
