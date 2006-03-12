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
** Classe .................: equipes.class.php
** Description ............:
** Date de création .......: 08/09/2004
** Dernière modification ..: 10/05/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

require_once(dir_database("ids.class.php"));
require_once(dir_database("equipe.tbl.php"));

class CEquipes
{
	var $oBdd;
	var $oIds;
	
	var $iIdNiveau;
	var $iTypeNiveau;
	
	var $oEquipe;
	var $aoEquipes;
	
	var $asRequetesSql;
	
	function CEquipes (&$v_oBdd,$v_iIdNiveau,$v_iTypeNiveau)
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdNiveau = $v_iIdNiveau;
		$this->iTypeNiveau = $v_iTypeNiveau;
		
		$this->asRequetesSqlEquipes = array(NULL
			, "SELECT * FROM Equipe WHERE IdForm='{niveau->id}' AND IdMod='0'"
			, "SELECT * FROM Equipe WHERE IdMod='{niveau->id}' AND IdRubrique='0'"
			, "SELECT * FROM Equipe WHERE IdRubrique='{niveau->id}' AND IdActiv='0'"
			, NULL
			, NULL
			, NULL
		);
		
		$this->asRequetesSqlEquipeMembre = array(NULL
			, " AND Equipe.IdForm='{niveau->id}' AND Equipe.IdMod='0'"
			, " AND Equipe.IdMod='{niveau->id}' AND Equipe.IdRubrique='0'"
			, " AND Equipe.IdRubrique='{niveau->id}' AND Equipe.IdActiv='0'"
			, NULL
			, NULL
			, NULL
		);
		
		$this->oIds = new CIds($this->oBdd,$this->iTypeNiveau,$this->iIdNiveau);
	}
	
	function initEquipes ($v_bInitMembres=FALSE)
	{
		$iIdxEquipe = 0;
		$this->aoEquipes = array();
		
		if ($this->iTypeNiveau < TYPE_FORMATION ||
			$this->iTypeNiveau > TYPE_SOUS_ACTIVITE ||
			$this->iIdNiveau < 1)
			return $iIdxEquipe;
		
		$aiIds = $this->oIds->retListeIds();
		
		for ($iIdxNiveau=$this->iTypeNiveau; $iIdxNiveau>=TYPE_FORMATION; $iIdxNiveau--)
		{
			$sRequeteSql = str_replace("{niveau->id}",$aiIds[$iIdxNiveau],$this->asRequetesSqlEquipes[$iIdxNiveau]);
			
			if (isset($sRequeteSql) && strlen($sRequeteSql) > 0)
			{
				$hResult = $this->oBdd->executerRequete($sRequeteSql);
				
				if ($hResult !== FALSE)
				{
					while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
					{
						$this->aoEquipes[$iIdxEquipe] = new CEquipe($this->oBdd);
						$this->aoEquipes[$iIdxEquipe]->init($oEnreg);
						
						if ($v_bInitMembres)
							$this->aoEquipes[$iIdxEquipe]->initMembres();
						
						$iIdxEquipe++;
					}
					
					$this->oBdd->libererResult($hResult);
					
					if ($iIdxEquipe > 0)
						break;
				}
			}
		}
		
		return $iIdxEquipe;
	}
	
	function initEquipeGraceIdPers ($v_iIdPers)
	{
		$this->oEquipe = NULL;
		
		$aiIds = $this->oIds->retListeIds();
		
		for ($iIdxNiveau=$this->iTypeNiveau; $iIdxNiveau>TYPE_INCONNU; $iIdxNiveau--)
		{
			if (empty($this->asRequetesSqlEquipeMembre[$iIdxNiveau]))
				continue;
			
			$sRequeteSql = "SELECT Equipe.* FROM Equipe"
				." LEFT JOIN Equipe_Membre USING (IdEquipe)"
				." WHERE Equipe_Membre.IdPers='{$v_iIdPers}'"
				.$this->asRequetesSqlEquipeMembre[$iIdxNiveau];
			
			$sRequeteSql = str_replace("{niveau->id}",$aiIds[$iIdxNiveau],$sRequeteSql);
			
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			if ($hResult !== FALSE)
			{
				if ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
				{
					$this->oEquipe = new CEquipe($this->oBdd);
					$this->oEquipe->init($oEnreg);
					$this->oBdd->libererResult($hResult);
					break;
				}
				
				$this->oBdd->libererResult($hResult);
			}
		}
		
		return $this->oEquipe;
	}
}

?>
