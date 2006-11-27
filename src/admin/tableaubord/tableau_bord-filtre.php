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
 * @file	tableau_bord-filtre.php
 * 
 * @date	2006/11/27
 * 
 * @author	Filippo PORCO
 */

require_once("globals.inc.php");
require_once(dir_locale("globals.lang"));

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdNiveau   = (empty($_GET["idNiveau"]) ? NULL : $_GET["idNiveau"]);
$url_iTypeNiveau = (empty($_GET["typeNiveau"]) ? NULL : $_GET["typeNiveau"]);
$url_iIdType     = (empty($_GET["idType"]) ? 0 : $_GET["idType"]);
$url_iIdModalite = (empty($_GET["idModal"]) ? NULL : $_GET["idModal"]);

// ---------------------
// Initialiser
// ---------------------
if (empty($url_iIdNiveau) || empty($url_iTypeNiveau))
	exit();

$oIds = new CIds($oProjet->oBdd,$url_iTypeNiveau,$url_iIdNiveau);

$g_iIdForm = $oIds->retIdForm();
$g_iIdMod  = $oIds->retIdMod();
$g_iIdRubr = $oIds->retIdRubrique();

$oFormation = new CFormation($oProjet->oBdd,$g_iIdForm);

unset($oIds);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("tableau_bord-filtre.tpl");

$oBlocModule = new TPL_Block("BLOCK_MODULE",$oTpl);

if ($oFormation->initModules() > 0)
{
	$oBlocModule->beginLoop();
	
	foreach ($oFormation->aoModules as $oModule)
	{
		$iIdMod = $oModule->retId();
		
		$oBlocModule->nextLoop();
		
		$oBlocRubrique = new TPL_Block("BLOCK_RUBRIQUE",$oBlocModule);
		
		if ($oModule->initRubriques(LIEN_UNITE) > 0)
		{
			$oBlocRubrique->beginLoop();
			
			foreach ($oModule->aoRubriques as $oRubrique)
			{
				$iIdRubrique = $oRubrique->retId();
				
				if ($g_iIdRubr == 0 && $g_iIdMod == $iIdMod)
					$g_iIdRubr = $iIdRubrique;
				
				$oBlocRubrique->nextLoop();
				$oBlocRubrique->remplacer(
					array("{rubrique.option.value}"
						,"{rubrique.option.selected}"
						,"{rubrique.nom}")
					, array($iIdRubrique
						,($g_iIdRubr == $iIdRubrique ? " selected=\"selected\"" : NULL)
						,emb_htmlentities($oRubrique->retNomComplet()))
				);				
			}
			
			$oBlocRubrique->afficher();
		}
		else
			$oBlocRubrique->effacer();
		
		$oBlocModule->remplacer(
			array("{module.option.value}","{module.nom}")
			, array("?idNiveau={$g_iIdRubr}&typeNiveau=".TYPE_RUBRIQUE,$oModule->retNomComplet())
		);
	}
	
	$oBlocModule->afficher();
}
else
	$oBlocModule->effacer();

// {{{ Les types d'actions
$oSousActiv = new CSousactiv($oProjet->oBdd);

$aiIdType  = array(LIEN_COLLECTICIEL,LIEN_FORMULAIRE,LIEN_FORUM,LIEN_CHAT);
$asRechTpl = array("{sous_activite_type.label}","{sous_activite_type.value}","{sous_activite_type.selected}");

$oBloc = new TPL_Block("BLOCK_SOUS_ACTIVITE_TYPE",$oTpl);
$oBloc->beginLoop();

foreach ($aiIdType as $iIdType)
{
	$amReplTpl = array(
		emb_htmlentities($oSousActiv->retTexteType($iIdType))
		, $iIdType
	);
	
	$oBloc->nextLoop();
	$oBloc->remplacer($asRechTpl,$amReplTpl);
}

$oBloc->afficher();
// }}}

// {{{ Modalité
$asRechTpl = array("{modalite.individuel.selected}","{modalite.par_equipe.selected}");
$amReplTpl = array(NULL,NULL);

if (MODALITE_PAR_EQUIPE == $url_iIdModalite)
	$amReplTpl = array(NULL," selected=\"selected\"");
else if (MODALITE_INDIVIDUEL == $url_iIdModalite)
	$amReplTpl = array(" selected=\"selected\"",NULL);

$oTpl->remplacer($asRechTpl,$amReplTpl);
// }}}

// {{{ Formulaire
$oTpl->remplacer("{typeNiveau.value}",TYPE_RUBRIQUE);
// }}}

// {{{ Traductions
$asRechTpl = array("[TXT_UNITE]");
$amReplTpl = array(TXT_UNITE);
$oTpl->remplacer($asRechTpl,$amReplTpl);
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

