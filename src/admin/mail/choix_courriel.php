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
** Fichier ................: choix_courriel.php
** Description ............:
** Date de création .......: 17/01/2005
** Dernière modification ..: 16/02/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

if (!is_object($oProjet->oUtilisateur))
	exit("<html><body></body></html>");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$sParamsUrl = NULL;

foreach ($_GET as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

$url_bToutesPersonnes = (empty($_GET["idPers"]) ? FALSE : $_GET["idPers"] == "tous");

// Types du courriel : cours, unite, forum
$url_sTypeEnvoiCourriel = (empty($_GET["typeCourriel"])
	? NULL
	: $_GET["typeCourriel"]);

// ---------------------
// Permissions
// ---------------------
$bPermisUtiliserBoiteCourriellePC = /*!$url_bToutesPersonnes & */ $oProjet->verifPermission("PERM_UTILISER_BOITE_COURRIELLE_PC");

// ---------------------
// Initialiser
// ---------------------

// {{{ Insérer ces lignes dans l'en-tête de la page html
$sBlockHtmlHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;
// }}}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("choix_courriel.tpl");

$oBlockHtmlHead = new TPL_Block("BLOCK_HTML_HEAD",$oTpl);
$oBlockHtmlHead->ajouter($sBlockHtmlHead);
$oBlockHtmlHead->afficher();

$oBlocEnvoyerA = new TPL_Block("BLOCK_ENVOYER_A",$oTpl);

$oBlocChoisirBoiteCourriel      = new TPL_Block("BLOCK_CHOISIR_BOITE_COURRIEL",$oTpl);
$oBlocUtiliserBoiteCourriellePC = new TPL_Block("BLOCK_UTILISER_BOITE_COURRIELLE_PC",$oBlocChoisirBoiteCourriel);

// {{{ Onglet
$oTplOnglet = new Template(dir_theme("onglet/onglet_tab.tpl",FALSE,TRUE));
$sOnglet = $oTplOnglet->defVariable("SET_ONGLET")
	.$oTpl->defVariable("SET_SEPARATEUR_BLOC");
unset($oTplOnglet);
// }}}


$oBlocEnvoyerA->ajouter($sOnglet);
$oBlocEnvoyerA->remplacer("{onglet->titre}",$oBlocEnvoyerA->defVariable("VAR_TITRE"));
$oBlocEnvoyerA->remplacer("{onglet->texte}",$oBlocEnvoyerA->defVariable("VAR_TEXTE"));
$oBlocEnvoyerA->afficher();

$oBlocChoisirBoiteCourriel->ajouter("<div id=\"idChoisirDansListe\">{$sOnglet}</div>");
$oBlocChoisirBoiteCourriel->remplacer("{onglet->titre}",$oBlocChoisirBoiteCourriel->defVariable("VAR_TITRE"));
$oBlocChoisirBoiteCourriel->remplacer("{onglet->texte}",$oBlocChoisirBoiteCourriel->defVariable("VAR_TEXTE"));

if ($bPermisUtiliserBoiteCourriellePC)
	$oBlocUtiliserBoiteCourriellePC->afficher();
else
	$oBlocUtiliserBoiteCourriellePC->effacer();

$oBlocChoisirBoiteCourriel->afficher();

// {{{ IFRAME
$oTpl->remplacer("{iframe.src}","choix_courriel-liste.php".(isset($sParamsUrl) ? $sParamsUrl : NULL));
// }}}

// {{{ Formulaire
$oTpl->remplacer(array("{form}","{/form}"),array("<form action=\"choix_courriel-valider.php\" target=\"Principale\" method=\"post\">","</form>"));
$oTpl->remplacer(array("{radio['plateforme'].value}","{radio['os'].value}"),array(BOITE_COURRIELLE_PLATEFORME,BOITE_COURRIELLE_OS));
$oTpl->remplacer("{type_courriel}",$url_sTypeEnvoiCourriel);
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

