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
 * @file	copie_rub.php
 * 
 * Copie une rubrique d'un module vers un autre
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
$oTpl = new Template("copie_rub.tpl");
$oBlocChoixSrc = new TPL_Block("ETAPE_CHOIX_SRC",$oTpl);
$oBlocChoixDst = new TPL_Block("ETAPE_CHOIX_DST",$oTpl);
$oBlocFinal = new TPL_Block("ETAPE_FINAL",$oTpl);

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdFormSrc   = (empty($HTTP_GET_VARS["IdFormSrc"]) ? 0 : $HTTP_GET_VARS["IdFormSrc"]);
$url_iIdModSrc = (empty($HTTP_GET_VARS["IdModSrc"]) ? 0 : $HTTP_GET_VARS["IdModSrc"]);
$url_iIdRubSrc = (empty($HTTP_GET_VARS["IdRubSrc"]) ? 0 : $HTTP_GET_VARS["IdRubSrc"]);
$url_iSrcOk = (empty($HTTP_GET_VARS["SrcOk"]) ? 0 : $HTTP_GET_VARS["SrcOk"]);
$url_iDstOk = (empty($HTTP_GET_VARS["DstOk"]) ? 0 : $HTTP_GET_VARS["DstOk"]);
$url_iIdFormDst   = (empty($HTTP_GET_VARS["IdFormDst"]) ? $url_iIdFormSrc : $HTTP_GET_VARS["IdFormDst"]);
$url_iIdModDst   = (empty($HTTP_GET_VARS["IdModDst"]) ? 0 : $HTTP_GET_VARS["IdModDst"]);
$url_iOrdreRubDst   = (empty($HTTP_GET_VARS["OrdreRubDst"]) ? 0 : $HTTP_GET_VARS["OrdreRubDst"]);

