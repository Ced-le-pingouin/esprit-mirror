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
** Fichier ................: gestion_mod.php
** Description ............: 
** Date de création .......: 01/02/2002
** Dernière modification ..: 13/11/2004
** Auteur .................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

if ($g_iIdUtilisateur < 1 || $g_iModule < 1)
	return;

if ($act == "ajouter")
{
	ajouter_module();
}
else if ($act == "supprimer")
{
	effacer_module();
}
else if ($act == "modifier")
{
	// Déclaration d'un module
	$oModule = new CModule($oProjet->oBdd,$g_iModule);
	
	if ($url_bModifierStatut)
		$oModule->defStatut($HTTP_POST_VARS["statut_module"]);
	
	if (!$url_bModifier)
		return;
	
	// {{{ Récupérer les variables de l'url
	$url_iOrdreMod = $HTTP_POST_VARS["ordre_module"];
	$url_sNomMod = $HTTP_POST_VARS["nom_module"];
	$url_sDescriptionMod = $HTTP_POST_VARS["descr_module"];
	
	$url_sNomIntitule = $HTTP_POST_VARS["intitule_module"];
	$url_iNumDepart = $HTTP_POST_VARS["numdepart_module"];
	// }}}
	
	// Retourner l'id de l'intitulé
	$oIntitule = new CIntitule($oProjet->oBdd);
	$oIntitule->initParNom($url_sNomIntitule,TYPE_MODULE);
	$iIdIntitule = $oIntitule->retId();
	$oIntitule = NULL;
	
	$oModule->redistNumsOrdre($url_iOrdreMod);
	$oModule->defNom($url_sNomMod);
	$oModule->defDescr($url_sDescriptionMod);
	$oModule->defNumDepart($url_iNumDepart);
	$oModule->defIdIntitule($iIdIntitule);
	
	unset($oModule);
}

?>

