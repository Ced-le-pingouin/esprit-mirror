<?php

/*
** Classe .................: CCouleur_CSS
** Description ............: 
** Date de création .......: 11-01-2002
** Dernière modification ..: 11-01-2002
** Auteur .................: Fili//0: Porco
** Email ..................: filippo.porco@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

class CCouleur_CSS 
{
	var $m_sCouleur;
	
	function CCouleur_CSS ($v_sCouleur=NULL)
	{
		return $this->m_sCouleur = $v_sCouleur;
	}
	
	function Couleur ($v_sCouleur=NULL)
	{
		if ($v_sCouleur !== NULL)
			$this->CCouleur_CSS ($v_sCouleur);
		else
			return $this->m_sCouleur;
	}
	
	function rgb ($v_sCouleurR=0,$v_sCouleurV=0,$v_sCouleurB=0)
	{
		return ($this->m_sCouleur = "rgb($v_sCouleurR,$v_sCouleurV,$v_sCouleurB)");
	}
	
	function hex ($v_sCouleurHex)
	{
		return ($this->m_sCouleur = "#{$v_sCouleurHex}");
	}
}

?>
