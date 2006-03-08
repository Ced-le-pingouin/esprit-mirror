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
** Fichier ................: collecticiel-filtre.php
** Description ............:
** Date de création .......: 15/04/2005
** Dernière modification ..: 21/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdPers   = (empty($HTTP_GET_VARS["idPers"]) ? 0 : $HTTP_GET_VARS["idPers"]);
$url_iIdEquipe = (empty($HTTP_GET_VARS["idEquipe"]) ? 0 : $HTTP_GET_VARS["idEquipe"]);

// ---------------------
// Initialiser
// ---------------------
$g_iModalite = $oProjet->oSousActivCourante->retModalite(TRUE);

$g_bResponsable = ($oProjet->verifPermission("PERM_VOIR_TOUS_COLLECTICIELS") || $oProjet->verifPermission("PERM_EVALUER_COLLECTICIEL"));

// ---------------------
// Template
// ---------------------
$oTpl = new Template("collecticiel-filtre.tpl");

$oBlocPersonneEquipe =  new TPL_Block("BLOCK_EQUIPE_PERSONNE",$oTpl);

// {{{ Sélectionner une personne ou une équipe
$sSetModaliteParEquipes = $oTpl->defVariable("SET_MODALITE_PAR_EQUIPES");
$sSetModaliteIndividuel = $oTpl->defVariable("SET_MODALITE_INDIVIDUEL");

if (MODALITE_PAR_EQUIPE == $g_iModalite)
{
	if ($g_bResponsable && $oProjet->initEquipes() > 0)
	{
		$oTpl->remplacer("{sltPersEquipe.options.tous}",$sSetModaliteParEquipes);
		
		$oBlocPersonneEquipe->beginLoop();
		
		foreach ($oProjet->aoEquipes as $oEquipe)
		{
			$iIdEquipe = $oEquipe->retId();
			
			$oBlocPersonneEquipe->nextLoop();
			$oBlocPersonneEquipe->remplacer("{sltPersEquipe.options.id}",$iIdEquipe);
			$oBlocPersonneEquipe->remplacer("{sltPersEquipe.options.nom}",htmlentities($oEquipe->retNom()));
			$oBlocPersonneEquipe->remplacer("{sltPersEquipe.options.selected}",($url_iIdEquipe == $iIdEquipe ? " selected=\"selected\"" : NULL));
		}
		
		$oBlocPersonneEquipe->afficher();
	}
	else
	{
		$oBlocPersonneEquipe->effacer();
		$oTpl->remplacer("{sltPersEquipe.options.tous}","Pas d'équipe trouvée");
	}
}
else
{
	if ($g_bResponsable && $oProjet->initInscritsModule() > 0)
	{
		$oTpl->remplacer("{sltPersEquipe.options.tous}",$sSetModaliteIndividuel);
		
		$oBlocPersonneEquipe->beginLoop();
		
		foreach ($oProjet->aoInscrits as $oInscrit)
		{
			$iIdInscrit = $oInscrit->retId();
			
			$oBlocPersonneEquipe->nextLoop();
			$oBlocPersonneEquipe->remplacer("{sltPersEquipe.options.id}",$iIdInscrit);
			$oBlocPersonneEquipe->remplacer("{sltPersEquipe.options.nom}",htmlentities($oInscrit->retNom()." ".$oInscrit->retPrenom()));
			$oBlocPersonneEquipe->remplacer("{sltPersEquipe.options.selected}",($url_iIdPers == $iIdInscrit ? " selected=\"selected\"" : NULL));
		}
		
		$oBlocPersonneEquipe->afficher();
	}
	else
	{
		$oBlocPersonneEquipe->effacer();
		$oTpl->remplacer("{sltPersEquipe.options.tous}","Pas d'étudiant trouvé");
	}
}
// }}}

// {{{ Sélectionner un statut des documents
$asRechTpl = array(
	"{sltStatutDoc.options.evalue}"
	, "{sltStatutDoc.options.accepte}"
	, "{sltStatutDoc.options.approfondir}"
	, "{sltStatutDoc.options.soumis_pour_evaluation}"
	, "{sltStatutDoc.options.en_cours}"
	, "{sltStatutDoc.options.transfere}"
);

$aiReplTpl = array(
	STATUT_RES_EVALUEE
	, STATUT_RES_ACCEPTEE
	, STATUT_RES_APPROF
	, STATUT_RES_SOUMISE
	, STATUT_RES_EN_COURS
	, STATUT_RES_TRANSFERE
);

$oTpl->remplacer($asRechTpl,$aiReplTpl);
// }}}

// {{{ Sélectionner une date
$asRechTpl = array(
	"{sltDateDoc.options.aujourdhui}"
	, "{sltDateDoc.options.hier}"
	, "{sltDateDoc.options.2jours}"
	, "{sltDateDoc.options.3jours}"
	, "{sltDateDoc.options.4jours}"
	, "{sltDateDoc.options.5jours}"
	, "{sltDateDoc.options.6jours}"
	, "{sltDateDoc.options.1semaine}"
	, "{sltDateDoc.options.1mois}"
);

$aiReplTpl = array(
	date("Y-m-d")
	, date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")))
	, date("Y-m-d",mktime(0,0,0,date("m"),date("d")-2,date("Y")))
	, date("Y-m-d",mktime(0,0,0,date("m"),date("d")-3,date("Y")))
	, date("Y-m-d",mktime(0,0,0,date("m"),date("d")-4,date("Y")))
	, date("Y-m-d",mktime(0,0,0,date("m"),date("d")-5,date("Y")))
	, date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y")))
	, date("Y-m-d",mktime(0,0,0,date("m"),date("d")-7,date("Y")))
	, date("Y-m-d",mktime(0,0,0,date("m")-1,date("d"),date("Y")))
);

$oTpl->remplacer($asRechTpl,$aiReplTpl);
// }}}

$oTpl->remplacer("{tri.value}","date");
$oTpl->remplacer("{typeTri.value}",TRI_CROISSANT);

$oTpl->afficher();

$oProjet->terminer();

?>

