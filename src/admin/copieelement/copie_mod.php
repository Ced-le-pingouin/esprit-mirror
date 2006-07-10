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
 * @file	copie_mod.php
 * 
 * Copie un module d'une formation vers une autre
 * 
 * @date	2006/04/13
 * 
 * @author	Jérôme TOUZE
 */

require_once("globals.inc.php");
require_once("../concept/admin_globals.inc.php");

$oProjet = new CProjet();

$oProjet->verifPeutUtiliserOutils();

// ---------------------
// Template
// ---------------------
$oTpl = new Template("copie_mod.tpl");
$oBlocChoixSrc = new TPL_Block("ETAPE_CHOIX_SRC",$oTpl);
$oBlocChoixDst = new TPL_Block("ETAPE_CHOIX_DST",$oTpl);
$oBlocFinal = new TPL_Block("ETAPE_FINAL",$oTpl);

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdFormSrc   = (empty($_GET["IdFormSrc"]) ? 0 : $_GET["IdFormSrc"]);
$url_iIdModSrc = (empty($_GET["IdModSrc"]) ? 0 : $_GET["IdModSrc"]);
$url_iSrcOk = (empty($_GET["SrcOk"]) ? 0 : $_GET["SrcOk"]);
$url_iDstOk = (empty($_GET["DstOk"]) ? 0 : $_GET["DstOk"]);
$url_iIdFormDst   = (empty($_GET["IdFormDst"]) ? $url_iIdFormSrc : $_GET["IdFormDst"]);
$url_iOrdreModDst   = (empty($_GET["OrdreModDst"]) ? 0 : $_GET["OrdreModDst"]);

if($url_iDstOk==1) // copie du module
{
	$oBlocChoixSrc->effacer();
	$oBlocChoixDst->effacer();
	$oBlocFinal->afficher();

	// lock tables Formation, Module, Module_Rubrique, Forum, Activ, SousActiv, Chat, Intitule
	$sRequeteSql = "LOCK TABLES Formation WRITE, Module WRITE, Module_Rubrique WRITE, Forum WRITE,"
					." Activ WRITE, SousActiv WRITE, Chat WRITE, Intitule WRITE";
	$oProjet->oBdd->executerRequete($sRequeteSql);
	$oModule = new CModule($oProjet->oBdd,$url_iIdModSrc);
	$iNumOrdreMaxMod = $oModule->retNumOrdreMax($url_iIdFormDst);
	$oModule->defNumOrdre($iNumOrdreMaxMod+1);
	$iIdMod = $oModule->copier($url_iIdFormDst);
	$oModule->defId($iIdMod);
	$oModule->redistNumsOrdre($url_iOrdreModDst);
	$oProjet->oBdd->executerRequete("UNLOCK TABLES");

	$oTpl->remplacer("[LOG_FINAL]","Copie réussie...");
}
else
{
	if($url_iSrcOk==1) // Choix de la destination
	{
		$oBlocChoixSrc->effacer();
		$oBlocFinal->effacer();
		$oBlocChoixDst->afficher();
	
		$oTpl->remplacer("[IDFORMSRC]",$url_iIdFormSrc);
		$oTpl->remplacer("[IDMODSRC]",$url_iIdModSrc);
		
		$oFormation = new CFormation($oProjet->oBdd);
		if (is_object($oProjet->oUtilisateur))
			$iNbrFormations = $oFormation->initFormationsPourCopie($oProjet->oUtilisateur->retId());
		else
			$iNbrFormations = 0;
			
		$sOptionsForm = "";
		
		for ($i=0; $i<$iNbrFormations; $i++)
		{
			if($url_iIdFormDst==$oFormation->aoFormations[$i]->retId())
			{
				$sOptionsForm = $sOptionsForm."<option value='".$oFormation->aoFormations[$i]->retId()."' selected='selected'>".$oFormation->aoFormations[$i]->retNom()."</option>";
				$oFormationCourante = new CFormation($oProjet->oBdd,$oFormation->aoFormations[$i]->retId());
			}
			else
			{
				$sOptionsForm = $sOptionsForm."<option value='".$oFormation->aoFormations[$i]->retId()."'>".$oFormation->aoFormations[$i]->retNom()."</option>";
			}
		}
		$oTpl->remplacer("[OPTIONSFORM]",$sOptionsForm);
		
		$oModule = new CModule($oProjet->oBdd);
		$iNumOrdreMaxMod = $oModule->retNumOrdreMax($url_iIdFormDst);
		$sOptNumOrdreMod = "";
		for ($i=2; $i<=$iNumOrdreMaxMod; $i++)
			$sOptNumOrdreMod = $sOptNumOrdreMod."<option value='".$i."'>".$i."</option>";
		if($iNumOrdreMaxMod>=1)
					$sOptNumOrdreMod = $sOptNumOrdreMod."<option value='".($iNumOrdreMaxMod+1)."' selected='selected'>".($iNumOrdreMaxMod+1)."</option>";
	
		$oTpl->remplacer("[OPTIONSORDRE]",$sOptNumOrdreMod);
	}
	else // choix de la source
	{
		$oBlocChoixSrc->afficher();
		$oBlocChoixDst->effacer();
		$oBlocFinal->effacer();
		$oFormation = new CFormation($oProjet->oBdd);
		if (is_object($oProjet->oUtilisateur))
			$iNbrFormations = $oFormation->initFormationsPourCopie($oProjet->oUtilisateur->retId());
		else
			$iNbrFormations = 0;
			
		if ($iNbrFormations == 0)
		{
			$sOptionsForm = "<option value='0'>Pas de formation trouvée</option>";
			$sOptionsMod = "<option value='0'>Pas de module trouv&eacute;</option>";
		}
		else
		{
			// création liste des formations
			$sOptionsForm = "";
			for ($i=0; $i<$iNbrFormations; $i++)
			{
				if( ($i == 0 && $url_iIdFormSrc == 0) || ($url_iIdFormSrc>0 && $url_iIdFormSrc==$oFormation->aoFormations[$i]->retId()) )
				{
					$sOptionsForm = $sOptionsForm."<option value='".$oFormation->aoFormations[$i]->retId()."' selected='selected'>".$oFormation->aoFormations[$i]->retNom()."</option>";
					$oFormationCourante = new CFormation($oProjet->oBdd,$oFormation->aoFormations[$i]->retId());
				}
				else
				{
					$sOptionsForm = $sOptionsForm."<option value='".$oFormation->aoFormations[$i]->retId()."'>".$oFormation->aoFormations[$i]->retNom()."</option>";
				}
			}
			// création liste des modules
			$iNbrModules = $oFormationCourante->initModules();
			if ($iNbrModules == 0)
			{
				$sOptionsMod = "<option value='0'>Pas de module trouv&eacute;</option>";
			}
			else
			{
				$sOptionsMod = "";
				for ($i=0; $i<$iNbrModules; $i++)
					$sOptionsMod = $sOptionsMod."<option value='".$oFormationCourante->aoModules[$i]->retId()."'>".$oFormationCourante->aoModules[$i]->retNom()."</option>";
			}
		}
		$oTpl->remplacer("[OPTIONSFORM]",$sOptionsForm);
		$oTpl->remplacer("[OPTIONSMOD]",$sOptionsMod);
	}
}
$oTpl->afficher();
$oProjet->terminer();
?>
