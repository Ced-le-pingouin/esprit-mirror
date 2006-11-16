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
 * @file	iterateur_tableau.class.php
 * 
 * Contient une classe/interface pour l'implémentation d'itérateurs de tableaux en PHP 4 et +
 */

require_once(dirname(__FILE__).'/../erreur.class.php');
require_once(dirname(__FILE__).'/iterateur.class.php');

/**
 * Sous-classe de CIterateur, qui permet d'effectuer des itérations sur un tableau (itérateur en lecture seule)
 * 
 * @note	Cet itérateur n'est pas récursif, càd que si l'un des éléments contenu est lui-même un tableau, il ne sera 
 * 			pas automatiquement parcouru par les fonctions #next() et autres de la classe, il sera retourné tel quel
 * 			(sous forme de tableau, donc) par la fonction #courant()
 */
class CIterateurTableau extends CIterateur
{
	var $aTableau; ///< Le tableau sur lequel aura lieu l'itération, et qui est passé au constructeur
	
	/**
	 * Constructeur
	 * 
	 * @param	le tableau sur lequel on effectuera l'itération
	 */
	function CIterateurTableau($v_aTableau)
	{
		if (!is_array($v_aTableau))
			CErreur::provoquer(__FUNCTION__."(): l'objet n'est pas un tableau");
			
		$this->aTableau = $v_aTableau;
		
		$this->debut();
	}
	
	/**
	 * Voir CIterateur#debut()
	 */
	function debut()
	{
		reset($this->aTableau);
	}
	
	/**
	 * Voir CIterateur#fin()
	 */
    function fin()
    {
    	end($this->aTableau);
    }
    
    /**
	 * Voir CIterateur#precedent()
	 */
    function precedent()
    {
    	prev($this->aTableau);
    }
    
    /**
	 * Voir CIterateur#suivant()
	 */
    function suivant()
    {
    	next($this->aTableau);
    }
    
    /**
	 * Méthode non implémentée pour les tableaux actuellement. Voir CIterateur#rechercher()
	 */
    function rechercher($v_Cle)
    {
    	parent::rechercher($v_Cle);
    }

    /**
	 * Voir CIterateur#estValide()
	 */
    function estValide()
    {
    	// si le pointeur de tableau est invalide, normalement la fonction key() retourne NULL, donc on utilise ça pour 
    	// déterminer si l'itérateur est dans une position valide. Ce comportement de key() n'est toutefois toujours 
    	// pas documenté dans le manuel en ligne de PHP...
    	return ( !is_null( key($this->aTableau) ) ) ;
    }
    
    /**
	 * Voir CIterateur#estPremier()
	 */
    function estPremier()
    {
    	return ( key($this->aTableau) === reset(array_keys($this->aTableau)) );
    }
    
    /**
	 * Voir CIterateur#estDernier()
	 */
    function estDernier()
    {
    	return ( key($this->aTableau) === end(array_keys($this->aTableau)) );
    }

    /**
	 * Voir CIterateur#courant()
	 */
    function courant()
    {
    	return current($this->aTableau);
    }
    
    /**
	 * Voir CIterateur#cle()
	 */
    function cle()
    {
    	return key($this->aTableau);
    }
    
    /**
	 * Voir CIterateur#taille()
	 */
    function taille()
    {
    	return count($this->aTableau);
    }
}

?>