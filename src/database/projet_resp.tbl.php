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
** Fichier ................: projet_resp.tbl.php
** Description ............: 
** Date de création .......: 20-09-2002
** Dernière modification ..: 24-09-2002
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

class CProjet_Resp 
{
	var $oBdd;
	var $aoPersonnes;
	
	function CProjet_Resp (&$v_oBdd)
	{
		$this->oBdd = &$v_oBdd;
	}
	
	function ajouter ($v_iIdPers)
	{
		if ($v_iIdPers < 0)
			return;
		
		$sRequeteSql = "REPLACE INTO Projet_Resp SET"
			." IdPers={$v_iIdPers}";
		
		$this->oBdd->executerRequete ($sRequeteSql);
	}
	
	function effacer ($v_iIdPers)
	{
		if ($v_iIdPers < 0)
			return;
		
		$sRequeteSql = "DELETE FROM Projet_Resp WHERE IdPers={$v_iIdPers}";
		
		$this->oBdd->executerRequete ($sRequeteSql);
	}
	
	function initResponsables ($v_sModeTri="ASC")
	{
		$idx = 0;
		
		$this->aoPersonnes = array ();
		
		//$sRequeteSql = "SELECT * FROM Projet_Resp";
		$sRequeteSql = "SELECT Personne.* FROM Projet_Resp"
				." LEFT JOIN Personne USING(IdPers)"
				." ORDER BY Personne.Nom {$v_sModeTri}, Personne.Prenom ASC";
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
