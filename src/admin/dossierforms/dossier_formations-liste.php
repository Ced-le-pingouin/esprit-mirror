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
** Fichier ................: dossier_formations-index.php
** Description ............:
** Date de création .......: 04/04/2005
** Dernière modification ..: 25/05/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("dossier_formations.tbl.php"));

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdDossierForms = (empty($_GET["idDossierForms"]) ? $oProjet->asInfosSession[SESSION_DOSSIER_FORMS] : $_GET["idDossierForms"]);

// ---------------------
// Initialiser
// ---------------------
$g_oDossierForms = new CDossierForms($oProjet->oBdd);
$g_oDossierForms->initDossierForms($oProjet->retIdUtilisateur(),TRUE);

$g_asRechTpl = array("{dossier_formation.id}","{dossier_formation.nom}","{dossier_formation.icone}");

// ---------------------
// Template
// ---------------------
$oTpl = new Template("dossier_formations-liste.tpl");

$oBlocDossierFormations = new TPL_Block("BLOCK_DOSSIER_FORMATIONS",$oTpl);

$asSetIcones = array(
	"avec" => $oTpl->defVariable("SET_ICONE_AVEC_FORMATIONS")
	, "sans" => $oTpl->defVariable("SET_ICONE_AUCUNE_FORMATION")
	, "premier" => $oTpl->defVariable("SET_ICONE_PREMIER_DOSSIER")
);

$asSetDossier = array(
	"sans" => $oTpl->defVariable("SET_SANS_DOSSIER")
	, "avec" => $oTpl->defVariable("SET_DOSSIER")
);

if (count($g_oDossierForms->aoDossierForms) > 0)
{
	$oBlocDossierFormations->remplacer("{dossier}",$asSetDossier["avec"]);
	
	$oBlocDossierFormations->beginLoop();
	
	foreach ($g_oDossierForms->aoDossierForms as $oDossierForms)
	{
		if ($url_iIdDossierForms < 1)
			$url_iIdDossierForms = $oDossierForms->retId();
		
		if ($oDossierForms->retPremierDossier())
			$sIcone = $asSetIcones["premier"];
		else if (count($oDossierForms->aoFormations) > 0)
			$sIcone = $asSetIcones["avec"];
		else
			$sIcone = $asSetIcones["sans"];
		
		$oBlocDossierFormations->nextLoop();
		$oBlocDossierFormations->remplacer($g_asRechTpl,array($oDossierForms->retId(),mb_convert_encoding($oDossierForms->retNom(),"HTML-ENTITIES","UTF-8"),$sIcone));
	}
}
else
	$oBlocDossierFormations->remplacer("{dossier}",$asSetDossier["sans"]);

$oBlocDossierFormations->afficher();

$oTpl->remplacer("{dossier_formations.id}",$url_iIdDossierForms);

$oTpl->afficher();

$oProjet->terminer();

?>

