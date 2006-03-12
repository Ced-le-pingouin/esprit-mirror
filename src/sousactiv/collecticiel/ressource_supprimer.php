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
** Fichier ................: ressource_supprimer.php
** Description ............:
** Date de création .......: 05/12/2002
** Dernière modification ..: 21/04/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");


// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_bRecharger   = (empty($HTTP_GET_VARS["recharger"]) ? FALSE : $HTTP_GET_VARS["recharger"]);
$url_sNomVariable = (empty($HTTP_GET_VARS["nom"]) ? NULL : $HTTP_GET_VARS["nom"]);
$url_sIdResSA     = (empty($url_sNomVariable) || empty($HTTP_GET_VARS[$url_sNomVariable]) ? NULL : $HTTP_GET_VARS[$url_sNomVariable]);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("ressource_supprimer.tpl");

$oBlocEffacerRessource = new TPL_Block("BLOCK_EFFACER_RESSOURCE",$oTpl);

$sSetPasRessourceSelectionnee = $oBlocEffacerRessource->defVariable("SET_PAS_RESSOURCE_SELECTIONNEE");
$sSetConfirmerEffacement      = $oBlocEffacerRessource->defVariable("SET_CONFIRMER_EFFACEMENT");
$sSetConfirmationEffacement   = $oBlocEffacerRessource->defVariable("SET_CONFIRMATION_EFFACEMENT");

if ($url_bRecharger)
	$oBlocEffacerRessource->ajouter($sSetConfirmationEffacement);
else if (isset($url_sIdResSA))
{
	$oBlocEffacerRessource->ajouter($sSetConfirmerEffacement);
	$oBlocEffacerRessource->remplacer("{idResSA.ids}",$url_sIdResSA);
}
else
	$oBlocEffacerRessource->ajouter($sSetPasRessourceSelectionnee);

$oBlocEffacerRessource->afficher();

$oTpl->afficher();

?>

