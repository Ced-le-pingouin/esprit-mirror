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
 * @file	PressePapiersElement.php
 */

/**
 * Représente un élément de presse-papiers (à combiner à la classe PressePapiers)
 */
class PressePapiersElement
{
	var $_action;	///< représentation de l'action à effectuer sur l'élément
	var $_sujet;	///< sujet sur lequel l'action est effectuée
	
	/**
	 * Constructeur. Définit le sujet et l'action à effectuer sur ce dernier, le
	 * tout constitue l'élément à enregistrer dans un presse-papiers
	 */
	function PressePapiersElement($sujet, $action)
	{
		$this->_action = $action;
		$this->_sujet = $sujet;
	}
	
	/**
	 * Retourne l'action de l'élément de presse-papiers
	 */
	function retAction()
	{
		return $this->_action;
	}
	
	/**
	 * Retourne le sujet sur lequel s'effectue l'action pour cet élément de 
	 * presse-papiers
	 */
	function retSujet()
	{
		return $this->_sujet;
	}
}
?>
