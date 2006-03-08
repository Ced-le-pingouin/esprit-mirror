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
** Fichier ................: gestion_activ.php
** Description ............: 
** Date de cr�ation .......: 01/02/2002
** Derni�re modification ..: 14/09/2004
** Auteur .................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

if ($g_iActiv < 1)
	return;

if ($act == "ajouter")
{
	ajouter_activite();
}
else if ($act == "supprimer")
{
	effacer_activite();
}
else if ($act == "modifier")
{
	$oActiv = new CActiv($oProjet->oBdd,$g_iActiv);
	
	if ($url_bModifierStatut)
		$oActiv->defStatut($HTTP_POST_VARS["STATUT"]);
	
	if (!$url_bModifier)
		return;
	
	$iNumOrdre    = $HTTP_POST_VARS["ORDRE"];
	$sNom         = $HTTP_POST_VARS["NOM"];
	$sDescription = (empty($HTTP_POST_VARS["DESCRIPTION"]) ? NULL : $HTTP_POST_VARS["DESCRIPTION"]);
	$iModalite    = $HTTP_POST_VARS["MODALITE"];
	
	// Sauvegarder les modifications
	$oActiv->redistNumsOrdre($iNumOrdre);
	$oActiv->defNom($sNom);
	$oActiv->defDescr($sDescription);
	$oActiv->defModalite($iModalite);
}

?>

