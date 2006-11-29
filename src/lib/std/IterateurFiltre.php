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
 * @file	IterateurFiltre.php
 */

require_once(dirname(__FILE__).'/OO.php');
require_once(dirname(__FILE__).'/Iterateur.php');

/**
 * Classe abstraite qui sert de filtre (sous forme de décorateur) à des objets dérivés de Iterateur.
 *
 * La méthode abstraite #accepter() doit être redéfinie dans les sous-classes, de façon à déterminer si un élément est
 * considéré comme "acceptable" ou pas selon le filtre utilisé.
 *
 * L'algorithme utilisant #accepter() pour retourner ou
 * pas un élément de l'itérateur par la méthode #courant() est quant à lui implémenté ici, et ne doit normalement pas
 * être modifié
 */
class IterateurFiltre
{
	var $oItr; ///< l'itérateur interne qui sera réellement utilisé (déplacé etc.) avant filtrage par la présente classe

	/**
	 * Constructeur
	 *
	 * @param	v_oItr	l'itérateur (déjà créé) qui sera parcouru en utilisant le filtre
	 */
	function IterateurFiltre($v_oItr)
	{
		// la méthode en elle-même n'est pas abstraite, mais la classe doit l'être, et donc non-instanciable
		OO::abstraite();

		$this->oItr = $v_oItr;
	}

	/**
	 * Retourne l'itérateur interne, passé au constructeur lors de la création de l'objet
	 *
	 * @return	l'itérateur filtré
	 */
	function retIterateurInterne()
	{
		return $this->oItr;
	}

	/**
	 * Méthode abstraite qui devra être surchargée dans les sous-classes, afin de définir si l'élément courant est
	 * "acceptable" ou pas selon le filtre voulu
	 *
	 * @return	\c true si l'élément courant est accepté par le filtre, \c false sinon
	 */
    function accepter()
    {
		OO::abstraite();
    }

	/**
	 * Avance l'itérateur interne jusqu'au prochain élément qui soit valide (existant) ET accepté par le filtre utilisé
	 */
    function trouverValideSuiv()
    {
    	while ($this->oItr->estValide())
    	{
    		if ($this->accepter())
    			return;

    		$this->oItr->suiv();
    	}
    }

	/**
	 * Voir Iterateur#debut()
	 */
	function debut()
	{
		$this->oItr->debut();
		$this->trouverValideSuiv();
	}

    /**
	 * Voir Iterateur#suiv()
	 */
    function suiv()
    {
    	$this->oItr->suiv();
    	$this->trouverValideSuiv();
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
    	$this->trouverValideSuiv();
    }

	/**
	 * Voir Iterateur#taille()
	 *
     * @warning	Même remarque sur la performance que pour Iterateur#taille()
	 */
	function taille()
	{
		$CleSauvee = $this->oItr->cle();

		for ($this->debut(), $iTaille = 0; $this->estValide(); $this->suiv())
			++$iTaille;

		$this->oItr->rechercher($CleSauvee);

		return $iTaille;
	}

    /**
	 * Voir Iterateur#rechercher()
	 *
	 * @note	Si l'élément recherché par rapport à sa clé est trouvé, mais qu'il ne satisfait pas le filtre,
	 * 			l'itérateur restera dans sa position actuelle et la méthode retournera \c false
	 */
    function rechercher($v_Cle)
    {
    	$CleSauvee = $this->oItr->cle();

    	$this->oItr->rechercher($v_Cle);
    	if ($this->accepter())
    		return TRUE;

   		$this->oItr->rechercher($CleSauvee);
   		return FALSE;
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