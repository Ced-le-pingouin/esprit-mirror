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
 * @file	IterateurDecorateur.php
 */

require_once(dirname(__FILE__).'/OO.php');
require_once(dirname(__FILE__).'/Iterateur.php');

/**
 * Classe abstraite qui sert de base pour des décorateurs d'Iterateur. Elle n'a pas d'utilité tant qu'elle n'est pas 
 * sous-classée, mais elle permet de ne pas recopier dans chaque décorateur des "$this->oItr->...()" pour transférer 
 * chaque appel du décorateur vers son itérateur interne (par exemple IterateurFiltre dérive de cette classe)
 */
class IterateurDecorateur
{
	var $oItr; ///< l'itérateur interne qui sera réellement utilisé (déplacé etc.)

	/**
	 * Constructeur
	 *
	 * @param	v_oItr	l'itérateur (déjà créé) qui sera parcouru en utilisant le décorateur
	 */
	function IterateurDecorateur($v_oItr)
	{
		// la méthode en elle-même n'est pas abstraite, mais la classe doit l'être, et donc non-instanciable
		OO::abstraite();

		$this->oItr = $v_oItr;
	}

	/**
	 * @return	l'itérateur interne, passé au constructeur lors de la création de l'objet
	 */
	function retIterateurInterne()
	{
		return $this->oItr;
	}
	
	// méthodes requises par l'interface Iterateur
	/**
	 * Voir Iterateur#debut()
	 */
	function debut()
	{
		$this->oItr->debut();
	}
	
    /**
	 * Voir Iterateur#suiv()
	 */
    function suiv()
    {
    	$this->oItr->suiv();
    }

    /**
	 * Voir Iterateur#estValide()
	 */
    function estValide()
    {
		return $this->oItr->estValide();
    }

    /**
	 * Voir Iterateur#cle()
	 */
    function cle()
    {
    	return $this->oItr->cle();
    }

    /**
	 * Voir Iterateur#courant()
	 */
    function courant()
    {
    	return $this->oItr->courant();
    }

	/**
	 * Voir Iterateur#fin()
	 */
    function fin()
    {
    	$this->oItr->fin();
    }

	/**
	 * Voir Iterateur#taille()
	 */
	function taille()
	{
		return $this->oItr->taille();
	}

    /**
	 * Voir Iterateur#rechercher()
	 *
	 * @note	Si l'élément recherché par rapport à sa clé est trouvé, mais qu'il ne satisfait pas le filtre,
	 * 			l'itérateur restera dans sa position actuelle et la méthode retournera \c false
	 */
    function rechercher($v_Cle)
    {
    	return $this->oItr->rechercher();
    }

    /**
	 * Voir Iterateur#estPremier()
	 */
    function estPremier()
    {
    	return $this->oItr->estPremier();
    }

    /**
	 * Voir Iterateur#estDernier()
	 */
    function estDernier()
    {
    	return $this->oItr->estDernier();
    }
}

OO::defClasseAbstraite();
OO::implemente('Iterateur');

?>
