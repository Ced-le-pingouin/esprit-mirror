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
 * @file	Erreur.php
 * 
 * Contient un classe de gestion des erreurs
 */

/** 
 * @name Constantes - types d'erreurs
 * 
 * Celles-ci reprennent les constantes \c E_USER_ de PHP. Plus tard, il faudrait qu'elles deviennent des constantes 
 * de classe en laissant tomber le préfixe \c CERREUR_ au passage, mais les constantes de classe n'existent qu'en PHP 5 
 */
//@{
define("CERREUR_FATALE", E_USER_ERROR);   /// l'erreur correspond aux erreurs fatales de PHP, elle provoquera donc l'arrêt du script                @enum CERREUR_FATALE
define("CERREUR_AVERT" , E_USER_WARNING); /// l'erreur correspond aux warnings de PHP => pas d'arrêt du script mais elles sont affichées par défaut @enum CERREUR_AVERT
define("CERREUR_NOTE"  , E_USER_NOTICE);  /// l'erreur correspond aux notices de PHP => je pense que par défaut elles ne sont pas affichées         @enum CERREUR_NOTE 
//@}

/**
 * Classe de gestion des erreurs. Pour l'instant, cette classe est minimale et permet seulement de "provoquer" une 
 * erreur dans du code perso, étant donné qu'en PHP 4 les exceptions n'existent pas
 */
class Erreur
{
	var $sTexte;  ///< Le texte associé à l'objet erreur/avertissement/note créé par le constructeur
	var $iNiveau; ///< Le niveau de gravité (erreur/avertissement/note) de l'objet créé par le constructeur
	
	/**
	 * Constructeur. La plupart des fonctions de cette classe sont des fonctions statiques, mais on peut tout de même
	 * instancier des objets de la classe, pour simuler différent types d'erreurs et les utiliser par exemple comme 
	 * valeur de retour, pour ensuite les tester grâce à la fonction #estErreur() (tout comme on peut en PHP 5 créer 
	 * des objets de type \c Exception)
	 * 
	 * @param	v_sTexte	le texte qui décrit l'erreur
	 * @param	v_iNiveau	le type d'erreur (voir constantes \c CERREUR_). Par défaut, il s'agit d'une erreur de type 
	 * 						\c CERREUR_FATALE (contrairement à PHP, ou l'erreur par défaut pour \c trigger_error() est 
	 * 						de type \c E_USER_NOTICE, donc de moindre importance) 
	 */	
	function Erreur($v_sTexte, $v_iNiveau = CERREUR_FATALE)
	{
		$this->sTexte  = $v_sTexte;
		$this->iNiveau = $v_iNiveau;
	}
	
	/**
	 * Provoque une erreur grâce à une fonction native de PHP
	 * 
	 * @param	v_sTexte	le texte qui décrit l'erreur
	 * @param	v_iNiveau	le type d'erreur (voir constantes \c CERREUR_). Par défaut, il s'agit d'une erreur de type 
	 * 						\c CERREUR_FATALE (contrairement à PHP, ou l'erreur par défaut pour \c trigger_error() est 
	 * 						de type \c E_USER_NOTICE, donc de moindre importance)
	 * 
	 * @note	Cette fonction est censée être statique (méthode de classe, pas d'instance)
	 */
	function provoquer($v_sTexte, $v_iNiveau = CERREUR_FATALE)
	{
		// on retrouve d'où vient l'appel, et on remonte d'un niveau s'il vient de la classe OO, car cette dernière est 
		// également censée afficher les messages d'erreurs concernant la classe qui l'appelle 
		$asTraces = debug_backtrace();
		$iAppelant = 1;
		if (strcasecmp($asTraces[$iAppelant]['class'], 'Objet') == 0)
			$iAppelant++;
		$sFichier  = $asTraces[$iAppelant-1]['file'];
		$iLigne    = $asTraces[$iAppelant-1]['line'];
		$sClasse   = $asTraces[$iAppelant]['class'];
		$sFonction = $asTraces[$iAppelant]['function'];
		
		// on affichera les infos dont on dispose sur le fichier, la ligne, la classe, et la fonction où s'est produite 
		// l'erreur
		$sInfosFichier = basename($sFichier).', ligne '.$iLigne;
		if (!empty($sClasse))
			$sClasse .= '::';
		if (!empty($sFonction))
			$sFonction .= '()';
			
		switch($v_iNiveau)
		{
			case CERREUR_FATALE:
				echo '<strong>',
				     'Erreur: ',
				     '<em>',
				     $sInfosFichier, ': ',
				     $sClasse,
				     $sFonction, ': ',
				     '</em>',
				     '</strong>',
				     $v_sTexte,
				     '<br />';
				die();
				break;
			
			case CERREUR_AVERT:
				echo '<strong>',
				     'Attention: ',
			         '<em>',
				     $sInfosFichier, ': ',
				     $sClasse,
				     $sFonction, ': ',
				     '</em>',
			         '</strong> ',
				     $v_sTexte,
				     '<br />';
				break;
				
			default:
				// pour l'instant, on ne fait rien si l'erreur est de type "note" (comme dans PHP avec le type E_NOTICE)
		}
		
		// pour l'instant, on n'utilise pas le système de gestion d'erreurs natif de PHP. Plus tard il faudrait le 
		// faire, et redéfinir automatiquement un gestionnaire perso dès le premier appel à la présente méthode 
		// (possible aussi de garder l'ancien gestionnaire de PHP pour y faire appel si l'erreur ne nous "intéresse" 
		// pas?)
		
		//trigger_error($v_sTexte, $v_iNiveau);
	}
	
	/**
	 * Vérifie qu'un objet donné est de type "erreur" (appartient à cette classe)
	 * 
	 * @param	l'objet dont il faut vérifier s'il s'agit d'un objet "erreur"
	 * 
	 * @return	\c true si l'objet est de type "erreur", \c false sinon
	 * 
	 * @note	Cette fonction est censée être statique
	 */
	function estErreur($v_oObjet)
	{
		// utilisation de is_a() au lieu de get_class(), pour détecter non seulement les objets de classe Erreur, mais 
		// également d'éventuelles sous-classes (erreurs plus précises, comme pour les exceptions PHP 5 ?)
		// Si cette classe survit à un passage exlusif du code à la version 5+ de PHP, is_a() sera peut-être obsolète 
		// et devra être remplacée par l'opérateur instanceof (qui teste pour une classe, sous-classe, ou interface)
		return ( is_a($v_oObjet, __CLASS__) ) ;
	}
}

?>