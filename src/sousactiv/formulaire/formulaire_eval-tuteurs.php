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
** Fichier ................: formulaire.php
** Description ............:
** Date de création .......: 05/11/2004
** Dernière modification ..: 08/11/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initModuleCourant();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdFCSousActiv = (empty($HTTP_GET_VARS["idFCSousActiv"]) ? 0 : $HTTP_GET_VARS["idFCSousActiv"]);
$url_bEvalFC        = (empty($HTTP_GET_VARS["evalFC"]) ? 0 : $HTTP_GET_VARS["evalFC"]);

// ---------------------
// Initialiser
// ---------------------
$oProjet->oModuleCourant->initTuteurs();

$iMonIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("formulaire_eval-tuteurs.tpl");
$oBlockTuteur = new TPL_Block("BLOCK_TUTEUR",$oTpl);
$oBlockTuteur->beginLoop();

foreach ($oProjet->oModuleCourant->aoTuteurs as $oTuteur)
{
	$iIdPers = $oTuteur->retId();
	
	$oBlockTuteur->nextLoop();
	
	$oBlockTuteur->remplacer("{personne->id}",$iIdPers);
	$oBlockTuteur->remplacer("{personne->nom_complet}",htmlentities($oTuteur->retNomComplet(),ENT_COMPAT,"UTF-8"));
	$oBlockTuteur->remplacer("{formulaire_complete->id}",$url_iIdFCSousActiv);
	$oBlockTuteur->remplacer("{personne->peutEvaluer}",($iIdPers == $iMonIdPers) ? $url_bEvalFC : 0);
}

$oBlockTuteur->afficher();

$oTpl->afficher();

$oProjet->terminer();

?>

