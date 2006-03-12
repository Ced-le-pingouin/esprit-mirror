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
** Fichier ................: statut_permission.tbl.php
** Description ............: 
** Date de création .......: 18/03/2005
** Dernière modification ..: 18/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

class CStatutPermission
{
	var $oBdd;
	var $oEnregBdd;
	
	var $abPermissions;
	
	function CStatutPermission (&$v_oBdd)
	{
		$this->oBdd = &$v_oBdd;
	}
	
	function initPermissions ($v_iIdStatut)
	{
		$iIdxPermis = 0;
		$this->abPermissions = array();
		
		$sRequeteSql = "SELECT Statut_Permission.*"
			.", Permission.NomPermis"
			." FROM Statut_Permission"
			." LEFT JOIN Permission USING (IdPermission)"
			." WHERE Statut_Permission.IdStatut='{$v_iIdStatut}'"
			." ORDER BY Statut_Permission.IdPermission ASC";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->abPermissions[$oEnreg->NomPermis] = TRUE;
			$iIdxPermis++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxPermis;
	}
	
	function verifPermission ($v_sNomPermission) { return is_array($this->abPermissions) && isset($this->abPermissions[$v_sNomPermission]); }
	
	function ajouter ($v_iIdPermis,$v_iIdStatut)
	{
		$sRequeteSql = "REPLACE INTO Statut_Permission"
			." (IdPermission,IdStatut) VALUES ('{$v_iIdPermis}','{$v_iIdStatut}')";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function effacer ($v_iIdPermis,$v_iIdStatut)
	{
		$sRequeteSql = "DELETE FROM Statut_Permission"
			." WHERE IdPermission='{$v_iIdPermis}'"
			." AND IdStatut='{$v_iIdStatut}'"
			." LIMIT 1";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	function optimiser () { $this->oBdd->executerRequete("OPTIMIZE TABLE Statut_Permission"); }
}

?>
