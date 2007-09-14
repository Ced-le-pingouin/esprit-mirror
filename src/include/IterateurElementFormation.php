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

/**
 * @file	IterateurElementFormation.php
 */
 
require_once dirname(__FILE__).'/globals.inc.php';
require_once dir_lib('std/IterateurTableau.php', TRUE);

/**
 * Itérateur(Composite) pour parcourir un élément de formation (formation, 
 * module, etc.), càd obtenir des "enfants" (et même les descendants suivants)
 */
class IterateurElementFormation extends IterateurTableau
{
	var $oElementFormation; ///< Elément sur lequel on demande l'itération
	
	/**
	 * Constructeur
	 * 
	 * @param	v_oElementFormation	l'élément sur lequel on désire effectuer 
	 * 								l'itération
	 */
	function IterateurElementFormation(&$v_oElementFormation)
	{
		$this->oElementFormation =& $v_oElementFormation;
		if (!is_null($this->oElementFormation->retElementsEnfants()))
			parent::IterateurTableau($this->oElementFormation->retElementsEnfants());
		else
			parent::IterateurTableau(array());
	}
	
	/**
	 * @see IterateurComposite#aEnfants()
	 */
	function aEnfants()
	{
		$e = $this->courant();
		return !is_null($e->retElementsEnfants());
	}
	
	/**
	 * @see IterateurComposite#retIterateurEnfants()
	 */
	function retIterateurEnfants()
	{
		return new IterateurElementFormation($this->courant());
	}
}
?>
