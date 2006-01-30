<?php

/*
** Fichier ................: gestion_activ.php
** Description ............: 
** Date de création .......: 01/02/2002
** Dernière modification ..: 14/09/2004
** Auteur .................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

if ($g_iActiv < 1)
	return;

if ($act == "ajouter")
{
	ajouter_activite();
}
else if ($act == "supprimer")
{
	effacer_activite();
}
else if ($act == "modifier")
{
	$oActiv = new CActiv($oProjet->oBdd,$g_iActiv);
	
	if ($url_bModifierStatut)
		$oActiv->defStatut($HTTP_POST_VARS["STATUT"]);
	
	if (!$url_bModifier)
		return;
	
	$iNumOrdre    = $HTTP_POST_VARS["ORDRE"];
	$sNom         = $HTTP_POST_VARS["NOM"];
	$sDescription = (empty($HTTP_POST_VARS["DESCRIPTION"]) ? NULL : $HTTP_POST_VARS["DESCRIPTION"]);
	$iModalite    = $HTTP_POST_VARS["MODALITE"];
	
	// Sauvegarder les modifications
	$oActiv->redistNumsOrdre($iNumOrdre);
	$oActiv->defNom($sNom);
	$oActiv->defDescr($sDescription);
	$oActiv->defModalite($iModalite);
}

?>

