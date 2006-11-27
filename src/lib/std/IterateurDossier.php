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
 * @file	IterateurDossier.php
 * 
 * Contient une classe/interface pour l'implémentation d'itérateurs de dossiers (répertoires)
 */

require_once(dirname(__FILE__).'/Erreur.php');
require_once(dirname(__FILE__).'/IterateurTableau.php');
require_once(dirname(__FILE__).'/FichierInfo.php');

/**
 * Sous-classe de Iterateur, qui permet d'effectuer des itérations sur un dossier (itérateur en lecture seule)
 * 
 * @note	Pour le moment, cet itérateur hérite directement de IterateurTableau, car grâce à la fonction glob() de 
 * 			PHP, on peut directement ramener sous forme de tableau la liste des fichiers/dossiers d'un dossier, et donc 
 * 			se passer de opendir() et readdir(), qui auraient empêché une implémentation simple de #precedent(), ou de 
 * 			#taille()
 */
class IterateurDossier extends IterateurTableau
{
	var $sFiltrePre;        ///< La chaîne qui contient le filtre passé au constructeur pour restreindre la recherche des fichiers/dossiers
	var $oDossier;          ///< L'objet CFichierInfo qui représente le dossier dont le chemin a été passé en paramètre au constructeur
	var $oFichierCourant;   ///< L'objet CFichierInfo qui représente l'élément courant de l'itérateur
	
	/**
	 * Constructeur
	 * 
	 * @param	v_sChemin	le dossier sur lequel on effectuera l'itération
	 * @param	v_sFiltre	le filtre à utiliser pour ne ramener que certains fichiers spécifiques. Ce filtre est celui 
	 * 						utilisé par la fonction native PHP glob()
	 * 
	 * @note	Contrairement aux filtres d'itérateurs (IterateurFiltre et sous-classes), le filtre agit ici 
	 * 			directement, avant que les éléments de l'itérateur ne soient trouvés, alors que les filtres d'itérateurs 
	 * 			agissent pendant l'itération, pour déterminer à chaque élément s'il est accepté ou pas.
	 * 			Le filtre disponible ici peut donc avoir un effet sur la #taille(), tandis que les filtres d'itérateurs 
	 * 			n'en ont aucun
	 */
	function IterateurDossier($v_sChemin, $v_sFiltrePre = '*')
	{
		if (!is_dir($v_sChemin) || !is_readable($v_sChemin))
			Erreur::provoquer("Le chemin fourni ne représente pas un dossier valide, ou le dossier est inaccessible",
			                   CERREUR_AVERT);
		
		$this->sFiltrePre = $v_sFiltrePre;
		$this->oDossier = new FichierInfo($v_sChemin);
		$asFichiers = glob($this->oDossier->formerChemin($v_sFiltrePre), GLOB_NOSORT);
		if (!is_array($asFichiers))
			$asFichiers = array(); 
		parent::IterateurTableau($asFichiers);
	}
	
    /**
	 * Voir Iterateur#courant()
	 */
    function courant()
    {
    	$sCheminFichierCourant = parent::courant();
    	
    	if (!isset($this->oFichierCourant))
    		$this->oFichierCourant = new FichierInfo($sCheminFichierCourant);
    	else if (strcmp($this->oFichierCourant->retChemin(), $sCheminFichierCourant) != 0)
    		$this->oFichierCourant->defChemin($sCheminFichierCourant);
    	
    	return $this->oFichierCourant;
    }
	
	/**
	 * Retourne le filtre passé au constructeur (ou celui par défaut)
	 * 
	 * @return	le filtre passé au constructeur pour restreindre les fichiers/dossiers pris en compte par l'itérateur
	 */
	function retFiltrePre()
	{
		return $this->sFiltrePre;
	}
}

?>