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
** Fichier ................: projet_concepteur.tbl.php
** Description ............: 
** Date de création .......: 24-09-2002
** Dernière modification ..: 15-12-2002
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

class CProjet_Concepteur
{
	var $oBdd;
	var $aoConcepteurs;
	var $iIdPers;

	function CProjet_Concepteur (&$v_oBdd,$v_iIdPers=0)
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdPers = $v_iIdPers;
	}

	function ajouterConcepteurs ($v_aiIdPers)
	{
		settype($v_aiIdPers,"array");

		$sValeursRequete = NULL;

		foreach ($v_aiIdPers as $iIdPers)
			$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
				." ({$iIdPers})";

		if (isset($sValeursRequete))
		{
			$sRequeteSql = "REPLACE INTO Projet_Concepteur"
				." (IdPers) VALUES {$sValeursRequete}";

			$this->oBdd->executerRequete($sRequeteSql);
		}
	}

	function effacerConcepteur()
	{
		if ($this->iIdPers < 1)
			return;

		$sRequeteSql = "DELETE FROM Projet_Concepteur"
			." WHERE IdPers=".$this->iIdPers;

		$this->oBdd->executerRequete($sRequeteSql);
	}

	function initConcepteurs ()
	{
		$idx = 0;

		$this->aoConcepteurs = array();

		$sRequeteSql = "SELECT Personne.* FROM Projet_Concepteur"
			." LEFT JOIN Personne USING(IdPers)";

		$hResult = $this->oBdd->executerRequete($sRequeteSql);

		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoConcepteurs[$idx] = new CPersonne($this->oBdd);
			$this->aoConcepteurs[$idx]->init($oEnreg);
			$idx++;
		}

		$this->oBdd->libererResult($hResult);

		return $idx;
	}
}

?>
