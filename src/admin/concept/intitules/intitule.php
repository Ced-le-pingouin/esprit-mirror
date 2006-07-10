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
** Fichier ................: intitule.php
** Description ............: 
** Date de création .......: 15/04/2003
** Dernière modification ..: 23/06/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// ---------------------
$sFonctionInit = NULL;

if (isset($_GET["MODE"]))
{
	$url_iIdIntitule   = (empty($_GET["ID_INTITULE"]) ? NULL : $_GET["ID_INTITULE"]);
	$url_sNomIntitule  = (empty($_GET["NOM_INTITULE"]) ? NULL : $_GET["NOM_INTITULE"]);
	$url_iTypeIntitule = (empty($_GET["TYPE_INTITULE"]) ? NULL : $_GET["TYPE_INTITULE"]);
	
	$oIntitule = new CIntitule($oProjet->oBdd,$url_iIdIntitule);
	
	$oIntitule->defNom($url_sNomIntitule);
	
	if (AJOUTER_INTITULE == $_GET["MODE"])
	{
		$oIntitule->defType($url_iTypeIntitule);
		$oIntitule->ajouter();
		$url_iIdIntitule = $oIntitule->retId();
	}
	else if ($url_iIdIntitule > 0)
	{
		if (MODIFIER_INTITULE == $_GET["MODE"])
		{
			$oIntitule->enregistrer();
		}
		else if (SUPPRIMER_INTITULE == $_GET["MODE"])
		{
			if ($url_iTypeIntitule == TYPE_RUBRIQUE)
				$oObj = new CModule_Rubrique($oProjet->oBdd);
			else if ($url_iTypeIntitule == TYPE_MODULE)
				$oObj = new CModule($oProjet->oBdd);
			else
				$oObj = NULL;
			
			if (is_object($oObj) && $oObj->peutSupprimerIntitule($url_iIdIntitule))
			{
				$oIntitule->supprimer();
				$url_iIdIntitule = $url_sNomIntitule = NULL;
			}
			else
			{
				$sFonctionInit = "\tdocument.getElementById('idInfos').style.visibility = 'visible';";
			}
			
			$oObj = NULL;
		}
	}
}
else
{
	$url_iTypeIntitule = (isset($_GET) ? $_GET["TYPE_INTITULE"] : NULL);
}

if (!isset($url_iTypeIntitule))
	exit();

if ($oProjet->verifPermission("PERM_MOD_TOUTES_SESSIONS"))
	$bGestionIntitule = TRUE;
else
	$bGestionIntitule = FALSE;

// ---------------------
// Rechercher toutes les intitulés
// ---------------------
$oIntitules = new CIntitule($oProjet->oBdd);
$iNbrIntitules = $oIntitules->initIntitules($url_iTypeIntitule);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("intitule.tpl");

$oTpl->remplacer("{fonction->init}",$sFonctionInit);

$oBloc_Intitule = new TPL_Block("BLOCK_INTITULE",$oTpl);
$oBloc_Intitule->beginLoop();

$oSet_Menu_Modif = $oTpl->defVariable("SET_MENU_MODIF");
$oSet_Menu_Vide  = $oTpl->defVariable("SET_MENU_VIDE");

$oSet_Fond_Clair = $oTpl->defVariable("SET_FOND_CELLULE_CLAIR");
$oSet_Fond_Fonce = $oTpl->defVariable("SET_FOND_CELLULE_FONCE");

$sListeIntitules = NULL;
$sNomClassCss = $oSet_Fond_Clair;

foreach ($oIntitules->aoIntitules as $oIntitule)
{
	$sNomIntitule = $oIntitule->retNom(FALSE);
	$sListeIntitules .= (isset($sListeIntitules) ? ";" : NULL)
		.phpString2js($sNomIntitule);
	
	$sMenus = ($bGestionIntitule ? $oSet_Menu_Modif : $oSet_Menu_Vide);
	
	// Insérer une nouvelle ligne
	$oBloc_Intitule->nextLoop();
	$oBloc_Intitule->remplacer("{intitule->style->classe}"," ".$sNomClassCss);
	$oBloc_Intitule->remplacer("{gestion_intitule}",$sMenus);
	$oBloc_Intitule->remplacer("{intitule->id}",$oIntitule->retId());
	$oBloc_Intitule->remplacer("{intitule->nom}",htmlentities($sNomIntitule,ENT_COMPAT,"UTF-8"));
	
	// Changer la couleur de la ligne de la table
	$sNomClassCss = ($oSet_Fond_Clair == $sNomClassCss
		? $oSet_Fond_Fonce
		: $oSet_Fond_Clair);
}

$oTpl->remplacer("{liste->intitules}",$sListeIntitules);

$oBloc_Intitule->afficher();
$oTpl->afficher();

$oProjet->terminer();
?>

