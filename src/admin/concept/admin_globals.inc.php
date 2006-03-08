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
** Fichier .................: admin_fonction.inc.php
** Description .............: 
** Date de création ........: 02/05/2002
** Dernière modification ...: 26/10/2004
** Auteurs .................: Filippo PORCO <filippo.porco@umh.ac.be>
** Emails ..................: ute@umh.ac.be
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

function ajouter_repertoire ($v_sNomRepertoire)
{
	if (is_dir($v_sNomRepertoire))
		return;
	
	// Créer le nouveau répertoire
	mkdir($v_sNomRepertoire,0744);
	
	// Copier le fichier qui va permettre d'afficher les pages html,
	// les images et les exercices correctement
	/*$sFichierSrc = dir_root_formation("main.php.cc2");
	$sFichierDst = "{$v_sNomRepertoire}main.php";
	
	@copy($sFichierSrc,$sFichierDst);
	@chmod($sFichierDst,0600);*/
}

function deleteTree ($v_sRepEffacer)
{
	$v_sRepEffacer = ereg_replace("/\$","",$v_sRepEffacer);

	if (is_dir($v_sRepEffacer))
	{
		$handle = opendir($v_sRepEffacer);

		while (($file = readdir($handle)) !== FALSE)
		{
			if (($file != ".") && ($file != ".."))
				deleteTree($v_sRepEffacer."/".$file);
		}

		closedir($handle);

		@rmdir($v_sRepEffacer);
	}
	else
	{
		@unlink($v_sRepEffacer);
	}
}

function copyTree ($rep_src,$rep_des)
{
	$sChemin = getcwd();

	if (!is_dir($rep_des))
		@mkdir($rep_des,0777);

	if (!is_dir($rep_src))
	  return;

	copyFiles($rep_src,$rep_des);

	chdir($sChemin);
}

function copyFiles ($rep_src,$rep_des)
{
  	if (!is_dir($rep_des))
		mkdir($rep_des,0777);

	if (is_dir($rep_src))
	{
		chdir($rep_src);

		$handle = opendir(".");

		while (($file = readdir($handle)) !== false)
		{
			if (($file != ".") && ($file != ".."))
			{
				if (is_dir($file))
				{
					copyFiles($rep_src."/".$file,$rep_des."/".$file);

					chdir($rep_src);
				}

				if (is_file($file))
					copy($rep_src."/".$file,$rep_des."/".$file);
			}
		}

		closedir($handle);
	}
}

function rendersize ($size) {

	$type = 'bytes';

	if ($size > '1023') {

		$size = $size/1024;
		$type = 'KB';

	}

	if ($size > '1023') {

		$size = $size/1024;
		$type = 'MB';

	}

	if ($size > '1023') {

		$size = $size/1024;
		$type = 'GB';

	}

	if ($size > '1023') {

		$size = $size/1024;
		$type = 'TB';

	}

	// Fix decimals and stuff
	if ($size < '10') $size = intval($size*100)/100;
	else if ($size < '100') $size = intval($size*10)/10;
	else $size = intval($size);

	// Comment the following line if you want X.XX KB displayed instead of X,XX KB
	$size = str_replace("." , "," , $size);
	return "$size $type";
}

// ---------------------
// FORMATION
// ---------------------
function ajouter_formation () { return 0; }

function effacer_formation ()
{
	global $oProjet;
	global $type;
	global $g_iFormation,$g_iModule,$g_iRubrique,$g_iUnite,$g_iActiv,$g_iSousActiv;
	
	if ($g_iFormation < 1 || !is_object($oProjet))
		return 0;
	
	$oFormation = new CFormation($oProjet->oBdd,$g_iFormation);
	$oFormation->effacerLogiquement();
	
	$type = 0;
	$g_iFormation = $g_iModule = $g_iRubrique = $g_iUnite = $g_iActiv = $g_iSousActiv = 0;
}

// ---------------------
// MODULE
// ---------------------
function ajouter_module ()
{
	global $oProjet;
	global $type;
	global $g_iFormation,$g_iModule,$g_iRubrique,$g_iUnite,$g_iActiv,$g_iSousActiv;
	
	$type = TYPE_MODULE;
	$g_iRubrique = $g_iUnite = $g_iActiv = $g_iSousActiv = 0;
	
	if ($g_iFormation < 1 || !is_object($oProjet->oUtilisateur))
		return 0;
	
	// Auteur de ce module
	$iIdPers = $oProjet->oUtilisateur->retId();
	
	$oModule = new CModule($oProjet->oBdd);
	
	if (($g_iModule = $oModule->ajouter($g_iFormation,$iIdPers)) < 1)
		return 0;
	
	$oModule = NULL;
	
	return $g_iModule;
}

