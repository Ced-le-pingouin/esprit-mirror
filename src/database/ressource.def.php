<?php

/*
** Fichier ................: ressource.tbl.php
** Description ............: 
** Date de cr�ation .......: 28/10/2004
** Derni�re modification ..: 13/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           C�dric FLOQUET <cedric.floquet@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

// ---------------------
// D�clarer les diff�rents statuts des ressources (collecticiel ou formulaire)
// ---------------------
define("STATUT_RES_TOUS_DOCUMENTS",0);	// Voir tous les documents

define("STATUT_RES_ORIGINAL",1);
define("STATUT_RES_EN_COURS",2);
define("STATUT_RES_SOUMISE",3);
define("STATUT_RES_APPROF",4);
define("STATUT_RES_ACCEPTEE",5);

define("STATUT_RES_EVALUEE",6);			// STATUT_RES_ACCEPTEE + STATUT_RES_APPROF
define("STATUT_RES_TRANSFERE",7);		// Le fichier a �t� transf�r� vers un autre collecticiel/formulaire

define("TRANSFERT_FICHIERS",255);		// STATUT_RES_ACCEPTEE + STATUT_RES_TRANSFERE

// ---------------------
// D�clarer les diff�rents type de transfert
// ---------------------
define("TYPE_TRANSFERT_II",1);			// Collecticiel "Individuel" vers un collecticiel "Individuel"
define("TYPE_TRANSFERT_IE",2);			// Collecticiel "Individuel" vers un collecticiel "Par �quipe"
define("TYPE_TRANSFERT_EE",3);			// Collecticiel "Par �quipe" vers un collecticiel "Par �quipe"
define("TYPE_TRANSFERT_EI",4);			// Collecticiel "Par �quipe" vers un collecticiel "Individuel"

// ---------------------
// Fonctions
// ---------------------
function retListeStatutsRessources ()
{
	return array(
			array(STATUT_RES_TOUS_DOCUMENTS,"Tous les documents")
			, array(STATUT_RES_ORIGINAL,"Original")
			, array(STATUT_RES_EN_COURS,"En cours")
			, array(STATUT_RES_SOUMISE,"Soumis pour �valuation")
			, array(STATUT_RES_APPROF,"Approfondir")
			, array(STATUT_RES_ACCEPTEE,"Accept�")
			, array(STATUT_RES_EVALUEE,"Evalu�")
			, array(STATUT_RES_TRANSFERE,"Transf�r�")
		);
}
?>
