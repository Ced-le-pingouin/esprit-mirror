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

/**
 * @file	copie_activ.php
 * 
 * Copie une activité d'une rubrique vers une autre
 * 
 * @date	2005/07/12
 * 
 * @author	Filippo PORCO <filippo.porco@umh.ac.be>
 * @author	Jérôme TOUZE
 */

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iCodeEtat = (empty($_GET["codeEtat"]) ? 0 : $_GET["codeEtat"]);

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("login/login.tpl",FALSE,TRUE));
$oBlocErreurLogin = new TPL_Block("BLOCK_ERREUR_LOGIN",$oTpl);
$oBlocAvertissementLogin = new TPL_Block("BLOCK_AVERTISSEMENT_LOGIN",$oTpl);
$oBlocInfosPlateforme = new TPL_Block("BLOCK_INFOS_PLATEFORME",$oTpl);
$oBlocListeFormations = new TPL_Block("BLOCK_LISTE_FORMATIONS",$oBlocInfosPlateforme);
$oBlocFormation       = new TPL_Block("BLOCK_FORMATION",$oBlocListeFormations);

//  {{{ Afficher un message d'erreur lorsque le pseudo ou le mot de passe de
//      l'utilisateur est incorrect
if ($url_iCodeEtat > 0)
	$oBlocErreurLogin->afficher();
else
	$oBlocErreurLogin->effacer();
// }}}

// {{{ Afficher un message d'avertissement
$sRequeteSql = "SELECT AvertissementLogin FROM Projet LIMIT 1";
$hResult = $oProjet->oBdd->executerRequete($sRequeteSql);
$oEnreg = $oProjet->oBdd->retEnregSuiv($hResult);
$sAvertissementLogin = $oEnreg->AvertissementLogin;
$oProjet->oBdd->libererResult($hResult);

if (strlen($sAvertissementLogin))
{
	$oBlocAvertissementLogin->remplacer("{login.avertissement}",convertBaliseMetaVersHtml($sAvertissementLogin));
	$oBlocAvertissementLogin->afficher();
}
else
 $oBlocAvertissementLogin->effacer();
// }}}

// {{{ Formulaire
$oTpl->remplacer(array("{form}","{/form}"), array("<form name=\"formulId\" action=\"index2.php\" method=\"post\" target=\"_top\">","</form>"));
// }}}

// {{{ Permet d'afficher que les formations accessibles qu'aux visiteurs
$oProjet->oUtilisateur = NULL;
$oProjet->asInfosSession[SESSION_FORM] = 0;
// }}}
$sRepHttpPlateforme = dir_http_plateform();

if ($oProjet->initFormationsUtilisateur() > 0)
{
	$oBlocFormation->beginLoop();
	
	foreach ($oProjet->aoFormations as $oFormation)
	{
		$sUrl = "<a href='{$sRepHttpPlateforme}index2.php?idForm=".$oFormation->retId()."' target='_top'>".$oFormation->retNom()."</a>";
		$oBlocFormation->nextLoop();
		$oBlocFormation->remplacer("{formation->url}",$sUrl);
	}
	
	$oBlocFormation->afficher();
	$oBlocListeFormations->afficher();
}
else
{
	$oBlocListeFormations->effacer();
}

$oBlocInfosPlateforme->afficher();
$oTpl->afficher();

$oProjet->terminer();
?>
