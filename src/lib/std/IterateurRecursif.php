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
 * @file	IterateurRecursif.php
 */

require_once(dirname(__FILE__).'/OO.php');
require_once(dirname(__FILE__).'/IterateurDecorateur.php');
require_once(dirname(__FILE__).'/IterateurComposite.php');

/** @name Constantes - modes de récursion */
//@{
define("ITR_REC_MODE_PREMIER" , 1); /// constante interne à des fins de vérification
define("ITR_REC_FEUILLES_SEUL", 1); /// les éléments qui on des enfants ne seront pas retournés par courant() (bien entendu, ils seront tout de même parcourus) @enum ITR_REC_FEUILLES_SEUL
define("ITR_REC_PARENT_AVANT" , 2); /// les éléments qui ont des enfants seront retournés par courant() avant que l'itération sur leurs enfants ait lieu        @enum ITR_REC_PARENT_AVANT
define("ITR_REC_ENFANTS_AVANT", 3); /// les éléments qui ont des enfants seront retournés par courant() après que l'itération sur leurs enfants ait eu lieu     @enum ITR_REC_ENFANTS_AVANT
define("ITR_REC_MODE_DERNIER" , 3); /// constante interne à des fins de vérification
//@}

/**
 * Itérateur "décorateur" qui permet de parcourir automatiquement non seulement les éléments d'un itérateur, mais 
 * aussi les enfants de ce dernier lorsqu'il y en a. L'itérateur "décoré" doit implémenter l'interface 
 * IterateurComposite pour que ça fonctionne
 * 
 * @todo: taille(), rechercher(), estPremier(), et estDernier() ???
 * @todo: prec() ??? (car on hérite d'IterateurTableau, bidirectionnel, gasp!)
 * @todo: voir s'il y a moyen de modifier la façon d'implémenter \c _bDoitRetournerParent, car le nom n'est pas clair, 
 *        et également, y a-t-il moyen de faire tous les test le concernant dans #_trouverElementSuiv(), qui 
 *        deviendrait peut-être lui-même #suiv() (car pour le moment #suiv() doit également effectuer des tests qui 
 *        devraient peut-être être effectués par #_trouverElementSuiv())
 */
class IterateurRecursif extends IterateurDecorateur
{
	var $iMode = ITR_REC_FEUILLES_SEUL; ///< Le mode de récursion utilisé (voir constantes \c ITR_REC_...) 
	var $aoItr = array();               ///< Les différents niveau d'itérateurs courants (le principal est toujours l'indice 0)

	/**
	 * Constructeur
	 * 
	 * @param	v_oItr	l'itérateur qui sera parcouru en utilisant la récursion. Ce dernier doit implémenter l'interface 
	 * 					ItérateurComposite
	 * @param	v_iMode	le mode de récursion voulu pour l'itération (voir constantes \c ITR_REC_...)
	 */	
	function IterateurRecursif($v_oItr, $v_iMode = ITR_REC_FEUILLES_SEUL)
	{
		if (!OO::instanceDe($v_oItr, 'IterateurComposite'))
			Erreur::provoquer("L'itérateur récursif ne fonctionne que sur des itérateurs implémentant l'interface "
			                 ."IterateurComposite");
		
		if ($v_iMode < ITR_REC_MODE_PREMIER || $v_iMode > ITR_REC_MODE_DERNIER)
			Erreur::provoquer("Le mode de récursion passé en paramètre est invalide");
		
		$this->oItr     =  $v_oItr;
		$this->aoItr[0] =& $this->oItr;
		$this->iMode    =  $v_iMode;

		$this->debut();
	}
	
	/**
	 * @return	une référence vers l'itérateur ou le sous-itérateur courant (celui sur lequel sont appliquées les  
	 * 			opérations telles que #suiv())
	 */
	function &_retIterateurCourant()
	{
		return $this->aoItr[count($this->aoItr)-1];
	}
	
	/**
	 * Détruit les données de l'itérateur (normalement un sous-itérateur) courant
	 * 
	 * @return	une référence vers le parent du sous-itérateur qui vient d'être détruit
	 */
	function &_terminerIterateurCourant()
	{
		array_pop($this->aoItr);
		return $this->_retIterateurCourant();
	}

	/**
	 * Indique si l'itérateur récursif se trouve actuellement dans un sous-itérateur, ou dans l'itérateur principal 
	 * (passé à l'origine au constructeur)
	 * 
	 * @return	\c true si l'itération a actuellement lieu dans un sous-itérateur, \c false si l'itération est en cours 
	 * 			dans l'itérateur principal
	 */
	function _aSousIterateurs()
	{
		return (count($this->aoItr) > 1);
	}
	
	/**
	 * Initialise un itérateur sur les enfants de l'élément courant de l'itérateur en cours
	 */
	function _initSousIterateur()
	{
		$oItr =& $this->_retIterateurCourant();
				
		$this->aoItr[] = $oItr->retIterateurEnfants();
		$oItr =& $this->_retIterateurCourant();

		$oItr->debut();
		$this->_trouverElementSuiv();
	}

