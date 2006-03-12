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
** Fichier ................: ressource_supprimer-index.php
** Description ............:
** Date de création .......: 05/12/2002
** Dernière modification ..: 21/04/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$sParamsUrl = NULL;

if (isset($HTTP_GET_VARS["idResSA"]))
{
	// Supprimer les documents sélectionnés
	$sParamsUrl = "?recharger=1";
	
	$oProjet = new CProjet();
	
	$oProjet->initActivCourante();
	
	$sRepCollecticiel = dir_collecticiel($oProjet->oFormationCourante->retId(),$oProjet->oActivCourante->retId(),NULL,TRUE);
	
	foreach (explode(",",$HTTP_GET_VARS["idResSA"]) as $iIdResSA)
	{
		$oResSousActiv = new CRessourceSousActiv($oProjet->oBdd,$iIdResSA);
		@unlink($sRepCollecticiel.$oResSousActiv->retUrl(FALSE,TRUE));
		$oResSousActiv->effacer();
	}
	
	$oProjet->terminer();
}
else if ($HTTP_GET_VARS["nom"])
{
	foreach ($HTTP_GET_VARS as $sCle => $v)
	{
		$mValeur = NULL;
		
		if (is_array($v))
			foreach ($v as $w)
				$mValeur .= (isset($mValeur) ? "," : NULL)
					.$w;
		else
			$mValeur .= $v;
		
		$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
			."{$sCle}={$mValeur}";
	}
}
?>
<html>
<head><title>Supprimer des documents</title></head>
<frameset rows="*,24">
<frame name="principale" src="ressource_supprimer.php<?=$sParamsUrl?>" frameborder="0" scrolling="no" noresize="noresize">
<frame name="menu" src="ressource_supprimer-menu.php" frameborder="0" scrolling="no" marginwidth="0" marginheight="0" noresize="noresize">
</frameset>
</html>

