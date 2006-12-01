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
 * @file	IterateurTableau.php
 */

require_once(dirname(__FILE__).'/OO.php');
require_once(dirname(__FILE__).'/Erreur.php');
require_once(dirname(__FILE__).'/IterateurAbstrait.php');
require_once(dirname(__FILE__).'/IterateurBidir.php');
require_once(dirname(__FILE__).'/IterateurComposite.php');

/**
 * Sous-classe d'IterateurAbstrait, qui permet d'effectuer des itérations sur un tableau, mais implémente en plus 
 * l'interface IterateurBidir pour fournir un itérateur bidirectionnel
 *
 * @note	Pour le moment, il n'y a pas d'implémentation spécifique pour #rechercher(), elle est récupérée
 * 			d'IterateurAbstrait, et est donc "générique"
 */
class IterateurTableau extends IterateurAbstrait
{
	var $aTableau; ///< Le tableau sur lequel aura lieu l'itération, et qui est passé au constructeur, puis sauvé ici

	/**
	 * Constructeur
	 *
	 * @param	v_aTableau	le tableau sur lequel on effectuera l'itération
	 */
	function IterateurTableau($v_aTableau)
	{
		if (!is_array($v_aTableau))
			Erreur::provoquer("L'objet n'est pas un tableau");

		$this->aTableau = $v_aTableau;
		$this->debut();
	}
	
	// méthodes implémentées pour l'interface Iterateur (simple)
	/**
	 * Voir Iterateur#debut()
	 */
	function debut()
	{
		reset($this->aTableau);
	}

    /**
	 * Voir Iterateur#suiv()
	 */
    function suiv()
    {
    	next($this->aTableau);
    }

    /**
	 * Voir Iterateur#estValide()
	 */
    function estValide()
    {
    	// si le pointeur de tableau est invalide, normalement la fonction key() retourne NULL, donc on utilise ça pour
    	// déterminer si l'itérateur est dans une position valide. Ce comportement de key() n'est toutefois toujours
    	// pas documenté dans le manuel en ligne de PHP...
    	return ( !is_null( key($this->aTableau) ) ) ;
    }

    /**
	 * Voir Iterateur#cle()
	 */
    function cle()
    {
    	return key($this->aTableau);
    }

    /**
	 * Voir Iterateur#courant()
	 */
    function courant()
    {
    	return current($this->aTableau);
    }

	/**
	 * Voir Iterateur#fin()
	 */
    function fin()
    {
    	end($this->aTableau);
    }

    /**
	 * Voir Iterateur#taille()
	 */
    function taille()
    {
    	return count($this->aTableau);
    }

    /**
	 * Voir Iterateur#estPremier()
	 */
    function estPremier()
    {
    	return ( key($this->aTableau) === reset(array_keys($this->aTableau)) );
    }

    /**
	 * Voir Iterateur#estDernier()
	 */
    function estDernier()
    {
    	return ( key($this->aTableau) === end(array_keys($this->aTableau)) );
    }
	

	// méthodes implémentées pour l'interface IterateurBidir uniquement (la classe IterateurAbstrait l'implémente)
    /**
	 * Voir Iterateur#prec()
	 */
    function prec()
    {
    	prev($this->aTableau);
    }
    
    
    // méthodes implémentées pour l'interface IterateurComposite
    /**
     * Voir IterateurComposite#aEnfants()
     * 
     * @return	\c true si l'élément courant du tableau est lui même un tableau, \c false sinon
     */
    function aEnfants()
    {
    	return is_array($this->courant());
    }
    
    /**
     * Voir IterateurComposite#retIterateurEnfants()
     */
    function retIterateurEnfants()
    {
    	return new IterateurTableau($this->courant());
    }
}

OO::implemente('IterateurBidir');
OO::implemente('IterateurComposite');

?>