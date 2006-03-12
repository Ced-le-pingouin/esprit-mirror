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

/*
** Fichier ................: rubr_modif.inc.php
** Description ............: 
** Date de création .......: 01-02-2001
** Dernière modification ..: 04-09-2002
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/


if (!isset ($modif))
	return;

require_once (dir_code_lib ("upload.inc.php"));

if (isset ($g_iRubrique))
{
	$oModule_Rubrique = new CModule_Rubrique ($oProjet->oBdd,$g_iRubrique);
	
	// *************************************
	// Charger le fichier vers le serveur
	// *************************************

	if ($html_rubrique != "none")
	{
		// *************************************
		// Charger le fichier vers le répertoire du serveur
		// *************************************

		$repDeposer = $oProjet->retRepRubriques ();

		// Effacer l'ancien fichier qui se trouve dans le répertoire du serveur
		$f = explode (":",$oModule_Rubrique->retDonnee ());

		@unlink ($repDeposer.$f[0]);

		// Charger le fichier et retourne une valeur booléenne: VRAI si réussi
		$bChargerFichier = (chargerFichier ($html_rubrique,$repDeposer.$html_rubrique_name,"html,htm") == 0);			
	}		

	// *************************************
	// Sauvegarder les modifications
	// *************************************

	$oModule_Rubrique->redistNumsOrdre ($ordre_rubrique);

	$oModule_Rubrique->defNom ($nom_rubrique);

	$oModule_Rubrique->defStatut ($statut_rubrique);

	$oModule_Rubrique->defType ($type_rubrique);

	if ($bChargerFichier)
		$oModule_Rubrique->defDonnee ($html_rubrique_name);			

	// *************************************
	// Recharger la table
	// *************************************

	$oModule_Rubrique->rafraichir ();
}

?>
