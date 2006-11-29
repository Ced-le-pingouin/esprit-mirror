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
 *
 * Contient une classe abstraite pour l'implémentation d'itérateurs en PHP 4 et +
 */

require_once(dirname(__FILE__).'/OO.php');

/**
 * Classe abstraite qui présente les méthodes requise pour implémenter un itérateur.
 * C'est également ici que se trouve la documentation sur le comportement attendu des implémentations.
 */
class Iterateur
{
	/**
	 * Constructeur. Celui de cette classe n'est pas opérationnel et provoque une erreur car il s'agit d'un semblant de
	 * classe abstraite/interface en PHP 4, qui ne devrait pas être utilisée ou instanciée telle quelle.
	 *
	 * Par contre, les sous-classes devraient, dans leur constructeur, initialiser l'objet à itérer, car l'itérateur
	 * devrait être positionné au début dès sa création
	 */
	function Iterateur()
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
     * Déplace l'itérateur vers l'avant
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
     *
     * @warning	Même remarque sur la performance que pour #taille()
     */
    function fin()
    {
    	$this->debut();

    	$iPosDernier = $this->taille() - 1;
    	for ($i = 0; $i < $iPosDernier; $i++)
			$this->suiv();
    }

    /**
     * Déplace l'itérateur vers l'arrière
     */
    function prec()
    {
    	OO::abstraite();
    }


    /**
     * Retourne le nombre d'éléments présents dans l'itérateur
     *
     * @return	le nombre d'éléments de l'itérateur
     *
     * @warning	l'implémentation par défaut de cette méthode, présente dans la classe abstraite Iterateur, parcourt
     * 			l'itérateur avec une boucle PHP pour accomplir sa tâche, ce qui peut être très très lent sur de grandes
     * 			"collections"; donc quand il est possible d'utiliser des fonctions natives directes ou plus rapides pour
     * 			implémenter cette méthode dans une sous-classe d'Iterateur, il est vivement conseillé de la redéfinir
     */
    function taille()
    {
		$CleSauvee = $this->cle();

		for ($this->debut(), $iTaille = 0; $this->estValide(); $this->suiv())
			++$iTaille;

		$this->rechercher($CleSauvee);

		return $iTaille;
    }

    /**
     * Déplace l'itérateur jusqu'à un emplacement donné en fonction d'une clé. Si cette dernière n'existe pas,
     * l'itérateur restera à la même position qu'avant l'appel
     *
     * @param	v_Cle	la clé utilisée pour déplacer le "pointeur" interne de l'itérateur
     *
     * @return	\c true si la clé recherchée existe bien dans la "collection" sur laquelle l'itérateur fonctionne,
     * 			\c false sinon
     *
     * @warning	Même remarque sur la performance que pour #taille()
     */
    function rechercher($v_Cle)
    {
    	$CleSauvee = $this->cle();

    	for ($this->debut(); $this->estValide(); $this->suiv())
    		if ($this->cle() === $v_Cle)
    			return TRUE;

		// si pas trouvé, on replace l'itérateur sur la position qu'il avait avant la recherche, SAUF si cette position
		// était identique en début de recherche à celle en fin => = position "invalide" car après le dernier élément
		// => si on demande à chercher cette position invalide, elle ne sera jamais trouvée, et provoquera un nouvel
		// appel à rechercher() à la fin de chaque appel rechercher() etc. => boucle infinie
		if ($this->cle() !== $CleSauvee)
			$this->rechercher($CleSauvee);
		return FALSE;
    }

    /**
     * Indique si l'élément courant de l'itérateur est le premier
     *
     * @return	\c true si l'itérateur est positionné sur le premier élément. Sinon \c false
     *
     * @warning	Même remarque sur la performance que pour #taille()
     */
    function estPremier()
    {
    	// on sauvegarde la clé actuelle de l'itérateur et on le place au début...
    	$CleSauvee = $this->cle();
    	$this->debut();

    	// ...pour vérifier si la clé actuelle était bien celle du premier élément
    	if ($this->cle() === $CleSauvee)
    	{
    		return TRUE;
    	}
    	// sinon, ça n'était pas le 1er élément, alors on replace l'itérateur dans sa position d'avant l'appel
    	else
    	{
    		$this->rechercher($CleSauvee);
    		return FALSE;
    	}
    }

    /**
     * Indique si l'élément courant de l'itérateur est le dernier
     *
     * @return	\c true si l'itérateur est positionné sur le dernier élément. Sinon \c false
     *
     * @warning	Même remarque sur la performance que pour #taille()
     */
    function estDernier()
    {
    	// on sauvegarde la clé actuelle de l'itérateur et on le place à la fin...
    	$CleSauvee = $this->cle();
    	$this->fin();

    	// ...pour vérifier si la clé actuelle était bien celle du dernier élément
    	if ($this->cle() === $CleSauvee)
    	{
    		return TRUE;
    	}
    	// sinon, ça n'était pas le dernier élément, alors on replace l'itérateur dans sa position d'avant l'appel
    	else
    	{
    		$this->rechercher($CleSauvee);
    		return FALSE;
    	}
    }
}

// n'est pas une interface, car certaines méthodes sont prédéfinies
OO::defClasseAbstraite();

?>