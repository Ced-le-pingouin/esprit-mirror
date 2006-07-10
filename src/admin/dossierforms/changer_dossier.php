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
** Fichier ................: changer_dossier.php
** Description ............:
** Date de création .......: 02/06/2005
** Dernière modification ..: 28/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("dossiers.lang"));
require_once(dir_database("dossier_formations.tbl.php"));

$oProjet = new CProjet();
$oProjet->initRubriqueCourante();

// {{{ Permission
$oProjet->initPermisUtilisateur(FALSE);

if (!$oProjet->verifPermission("PERM_CLASSER_FORMATIONS"))
	exit();
// }}}

$g_iIdUtilisateur = $oProjet->retIdUtilisateur();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sEvent          = (empty($_GET["event"]) ? NULL : $_GET["event"]);
$url_iIdDossierForms = (isset($url_sEvent) ? $_GET["idDossierForms"] : $oProjet->retInfosSession(SESSION_DOSSIER_FORMS));

// ---------------------
// Appliquer les changements
// ---------------------
$g_oDossierForms = new CDossierForms($oProjet->oBdd,$url_iIdDossierForms);

if ("valider" == $url_sEvent)
{
	if ($url_iIdDossierForms > 0)
	{
		$g_oDossierForms->defPremierDossier(TRUE);
		$g_oDossierForms->enregistrer();
	}
	else
		$g_oDossierForms->effacerPremierDossier($g_iIdUtilisateur);
	
	$oProjet->modifierInfosSession(SESSION_DOSSIER_FORMS,$url_iIdDossierForms,TRUE);
	
	exit();
}

// ---------------------
// Initialiser
// ---------------------
$iNbDossiersForms = $g_oDossierForms->initDossierForms($g_iIdUtilisateur,TRUE);

$g_aoDossierForms = array();

$g_aoDossierForms[0] = new CDossierForms($oProjet->oBdd,0);
$g_aoDossierForms[0]->initAjouter(0);
$g_aoDossierForms[0]->defNom("Toutes les formations");
$g_aoDossierForms[0]->defPremierDossier(!$g_oDossierForms->initPremierDossierForms($g_iIdUtilisateur));

$g_aoDossierForms = array_merge($g_aoDossierForms,$g_oDossierForms->aoDossierForms);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("changer_dossier.tpl");

$oBlocDossierForms = new TPL_Block("BLOCK_DOSSIER_FORMATIONS",$oTpl);
$asIcones = $oBlocDossierForms->defTableau("ARRAY_ICONES","###");

$oBlocDossierForms->beginLoop();

$bIdDossierFormsValide = FALSE;

foreach ($g_aoDossierForms as $oDossierForms)
{
	$iIdDossierForms = $oDossierForms->retId();
	
	if ($oDossierForms->retPremierDossier())
		$sIcone = $asIcones[2];
	else if (count($oDossierForms->aoFormations) > 0 || $iIdDossierForms < 1)
		$sIcone = $asIcones[1];
	else
		$sIcone = $asIcones[0];
	
	if ($iIdDossierForms == $url_iIdDossierForms)
		$bIdDossierFormsValide = TRUE;
	
	$oBlocDossierForms->nextLoop();
	$oBlocDossierForms->remplacer("{dossier_formations.id}",$iIdDossierForms);
	$oBlocDossierForms->remplacer("{dossier_formations.icone}",$sIcone);
	$oBlocDossierForms->remplacer("{dossier_formations.titre}",htmlentities($oDossierForms->retNom(),ENT_COMPAT,"UTF-8"));
}

$oBlocDossierForms->afficher();

if (!$bIdDossierFormsValide)
	$url_iIdDossierForms = 0;

// {{{ Formulaire
$oTpl->remplacer(array("{form}","{/form}"),array("<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"get\">","</form>"));
$oTpl->remplacer("{dossier_formations.id}",$url_iIdDossierForms);
// }}}

if ($g_oDossierForms->oPremierDossierForms->retId() > 0)
	$sNomDossierForms = $g_oDossierForms->oPremierDossierForms->retNom();
else
	$sNomDossierForms = "Toutes les formations";

$oTpl->remplacer("{dossier_formation.nom}",htmlentities($sNomDossierForms,ENT_COMPAT,"UTF-8"));

$oTpl->remplacer("[TXT_REMARQUE]",TXT_REMARQUE);

$oTpl->afficher();

$oProjet->terminer();

?>

