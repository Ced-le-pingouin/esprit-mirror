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

class CEvaluation
{
	var $oBdd;
	var $oEnregBdd;
	
	var $iIdResSousActiv;
	var $iIdPers;
	
	var $oEvaluateur;
	var $aoEvaluations;
	
	function CEvaluation (&$v_oBdd,$v_iIdResSousActiv=NULL,$v_iIdPers=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdResSousActiv = $v_iIdResSousActiv;
		$this->iIdPers = $v_iIdPers;
		
		if (isset($this->iIdResSousActiv) && isset($this->iIdPers))
			$this->init();
		else if (isset($this->iIdResSousActiv))
			$this->initGraceIdResSousActiv();
		else if (isset($this->iIdPers))
			$this->initGraceIdPers();
	}
	
	function init ($v_oEnregExistant = NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Ressource_SousActiv_Evaluation"
				." WHERE IdResSousActiv='{$this->iIdResSousActiv}'"
				." AND IdPers='{$this->iIdPers}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		$this->iIdResSousActiv = $this->oEnregBdd->IdResSousActiv;
		$this->iIdPers = $this->oEnregBdd->IdPers;
	}
	
	function initGraceIdResSousActiv ($v_iIdResSousActiv)
	{
	}
	
	function initGraceIdPers ($v_iIdPers)
	{
	}
	
	function initEvaluateur () { $this->oEvaluateur = new CPersonne($this->oBdd, $this->retIdEvaluateur()); }
	function retIdRessource () { return $this->iIdResSousActiv; }
	function retIdEvaluateur () { return $this->iIdPers; }
	function retDate () { return $this->oEnregBdd->DateEval; }
	function retAppreciation () { return stripslashes($this->oEnregBdd->AppreciationEval); }
	function retCommentaire () { return stripslashes($this->oEnregBdd->CommentaireEval); }
}

?>
