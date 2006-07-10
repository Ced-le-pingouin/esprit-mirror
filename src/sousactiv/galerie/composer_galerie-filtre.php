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
** Fichier ................: composer_galerie-filtre.php
** Description ............: 
** Date de création .......: 12/09/2005
** Dernière modification ..: 13/09/2005
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
$url_iIdSousActiv = (empty($_GET["idSA"]) ? 0 : $_GET["idSA"]);

// ---------------------
// Initialiser
// ---------------------
$oGalerie = new CGalerie($oProjet->oBdd,$oProjet->oSousActivCourante->retId());
$iNbCollecticiels = $oGalerie->initCollecticiels();

// ---------------------
// Template
// ---------------------
$oTpl = new Template("composer_galerie-filtre.tpl");

// {{{ Liste des inscrits
$oBlocPersonne = new TPL_Block("BLOCK_PERSONNE",$oTpl);

if ($oProjet->initInscritsModule() > 0)
{
	$asRechTpl = array("{personne.id}","{personne.nom}");
	
	$oBlocPersonne->beginLoop();
	
	foreach ($oProjet->aoInscrits as $oInscrit)
	{
		$amReplTpl = array(
			$oInscrit->retId()
			, htmlentities($oInscrit->retNom()." ".$oInscrit->retPrenom(),ENT_COMPAT,"UTF-8")
		);
		
		$oBlocPersonne->nextLoop();
		$oBlocPersonne->remplacer($asRechTpl,$amReplTpl);
	}
	
	$oBlocPersonne->afficher();
}
else
	$oBlocPersonne->effacer();
// }}}

// {{{ Liste des statuts des ressources
$aiStatuts = array(STATUT_RES_TOUS_DOCUMENTS,STATUT_RES_ACCEPTEE,STATUT_RES_APPROF,STATUT_RES_TRANSFERE);
$aaStatutsRes = retListeStatutsRessources();

$asRechTpl = array("{statut.id}","{statut.nom}");

$oBlocStatutsRes = new TPL_Block("BLOCK_STATUT",$oTpl);
$oBlocStatutsRes->beginLoop();

foreach ($aiStatuts as $iStatut)
{
	$amReplTpl = array(
		$aaStatutsRes[$iStatut][0]
		, htmlentities($aaStatutsRes[$iStatut][1],ENT_COMPAT,"UTF-8")
	);
	
	$oBlocStatutsRes->nextLoop();
	$oBlocStatutsRes->remplacer($asRechTpl,$amReplTpl);
}

$oBlocStatutsRes->afficher();
// }}}

// {{{ Liste des collecticiels
$oBlocCollecticiels = new TPL_Block("BLOCK_COLLECTICIELS",$oTpl);

if ($iNbCollecticiels > 0)
{
	$asRechTpl = array("{collecticiel.id}","{collecticiel.nom}");
	
	$oBlocCollecticiel = new TPL_Block("BLOCK_COLLECTICIEL",$oBlocCollecticiels);
	$oBlocCollecticiel->beginLoop();
	
	foreach ($oGalerie->aoCollecticiels as $oCollecticiel)
	{
		$amReplTpl = array(
			$oCollecticiel->retId()
			, htmlentities($oCollecticiel->retNom(),ENT_COMPAT,"UTF-8")
		);
		
		$oBlocCollecticiel->nextLoop();
		$oBlocCollecticiel->remplacer($asRechTpl,$amReplTpl);
	}
	
	$oBlocCollecticiel->afficher();
	$oBlocCollecticiels->afficher();
}
else
	$oBlocCollecticiels->effacer();
// }}}

// {{{ Formulaire
$oTpl->remplacer("{sousactiv.id}",$url_iIdSousActiv);
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

