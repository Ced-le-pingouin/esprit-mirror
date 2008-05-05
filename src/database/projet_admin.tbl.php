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
** Fichier ................: projet_admin.tbl.php
** Description ............: 
** Date de création .......: 15-12-2006
** Dernière modification ..: 15-12-2006
** Auteurs ................: Cécile Guilloux
** Emails .................: 
**
** classe non définitive !

** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

class CProjet_Admin 
{
	var $oBdd;
	var $aoPersonnes;
	
	function CProjet_Admin (&$v_oBdd)
	{
		$this->oBdd = &$v_oBdd;
	}
	
	function ajouter ($v_iIdPers)
	{
		if ($v_iIdPers < 0)
			return;
		
		$sRequeteSql = "REPLACE INTO Projet_Admin SET"
			." IdPers={$v_iIdPers}";
		
		$this->oBdd->executerRequete ($sRequeteSql);
	}
	
	function effacer ($v_iIdPers)
	{
		if ($v_iIdPers < 0)
			return;
		
		$sRequeteSql = "DELETE FROM Projet_Admin WHERE IdPers={$v_iIdPers}";
		
		$this->oBdd->executerRequete ($sRequeteSql);
	}
	
	function initResponsables ()
	{
		$idx = 0;
		
		$this->aoPersonnes = array ();
		
		$sRequeteSql = "SELECT * FROM Projet_Admin";
		
		$hResult = $this->oBdd->executerRequete ($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv ($hResult))
		{
			$this->aoPersonnes[$idx] = new CPersonne ($this->oBdd,$oEnreg->IdPers);
			
			$idx++;
		}
		
		$this->oBdd->libererResult ($hResult);
		
		return $idx;
	}
}


?>
