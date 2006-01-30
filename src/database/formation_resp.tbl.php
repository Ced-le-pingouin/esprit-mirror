<?php

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
