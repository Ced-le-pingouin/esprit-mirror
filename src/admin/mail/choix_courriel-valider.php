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
** Fichier ................: choix_courriel-valider.php
** Description ............:
** Date de création .......: 19/01/2005
** Dernière modification ..: 23/02/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iBoiteEnvoi      = (empty($_POST["boiteCourrielle"]) ? BOITE_COURRIELLE_PLATEFORME : $_POST["boiteCourrielle"]);
$url_sSujetCourriel   = (empty($_POST["sujetCourriel"]) ? NULL : $_POST["sujetCourriel"]);
$url_sMessageCourriel = (empty($_POST["messageCourriel"]) ? NULL : $_POST["messageCourriel"]);
$url_iIdStatuts       = (empty($_POST["idStatuts"]) ? NULL : $_POST["idStatuts"]);
$url_iIdEquipes       = (empty($_POST["idEquipes"]) ? NULL : $_POST["idEquipes"]);
$url_iIdPers          = (empty($_POST["idPers"]) ? NULL : $_POST["idPers"]);
$url_sTypeCourriel    = (empty($_POST["typeCourriel"]) ? NULL : $_POST["typeCourriel"]);

// ---------------------
// Initialiser
// ---------------------
$sCourrielParams  = (isset($url_iIdStatuts) ? "?idStatuts={$url_iIdStatuts}" : NULL);
$sCourrielParams .= (isset($url_iIdEquipes) ? (empty($sCourrielParams) ? "?" : "&")."idEquipes={$url_iIdEquipes}" : NULL);
$sCourrielParams .= (isset($url_iIdPers) ? (empty($sCourrielParams) ? "?" : "&")."idPers={$url_iIdPers}" : NULL);
$sCourrielParams .= (isset($url_sTypeCourriel) ? (empty($sCourrielParams) ? "?" : "&")."typeCourriel={$url_sTypeCourriel}" : NULL);

// Rechercher les adresses courriel
$asAdressesCourrielles = array();

if (BOITE_COURRIELLE_OS == $url_iBoiteEnvoi)
{
	include_once(dir_database("personnes.class.php"));
	
	$oPersonnes = new CPersonnes($oProjet);
	
	if (isset($url_iIdStatuts))
		$oPersonnes->initGraceIdStatuts(explode("x",$url_iIdStatuts));
	
	if (isset($url_iIdEquipes))
		$oPersonnes->initGraceIdEquipes(explode("x",$url_iIdEquipes));
	
	if (isset($url_iIdPers))
		$oPersonnes->initGraceIdPers(explode("x",$url_iIdPers));
}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("choix_courriel-valider.tpl");

$oBlocJavascriptFunctionInit = new TPL_Block("BLOCK_JAVASCRIPT_FUNCTION_INIT",$oTpl);
$oBlocBoiteEnvoiOs           = new TPL_Block("BLOCK_BOITE_ENVOI_OS",$oTpl);

$asSetJavascriptFunctionInit = array(
	"plateforme" => $oBlocJavascriptFunctionInit->defVariable("SET_BOITE_COURRIELLE_PLATEFORME")
	, "os" => $oBlocJavascriptFunctionInit->defVariable("SET_BOITE_COURRIELLE_OS")
);

if (BOITE_COURRIELLE_OS == $url_iBoiteEnvoi)
{
	// Pour récupérer la constante COURRIEL_MAX_UTILISATEURS
	include_once(dir_code_lib("mail.class.php"));
	
	$oMail = new CMail();
	
	$oBlocBoiteEnvoiDirecte   = new TPL_Block("BLOCK_BOITE_ENVOI_DIRECTE",$oBlocBoiteEnvoiOs);
	$oBlocBoiteEnvoiIndirecte = new TPL_Block("BLOCK_BOITE_ENVOI_INDIRECTE",$oBlocBoiteEnvoiOs);
	
	if (($iNbPersonnes = count($oPersonnes->aoPersonnes)) < COURRIEL_MAX_UTILISATEURS)
	{
		// Dans le cas où, le nombre de personnes est inférieur au nombre maximum
		// de personnes autorisées, on pourra lancer directement la boite d'envoi
		// du système d'exploitation
		$oBlocBoiteEnvoiIndirecte->effacer();
		
		$sListeAdressesCourrielles = NULL;
		
		foreach ($oPersonnes->aoPersonnes as $oPersonne)
		{
			$sAdresseCourrielle = $oPersonne->retEmail();
			
			if (strlen($sAdresseCourrielle) > 0)
				$sListeAdressesCourrielles .= (isset($sListeAdressesCourrielles) ? ", " : NULL)
					.$oMail->retFormatterAdresse($sAdresseCourrielle,$oPersonne->retNomComplet());
		}
		
		$oBlocJavascriptFunctionInit->ajouter($asSetJavascriptFunctionInit["os"]);
		
		$oBlocBoiteEnvoiDirecte->remplacer("{liste_adresses_courrielles}",$sListeAdressesCourrielles);
		$oBlocBoiteEnvoiDirecte->afficher();
	}
	else
	{
		$oBlocBoiteEnvoiDirecte->effacer();
		
		$oBlocListeDestinataires = new TPL_Block("BLOCK_LISTE_DESTINATAIRES",$oBlocBoiteEnvoiIndirecte);
		
		$oBlocListeDestinataires->beginLoop();
		
		$iIdxPers = 0;
		$iIdxPersCourant = 0;
		$sListeAdressesCourrielles = NULL;
		
		foreach ($oPersonnes->aoPersonnes as $oPersonne)
		{
			$sAdresseCourrielle = $oPersonne->retEmail();
			
			if (strlen($sAdresseCourrielle) < 1)
				continue;
			
			$sListeAdressesCourrielles .= (isset($sListeAdressesCourrielles) ? ", " : NULL)
				.$oMail->retFormatterAdresse($sAdresseCourrielle,$oPersonne->retNomComplet());
			
			if (++$iIdxPersCourant == $iNbPersonnes ||
				++$iIdxPers == COURRIEL_MAX_UTILISATEURS)
			{
				$oBlocListeDestinataires->nextLoop();
				$oBlocListeDestinataires->remplacer("{liste_adresses_courrielles}",$sListeAdressesCourrielles);
				$oBlocListeDestinataires->remplacer("{liste_adresses_courrielles:htmlentities}",mb_convert_encoding($sListeAdressesCourrielles,"HTML-ENTITIES","UTF-8"));
				$iIdxPers = 0;
				$sListeAdressesCourrielles = NULL;
			}
		}
		
		$oBlocListeDestinataires->afficher();
		
		$oBlocBoiteEnvoiIndirecte->afficher();
	}
	
	$oBlocBoiteEnvoiOs->afficher();
}
else
{
	// Par défaut ou dans le cas d'erreur dans l'envoi de paramètres, c'est
	// la boîte courrielle de la plate-forme qui sera ouvert
	$oBlocJavascriptFunctionInit->ajouter($asSetJavascriptFunctionInit["plateforme"]);
	$oBlocBoiteEnvoiOs->effacer();
}

$oBlocJavascriptFunctionInit->afficher();

$oTpl->remplacer("{courriel_params}",$sCourrielParams);

$oTpl->afficher();

$oProjet->terminer();

?>

