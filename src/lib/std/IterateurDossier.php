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
 */

require_once(dirname(__FILE__).'/Erreur.php');
require_once(dirname(__FILE__).'/IterateurTableau.php');
require_once(dirname(__FILE__).'/FichierInfo.php');

/**
 * Classe d'itérateur permettant d'effectuer des itérations sur un dossier
 *
 * @note	Pour le moment, cet itérateur hérite directement d'IterateurTableau, car grâce à la fonction glob() de PHP,
 * 			on peut directement ramener sous forme de tableau la liste des fichiers/dossiers d'un dossier, et donc se
 * 			passer de opendir() et readdir(), qui auraient empêché une implémentation simple de #prec(), ou de #taille()
 */
class IterateurDossier extends IterateurTableau
{
	var $oDossier;          ///< L'objet FichierInfo qui représente le dossier dont le chemin a été passé en paramètre au constructeur
	var $sFiltrePre;        ///< La chaîne qui contient le filtre passé au constructeur pour restreindre la recherche des fichiers/dossiers
	var	$bTrier;			///< L'indication du tri alphabétique demandé ou pas à l'initialisation de l'objet
	var $bDossiersSeulement;///< Indique si on a demandé, à l'initialisation, à recevoir uniquement les dossiers
	var $oFichierCourant;   ///< L'objet FichierInfo qui représente l'élément courant de l'itérateur

	/**
	 * Constructeur
	 *
	 * @param	v_sChemin				le dossier sur lequel on effectuera l'itération
	 * @param	v_sFiltre				le filtre à utiliser pour ne ramener que certains fichiers spécifiques.
	 * 									Ce filtre est celui utilisé par la fonction native PHP glob(). Par défaut, il
	 * 									comprend tous les fichiers/dossiers (apparemment les entrées dont le nom débute 
	 * 									par un point ne sont pas prises en compte!!!)
	 * @param	v_bTrier				indique si les entrées du dossier doivent être triées par ordre alphabétique
	 * 									(\c false par défaut)
	 * @param	v_bDossiersSeulement	indique si on désire uniquement itérer sur les sous-dossiers, et donc omettre
	 * 									les fichiers 
	 *
	 * @note	Contrairement aux filtres d'itérateurs (IterateurFiltre et sous-classes), le filtre agit ici
	 * 			directement, avant que les éléments de l'itérateur ne soient trouvés, alors que les filtres d'itérateurs
	 * 			agissent pendant l'itération, pour déterminer à chaque élément s'il est accepté ou pas.
	 */
	function IterateurDossier($v_sChemin, $v_sFiltrePre = '*', $v_bTrier = FALSE, $v_bDossiersSeulement = FALSE)
	{
		if (!is_dir($v_sChemin) || !is_readable($v_sChemin))
			Erreur::provoquer("Le chemin fourni ne représente pas un dossier valide, ou le dossier est inaccessible",
			                   ERREUR_AVERT);

		$this->sFiltrePre = $v_sFiltrePre;
		$this->oDossier = new FichierInfo($v_sChemin);
		$this->bTrier = $v_bTrier;
		$this->bDossiersSeulement = $v_bDossiersSeulement;
		$paramsGlob = ($v_bTrier ? 0 : GLOB_NOSORT) | ($v_bDossiersSeulement ? GLOB_ONLYDIR: 0); 
		$asFichiers = glob($this->oDossier->formerChemin($v_sFiltrePre), $paramsGlob);
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
	
	// réimplémenter les méthodes de l'interface ItérateurComposite: un élément d'IterateurDossier considéré comme 
	// "parent" uniquement s'il s'agit d'un dossier
    /**
     * Voir IterateurComposite#aEnfants()
     * 
     * @return	\c true si l'élément courant représente un dossier, \c false sinon
     */
    function aEnfants()
    {
    	$f = $this->courant();
    	return $f->estDossier();
    }
    
    /**
     * Voir IterateurComposite#retIterateurEnfants()
     */
    function retIterateurEnfants()
    {
    	$f = $this->courant();
    	return new IterateurDossier($f->retChemin(), $this->sFiltrePre, $this->bTrier, $this->bDossiersSeulement);
    }
}

?>