if($url_iDstOk==1) // copie de la rubrique
{
	$oBlocChoixSrc->effacer();
	$oBlocChoixDst->effacer();
	$oBlocFinal->afficher();

	// lock tables Formation, Module, Module_Rubrique, Forum, Activ, SousActiv, Chat, Intitule
	$sRequeteSql = "LOCK TABLES Formation WRITE, Module WRITE, Module_Rubrique WRITE, Forum WRITE,"
					." Activ WRITE, SousActiv WRITE, Chat WRITE, Intitule WRITE";
	$oProjet->oBdd->executerRequete($sRequeteSql);
	$oRubrique = new CModule_Rubrique($oProjet->oBdd,$url_iIdRubSrc);
	$iNumOrdreMaxRub = $oRubrique->retNumOrdreMax($url_iIdModDst);
	$oRubrique->defNumOrdre($iNumOrdreMaxRub+1);
	$iIdRub = $oRubrique->copier($url_iIdModDst);
	$oRubrique = new CModule_Rubrique($oProjet->oBdd,$iIdRub);
	$oRubrique->redistNumsOrdre($url_iOrdreRubDst);
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
		$oTpl->remplacer("[IDRUBSRC]",$url_iIdRubSrc);
		
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
		$sOptionsMod = "";
		$iNbrModules = $oFormationCourante->initModules();
		if($iNbrModules == 0)
		{
			$sOptionsMod = "<option value='0'>Pas de module trouv&eacute;</option>";
			$sOptNumOrdreRub = "<option value='0'>Pas de module trouv&eacute;</option>";
		}
		else
		{
			//verification que le module passée en parametre appartient a la formation courante(séléctionné)
			if($url_iIdModDst>0)
			{
				$oModuleCourant = new CModule($oProjet->oBdd,$url_iIdModDst);
				if($oModuleCourant->retIdParent()!=$oFormationCourante->retId())
					$url_iIdModDst=0;
			}
			for ($i=0; $i<$iNbrModules; $i++)
			{
				if( ($i == 0 && $url_iIdModDst==0) || ($url_iIdModDst==$oFormationCourante->aoModules[$i]->retId()) )
				{
					$sOptionsMod = $sOptionsMod."<option value='".$oFormationCourante->aoModules[$i]->retId()."' selected='selected'>".$oFormationCourante->aoModules[$i]->retNom()."</option>";
					$oModuleCourant = new CModule($oProjet->oBdd,$oFormationCourante->aoModules[$i]->retId());
				}
				else
				{
					$sOptionsMod = $sOptionsMod."<option value='".$oFormationCourante->aoModules[$i]->retId()."'>".$oFormationCourante->aoModules[$i]->retNom()."</option>";
				}
			}
			//affichage des numero d'ordre des rub.
			$oRubrique = new CModule_Rubrique($oProjet->oBdd);
			$iNumOrdreMaxRub = $oRubrique->retNumOrdreMax($oModuleCourant->retId());
			$sOptNumOrdreRub = "<option value='1'>1</option>";
			for ($i=2; $i<=$iNumOrdreMaxRub; $i++)
				$sOptNumOrdreRub = $sOptNumOrdreRub."<option value='".$i."'>".$i."</option>";
			if($iNumOrdreMaxRub>=1)
						$sOptNumOrdreRub = $sOptNumOrdreRub."<option value='".($iNumOrdreMaxRub+1)."' selected='selected'>".($iNumOrdreMaxRub+1)."</option>";
		}
		$oTpl->remplacer("[OPTIONSMOD]",$sOptionsMod);
		$oTpl->remplacer("[OPTIONSORDRE]",$sOptNumOrdreRub);
		
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
			$sOptionsForm = "<option value='0'>Pas de formation trouv&eacute;e</option>";
			$sOptionsMod = "<option value='0'>Pas de module trouv&eacute;</option>";
			$sOptionsRub = "<option value='0'>Pas de rubrique trouv&eacute;e</option>";
		}
		else
		{
			//affichage de la liste des formations
			$sOptionsForm = "";
			for ($i=0; $i<$iNbrFormations; $i++)
			{
				if(($i == 0 && $url_iIdFormSrc == 0) || ($url_iIdFormSrc>0 && $url_iIdFormSrc==$oFormation->aoFormations[$i]->retId()) )
				{
					$sOptionsForm = $sOptionsForm."<option value='".$oFormation->aoFormations[$i]->retId()."' selected='selected'>".$oFormation->aoFormations[$i]->retNom()."</option>";
					$oFormationCourante = new CFormation($oProjet->oBdd,$oFormation->aoFormations[$i]->retId());
				}
				else
				{
					$sOptionsForm = $sOptionsForm."<option value='".$oFormation->aoFormations[$i]->retId()."'>".$oFormation->aoFormations[$i]->retNom()."</option>";
				}
			}
			$iNbrModules = $oFormationCourante->initModules();
			if ($iNbrModules == 0)	// affichage de la liste des rubriques
			{
				$sOptionsMod = "<option value='0'>Pas de module trouv&eacute;</option>";
				$sOptionsRub = "<option value='0'>Pas de rubrique trouv&eacute;e</option>";
			}
			else
			{
				// Verification que le module passée en parametre appartient a la formation courante(séléctionné)
				if($url_iIdModSrc>0)
				{
					$oModuleCourant = new CModule($oProjet->oBdd,$url_iIdModSrc);
					if($oModuleCourant->retIdParent()!=$oFormationCourante->retId())
						$url_iIdModSrc=0;
				}
				// affichage de la liste des modules
				$sOptionsMod = "";
				for ($i=0; $i<$iNbrModules; $i++)
				{
					if(($i == 0 && $url_iIdModSrc==0) ||($url_iIdModSrc>0 && $url_iIdModSrc==$oFormationCourante->aoModules[$i]->retId()))
					{
						$sOptionsMod = $sOptionsMod."<option value='".$oFormationCourante->aoModules[$i]->retId()."' selected='selected'>".$oFormationCourante->aoModules[$i]->retNom()."</option>";
						$oModuleCourant = new CModule($oProjet->oBdd,$oFormationCourante->aoModules[$i]->retId());
					}
					else
					{
						$sOptionsMod = $sOptionsMod."<option value='".$oFormationCourante->aoModules[$i]->retId()."'>".$oFormationCourante->aoModules[$i]->retNom()."</option>";
					}
				}
				// affichage de la liste des rubriques
				$iNbrRubriques = $oModuleCourant->initRubriques();
				if($iNbrRubriques == 0)
				{
					$sOptionsRub = "<option value='0'>Pas de rubrique trouv&eacute;e</option>";
				}
				else
				{
					$sOptionsRub = "";
					for ($i=0; $i<$iNbrRubriques; $i++)
							$sOptionsRub = $sOptionsRub."<option value='".$oModuleCourant->aoRubriques[$i]->retId()."'>".$oModuleCourant->aoRubriques[$i]->retNom()."</option>";
				}
			}
		}
		$oTpl->remplacer("[OPTIONSFORM]",$sOptionsForm);
		$oTpl->remplacer("[OPTIONSMOD]",$sOptionsMod);
		$oTpl->remplacer("[OPTIONSRUB]",$sOptionsRub);
	}
}
$oTpl->afficher();
$oProjet->terminer();
?>
