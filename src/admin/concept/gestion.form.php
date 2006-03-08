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
** Fichier ................: gestion_formation.php
** Description ............: 
** Date de cr�ation .......: 01/02/2002
** Derni�re modification ..: 14/09/2004
** Auteur .................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

if ($g_iFormation < 1)
	return;

if ($act == "ajouter")
{
	ajouter_formation();
}
else if ($act == "supprimer")
{
	effacer_formation();
}
else if ($act == "modifier")
{
	// Modifier la table "Formation"
	$oFormation = new CFormation($oProjet->oBdd,$g_iFormation);
	
	if ($url_bModifierStatut)
		$oFormation->defStatut($HTTP_POST_VARS["statut_formation"]);
	
	if (!$url_bModifier)
		return;
	
	$oFormation->redistNumsOrdre($HTTP_POST_VARS["ordre_formation"]);
	$oFormation->defNom($HTTP_POST_VARS["nom_formation"]);
	$oFormation->defdescr($HTTP_POST_VARS["descr_formation"]);
	$oFormation->defInscrAutoModules($HTTP_POST_VARS["INSCR_AUTO_MODULES"]);
	$oFormation->defVisiteurAutoriser((isset($HTTP_POST_VARS["VISITEUR_AUTORISER"]) && $HTTP_POST_VARS["VISITEUR_AUTORISER"] == "on" ? '1' : '0'));
}

?>

