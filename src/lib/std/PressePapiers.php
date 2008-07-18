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
 * @file	PressePapiers.php
 */

require_once(dirname(__FILE__).'/IterateurTableau.php');
require_once(dirname(__FILE__).'/PressePapiersElement.php');

/**
 * Gestion de presse-papiers dans lequel on ajoute ou enlève des éléments et 
 * l'action qu'on désire y appliquer (copier etc.)
 */
class PressePapiers
{
	var $_aElements = array();			///< Tableau des éléments du presse-papiers
	var $_aElementsAEnlever = array();	///< Tableau des éléments marqués pour suppression "tardive"
	
	/**
	 * Ajoute un élément au presse-papiers
	 * 
	 * @param	elem	l'élément à ajouter (objet PressePapiersElement)
	 */
	function ajouterElement($elem)
	{
		$index = md5($elem->retSujet().$elem->retAction());
		$this->_aElements[$index] = $elem;
	}
	
	/**
	 * Enlève un élément du presse-papiers
	 * 
	 * @param	elem	l'élément à enlever du presse papier (objet 
	 * 					PressePapiersElement)
	 * @param	differe	si \true, l'élément est seulement "marqué" pour 
	 * 					suppression, mais celle-ci n'est effective qu'à l'appel
	 * 					de #enleverElementsDiffere(). Si \c false (défaut), 
	 * 					l'élément est enlevé immédiatement
	 */
	function enleverElement($elem, $differe = FALSE)
	{
		$index = md5($elem->retSujet().$elem->retAction());
		if (!$differe)
			unset($this->_aElements[$index]);
		else
			$this->_aElementsAEnlever[] = $index;
	}
	
	/**
	 * Enlève les éléments préalablement marqués pour suppression du 
	 * presse-papiers
	 */
	function enleverElementsDiffere()
	{
		foreach ($this->_aElementsAEnlever as $indexElem)
			unset($this->_aElements[$indexElem]);
	}
	
	/**
	 * Enlève les éléments considérés comme invalides (par ex. qui auraient été
	 * supprimés depuis leur référencement dans le presse-papiers
	 * 
	 * @param	fctEstValide	fonction "callback" à appeler pour chaque 
	 * 							élément, qui devra accepter un élément en 
	 * 							paramètre, et retourner \c true si celui-ci est
	 * 							considéré comme valide, \c false sinon 
	 *
	 * @return	\c true si des éléments invalides ont été trouvés (et enlevés)
	 */
	function enleverElementsInvalides($fctEstValide)
	{
		$elemsInvalidesTrouves = FALSE;
		
		foreach ($this->_aElements as $indexElem => $elem)
		{
			if (!call_user_func($fctEstValide, $elem))
			{
				unset($this->_aElements[$indexElem]);
				$elemsInvalidesTrouves = TRUE;
			}
		}
		
		return $elemsInvalidesTrouves;
	}
	
	/**
	 * Vide complètement le presse-papiers
	 */
	function vider()
	{
		$this->_aElements = array();
	}
	
	/**
	 * Indique si le presse-papiers est vide
	 * 
	 * @return	\c true si le presse-papiers ne comporte aucun élément, \c false
	 * 			sinon
	 */
	function estVide()
	{
		return (count($this->_aElements) == 0);
	}
	
	/**
	 * Retourne un objet IterateurTableau sur les éléments du presse-papiers
	 */
	function retIterateur()
	{
		return new IterateurTableau($this->_aElements);
	}
}
?>
