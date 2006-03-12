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
** Fichier ................: formulairecomplete_eval.tbl.php
** Description ............: 
** Date de création .......: 05/11/2004
** Dernière modification ..: 09/11/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_database("ressource.def.php"));

class CFormulaireComplete_Evaluation
{
	var $iId;
	var $iIdPers;
	
	var $oBdd;
	var $oEnregBdd;
	
	var $oEvaluateur;
	
	/**
	 *
	 * @param $v_iId integer Cette variable doit recevoir l'identifiant unique
	 *        du formulaire complété de la sous-activité
	 * 
	 */
	function CFormulaireComplete_Evaluation (&$v_oBdd,$v_iId=NULL,$v_iIdPers=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iId;
		$this->iIdPers = $v_iIdPers;
		
		if (isset($this->iId))
			$this->init();
	}
	
	function init ($v_oEnregExistant=NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
			$this->iId = $this->oEnregBdd->IdFCSousActiv;
			$this->iIdPers = $this->oEnregBdd->IdPers;
		}
		else
		{
			$sRequeteSql = "SELECT FormulaireComplete_SousActiv.*"
				.", FormulaireComplete_Evaluation.IdPers"
				.", FormulaireComplete_Evaluation.DateEval"
				.", FormulaireComplete_Evaluation.AppreciationEval"
				.", FormulaireComplete_Evaluation.CommentaireEval"
				." FROM FormulaireComplete_SousActiv"
				." LEFT JOIN FormulaireComplete_Evaluation"
					." ON FormulaireComplete_SousActiv.IdFCSousActiv=FormulaireComplete_Evaluation.IdFCSousActiv"
					." AND FormulaireComplete_Evaluation.IdPers='{$this->iIdPers}'"
				." WHERE FormulaireComplete_SousActiv.IdFCSousActiv='{$this->iId}'"
				." LIMIT 1";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function ajouter ($v_iStatutEval,$v_sAppreciationEval,$v_sCommentaireEval)
	{
		if ($this->iId < 1 || $this->iIdPers < 1)
			return 0;
		
		$sRequeteSql = "UPDATE FormulaireComplete_SousActiv SET"
			." StatutFormSousActiv='{$v_iStatutEval}'"
			." WHERE IdFCSousActiv='{$this->iId}'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSql = "REPLACE INTO FormulaireComplete_Evaluation SET"
			." IdFCSousActiv='{$this->iId}'"
			.", IdPers='{$this->iIdPers}'"
			.", DateEval=NOW()"
			.", AppreciationEval='".MySQLEscapeString($v_sAppreciationEval)."'"
			.", CommentaireEval='".MySQLEscapeString($v_sCommentaireEval)."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		return $this->iId;
	}
	
	function initEvaluateur () { $this->oEvaluateur = new CPersonne($this->oBdd,$this->iIdPers); }
	
	// {{{ Méthodes de retour
	function retId () { return (empty($this->iId) ? 0 : $this->iId); }
	function retIdFC () { return (empty($this->oEnregBdd) ? 0 : $this->oEnregBdd->IdFC); }
	function retStatut () { return (empty($this->oEnregBdd) ? STATUT_RES_APPROF : $this->oEnregBdd->StatutFormSousActiv); }
	function retIdEvaluateur () { return $this->iIdPers; }
	function retDate () { return (empty($this->oEnregBdd->DateEval) ? date("d/m/y") : retDateFormatter($this->oEnregBdd->DateEval)); }
	function retAppreciation () { return (isset($this->oEnregBdd->AppreciationEval) ? $this->oEnregBdd->AppreciationEval : NULL); }
	function retCommentaire () { return (isset($this->oEnregBdd->CommentaireEval) ? $this->oEnregBdd->CommentaireEval : NULL); }
	// }}}
}

?>
