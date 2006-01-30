<?php

class CEvaluation
{
	var $oBdd;
	var $oEnregBdd;
	
	var $iIdResSousActiv;
	var $iIdPers;
	
	var $oEvaluateur;
	var $aoEvaluations;
	
	function CEvaluation (&$v_oBdd,$v_iIdResSousActiv=NULL,$v_iIdPers=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iIdResSousActiv = $v_iIdResSousActiv;
		$this->iIdPers = $v_iIdPers;
		
		if (isset($this->iIdResSousActiv) && isset($this->iIdPers))
			$this->init();
		else if (isset($this->iIdResSousActiv))
			$this->initGraceIdResSousActiv();
		else if (isset($this->iIdPers))
			$this->initGraceIdPers();
	}
	
	function init ($v_oEnregExistant = NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Ressource_SousActiv_Evaluation"
				." WHERE IdResSousActiv='{$this->iIdResSousActiv}'"
				." AND IdPers='{$this->iIdPers}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		$this->iIdResSousActiv = $this->oEnregBdd->IdResSousActiv;
		$this->iIdPers = $this->oEnregBdd->IdPers;
	}
	
	function initGraceIdResSousActiv ($v_iIdResSousActiv)
	{
	}
	
	function initGraceIdPers ($v_iIdPers)
	{
	}
	
	function initEvaluateur () { $this->oEvaluateur = new CPersonne($this->oBdd, $this->retIdEvaluateur()); }
	function retIdRessource () { return $this->iIdResSousActiv; }
	function retIdEvaluateur () { return $this->iIdPers; }
	function retDate () { return $this->oEnregBdd->DateEval; }
	function retAppreciation () { return stripslashes($this->oEnregBdd->AppreciationEval); }
	function retCommentaire () { return stripslashes($this->oEnregBdd->CommentaireEval); }
}

?>
