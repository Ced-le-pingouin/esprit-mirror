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
 *
 * Contient une classe/interface pour l'implémentation d'itérateurs de tableaux en PHP 4 et +
 */

require_once(dirname(__FILE__).'/Iterateur.php');
require_once(dirname(__FILE__).'/Erreur.php');

/**
 * Sous-classe de Iterateur, qui permet d'effectuer des itérations sur un tableau (itérateur en lecture seule)
 *
 * @note	Cet itérateur n'est pas récursif, càd que si l'un des éléments contenu est lui-même un tableau, il ne sera
 * 			pas automatiquement parcouru par les fonctions #next() et autres de la classe, il sera retourné tel quel
 * 			(sous forme de tableau, donc) par la fonction #courant()
 */
class IterateurTableau extends Iterateur
{
	var $aTableau; ///< Le tableau sur lequel aura lieu l'itération, et qui est passé au constructeur

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
	 * Voir Iterateur#prec()
	 */
    function prec()
    {
    	prev($this->aTableau);
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
}

?>