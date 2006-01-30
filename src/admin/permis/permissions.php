<?php

/*
** Fichier ................: permissions.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 18/03/2005
** Auteurs ................: Jérome TOUZE
**                           Filippo PORCO <filippo.porco@umh.ac.be>
** 
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// Vérifier que cet utilisateur a le droit d'utiliser cet outil
$oProjet->verifPeutUtiliserOutils("PERM_OUTIL_PERMISSION");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
if (isset($HTTP_GET_VARS["idStatut"]))
	$url_iIdStatut = $HTTP_GET_VARS["idStatut"];
else if (isset($HTTP_POST_VARS["idStatut"]))
	$url_iIdStatut = $HTTP_POST_VARS["idStatut"];
else
	$url_iIdStatut = 0;

$url_sFiltre   = (empty($HTTP_POST_VARS["filtre"]) ? NULL : $HTTP_POST_VARS["filtre"]);
$url_aiIdsPerm = (empty($HTTP_POST_VARS["idPermis"]) ? array() : $HTTP_POST_VARS["idPermis"]);

// ---------------------
// Initialisation
// ---------------------

// Rechercher toutes les permissions
$oPermission = new CPermission($oProjet->oBdd);
$oPermission->initPermissions($url_sFiltre);

// Permissions de l'utilisateur
$oPermisUtilisateur = new CStatutPermission($oProjet->oBdd);

if (count($url_aiIdsPerm) > 0)
{
	// Mettre à jour les permissions modifiées
	foreach ($oPermission->aoPermissions as $oPermis)
	{
		$iIdPermis = $oPermis->retId();
		
		if ($url_aiIdsPerm[$iIdPermis])
			$oPermisUtilisateur->ajouter($iIdPermis,$url_iIdStatut);
		else
			$oPermisUtilisateur->effacer($iIdPermis,$url_iIdStatut);
	}
	
	$oPermisUtilisateur->optimiser();
}

// Rechercher les permissions par rapport au statut de l'utilisateur
$oPermisUtilisateur->initPermissions($url_iIdStatut);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("permissions.tpl");

$oTpl->remplacer("{idStatut}",$url_iIdStatut);
$oTpl->remplacer("{filtre}",$url_sFiltre);

$oBloc_permission = new TPL_Block("BLOCK_PERMISSION",$oTpl);

$oBloc_permission->beginLoop();

$sPermissionTdClass = NULL;

foreach ($oPermission->aoPermissions as $oPermis)
{
	$sPermissionTdClass = ($sPermissionTdClass == "cellule_fonce" ? "cellule_clair" : "cellule_fonce");
	$sNomPermis         = $oPermis->retNom();
	$bPermisStatut      = $oPermisUtilisateur->verifPermission($sNomPermis);
	
	$oBloc_permission->nextLoop();
	
	$oBloc_permission->remplacer("{permission.td.class}",$sPermissionTdClass);
	
	$oBloc_permission->remplacer("{permission.input.name}",$oPermis->retId());
	$oBloc_permission->remplacer("{permission.input.oui.checked}",($bPermisStatut) ? " checked" : NULL);
	$oBloc_permission->remplacer("{permission.input.non.checked}",($bPermisStatut) ? NULL : " checked");
	
	$oBloc_permission->remplacer("{permission.nom}",htmlentities($sNomPermis));
	$oBloc_permission->remplacer("{permission.description}",htmlentities($oPermis->retDescr()));
}

$oBloc_permission->afficher();

$oTpl->afficher();

$oProjet->terminer();

?>

