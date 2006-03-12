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
** Fichier .................: modules.class.php
** Description .............: 
** Date de création ........: 13/01/2005
** Dernière modification ...: 23/03/2005
** Auteurs .................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

class CModules
{
	var $oBdd;
	var $iIdForm;
	
	var $aoModules;
	
	function CModules (&$v_oBdd,$v_iIdForm,&$v_aoModules)
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdForm = $v_iIdForm;
		$this->aoModules = &$v_aoModules;
	}
	
	function initModules ($v_sRequeteSql)
	{
		$iIdxModule = 0;
		$this->aoModules = array();
		
		$hResult = $this->oBdd->executerRequete($v_sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoModules[$iIdxModule] = new CModule($this->oBdd);
			$this->aoModules[$iIdxModule]->init($oEnreg);
			$iIdxModule++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxModule;
	}
	
	/**
	 * Cette méthode recherche tous les modules de la formation.
	 * @param Aucun
	 * @see CModules::initModules()
	 */
	function initTousModules ()
	{
		$sRequeteSql = "SELECT Module.* FROM Module"
			." WHERE Module.IdForm='{$this->iIdForm}'"
			." ORDER BY Module.OrdreMod ASC";
		return $this->initModules($sRequeteSql);
	}
	
	function initModulesParStatut ($v_iIdPers,$v_iIdStatutUtilisateur)
	{
		if ($v_iIdStatutUtilisateur < STATUT_PERS_PREMIER ||
			$v_iIdStatutUtilisateur > STATUT_PERS_DERNIER)
			return 0;
		
		$sRequeteSql = "SELECT Module.* FROM Module";
		$asConditions = NULL;
		
		switch ($v_iIdStatutUtilisateur)
		{
			case STATUT_PERS_RESPONSABLE:
				// Responsable
				$sRequeteSql .= " LEFT JOIN Formation_Resp USING (IdForm)";
				$asConditions = " AND Formation_Resp.IdPers='{$v_iIdPers}'";
				break;
			case STATUT_PERS_CONCEPTEUR:
				$sRequeteSql .= " LEFT JOIN Formation_Concepteur USING (IdForm)"
					." LEFT JOIN Module_Concepteur ON Module.IdMod=Module_Concepteur.IdMod";
				$asConditions = " AND Formation_Concepteur.IdPers='{$v_iIdPers}' AND Module_Concepteur.IdPers='{$v_iIdPers}'";
				break;
			case STATUT_PERS_TUTEUR:
				$sRequeteSql .= " LEFT JOIN Formation_Tuteur USING (IdForm)"
					." LEFT JOIN Module_Tuteur ON Module.IdMod=Module_Tuteur.IdMod";
				$asConditions = " AND Formation_Tuteur.IdPers='{$v_iIdPers}' AND Module_Tuteur.IdPers='{$v_iIdPers}'";
				break;
			case STATUT_PERS_ETUDIANT:
				$bInscrAutoModules = $this->retInscrAutoModules();
				$sRequeteSql .= " LEFT JOIN Formation_Inscrit USING (IdForm)"
					.($bInscrAutoModules ? NULL : " LEFT JOIN Module_Inscrit ON Module.IdMod=Module_Inscrit.IdMod");
				$asConditions = " AND Formation_Inscrit.IdPers='{$v_iIdPers}'"
					.($bInscrAutoModules ? NULL : " AND Module_Inscrit.IdPers='{$v_iIdPers}'");
				break;
		}
		
		// Conditions
		$sRequeteSql .= " WHERE Module.IdForm='{$this->iIdForm}'"
			." {$asConditions} ORDER BY Module.OrdreMod ASC";
		return $this->initModules($sRequeteSql);
	}
	
	function initModulesUtilisateur ($v_iIdPers,$v_iIdStatutUtilisateur,$v_bInscrAutoModules=TRUE)
	{
		if ($v_iIdStatutUtilisateur < STATUT_PERS_PREMIER ||
			$v_iIdStatutUtilisateur > STATUT_PERS_DERNIER)
			return 0;
		
		$sTables = NULL;
		$sConditions = NULL;
		
		switch ($v_iIdStatutUtilisateur)
		{
			case STATUT_PERS_RESPONSABLE_POTENTIEL:
			case STATUT_PERS_RESPONSABLE:
				$sTables .= " LEFT JOIN Formation_Resp"
					." ON Module.IdForm=Formation_Resp.IdForm"
					." AND Formation_Resp.IdPers='{$v_iIdPers}'";
				$sConditions .= "Formation_Resp.IdPers IS NOT NULL";
				
			case STATUT_PERS_CONCEPTEUR:
				$sTables .= " LEFT JOIN Formation_Concepteur"
					." ON Module.IdForm=Formation_Concepteur.IdForm"
					." AND Formation_Concepteur.IdPers='{$v_iIdPers}'"
					." LEFT JOIN Module_Concepteur"
					." ON Module.IdMod=Module_Concepteur.IdMod"
					." AND Module_Concepteur.IdPers='{$v_iIdPers}'";
				$sConditions .= (isset($sConditions) ? " OR" : NULL)
					." Module_Concepteur.IdPers IS NOT NULL";
				
			case STATUT_PERS_TUTEUR:
				$sTables .= " LEFT JOIN Formation_Tuteur"
					." ON Module.IdForm=Formation_Tuteur.IdForm"
					." AND Formation_Tuteur.IdPers='{$v_iIdPers}'"
					." LEFT JOIN Module_Tuteur"
					." ON Module.IdMod=Module_Tuteur.IdMod"
					." AND Module_Tuteur.IdPers='{$v_iIdPers}'";
				$sConditions .= (isset($sConditions) ? " OR" : NULL)
					." Module_Tuteur.IdPers IS NOT NULL";
				
			case STATUT_PERS_ETUDIANT:
				$sTables .= " LEFT JOIN Formation_Inscrit"
					." ON Module.IdForm=Formation_Inscrit.IdForm"
					." AND Formation_Inscrit.IdPers='{$v_iIdPers}'"
					." LEFT JOIN Module_Inscrit"
					." ON Module.IdMod=Module_Inscrit.IdMod"
					." AND Module_Inscrit.IdPers='{$v_iIdPers}'";
				$sConditions .= (isset($sConditions) ? " OR" : NULL)
					.($v_bInscrAutoModules ? " Formation_Inscrit.IdPers IS NOT NULL" : " Module_Inscrit.IdPers IS NOT NULL");
		}
		
		$sRequeteSql = "SELECT Module.* FROM Module"
			.(isset($sTables) ? "{$sTables}" : NULL)
			." WHERE Module.IdForm='{$this->iIdForm}'"
			.(isset($sConditions) ? " AND ({$sConditions})" : NULL)
			." ORDER BY Module.OrdreMod ASC";
		return $this->initModules($sRequeteSql);
	}
}

?>
