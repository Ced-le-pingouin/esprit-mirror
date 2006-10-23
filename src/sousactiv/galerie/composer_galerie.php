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
** Fichier ................: composer_galerie.php
** Description ............: 
** Date de création .......: 12/09/2005
** Dernière modification ..: 06/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

error_reporting(E_ALL);

require_once("globals.inc.php");
require_once(dir_locale("globals.lang"));
require_once(dir_locale("galerie.lang"));

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

if (!$oProjet->verifPermission("PERM_COMPOSER_GALERIE"))
	exit("Vous n'&ecirc;tes pas autoris&eacute; &agrave; utiliser l'outil 'Composer sa galerie'");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdSousActiv    = (empty($_POST["idSA"]) ? (empty($_GET["idSA"]) ? 0 : $_GET["idSA"]) : $_POST["idSA"]);
$url_sAction         = (empty($_POST["action"]) ? NULL : $_POST["action"]);
$url_iIdPers         = (empty($_POST["personne"]) ? 0 : $_POST["personne"]);
$url_iDocument       = (empty($_POST["document"]) ? 0 : $_POST["document"]);
$url_iIdCollecticiel = (empty($_POST["collecticiel"]) ? 0 : $_POST["collecticiel"]);

if ($url_iIdSousActiv == 0 || $url_iIdSousActiv != $oProjet->oSousActivCourante->retId())
	exit("Erreur : Identifiant action non correspondant");

// ---------------------
// Initialiser
// ---------------------
$oGalerie = new CGalerie($oProjet->oBdd,$oProjet->oSousActivCourante->retId());

// {{{ Appliquer les changements
if (isset($url_sAction))
{
	$oGalerie->effacerRessources(explode(",",(empty($_POST["idsres"]) ? "" : $_POST["idsres"])));
	$oGalerie->ajouterRessources((empty($_POST["ressources"]) ? array() : $_POST["ressources"]),FALSE);
}
// }}}

$g_sIdsRes = NULL;

$iNbCollecticiels = $oGalerie->initCollecticiels(TRUE,TRUE);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("composer_galerie.tpl");

$asSetTpl = array(
	"collecticiel" => $oTpl->defVariable("SET_COLLECTICIEL")
	, "ressource" => $oTpl->defVariable("SET_RESSOURCE")
);

// {{{ Liste des ressources
$oBlocRessource = new TPL_Block("BLOCK_RESSOURCE",$oTpl);

if ($iNbCollecticiels > 0)
{
	$asRechTpl = array(
		"{ressource.checked}"
		, "{ressource.id}"
		, "{ressource.nom}"
		, "{ressource.etat}"
		, "{ressource.auteur}"
	);
	
	$oBlocRessource->beginLoop();
	
	foreach ($oGalerie->aoCollecticiels as $oCollecticiel)
	{
		if ($url_iIdCollecticiel > 0 && $url_iIdCollecticiel != $oCollecticiel->retId())
			continue;
		
		$oBlocRessource->nextLoop();
		$oBlocRessource->ajouter($asSetTpl["collecticiel"]);
		$oBlocRessource->remplacer("{collecticiel.nom}",htmlentities($oCollecticiel->retNom(),ENT_COMPAT,"UTF-8"));
		
		foreach ($oCollecticiel->aoRessources as $oRessource)
		{
			if (STATUT_RES_ACCEPTEE != ($iStatut = $oRessource->retStatut())
				&& STATUT_RES_APPROF != $iStatut
				&& STATUT_RES_TRANSFERE != $iStatut)
				continue;
			
			if ($url_iDocument > 0 && $url_iDocument != $oRessource->retStatut())
				continue;
			
			$oRessource->initExpediteur();
			
			if ($url_iIdPers > 0 && $url_iIdPers != $oRessource->oExpediteur->retId())
				continue;
			
			$iIdRes = $oRessource->retId();
			
			if ($oRessource->estSelectionne)
				$g_sIdsRes .= (isset($g_sIdsRes) ? "," : NULL)
					.$iIdRes;
			
			$amReplTpl = array(
				($oRessource->estSelectionne ? " checked=\"checked\"" : NULL)
				, $iIdRes
				, htmlentities($oRessource->retNom(),ENT_COMPAT,"UTF-8")
				, htmlentities($oRessource->retTexteStatut(),ENT_COMPAT,"UTF-8")
				, htmlentities($oRessource->oExpediteur->retNom()." ".$oRessource->oExpediteur->retPrenom(),ENT_COMPAT,"UTF-8")
			);
			
			$oBlocRessource->nextLoop();
			$oBlocRessource->ajouter($asSetTpl["ressource"]);
			$oBlocRessource->remplacer($asRechTpl,$amReplTpl);
		}
	}
	
	$oBlocRessource->afficher();
}
// }}}

// {{{ Formulaire
$asRechTpl = array("{personne.value}","{document.value}","{collecticiel.value}","{idsres.value}","{sousactiv.id}");
$amReplTpl = array($url_iIdPers,$url_iDocument,$url_iIdCollecticiel,$g_sIdsRes,$url_iIdSousActiv);
$oTpl->remplacer($asRechTpl,$amReplTpl);
// }}}

// {{{ Globales
$oTpl->remplacer("{sousactiv.nom}",htmlentities($oProjet->oSousActivCourante->retNom(),ENT_COMPAT,"UTF-8"));
// }}}

// {{{ Traduction des termes
$asRechTpl = array(
	"[TXT_COMPOSER_SA_GALERIE_TITRE]"
	, "[TXT_GALERIE_TITRE]"
	, "[TXT_COMPOSER_SA_GALERIE_CONSIGNE]"
	, "[TXT_TITRE]"
	, "[TXT_ETAT]"
	, "[TXT_DEPOSE_PAR]"
);

$asReplTpl = array(
	htmlentities(TXT_COMPOSER_SA_GALERIE_TITRE,ENT_COMPAT,"UTF-8")
	, htmlentities(TXT_GALERIE_TITRE,ENT_COMPAT,"UTF-8")
	, nl2br(htmlentities(TXT_COMPOSER_SA_GALERIE_CONSIGNE,ENT_COMPAT,"UTF-8"))
	, htmlentities(TXT_TITRE,ENT_COMPAT,"UTF-8")
	, htmlentities(TXT_ETAT,ENT_COMPAT,"UTF-8")
	, htmlentities(TXT_DEPOSE_PAR,ENT_COMPAT,"UTF-8")
);

$oTpl->remplacer($asRechTpl,$asReplTpl);
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

