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
 * @file	IterateurAbstrait.php
 */

require_once(dirname(__FILE__).'/OO.php');
require_once(dirname(__FILE__).'/IterateurBidir.php');

/**
 * Classe abstraite qui implémente quelques méthodes requises par un itérateur. Elle se base sur l'interface
 * IterateurBidir
 */
class IterateurAbstrait
{
	/**
	 * Constructeur. Celui de cette classe n'est pas opérationnel et provoque une erreur car il s'agit d'une classe
	 * abstraite, qui ne devrait pas être utilisée ou instanciée telle quelle.
	 *
	 * Par contre, les sous-classes devraient, dans leur constructeur, initialiser l'objet à itérer, car l'itérateur
	 * devrait être positionné au début dès sa création
	 */
	function IterateurAbstrait()
	{
		OO::abstraite();
	}

	/**
	 * Voir Iterateur#debut()
	 */
    function debut()
    {
    	OO::abstraite();
    }

    /**
     * Voir Iterateur#suiv()
     */
    function suiv()
    {
    	OO::abstraite();
    }

    /**
     * Voir Iterateur#estValide()
     */
    function estValide()
    {
    	OO::abstraite();
    }

    /**
     * Voir Iterateur#cle()
     */
    function cle()
    {
    	OO::abstraite();
    }

    /**
     * Voir Iterateur#courant()
     */
    function courant()
    {
    	OO::abstraite();
    }

    /**
     * Voir Iterateur#fin()
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
     * Voir Iterateur#taille()
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
     * Voir Iterateur#rechercher()
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
     * Voir Iterateur#estPremier()
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
     * Voir Iterateur#estDernier()
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

    /**
     * Voir IterateurBidir#prec()
     */
    function prec()
    {
    	OO::abstraite();
    }
}

// propose un itérateur avec des méthodes prédéfinies, mais n'implémente pas tout => classe abstraite
OO::defClasseAbstraite();
OO::implemente('IterateurBidir');

?>