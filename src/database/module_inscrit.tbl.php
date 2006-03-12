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
** Fichier ................: module_inscrit.tbl.php
** Description ............: 
** Date de création .......: 18-09-2002
** Dernière modification ..: 12-02-2003
** Auteurs ................: Filippo PORCO, Cédric FLOQUET
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

class CModule_Inscrit 
{
	var $oBdd;
	var $oEnregBdd;
	var $iIdMod;
	var $iIdPers;
	var $aoModules;
	var $amErreurs;
	
	function CModule_Inscrit (&$v_oBdd,$v_iIdMod=0,$v_iIdPers=0)
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdMod = $v_iIdMod;
		$this->iIdPers = $v_iIdPers;
	}

	function ajouter ($v_iIdMod)
	{
		if ($v_iIdMod < 1 || $this->iIdPers < 1)
			return;

		$sRequeteSql = "REPLACE INTO Module_Inscrit SET"
			." IdMod={$v_iIdMod}"
			." ,IdPers={$this->iIdPers}";

		$this->oBdd->executerRequete ($sRequeteSql);
	}

	function effacer ($v_iIdMod,$v_iIdPers)
	{
		if ($v_iIdMod < 1 || $this->iIdPers < 1)
			return;

		$sRequeteSql = "DELETE FROM Module_Inscrit"
			." WHERE IdMod='{$v_iIdMod}'"
			." AND IdPers='{$this->iIdPers}'";

		$this->oBdd->executerRequete ($sRequeteSql);
	}
	
	function initModules ($v_bRechTousCours=FALSE,$v_iIdForm=0)
	{
		$iIndexModules = 0;

		$this->aoModules = array();

		if ($this->iIdPers > 0)
		{
			$sRequeteSql = "SELECT Module.*, Module_Inscrit.IdPers FROM Module"
				." LEFT JOIN Module_Inscrit ON Module.IdMod=Module_Inscrit.IdMod"
				.($v_bRechTousCours ? " AND Module_Inscrit.IdPers=".$this->iIdPers : NULL)
				.($v_iIdForm > 0 ? " WHERE Module.IdForm={$v_iIdForm}" : NULL)
				.($v_bRechTousCours ? NULL : ($v_iIdForm > 0 ? " AND " : " WHERE ")."Module_Inscrit.IdPers=".$this->iIdPers)
				." ORDER BY Module.OrdreMod ASC";
			
			$hResult = $this->oBdd->executerRequete($sRequeteSql);

			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$this->aoModules[$iIndexModules] = new CModule($this->oBdd);

				$this->aoModules[$iIndexModules]->init($oEnreg);

				if ($v_bRechTousCours)
					$this->aoModules[$iIndexModules]->estSelectionne = ($oEnreg->IdPers == $this->iIdPers);
				
				$this->aoModules[$iIndexModules]->estMembre = $this->aoModules[$iIndexModules]->verifMembre($this->iIdPers);

				$iIndexModules++;
			}

			$this->oBdd->libererResult($hResult);
		}

		return $iIndexModules;
	}

	function ajouterModules ($v_amIdMod)
	{
		settype($v_amIdMod,'array');

		foreach ($v_amIdMod as $iIdMod)
		{
			$sRequeteSql = "REPLACE INTO Module_Inscrit SET"
				." IdMod={$iIdMod}, IdPers={$this->iIdPers}";

			$this->oBdd->executerRequete($sRequeteSql);
		}
	}

	function effacerModules ($v_iIdForm)
	{
		$this->amErreurs = array();
		
		if (($iNbrModules = $this->initModules(FALSE,$v_iIdForm)) > 0)
		{
			$sValeursRequete = NULL;
			
			for ($i=0; $i<$iNbrModules; $i++)
				$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
					.$this->aoModules[$i]->retId();

			if (isset($sValeursRequete))
			{						
				$sRequeteSql = "DELETE FROM Module_Inscrit WHERE IdMod IN ($sValeursRequete)"
					." AND IdPers='{$this->iIdPers}'";

				$this->oBdd->executerRequete($sRequeteSql);
			}
		}
	}
}

?>
