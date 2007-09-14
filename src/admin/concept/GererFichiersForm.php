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
 * @file	GererFichiersForm.php
 */

require_once 'globals.inc.php';
require_once 'admin_globals.inc.php';
require_once dir_include('nav_fichiers/NavigateurFichiers.php');

/**
 * Contrôleur dérivé du navigateur de fichiers, adapté aux formations d'Esprit
 */
class GererFichiersForm extends NavigateurFichiers
{
	// filtres pour les fichiers/dossiers d'Esprit sur lesquels les actions sont interdites
	var $sFiltreFichiers = '%(?:[/\\\\]|^)(?:activ_[0-9]+|chatlog|forum|html\.php|ressources|rubriques|tableaudebord\.csv)$%i';
	
	/**
	 * @see AfficheurPage#recupererDonnees()
	 */
	function recupererDonnees()
	{
		$oProjet = new CProjet();
		// la racine est le dossier de la formation courante, seulement si l'utlisateur connecté peut la modifier
		if ($oProjet->verifModifierFormation())
			$this->aDonneesUrl['r'] = $oProjet->oFormationCourante->retDossier();
		
		parent::recupererDonnees();
	}
}

// affichage de la page
$page = new GererFichiersForm();
$page->demarrer(dir_include('nav_fichiers/NavigateurFichiers.tpl'));
?>
