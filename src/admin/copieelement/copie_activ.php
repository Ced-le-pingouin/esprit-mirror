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
$oTpl = new Template("copie_activ.tpl");
$oBlocChoixSrc = new TPL_Block("ETAPE_CHOIX_SRC",$oTpl);
$oBlocChoixDst = new TPL_Block("ETAPE_CHOIX_DST",$oTpl);
$oBlocFinal = new TPL_Block("ETAPE_FINAL",$oTpl);

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdFormSrc   = (empty($HTTP_GET_VARS["IdFormSrc"]) ? 0 : $HTTP_GET_VARS["IdFormSrc"]);
$url_iIdModSrc = (empty($HTTP_GET_VARS["IdModSrc"]) ? 0 : $HTTP_GET_VARS["IdModSrc"]);
$url_iIdRubSrc = (empty($HTTP_GET_VARS["IdRubSrc"]) ? 0 : $HTTP_GET_VARS["IdRubSrc"]);
$url_iIdActivSrc = (empty($HTTP_GET_VARS["IdActivSrc"]) ? 0 : $HTTP_GET_VARS["IdActivSrc"]);
$url_iSrcOk = (empty($HTTP_GET_VARS["SrcOk"]) ? 0 : $HTTP_GET_VARS["SrcOk"]);
$url_iDstOk = (empty($HTTP_GET_VARS["DstOk"]) ? 0 : $HTTP_GET_VARS["DstOk"]);
$url_iIdFormDst   = (empty($HTTP_GET_VARS["IdFormDst"]) ? $url_iIdFormSrc : $HTTP_GET_VARS["IdFormDst"]);
$url_iIdModDst   = (empty($HTTP_GET_VARS["IdModDst"]) ? 0 : $HTTP_GET_VARS["IdModDst"]);
$url_iIdRubDst   = (empty($HTTP_GET_VARS["IdRubDst"]) ? 0 : $HTTP_GET_VARS["IdRubDst"]);
$url_iOrdreActivDst   = (empty($HTTP_GET_VARS["OrdreActivDst"]) ? 0 : $HTTP_GET_VARS["OrdreActivDst"]);

