<?php
require_once(dirname(__FILE__).'/IterateurTableau.php');
require_once(dirname(__FILE__).'/PressePapiersElement.php');

class PressePapiers
{
	var $_aElements = array();
	var $_aElementsAEnlever = array();
	
	function ajouterElement($elem)
	{
		$index = md5($elem->retSujet().$elem->retAction());
		$this->_aElements[$index] = $elem;
	}
	
	function enleverElement($elem, $differe = FALSE)
	{
		$index = md5($elem->retSujet().$elem->retAction());
		if (!$differe)
			unset($this->_aElements[$index]);
		else
			$this->_aElementsAEnlever[] = $index;
	}
	
	function enleverElementsDiffere()
	{
		foreach ($this->_aElementsAEnlever as $indexElem)
			unset($this->_aElements[$indexElem]);
	}
	
	function vider()
	{
		$this->_aElements = array();
	}
	
	function estVide()
	{
		return (count($this->_aElements) == 0);
	}
	
	function retIterateur()
	{
		return new IterateurTableau($this->_aElements);
	}
}
?>
