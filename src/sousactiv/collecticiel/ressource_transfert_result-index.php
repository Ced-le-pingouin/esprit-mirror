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
** Fichier ................: ressource_transfert_result-index.php
** Description ............:
** Date de création .......: 27/11/2002
** Dernière modification ..: 22/06/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

$oProjet->initEquipes();

$aiIdResSA = array();

$iIdSADest = 0;

$iErr = NULL;

if (isset($HTTP_GET_VARS["idResSA"]))
{
	include_once(dir_code_lib("upload.inc.php"));
	
	if (($iIdSADest = $HTTP_GET_VARS["idSADest"]) > 0)
	{
		// {{{ Répertoire source
		$iIdForm = $oProjet->oFormationCourante->retId();
		$sRepSrc = dir_collecticiel($iIdForm,$oProjet->oActivCourante->retId(),NULL,TRUE);
		// }}}
		
		// {{{ Répertoire de destination
		$oSousActiv = new CSousActiv($oProjet->oBdd,$iIdSADest);
		$sRepDst = dir_collecticiel($iIdForm,$oSousActiv->retIdParent(),NULL,TRUE);
		
		// Déterminer le type de transfert
		$iTypeTransfert = $oProjet->oActivCourante->retTypeTransfert($oSousActiv->retIdParent());
		// }}}
		
		foreach (explode("x",$HTTP_GET_VARS["idResSA"]) as $iIdResSA)
		{
			$oRessourceSousActiv = new CRessourceSousActiv($oProjet->oBdd,$iIdResSA);
			$oRessourceSousActiv->initResSousActivSource();
			
			$g_iIdResSA = $oRessourceSousActiv->retId();
			
			$sNomFichierSrc = $oRessourceSousActiv->retUrl(FALSE,TRUE);
			
			if (TYPE_TRANSFERT_EI == $iTypeTransfert)
			{
				// Rechercher les membres de cette équipe
				$oRessourceSousActiv->initEquipe(TRUE);
				
				// Pour chaque membre de l'équipe lui transférer le document
				foreach ($oRessourceSousActiv->oEquipe->aoMembres as $oMembre)
				{
					$g_iIdPersDest = $oMembre->retId();
					include("ressource_transfert.inc.php");
				}
			}
			else
			{
				$g_iIdPersDest = $oRessourceSousActiv->retIdExped();
				include("ressource_transfert.inc.php");
			}
		}
	}
	
	// ---------------------------
	// Si le nombre de transfert échoué est égal au nombre de document à transférer
	// ---------------------------
	if (count(explode("x",$HTTP_GET_VARS["idResSA"])) == count($aiIdResSA))
		$iErr = TRANSFERT_ECHOUE;
	
	$oProjet->terminer();
}
else
	$iErr = PAS_DOCUMENTS_SELECTIONNER;

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Résultat des transferts</title></head>
<frameset border="0" rows="*,24" frameborder="0">
<frame src="ressource_transfert_result.php<?="?err={$iErr}"."&idSA={$iIdSADest}".(count($aiIdResSA) > 0 ? "&idResSA=".implode("x",$aiIdResSA) : NULL)?>" frameborder="0">
<frame src="ressource_transfert-menu.php" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize>
</frameset>
</html>

