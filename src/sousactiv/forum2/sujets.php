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
** Sous-activité ..........: sujets.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 21/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Jérôme TOUZE <>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForum  = (empty($HTTP_GET_VARS["idForum"]) ? 0 : $HTTP_GET_VARS["idForum"]);
$url_iIdSujet  = (empty($HTTP_GET_VARS["idSujet"]) ? 0 : $HTTP_GET_VARS["idSujet"]);
$url_iIdEquipe = (empty($HTTP_GET_VARS["idEquipe"]) ? 0 : $HTTP_GET_VARS["idEquipe"]);

// ---------------------
// Initialiser les variables globales
// ---------------------
$g_iIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

// ---------------------
// Permissions
// ---------------------
$bPeutGererTousSujets  = $oProjet->verifPermission("PERM_MOD_SUJETS_FORUMS");
$bPeutGererTousSujets |= ($oProjet->verifPermission("PERM_MOD_SUJETS_FORUM") && $oProjet->verifModifierModule());

$bPeutGererSonSujet  = $oProjet->verifPermission("PERM_MOD_SUJET_FORUM");
//$bPeutGererSonSujet |= $oProjet->verifPermission("PERM_SUP_SUJET_FORUM");

// ---------------------
// Forum
// ---------------------
$oForum = new CForum($oProjet->oBdd,$url_iIdForum);
$oForum->initSujets($url_iIdEquipe);

// Afficher les messages du premier sujet
if ($url_iIdSujet == 0 && count($oForum->aoSujets) > 0)
	$url_iIdSujet = $oForum->aoSujets[0]->retId();

$oTpl = new Template("sujets.tpl");

$oBloc_Sujet = new TPL_Block("BLOCK_SUJET",$oTpl);
$oBloc_Sujet->beginLoop();

$iIdxSujetForum = 1;
$sSujetStyle = NULL;

$bIdSujetValide = FALSE;

foreach ($oForum->aoSujets as $oSujetForum)
{
	$iIdSujet = $oSujetForum->retId();
	
	if ($iIdSujet == $url_iIdSujet)
		$bIdSujetValide = TRUE;
	
	$oSujetForum->initAuteur();
	
	$iNbMessages = $oSujetForum->initMessages($url_iIdEquipe);
	
	$sSujetStyle = ($sSujetStyle == "cellule_clair" ? "cellule_fonce" : "cellule_clair");
	
	$sEmail = "<a"
		." href=\"mailto:".$oSujetForum->oAuteur->retEmail()."\""
		." target=\"_blank\""
		.">".$oSujetForum->oAuteur->retPseudo()."</a>";
	
	$oBloc_Sujet->nextLoop();
	
	if ($bPeutGererTousSujets || ($bPeutGererSonSujet && $oSujetForum->oAuteur->retId() == $g_iIdPers))
		$oBloc_Sujet->remplacer("{sujet->selecteur}","<input type=\"radio\" name=\"idSujet[]\" value=\"{$iIdSujet}\" onclick=\"g_iDernierRadioSelect = select_deselect_radio(document.forms[0].elements['idSujet[]'],g_iDernierRadioSelect)\"onfocus=\"blur()\">");
	else
		$oBloc_Sujet->remplacer("{sujet->selecteur}","&nbsp;");
	
	$oBloc_Sujet->remplacer("{sujet->td->class}",$sSujetStyle);
	
	$oBloc_Sujet->remplacer("{sujet->numero_ordre}",($iIdxSujetForum > 2 && $url_iIdSujet == $iIdSujet ? "<a name=\"go\"></a>" : NULL).$iIdxSujetForum++);
	$oBloc_Sujet->remplacer("{sujet->id}",$iIdSujet);
	$oBloc_Sujet->remplacer("{sujet->titre}",$oSujetForum->retTitre());
	$oBloc_Sujet->remplacer("{sujet->modalite}",$oSujetForum->retTexteModalite());
	$oBloc_Sujet->remplacer("{sujet->date}",$oSujetForum->retDate());
	$oBloc_Sujet->remplacer("{sujet->auteur}",$sEmail);
	$oBloc_Sujet->remplacer("{sujet->messages}",$iNbMessages);
	$oBloc_Sujet->remplacer("{sujet->dernier_poster}",$oSujetForum->retDateDernierMessagePoster("d/m/y",$url_iIdEquipe));
}

$oBloc_Sujet->afficher();

// Il faut vérifier que l'id du sujet est toujours valable (dans le cas de
// changement d'équipe)
if (!$bIdSujetValide)
	$url_iIdSujet = (count($oForum->aoSujets) > 0 ? $oForum->aoSujets[0]->retId() : 0);

// Formulaire
$oTpl->remplacer("{form->action}",$HTTP_SERVER_VARS["PHP_SELF"]);

$oTpl->remplacer("{forum->id}",$url_iIdForum);
$oTpl->remplacer("{sujet->id}",$url_iIdSujet);
$oTpl->remplacer("{equipe->id}",$url_iIdEquipe);

$oTpl->afficher();

$oProjet->terminer();

?>

