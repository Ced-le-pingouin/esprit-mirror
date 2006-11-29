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
 * @file	Iterateur.php
 */

require_once(dirname(__FILE__).'/OO.php');

/**
 * Interface qui définit les méthodes requises par un itérateur "unidirectionnel" simple.
 * C'est également ici que se trouve la documentation sur le comportement attendu des implémentations.
 */
class Iterateur
{
	/**
	 * Constructeur
	 *
	 * @param	v_oObjetAIterer	l'objet sur lequel aura lieu l'itération. Son type dépendra bien entendu de
	 * 			l'implémentation
	 *
	 * @note	Selon l'implémentation, des paramètres supplémentaires pourront être acceptés ou requis
	 */
	function Iterateur($v_oObjetAIterer)
	{
		OO::abstraite();
	}

	/**
	 * Place l'itérateur sur le premier élément
	 */
    function debut()
    {
    	OO::abstraite();
    }

    /**
     * Déplace l'itérateur d'une position vers l'avant
     */
    function suiv()
    {
    	OO::abstraite();
    }

    /**
     * Indique si la position courante de l'itérateur est valide, càd si un élément s'y trouve. Cette fonction devrait
     * toujours être appelée avant de récupérer une valeur ou une clé, afin de déterminer si le "pointeur" de
     * l'itérateur est dans une position valide
     *
     * @return	\c true si la position courante de l'itérateur contient bien un élément valide. Sinon \c false
     */
    function estValide()
    {
    	OO::abstraite();
    }

    /**
     * Retourne la clé de l'élément courant de l'itérateur
     *
     * @return	la clé de l'élément courant de l'itérateur
     */
    function cle()
    {
    	OO::abstraite();
    }

    /**
     * Retourne l'élément courant de l'itérateur
     *
     * @return	l'élément courant de l'itérateur
     */
    function courant()
    {
    	OO::abstraite();
    }

    /**
     * Place l'itérateur sur le dernier élément
     */
    function fin()
    {
		OO::abstraite();
    }

    /**
     * Retourne le nombre d'éléments présents dans l'itérateur
     *
     * @return	le nombre d'éléments de l'itérateur
     */
    function taille()
    {
		OO::abstraite();
    }

    /**
     * Déplace l'itérateur jusqu'à un emplacement donné en fonction d'une clé. Si cette dernière n'existe pas,
     * l'itérateur restera à la même position qu'avant l'appel
     *
     * @param	v_Cle	la clé utilisée pour déplacer le "pointeur" interne de l'itérateur
     *
     * @return	\c true si la clé recherchée existe bien dans la "collection" sur laquelle l'itérateur fonctionne,
     * 			\c false sinon
     */
    function rechercher($v_Cle)
    {
		OO::abstraite();
    }

    /**
     * Indique si l'élément courant de l'itérateur est le premier
     *
     * @return	\c true si l'itérateur est positionné sur le premier élément. Sinon \c false
     */
    function estPremier()
    {
		OO::abstraite();
    }

    /**
     * Indique si l'élément courant de l'itérateur est le dernier
     *
     * @return	\c true si l'itérateur est positionné sur le dernier élément. Sinon \c false
     */
    function estDernier()
    {
		OO::abstraite();
    }
}

OO::defInterface();

?>