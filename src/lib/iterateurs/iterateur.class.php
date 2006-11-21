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
 * @file	iterateur.class.php
 * 
 * Contient une classe/interface pour l'implémentation d'itérateurs en PHP 4 et +
 */

require_once(dirname(__FILE__).'/../erreur.class.php');

/**
 * Classe "vide" qui présente les méthodes requise pour implémenter un itérateur. Si PHP 4 le permettait, cette classe 
 * devrait plutôt être une interface. Dans sa forme actuelle, elle servira juste de classe parente pour de véritables 
 * implémentations d'itérateurs en fonction de différents objets (par ex. un itérateur pour tableaux PHP). C'est 
 * également ici que la documentation sur le comportement attendu des implémentations se trouve.
 */
class CIterateur
{
	/**
	 * Constructeur. Celui de cette classe n'est pas opérationnel et provoque une erreur car il s'agit d'un semblant de 
	 * classe abstraite/interface en PHP 4, qui ne devrait pas être utilisée ou instanciée telle quelle.
	 * 
	 * Par contre, les sous-classes devraient, dans leur constructeur, initialiser l'objet à itérer, car l'itérateur 
	 * devrait être positionné au début dès sa création
	 */
	function CIterateur()
	{
		CErreur::provoquer(get_class($this).' est une classe abstraite, et ne devrait pas être instanciée');
	}
	
	/**
	 * Place l'itérateur sur le premier élément
	 */
    function debut()
    {
    	CErreur::provoquer('Méthode '.__FUNCTION__.'() non implémentée dans '.get_class($this));
    }

    /**
     * Déplace l'itérateur vers l'avant
     */
    function suivant()
    {
    	CErreur::provoquer('Méthode '.__FUNCTION__.'() non implémentée dans '.get_class($this));
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
    	CErreur::provoquer('Méthode '.__FUNCTION__.'() non implémentée dans '.get_class($this));
    }

    /**
     * Retourne la clé de l'élément courant de l'itérateur
     * 
     * @return	la clé de l'élément courant de l'itérateur
     */
    function cle()
    {
    	CErreur::provoquer('Méthode '.__FUNCTION__.'() non implémentée dans '.get_class($this));
    }

    /**
     * Retourne l'élément courant de l'itérateur
     * 
     * @return	l'élément courant de l'itérateur
     */
    function courant()
    {
    	CErreur::provoquer('Méthode '.__FUNCTION__.'() non implémentée dans '.get_class($this));
    }

    /**
     * Place l'itérateur sur le dernier élément
     * 
     * @return	le dernier élément de l'itérateur
     */
    function fin()
    {
    	CErreur::provoquer('Méthode '.__FUNCTION__.'() non implémentée dans '.get_class($this));
    }
    
    
    /**
     * Indique si cet itérateur supporte la méthode #precedent()
     * 
     * @return	doit retourner \c true si l'itérateur (sous-classe) supporte la méthode #precedent(), \c false sinon 
     * 			(par défaut).
     * 
     * @note 	Si vous sous-classez l'itérateur et que vous définissez une méthode #precedent(), n'oubliez pas 
     * 			d'également redéfinir la présente méthode pour qu'elle retourne \c true. Sinon, il n'est pas nécessaire 
     * 			de la redéfinir
     */
    function supportePrecedent()
    {
    	return FALSE;
    }
    
    /**
     * Déplace l'itérateur vers l'arrière
     */
    function precedent()
    {
    	CErreur::provoquer('Méthode '.__FUNCTION__.'() non implémentée dans '.get_class($this));
    }


    /**
     * Indique si cet itérateur supporte la méthode #taille()
     * 
     * @return	doit retourner \c true si l'itérateur (sous-classe) supporte la méthode #taille(), \c false sinon 
     * 			(par défaut).
     * 
     * @note 	même remarque que pour #supportePrecedent()
     */
	function supporteTaille()
	{
		return FALSE;
	}

    /**
     * Retourne le nombre d'éléments présents dans l'itérateur
     * 
     * @return	le nombre d'éléments de l'itérateur
     */
    function taille()
    {
    	CErreur::provoquer('Méthode '.__FUNCTION__.'() non implémentée dans '.get_class($this));
    }

    /**
     * Indique si cet itérateur supporte la méthode #rechercher()
     * 
     * @return	doit retourner \c true si l'itérateur (sous-classe) supporte la méthode #rechercher(), \c false sinon 
     * 			(par défaut).
     * 
     * @note 	même remarque que pour #supportePrecedent()
     */
    function supporteRechercher()
    {
    	return FALSE;
    }
    
    /**
     * Déplace l'itérateur jusqu'à un emplacement donné en fonction d'une clé
     * 
     * @param	v_Cle	la clé utilisée pour déplacer le "pointeur" interne de l'itérateur
     */
    function rechercher($v_Cle)
    {
    	CErreur::provoquer('Méthode '.__FUNCTION__.'() non implémentée dans '.get_class($this));
    }


    /**
     * Indique si cet itérateur supporte la méthode #estPremier()
     * 
     * @return	doit retourner \c true si l'itérateur (sous-classe) supporte la méthode #estPremier(), \c false sinon 
     * 			(par défaut).
     * 
     * @note 	même remarque que pour #supportePrecedent()
     */
    function supporteEstPremier()
    {
    	return FALSE;
    }

    /**
     * Indique si l'élément courant de l'itérateur est le premier
     * 
     * @return	\c true si l'itérateur est positionné sur le premier élément. Sinon \c false
     */
    function estPremier()
    {
    	CErreur::provoquer('Méthode '.__FUNCTION__.'() non implémentée dans '.get_class($this));
    }
    
    /**
     * Indique si cet itérateur supporte la méthode #estDernier()
     * 
     * @return	doit retourner \c true si l'itérateur (sous-classe) supporte la méthode #estDernier(), \c false sinon 
     * 			(par défaut).
     * 
     * @note 	même remarque que pour #supportePrecedent()
     */
    function supporteEstDernier()
    {
    	return FALSE;
    }

    /**
     * Indique si l'élément courant de l'itérateur est le dernier
     * 
     * @return	\c true si l'itérateur est positionné sur le dernier élément. Sinon \c false
     */
    function estDernier()
    {
    	CErreur::provoquer('Méthode '.__FUNCTION__.'() non implémentée dans '.get_class($this));
    }


	/** @name Alias de méthodes existantes : les méthodes ci-dessous ne DOIVENT PAS ETRE REDEFINIES */
	//@{    
    /**
     * Alias pour #precedent(). Ne doit pas être redéfini par les sous-classe (c'est automatique)
     */
    function prec()
    {
    	$this->precedent();
    }

    /**
     * Alias pour #suivant(). Ne doit pas être redéfini par les sous-classe (c'est automatique)
     */
    function suiv()
    {
    	$this->suivant();
    }
    
    /**
     * Alias pour #chercher(). Ne doit pas être redéfini par les sous-classe (c'est automatique)
     */
    function aller($v_Cle)
    {
    	$this->rechercher($v_Cle);
    }
    //@}
}

?>