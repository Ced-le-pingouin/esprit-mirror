<?php

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