function effacer_module ()
{
	global $oProjet;
	global $type;
	global $g_iModule,$g_iRubrique,$g_iUnite,$g_iActiv,$g_iSousActiv;
	
	if ($g_iModule < 1 || !is_object($oProjet))
		return 0;
	
	$oModule = new CModule($oProjet->oBdd,$g_iModule);
	$oModule->effacer();
	
	$type = TYPE_FORMATION;
	$g_iModule = $g_iRubrique = $g_iUnite = $g_iActiv = $g_iSousActiv = 0;
	
	return 0;
}

// ---------------------
// RUBRIQUE
// ---------------------
function ajouter_rubrique ()
{
	global $oProjet;
	global $type;
	global $g_iModule,$g_iRubrique,$g_iUnite,$g_iActiv,$g_iSousActiv;
	
	if (gettype($oProjet) != "object")
		return 0;
	
	$oModule_Rubrique = new CModule_Rubrique($oProjet->oBdd);
	$oModule_Rubrique->defIdParent($g_iModule);
	
	$type = TYPE_RUBRIQUE;
	$g_iRubrique = $oModule_Rubrique->ajouter();
	$g_iUnite = $g_iActiv = $g_iSousActiv = 0;
}

function effacer_rubrique ()
{
	global $oProjet;
	global $type;
	global $g_iRubrique,$g_iUnite,$g_iActiv,$g_iSousActiv;
	
	if ($g_iRubrique < 1 || gettype($oProjet) != "object")
		return 0;
	
	$oRubrique = new CModule_Rubrique($oProjet->oBdd,$g_iRubrique);
	$oRubrique->effacer();
	
	$type = TYPE_MODULE;
	$g_iRubrique = $g_iUnite = $g_iActiv = $g_iSousActiv = 0;
	
	return 0;
}

// ---------------------
// ACTIVITE
// ---------------------
function ajouter_activite ()
{	
	global $oProjet;
	global $type;
	global $g_iFormation,$g_iRubrique,$g_iActiv,$g_iSousActiv;
	
	if (gettype($oProjet) != "object")
		return 0;
	
	$oActiv = new CActiv($oProjet->oBdd);
	$g_iActiv = $oActiv->ajouter($g_iRubrique);
	$type = TYPE_ACTIVITE;
	$g_iSousActiv = 0;
	
	// Créer le répertoire
	if ($g_iFormation > 0 && $g_iActiv > 0)
	{
		$sRepActiv = dir_cours($g_iActiv,$g_iFormation,NULL,TRUE);
		if (!is_dir($sRepActiv))
			mkdir($sRepActiv);
	}
}

function effacer_activite ()
{
	global $oProjet;
	global $type;
	global $g_iActiv,$g_iSousActiv;
	
	if ($g_iActiv < 1 || gettype($oProjet) != "object")
		return 0;
	
	$oActiv = new CActiv($oProjet->oBdd,$g_iActiv);
	
	$oActiv->effacer();
	
	$type = TYPE_RUBRIQUE;
	$g_iActiv = $g_iSousActiv = 0;
}

// *************************************
// SOUS-ACTIVITE
// *************************************
function ajouter_sous_activite ()
{
	global $oProjet,$type,$g_iActiv,$g_iSousActiv,$g_iIdUtilisateur;
	if (gettype($oProjet) != "object")
		return 0;
	$oSousActiv = new CSousActiv($oProjet->oBdd);
	$g_iSousActiv = $oSousActiv->ajouter($g_iActiv);
	$type = TYPE_SOUS_ACTIVITE;
}

function effacer_sous_activite ()
{
	global $oProjet,$type,$g_iSousActiv;
	if ($g_iSousActiv < 1 || gettype($oProjet) != "object")
		return 0;
	$oSousActiv = new CSousActiv($oProjet->oBdd,$g_iSousActiv);
	$oSousActiv->effacer();
	$type = TYPE_ACTIVITE; $g_iSousActiv = 0;
}

?>
