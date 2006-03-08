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
** Fichier ................: formation_resp.tbl.php
** Description ............:
** Date de création .......: 04-07-2002
** Dernière modification ..: 06-12-2002
** Auteur .................: Filippo PORCO
** Email ..................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

class CFormation_Resp
{
	var $oBdd;
	var $aoPersonnes;

	var $iId;

	function CFormation_Resp (&$v_oBdd,$v_iIdForm=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iId = $v_iIdForm;
	}

	function initResponsables ()
	{
		$idx = 0;

		$this->aoPersonnes = array();

		if ($this->iId > 0)
		{
			$sRequeteSql = "SELECT * FROM Formation_Resp"
				." WHERE IdForm='{$this->iId}'";

			$hResult = $this->oBdd->executerRequete($sRequeteSql);

			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
				$this->aoPersonnes[$idx++] = new CPersonne($this->oBdd,$oEnreg->IdPers);

			$this->oBdd->libererResult($hResult);
		}

		return $idx;
	}

	function ajouter ($v_iIdPers)
	{
		if ($this->iId < 1 || $v_iIdPers < 1)
			return;

		$sRequeteSql = "REPLACE INTO Formation_Resp SET"
			." IdForm={$this->iId}"
			." ,IdPers={$v_iIdPers}";

		$this->oBdd->executerRequete($sRequeteSql);
	}

	function effacer ($v_iIdPers)
	{
		if ($this->iId < 1 || $v_iIdPers < 1)
			return;

		$sRequeteSql = "DELETE FROM Formation_Resp"
			." WHERE IdForm='{$this->iId}'"
			." AND IdPers='{$v_iIdPers}'";

		$this->oBdd->executerRequete($sRequeteSql);
	}

	function defIdForm ($v_iIdForm)
	{
		$this->iId = $v_iIdForm;
	}

	function retIdForm ()
	{
		return $this->iId;
	}
}

?>