	/**
	 * Détermine si l'élément courant de l'itérateur courant est celui qui devra réellement être retourné lors du 
	 * prochain appel à #courant(). Cette méthode se charge par exemple de passer dans un sous-itérateur sur les enfants 
	 * de l'élément courant si c'est possible, de revenir dans un itérateur parent quand le sous-itérateur courant se 
	 * termine, etc.
	 */		
	function _trouverElementSuiv()
	{
		$oItr =& $this->_retIterateurCourant();
		
		// si l'élément courant de l'itérareur courant est valide... 
		if ($oItr->estValide())
		{
			// ...et qu'il a des "enfants"
			if ($oItr->aEnfants())
			{
				// si l'élément courant a des enfants mais que lui-même en tant qu'élément a déjà été retourné, 
				// OU si on est en mode "feuilles seulement", on peut directement commencer l'itération sur les enfants
				if ((isset($oItr->_bDoitRetournerParent) && $oItr->_bDoitRetournerParent === FALSE)
				    || $this->iMode == ITR_REC_FEUILLES_SEUL)
				{
					$this->_initSousIterateur();
				}
				// si par contre on est en mode "itération des enfants avant de retourner le parent", on marque le 
				// parent pour indiquer qu'il n'a pas encore été retourné par l'itérateur, et on commence l'itération 
				// sur les enfants
				else if ($this->iMode == ITR_REC_ENFANTS_AVANT)
				{
					$oItr->_bDoitRetournerParent = TRUE;
					$this->_initSousIterateur();
				}
				// enfin, si on est en mode "retourner l'élément parent avant de commencer l'itération sur les enfants", 
				// on ne fait rien concernant les enfants, donc l'élément courant reste celui de l'itérateur courant, 
				// mais on indique qu'au prochain déplacement, il ne faudra plus retourner le parent mais commencer 
				// l'itération sur les enfants
				else if ($this->iMode == ITR_REC_PARENT_AVANT)
				{
					$oItr->_bDoitRetournerParent = FALSE;
				}
			}
			// si l'élément courant n'a pas d'enfants, il ne sera pas concerné par le marqueur "parent déjà retourné ou 
			// pas par l'itérateur"
		}
		// si l'élément courant de l'itérateur n'est pas valide...
		else
		{
			// ...si on est dans un sous-itérateur...
			if ($this->_aSousIterateurs())
			{
				// ...on revient à l'élément courant de l'itérateur parent...
				$oItr =& $this->_terminerIterateurCourant();
			
				// ..., et si l'itérateur parent ne devait pas être retourné, ou l'avait déjà été avant l'itération sur 
				// les enfants, on passe à l'élément suivant directement
				if (!isset($oItr->_bDoitRetournerParent))
				{
					$this->suiv();
				}
				else if ($oItr->_bDoitRetournerParent === FALSE)
				{
					unset($oItr->_bDoitRetournerParent);
					$this->suiv();
				}
				else
				{
					// on enlève le marqueur "élément parent à retourner ou pas"
					unset($oItr->_bDoitRetournerParent);					
				}
			}
		}
	}
	
	/**
	 * Réinitialise l'itération au début de l'itérateur principal (les emplacement dans d'éventuels sous-itérateurs sont 
	 * perdus)
	 */
	function debut()
	{
		// effacer tous les sous-itérateurs
		while ($this->_aSousIterateurs())
			$this->_terminerIterateurCourant();
		
		// enlever un éventuel "marqueur" qui indiquerait qu'un élément parent de l'itérateur a déjà été retourné et que 
		// l'itération doit continuer directement sur ses enfants
		if (isset($this->oItr->_bDoitRetournerParent))
			unset($this->oItr->_bDoitRetournerParent);
		
		// et enfin revenir au début de l'itérateur principal
		$this->oItr->debut();
		$this->_trouverElementSuiv();
	}
	
	/**
	 * Déplace l'itérateur d'une position vers l'avant. Dans le cas de notre itérateur récursif, cela veut également 
	 * dire que si un élément d'itérateur est lui-même "traversable", l'itération continue également dans les enfants 
	 * de cet élément (=sous-itérateur). De même, un appel à #suiv() lorsqu'un sous-itérateur est en position finale 
	 * retourne le prochain élément de l'itérateur parent qui avait été "laissé" (si ce dernier en contient encore, 
	 * évidemment)  
	 */
	function suiv()
	{
		$oItr =& $this->_retIterateurCourant();

		if (!isset($oItr->_bDoitRetournerParent) || $oItr->_bDoitRetournerParent === TRUE)
			$oItr->suiv();
		
		$this->_trouverElementSuiv();
	}
	
	/**
	 * Voir Iterateur#estValide()
	 */
	function estValide()
	{
		$oItr =& $this->_retIterateurCourant();
		
		return $oItr->estValide(); 
	}
	
	/**
	 * Voir Iterateur#cle()
	 */
	function cle()
	{
		$oItr =& $this->_retIterateurCourant();
		
		return $oItr->cle(); 
	}
	
	/**
	 * Voir Iterateur#courant()
	 */
	function courant()
	{
		$oItr =& $this->_retIterateurCourant();
		
		return $oItr->courant(); 
	}
	
	/**
	 * Voir Iterateur#fin().
	 * 
	 * @note	Comme #debut(), agit également uniquement sur l'itérateur principal, pas sur les sous-itérateurs 
	 * 			éventuellements en cours
	 */
	function fin()
	{
		while ($this->_aSousIterateurs())
			$this->_terminerIterateurCourant();
			
		$this->oItr->fin();
		$this->_trouverElementSuiv();
	}
}

?>