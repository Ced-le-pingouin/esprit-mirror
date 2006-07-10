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
** Fichier ................: mail-infos.php
** Description ............:
** Date de création .......: 14/12/2004
** Dernière modification ..: 18/02/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_code_lib("mail.class.php"));

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

$sParamsUrl = NULL;

// ---------------------
// Envoyer l'email
// ---------------------
if (is_array($_POST) && count($_POST) > 0)
{
	$url_sExpediteurCourriel = (empty($_POST["expediteurCourriel"]) ? NULL : $_POST["expediteurCourriel"]);
	$url_sMessageCourriel    = (empty($_POST["messageCourriel"]) ? NULL : $_POST["messageCourriel"]);
	$url_sSujetCourriel      = (empty($_POST["sujetCourriel"]) ? NULL : $_POST["sujetCourriel"]);
	$url_sTypeCourriel       = (empty($_POST["typeCourriel"]) ? NULL : $_POST["typeCourriel"]);
	
	$oMail = new CMail($url_sSujetCourriel,$url_sMessageCourriel);
	$oMail->defExpediteur($url_sExpediteurCourriel);
	
	// Permet d'envoyer une copie cachée à l'administrateur de la plate-forme
	if (defined("GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN") &&
		strlen(GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN))
		$oMail->defCopieCarboneInvisible(GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN);
	
	$sListeDestinatairesErrones = NULL;
	
	foreach ($_POST["destinataireCourriel"] as $sDestinataire)
	{
		if (strstr($sDestinataire,"*"))
			continue;
		
		// Envoyé la liste des personnes qui ONT bien RECU un courriel
		$sListeDestinatairesErrones .= "<input type=\"hidden\" name=\"destinataireCourriel[]\" value=\"{$sDestinataire}\">\n";
		
		$oMail->ajouterDestinataire(urldecode($sDestinataire));
	}
	
	// Envoyer le courriel
	$oMail->envoyer();
	
	echo "<html>\n"
		."<head>\n"
	        ."<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n"
		."<script type=\"text/javascript\" language=\"javascript\">\n"
		."<!--\n"
		."function init()"
		."{\n"
		."\tdocument.forms[0].submit();\n"
		."\ttop.resizeTo(360,350);"
		."}\n"
		."//-->\n"
		."</script>\n"
		."</head>\n"
		."<body onload=\"init()\">\n"
		."<form action=\"mail_erreur-index.php\" method=\"post\" target=\"_top\">\n"
		.$sListeDestinatairesErrones
		."</form>\n"
		."</body>\n"
		."</html>\n";
	
	exit();
}

// ---------------------
// Récupérer les variales de l'url
// ---------------------
foreach ($_GET as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialiser
// ---------------------
$url_sTypeCourriel = (empty($_GET["typeCourriel"]) ? NULL : $_GET["typeCourriel"]);

$sSujetCourriel   = NULL;
$sMessageCourriel = NULL;

if (strlen($url_sTypeCourriel) > 0)
{
	$oTpl = new Template("sujet_msg_courriel.inc.tpl");
	
	$oBlocEnvoiCourriel = new TPL_Block("BLOCK_ENVOI_COURRIEL",$oTpl);
	
	// {{{ Modification globale
	$asTplRechercher   = array();
	$asTplRechercher[] = "{plateforme.nom}";
	$asTplRechercher[] = "{plateforme.url}";
	$asTplRechercher[] = "{formation.nom}";
	$asTplRechercher[] = "{module.nom}";
	$asTplRechercher[] = "{rubrique.nom}";
	$asTplRechercher[] = "{sousactivite.nom}";
	$asTplRechercher[] = "{personne.nom}";
	$asTplRechercher[] = "{personne.prenom}";
	$asTplRechercher[] = "{personne.pseudo}";
	
	$asTplRemplacer   = array();
	$asTplRemplacer[] = $oProjet->retNom();
	$asTplRemplacer[] = dir_http_plateform();
	$asTplRemplacer[] = $oProjet->oFormationCourante->retNom();
	$asTplRemplacer[] = $oProjet->oModuleCourant->retNom();
	$asTplRemplacer[] = (isset($oProjet->oRubriqueCourante) && is_object($oProjet->oRubriqueCourante) ? $oProjet->oRubriqueCourante->retNom() : NULL);
	$asTplRemplacer[] = (isset($oProjet->oSousActivCourante) && is_object($oProjet->oSousActivCourante) ? $oProjet->oSousActivCourante->retNom() : NULL);
	$asTplRemplacer[] = $oProjet->oUtilisateur->retNom();
	$asTplRemplacer[] = $oProjet->oUtilisateur->retPrenom();
	$asTplRemplacer[] = $oProjet->oUtilisateur->retPseudo();
	
	$oBlocEnvoiCourriel->remplacer($asTplRechercher,$asTplRemplacer);
	// }}}
	
	// {{{ Modification par type
	$asTplRechercher = array("{plateforme.niveau}");
	$asTplRemplacer  = array();
	
	$asVarEndroits = $oBlocEnvoiCourriel->defTableau("ARRAY_ENDROIT");
	
	if (strstr($url_sTypeCourriel,"courriel-formulaire"))
	{
		$asTplRemplacer[] = $asVarEndroits[4];
	}
	else if (strstr($url_sTypeCourriel,"courriel-collecticiel"))
	{
		$asTplRemplacer[] = $asVarEndroits[3];
	}
	else if (strstr($url_sTypeCourriel,"courriel-forum"))
	{
			list($s,$iIdForum) = explode("@",$url_sTypeCourriel);
			$oForum = new CForum($oProjet->oBdd,$iIdForum);
			$asTplRechercher[] = "{forum.nom}";
			$asTplRemplacer[] = $asVarEndroits[2];
			$asTplRemplacer[] = $oForum->retNom();
			unset($iIdForum,$oForum);
	}
	else if (strstr($url_sTypeCourriel,"courriel-unite"))
	{
		$asTplRemplacer[] = $asVarEndroits[1];
	}
	else if (strstr($url_sTypeCourriel,"courriel-module"))
	{
		$asTplRemplacer[] = $asVarEndroits[0];
	}
	else
	{
		$asTplRemplacer[] = "INCONNU";
	}
	
	$oBlocEnvoiCourriel->remplacer($asTplRechercher,$asTplRemplacer);
	// }}}
	
	$sSujetCourriel   = $oBlocEnvoiCourriel->defVariable("VAR_SUJET_COURRIEL");
	$sMessageCourriel = $oBlocEnvoiCourriel->defVariable("VAR_MESSAGE_COURRIEL");
}

$sHtmlOptions = "<option>"
	.htmlentities($oProjet->oUtilisateur->retNomComplet()." "."<".$oProjet->oUtilisateur->retEmail().">",ENT_COMPAT,"UTF-8")
	."</option>"
	.($oProjet->verifAdministrateur()
		? "<option>".htmlentities($oProjet->retNom()." "."<".$oProjet->retEmail().">",ENT_COMPAT,"UTF-8")."</option>"
		: NULL);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("mail-infos.tpl");

// {{{ Formulaire
$oTpl->remplacer("{form}","<form action=\"".$_SERVER["PHP_SELF"]."\" target=\"_self\" method=\"post\">");
$oTpl->remplacer("{html_options}",$sHtmlOptions);
$oTpl->remplacer("{iframe->src}","destinataires-liste.php{$sParamsUrl}");
$oTpl->remplacer("{sujet_courriel}",$sSujetCourriel);
$oTpl->remplacer("{message_courriel}",$sMessageCourriel);
$oTpl->remplacer("{/form}","</form>");
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

