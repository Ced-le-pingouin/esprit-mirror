<?php

/*
** Fichier ................: rubr_modif.inc.php
** Description ............: 
** Date de cr�ation .......: 01-02-2001
** Derni�re modification ..: 04-09-2002
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
		// Charger le fichier vers le r�pertoire du serveur
		// *************************************

		$repDeposer = $oProjet->retRepRubriques ();

		// Effacer l'ancien fichier qui se trouve dans le r�pertoire du serveur
		$f = explode (":",$oModule_Rubrique->retDonnee ());

		@unlink ($repDeposer.$f[0]);

		// Charger le fichier et retourne une valeur bool�enne: VRAI si r�ussi
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
