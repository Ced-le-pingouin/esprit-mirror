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
** Fichier ................: liste-destinataires.php
** Description ............:
** Date de cr�ation .......: 14/12/2004
** Derni�re modification ..: 17/01/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------

// Envoyer un email aux personnes correspondant aux statuts autoris�s
// Les statuts suivants seront pris en compte:
//   > mail-index.php?idStatuts=3x7x9
//                               | | +-- Les �tudiants
//                               | +-- Les tuteurs
//                               +-- Les responsables de formation
//   > mail-index.php?idStatuts=9
//                               +-- Les �tudiants
$url_aiIdStatuts = (empty($HTTP_GET_VARS["idStatuts"]) ? NULL : explode("x",$HTTP_GET_VARS["idStatuts"]));

// Envoyer un email � cette liste des �quipes
//   > mail-index.php?idEquipes=12x13x15x20
//   > mail-index.php?idEquipes=15
$url_aiIdEquipes = (empty($HTTP_GET_VARS["idEquipes"]) ? NULL : explode("x",$HTTP_GET_VARS["idEquipes"]));

// Envoyer un email � cette liste de personnes
//   > mail-index.php?idPers=tous
//   > mail-index.php?idPers=1x15x27x14x500
//   > mail-index.php?idPers=27
$url_aiIdPers = (empty($HTTP_GET_VARS["idPers"]) ? NULL : explode("x",$HTTP_GET_VARS["idPers"]));

// ---------------------
// Initialiser
// ---------------------
$aoDestinataires = array();

if (is_array($url_aiIdStatuts))
{
	// Rechercher les personnes par rapport au statut
	foreach ($url_aiIdStatuts as $iIdStatut)
	{
		switch ($iIdStatut)
		{
			case STATUT_PERS_RESPONSABLE:
			//   -----------------------
				if (is_object($oProjet->oFormationCourante) &&
					$oProjet->oFormationCourante->initResponsables() > 0);
					$aoDestinataires = array_merge($aoDestinataires,$oProjet->oFormationCourante->aoResponsables);
				break;
				
			case STATUT_PERS_TUTEUR:
			//   ------------------
				if (is_object($oProjet->oModuleCourant) &&
					$oProjet->oModuleCourant->initTuteurs() > 0)
					$aoDestinataires = array_merge($aoDestinataires,$oProjet->oModuleCourant->aoTuteurs);
				break;
				
			case STATUT_PERS_ETUDIANT:
			//   --------------------
				if (is_object($oProjet->oFormationCourante) &&
					$oProjet->oFormationCourante->retInscrAutoModules() &&
					$oProjet->oFormationCourante->initInscrits() > 0)
					$aoDestinataires = $oProjet->oFormationCourante->aoInscrits;
				else if (is_object($oProjet->oModuleCourant) &&
					$oProjet->oModuleCourant->initInscrits() > 0)
					$aoDestinataires = array_merge($aoDestinataires,$oProjet->oModuleCourant->aoInscrits);
				break;
		}
	}
}

if (is_array($url_aiIdEquipes))
{
	// Rechercher les personnes par rapport � l'�quipe
	foreach ($url_aiIdEquipes as $iIdEquipe)
	{
		$oEquipe = new CEquipe($oProjet->oBdd,$iIdEquipe);
		$oEquipe->initMembres();
		$aoDestinataires = array_merge($aoDestinataires,$oEquipe->aoMembres);
	}
}

if (is_array($url_aiIdPers))
{
	// Rechercher les personnes qui sont inscrites dans la table des personnes
	include_once(dir_database("personnes.class.php"));
	$oPersonnes = new CPersonnes($oProjet);
	$oPersonnes->initGraceIdPers($url_aiIdPers);
	$aoDestinataires = array_merge($aoDestinataires,$oPersonnes->aoPersonnes);
	unset($oPersonnes);
}

// ---------------------
// Composer la liste des destinataires
// ---------------------
$asDestinataires = array();

if (isset($aoDestinataires) && is_array($aoDestinataires))
{
	foreach ($aoDestinataires as $oDestinataire)
	{
		$sEmail = $oDestinataire->retEmail();
		
		if (strlen($sEmail) < 1)
			$sEmail = NULL;
		
		$sNomComplet = $oDestinataire->retNomComplet(TRUE);
		$asDestinataires[$sNomComplet] = $sEmail;
	}
}

// ---------------------
// Trier par rapport au nom et au pr�nom de l'utilisateur
// ---------------------
ksort($asDestinataires);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("destinataires-liste.tpl");

$oBlocDestinataire = new TPL_Block("BLOCK_DESTINATAIRE",$oTpl);

$asVarDestinataire = $oBlocDestinataire->defVariable("VAR_DESTINATAIRE",TRUE);

if (count($asDestinataires) > 0)
{
	foreach ($asDestinataires as $sNomComplet => $sEmail)
	{
		if (isset($sEmail))
		{
			$oBlocDestinataire->ajouter($asVarDestinataire[1]);
		}
		else
		{
			$oBlocDestinataire->ajouter($asVarDestinataire[0]);
			$sEmail = "sans adresse �lectronique";
		}
		
		$sTexte = "{$sNomComplet} <{$sEmail}>";
		
		$oBlocDestinataire->remplacer("{destinataire->email:urlencode}",rawurlencode($sTexte));
		$oBlocDestinataire->remplacer("{destinataire->email}",htmlentities($sTexte));
	}
}
else
{
	$oBlocDestinataire->effacer();
}

$oBlocDestinataire->afficher();

// {{{ Formulaire
$oTpl->remplacer(array("{form}","{/form}"),array("<form>","</form>"));
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

