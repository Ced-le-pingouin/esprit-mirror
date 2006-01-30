<?php

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
	
	function initResponsables ()
	{
		$idx = 0;
		
		$this->aoPersonnes = array ();
		
		$sRequeteSql = "SELECT * FROM Projet_Resp";
		
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
