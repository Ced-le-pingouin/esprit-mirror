<?php
require_once dirname(__FILE__).'/globals.inc.php';
require_once dir_lib('std/IterateurTableau.php', TRUE);

class IterateurElementFormation extends IterateurTableau
{
	var $oElementFormation;
	
	function IterateurElementFormation(&$v_oElementFormation)
	{
		$this->oElementFormation =& $v_oElementFormation;
		if (!is_null($this->oElementFormation->retElementsEnfants()))
			parent::IterateurTableau($this->oElementFormation->retElementsEnfants());
		else
			parent::IterateurTableau(array());
	}
	
	function aEnfants()
	{
		$e = $this->courant();
		return !is_null($e->retElementsEnfants());
	}
	
	function retIterateurEnfants()
	{
		return new IterateurElementFormation($this->courant());
	}
}
?>