if($url_iDstOk==1) // copie de l'activité
{
	$oBlocChoixSrc->effacer();
	$oBlocChoixDst->effacer();
	$oBlocFinal->afficher();

	// lock tables Formation, Module, Module_Rubrique, Forum, Activ, SousActiv, Chat, Intitule
	$sRequeteSql = "LOCK TABLES Formation WRITE, Module WRITE, Module_Rubrique WRITE, Forum WRITE,"
					." Activ WRITE, SousActiv WRITE, Chat WRITE, Intitule WRITE";
	$oProjet->oBdd->executerRequete($sRequeteSql);
	$oActiv = new CActiv($oProjet->oBdd,$url_iIdActivSrc);
	$iNumOrdreMaxActiv = $oActiv->retNumOrdreMax($url_iIdRubDst);
	$oActiv->defNumOrdre($iNumOrdreMaxActiv+1);
	$iIdActiv = $oActiv->copier($url_iIdRubDst);
	$oActiv = new CActiv($oProjet->oBdd,$iIdActiv);
	$oActiv->redistNumsOrdre($url_iOrdreActivDst);
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
		$oTpl->remplacer("[IDACTIVSRC]",$url_iIdActivSrc);
		
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
		$iNbrModules = $oFormationCourante->initModules();
		if($iNbrModules == 0)
		{
			$sOptionsMod = "<option value='0'>Pas de module trouv&eacute;</option>";
			$sOptionsRub = "<option value='0'>Pas de rubrique trouv&eacute;e</option>";
			$sOptNumOrdreActiv = "<option value='0'>Pas de rubrique trouv&eacute;e</option>";
		}
		else
		{
			$sOptionsMod = "";
			//verification que le module passée en parametre appartient a la formation courante(séléctionné)
			if($url_iIdModDst>0)
			{
				$oModuleCourant = new CModule($oProjet->oBdd,$url_iIdModDst);
				if($oModuleCourant->retIdParent()!=$oFormationCourante->retId())
				{
					$url_iIdModDst=0;
					$url_iIdRubDst=0;	
				}
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
			// affichage de la liste des rubriques
			$iNbrRubriques = $oModuleCourant->initRubriques();
			if($iNbrRubriques == 0)
			{
				$sOptionsRub = "<option value='0'>Pas de rubrique trouv&eacute;e</option>";
				$sOptNumOrdreActiv = "<option value='0'>Pas de rubrique trouv&eacute;e</option>";
			}
			else
			{
				$sOptionsRub = "";
				// verification que la rubrique passée en paramètre appartienne au module courant
				if($url_iIdRubDst>0)
				{
					$oRubriqueCourante = new CModule_Rubrique($oProjet->oBdd,$url_iIdRubDst);
					if($oRubriqueCourante->retIdParent()!=$oModuleCourant->retId())
						$url_iIdRubDst=0;
				}				
				for ($i=0; $i<$iNbrRubriques; $i++)
				{
					if(($i == 0 && $url_iIdRubDst==0) ||($url_iIdRubDst>0 && $url_iIdRubDst==$oModuleCourant->aoRubriques[$i]->retId()))
					{
						$sOptionsRub = $sOptionsRub."<option value='".$oModuleCourant->aoRubriques[$i]->retId()."' selected='selected'>".$oModuleCourant->aoRubriques[$i]->retNom()."</option>";
						$oRubriqueCourante = new CModule_Rubrique($oProjet->oBdd,$oModuleCourant->aoRubriques[$i]->retId());
					}
					else
					{
						$sOptionsRub = $sOptionsRub."<option value='".$oModuleCourant->aoRubriques[$i]->retId()."'>".$oModuleCourant->aoRubriques[$i]->retNom()."</option>";
					}
				}
				// affichage des numeros d'ordre des activités
				$oActiv = new CActiv($oProjet->oBdd);
				$iNumOrdreMaxActiv = $oActiv->retNumOrdreMax($oRubriqueCourante->retId());
				$sOptNumOrdreActiv = "<option value='1'>1</option>";
				for ($i=2; $i<=$iNumOrdreMaxActiv; $i++)
					$sOptNumOrdreActiv = $sOptNumOrdreActiv."<option value='".$i."'>".$i."</option>";
				if($iNumOrdreMaxActiv>=1)
							$sOptNumOrdreActiv = $sOptNumOrdreActiv."<option value='".($iNumOrdreMaxActiv+1)."' selected='selected'>".($iNumOrdreMaxActiv+1)."</option>";
			}
		}
		$oTpl->remplacer("[OPTIONSMOD]",$sOptionsMod);
		$oTpl->remplacer("[OPTIONSRUB]",$sOptionsRub);
		$oTpl->remplacer("[OPTIONSORDRE]",$sOptNumOrdreActiv);
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
			$sOptionActiv = "<option value='0'>Pas d'activité trouv&eacute;e</option>";
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
				$sOptionActiv = "<option value='0'>Pas d'activité trouv&eacute;e</option>";
			}
			else
			{
				// Verification que le module passée en parametre appartient a la formation courante(séléctionné)
				if($url_iIdModSrc>0)
				{
					$oModuleCourant = new CModule($oProjet->oBdd,$url_iIdModSrc);
					if($oModuleCourant->retIdParent()!=$oFormationCourante->retId())
					{
						$url_iIdModSrc=0;
						$url_iIdRubSrc=0;
					}
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
					$sOptionActiv = "<option value='0'>Pas d'activité trouv&eacute;e</option>";
				}
				else
				{
					// verification que la rubrique passée en paramètre appartienne au module courant
					if($url_iIdRubSrc>0)
					{
						$oRubriqueCourante = new CModule_Rubrique($oProjet->oBdd,$url_iIdRubSrc);
						if($oRubriqueCourante->retIdParent()!=$oModuleCourant->retId())
							$url_iIdRubSrc=0;
					}				
					$sOptionsRub = "";
					for ($i=0; $i<$iNbrRubriques; $i++)
					{
						if(($i == 0 && $url_iIdRubSrc==0) ||($url_iIdRubSrc>0 && $url_iIdRubSrc==$oModuleCourant->aoRubriques[$i]->retId()))
						{
							$sOptionsRub = $sOptionsRub."<option value='".$oModuleCourant->aoRubriques[$i]->retId()."' selected='selected'>".$oModuleCourant->aoRubriques[$i]->retNom()."</option>";
							$oRubriqueCourante = new CModule_Rubrique($oProjet->oBdd,$oModuleCourant->aoRubriques[$i]->retId());
						}
						else
						{
							$sOptionsRub = $sOptionsRub."<option value='".$oModuleCourant->aoRubriques[$i]->retId()."'>".$oModuleCourant->aoRubriques[$i]->retNom()."</option>";
						}
					}
					// affichage de la liste des activités
					$iNbreActivites = $oRubriqueCourante->initActivs();
					if($iNbreActivites==0)
					{
						$sOptionActiv = "<option value='0'>Pas d'activité trouv&eacute;e</option>";
					}
					else
					{
						$sOptionActiv = "";
						for ($i=0; $i<$iNbreActivites; $i++)
						{
							$sOptionActiv = $sOptionActiv."<option value='".$oRubriqueCourante->aoActivs[$i]->retId()."'>".$oRubriqueCourante->aoActivs[$i]->retNom()."</option>";
						}
					}
				}
			}
		}
		$oTpl->remplacer("[OPTIONSFORM]",$sOptionsForm);
		$oTpl->remplacer("[OPTIONSMOD]",$sOptionsMod);
		$oTpl->remplacer("[OPTIONSRUB]",$sOptionsRub);
		$oTpl->remplacer("[OPTIONSACTIV]",$sOptionActiv);
	}
}
$oTpl->afficher();
$oProjet->terminer();
?>
