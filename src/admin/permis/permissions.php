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
if (isset($_GET["idStatut"]))
	$url_iIdStatut = $_GET["idStatut"];
else if (isset($_POST["idStatut"]))
	$url_iIdStatut = $_POST["idStatut"];
else
	$url_iIdStatut = 0;

$url_sFiltre   = (empty($_POST["filtre"]) ? NULL : $_POST["filtre"]);
$url_aiIdsPerm = (empty($_POST["idPermis"]) ? array() : $_POST["idPermis"]);

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
if (empty($_REQUEST['onglet'])) {
	$oTpl->remplacer("{onglet}",'permission');
	$_REQUEST['onglet'] = 'permission';
} else {
	$oTpl->remplacer("{onglet}",$_REQUEST['onglet']);
}


$oTpl->remplacer("{idStatut}",$url_iIdStatut);
$oTpl->remplacer("{filtre}",$url_sFiltre);
$oBloc_liste_permission = new TPL_Block("BLOCK_LISTE_PERMISSION",$oTpl);
$oBloc_permission = new TPL_Block("BLOCK_PERMISSION",$oBloc_liste_permission);

if ($_REQUEST['onglet']==='permission') {




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
	
	$oBloc_permission->remplacer("{permission.nom}",emb_htmlentities($sNomPermis));
	$oBloc_permission->remplacer("{permission.description}",emb_htmlentities($oPermis->retDescr()));
}

$oBloc_permission->afficher();

} else {
	$oBloc_liste_permission->effacer();
}

$oBloc_liste_permission->afficher();

$oBloc_admin = new TPL_Block("BLOCK_ADMIN",$oTpl);

if ($_REQUEST['onglet']==='admin') {

  // $oBloc_admin->remplacer("{admin}", "");

}else{
   $oBloc_admin->effacer();
}
$oBloc_admin->afficher();

$oTpl->remplacer("{self}",$_SERVER['PHP_SELF']);
$oTpl->remplacer("js://",dir_javascript());

$oTpl->afficher();

$oProjet->terminer();

?>

