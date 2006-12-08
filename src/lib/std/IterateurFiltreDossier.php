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
 * @file	IterateurFiltreDossier.php
 */

require_once(dirname(__FILE__).'/OO.php');
require_once(dirname(__FILE__).'/IterateurFiltre.php');
require_once(dirname(__FILE__).'/IterateurRecursif.php');
require_once(dirname(__FILE__).'/IterateurDossier.php');

/**
 * Classe permettant de filtrer les éléments d'un itérateur de dossier, en fonction du nom de fichier/dossier de ceux-ci
 */
class IterateurFiltreDossier extends IterateurFiltre
{
	var $sFiltre;

	/**
	 * Constructeur
	 *
	 * @param	v_oItr		l'itérateur (déjà créé) qui sera parcouru en utilisant le filtre
	 * @param	v_sFiltre	la chaîne de caractère représentant une expression régulière, qui déterminera si un élement
	 * 						est accepté. La syntaxe est celle des expressions régulières Perl (fonctions preg_...() en
	 * 						PHP)
	 *
	 * @note	l'itérateur passé au constructeur est réinitialisé (#debut()) par défaut
	 */
	function IterateurFiltreDossier($v_oItr, $v_sFiltre)
	{
		if (OO::instanceDe($v_oItr, 'IterateurDossier')
		    || (OO::instanceDe($v_oItr, 'IterateurRecursif')
		        && OO::instanceDe($v_oItr->retIterateurInterne(), 'IterateurDossier')))
		{
			$this->oItr    = $v_oItr;
			$this->sFiltre = $v_sFiltre;
	
			$this->debut();
		}
		else
		{
			Erreur::provoquer("L'itérateur filtré doit être une instance d'IterateurDossier ou d'une de ses "
			                 ."sous-classes");
		}
	}

	/**
	 * Voir IterateurFiltre#accepter()
	 */
	function accepter()
	{
		$f = $this->oItr->courant();
		return (bool)preg_match($this->sFiltre, $f->retNom());
	}
	
	function aEnfants()
	{
		return $this->oItr->aEnfants();
	}
	
	function retIterateurEnfants()
	{
		return $this->oItr->retIterateurEnfants();
	}
}

OO::implemente('IterateurComposite');

?>
