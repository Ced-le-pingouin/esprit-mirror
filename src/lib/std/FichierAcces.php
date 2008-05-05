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
 * @file	FichierAcces.php
 */

require_once(dirname(__FILE__).'/Erreur.php');
require_once(dirname(__FILE__).'/FichierInfo.php');

/** Constantes - Modes d'ouverture des fichiers (lecture, ecriture, etc) */
//@{
define('FICHIER_MODE_LECTURE' , 'rb'); ///< Le fichier sera ouvert en lecture seule				@enum FICHIER_MODE_LECTURE
define('FICHIER_MODE_ECRITURE', 'wb'); ///< Le fichier sera ouvert en écriture seule			@enum FICHIER_MODE_ECRITURE
define('FICHIER_MODE_AJOUT'   , 'ab'); ///< Le fichier sera ouvert en ajout (écriture seule)	@enum FICHIER_MODE_AJOUT
//@}

/**
 * Classe qui permet d'effectuer des opérations physiques sur le contenu des fichiers, càd ouvrir, fermer, lire, 
 * écrire, etc
 * 
 * @todo	Pour le moment, seul les méthodes dont j'ai eu besoin sont implémentées, il faudra compléter la classe au 
 * 			fur et à mesure
 */
class FichierAcces extends FichierInfo
{
	var $hFichier = NULL; ///< Le handle pour la gestion du fichier
	
	/**
	 * voir FichierInfo#defChemin(). Cette surcharge de la méthode d'origine ferme le fichier représenté par l'objet 
	 * s'il était ouvert, avant de modifier le chemin de l'objet
	 */
	function defChemin($v_sChemin)
	{
		if ($this->estOuvert())
			$this->fermer();
			
		$this->hFichier = NULL;
		
		parent::defChemin($v_sChemin);
	}
	
	/**
	 * Ouvre le fichier représenté par l'objet courant pour pouvoir y effectuer des opérations de lecture ou écriture
	 * 
	 * @param	v_iMode	le mode d'ouverture du fichier, qui doit être l'une des constantes \c FICHIER_MODE_...
	 * 
	 * @return	le handle du fichier ouvert, ou \c false si l'ouverture a échoué
	 */
	function ouvrir($v_iMode)
	{
		$h = fopen($this->retChemin(), $v_iMode);
		if ($h !== FALSE)
			$this->hFichier = $h;
		
		return $h;
	}
	
	/**
	 * Ferme le fichier représenté par l'objet courant
	 * 
	 * @return	\c true si la fermeture s'est bien déroulée, \c false en cas d'erreur
	 */
	function fermer()
	{
		return fclose($this->hFichier);
	}
	
	/**
	 * Ecrit des données en une seule fois dans le fichier représenté par l'objet courant. Le fichier ne doit pas être 
	 * ouvert préalablement
	 */
 	function ecrireTout($v_sContenu)
 	{
 		if ($this->existe())
			Erreur::provoquer("Le fichier existe déjà");

		$this->ouvrir(FICHIER_MODE_ECRITURE);
		$iNbOctets = $this->ecrire($v_sContenu);
		$this->fermer();
		
		return $iNbOctets;
 	}
 	
 	/**
 	 * Ecrit des données dans le fichier représenté par l'objet. Ce fichier doit être ouvert
 	 * 
 	 * @param	les données à écrire dans le fichier, sous forme de chaîne;
 	 * 
 	 * @return	le nombre d'octets effectivement écrits dans le fichier, ou \c false si une erreur se produit
 	 */
 	function ecrire($v_sContenu)
 	{
 		return fwrite($this->hFichier, $v_sContenu);
 	}
 	
 	/**
 	 * Indique si le fichier actuellement représenté par l'objet est ouvert
 	 * 
 	 * @return	\c true si le fichier est ouvert, \c false sinon
 	 */
 	function estOuvert()
 	{
 		return (!is_null($this->hFichier));
 	}
}

?>
