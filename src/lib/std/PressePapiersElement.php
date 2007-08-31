<?php
class PressePapiersElement
{
	var $_action;
	var $_sujet;
	
	function PressePapiersElement($sujet, $action)
	{
		$this->_action = $action;
		$this->_sujet = $sujet;
	}
	
	function retAction()
	{
		return $this->_action;
	}
	
	function retSujet()
	{
		return $this->_sujet;
	}
}
?>
