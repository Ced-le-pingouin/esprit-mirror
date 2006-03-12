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
