<?php

/*
** Fichier ................: gestion_formation.php
** Description ............: 
** Date de création .......: 01/02/2002
** Dernière modification ..: 14/09/2004
** Auteur .................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

if ($g_iFormation < 1)
	return;

if ($act == "ajouter")
{
	ajouter_formation();
}
else if ($act == "supprimer")
{
	effacer_formation();
}
else if ($act == "modifier")
{
	// Modifier la table "Formation"
	$oFormation = new CFormation($oProjet->oBdd,$g_iFormation);
	
	if ($url_bModifierStatut)
		$oFormation->defStatut($HTTP_POST_VARS["statut_formation"]);
	
	if (!$url_bModifier)
		return;
	
	$oFormation->redistNumsOrdre($HTTP_POST_VARS["ordre_formation"]);
	$oFormation->defNom($HTTP_POST_VARS["nom_formation"]);
	$oFormation->defdescr($HTTP_POST_VARS["descr_formation"]);
	$oFormation->defInscrAutoModules($HTTP_POST_VARS["INSCR_AUTO_MODULES"]);
	$oFormation->defVisiteurAutoriser((isset($HTTP_POST_VARS["VISITEUR_AUTORISER"]) && $HTTP_POST_VARS["VISITEUR_AUTORISER"] == "on" ? '1' : '0'));
}

?>

