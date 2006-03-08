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
** Fichier ................: formation_inscrit.tbl.php
** Description ............: 
** Date de création .......: 17-09-2002
** Dernière modification ..: 13-02-2003
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2003 UTE. All rights reserved.
**
*/

class CFormation_Inscrit 
{
	var $oBdd;
	var $oEnregBdd;
	var $iIdForm;
	var $iIdPers;
	var $asErreurs;
	
	function CFormation_Inscrit (&$v_oBdd,$v_iIdForm=0,$v_iIdPers=0)
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdForm = $v_iIdForm;
		$this->iIdPers = $v_iIdPers;
	}

	function ajouterInscrits ($v_aiIdPers)
	{
		if ($this->iIdForm <= 0)
			return;
		
		settype($v_aiIdPers,"array");
		
		$sValeursRequete = NULL;
		
		for ($i=0; $i<count($v_aiIdPers); $i++)
			$sValeursRequete .= (isset($sValeursRequete) ? ",": NULL)
				." ({$this->iIdForm},{$v_aiIdPers[$i]})";
		
		if (isset($sValeursRequete))
		{
			echo $sRequeteSql = "REPLACE INTO Formation_Inscrit"
				." (IdForm,IdPers)"
				." VALUES"
				.$sValeursRequete;
			
			$this->oBdd->executerRequete($sRequeteSql);
		}
	}
	
	function ajouter ($v_iIdPers)
	{
		if ($this->iIdForm < 1 || $v_iIdPers < 1)
			return;

		$sRequeteSql = "REPLACE INTO Formation_Inscrit SET"
			." IdForm={$this->iIdForm}"
			." ,IdPers={$v_iIdPers}";

		$this->oBdd->executerRequete ($sRequeteSql);
	}

	function effacer ()
	{
		if ($this->iIdForm < 1 || $this->iIdPers < 1)
			return;

		$oEquipeMembre = new CEquipe_Membre($this->oBdd);
		$oEquipeMembre->effacerMembre($this->iIdPers,TYPE_FORMATION,$this->iIdForm);
		
		$oModulesInscrit = new CModule_inscrit($this->oBdd,0,$this->iIdPers);
		$oModulesInscrit->effacerModules($this->iIdForm);

		$sRequeteSql = "DELETE FROM Formation_Inscrit"
			." WHERE IdForm={$this->iIdForm}"
			." AND IdPers={$this->iIdPers}";

		$this->oBdd->executerRequete ($sRequeteSql);
	}
	
	function verifEtudiant ()
	{
		$bEstEtudiant = FALSE;
		
		if ($this->iIdForm > 0 && $this->iIdPers > 0)
		{
			$sRequeteSql = "SELECT IdPers FROM Formation_Inscrit"
				." WHERE IdForm='{$this->iIdForm}' AND IdPers='{$this->iIdPers}'";

			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($this->oBdd->retEnregSuiv($hResult))
				$bEstEtudiant = TRUE;
			
			$this->oBdd->libererResult($hResult);
		}
		
		return $bEstEtudiant;
	}
}

?>
