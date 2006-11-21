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
 * @file	iterateur_dossier.class.php
 * 
 * Contient une classe/interface pour l'implémentation d'itérateurs de dossiers (répertoires)
 */

require_once(dirname(__FILE__).'/../erreur.class.php');
require_once(dirname(__FILE__).'/iterateur.class.php');
require_once(dirname(__FILE__).'/../systeme_fichiers/fichier_info.class.php');

/**
 * Sous-classe de CIterateur, qui permet d'effectuer des itérations sur un dossier (itérateur en lecture seule)
 * 
 * @note	Pour le moment, cet itérateur hérite directement de CIterateurTableau, car grâce à la fonction glob() de 
 * 			PHP, on peut directement ramener sous forme de tableau la liste des fichiers/dossiers d'un dossier, et donc 
 * 			se passer de opendir() et readdir(), qui auraient empêché une implémentation simple de #precedent(), ou de 
 * 			#taille()
 */
class CIterateurDossier extends CIterateurTableau
{
	var $oDossier;          ///< L'objet CFichierInfo qui représente le dossier dont le chemin a été passé en paramètre au constructeur
	var $oFichierCourant;   ///< L'objet CFichierInfo qui représente l'élément courant de l'itérateur
	
	/**
	 * Constructeur
	 * 
	 * @param	le dossier sur lequel on effectuera l'itération
	 */
	function CIterateurDossier($v_sChemin, $v_sFiltre = '*')
	{
		if (!is_dir($v_sChemin) || !is_readable($v_sChemin))
			CErreur::provoquer(__FUNCTION__."(): le chemin fourni ne représente pas un dossier valide, ou le dossier".
			                   " est inaccessible", CERREUR_AVERT);
			                   
		$this->oDossier = new CFichierInfo($v_sChemin);
		$asFichiers = glob($this->oDossier->formerChemin($v_sFiltre), GLOB_NOSORT);
		if (!is_array($asFichiers))
			$asFichiers = array(); 
		parent::CIterateurTableau($asFichiers);
	}
	
    /**
	 * Voir CIterateur#courant()
	 */
    function courant()
    {
    	$sCheminFichierCourant = parent::courant();
    	
    	if (!isset($this->oFichierCourant))
    		$this->oFichierCourant = new CFichierInfo($sCheminFichierCourant);
    	else if (strcmp($this->oFichierCourant->retChemin(), $sCheminFichierCourant) != 0)
    		$this->oFichierCourant->defChemin($sCheminFichierCourant);
    	
    	return $this->oFichierCourant;
    }

}

?>