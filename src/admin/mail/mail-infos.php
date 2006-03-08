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
** Date de cr�ation .......: 14/12/2004
** Derni�re modification ..: 18/02/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
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
if (is_array($HTTP_POST_VARS) && count($HTTP_POST_VARS) > 0)
{
	$url_sExpediteurCourriel = (empty($HTTP_POST_VARS["expediteurCourriel"]) ? NULL : $HTTP_POST_VARS["expediteurCourriel"]);
	$url_sMessageCourriel    = (empty($HTTP_POST_VARS["messageCourriel"]) ? NULL : $HTTP_POST_VARS["messageCourriel"]);
	$url_sSujetCourriel      = (empty($HTTP_POST_VARS["sujetCourriel"]) ? NULL : $HTTP_POST_VARS["sujetCourriel"]);
	$url_sTypeCourriel       = (empty($HTTP_POST_VARS["typeCourriel"]) ? NULL : $HTTP_POST_VARS["typeCourriel"]);
	
	$oMail = new CMail($url_sSujetCourriel,$url_sMessageCourriel);
	$oMail->defExpediteur($url_sExpediteurCourriel);
	
	// Permet d'envoyer une copie cach�e � l'administrateur de la plate-forme
	if (defined("GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN") &&
		strlen(GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN))
		$oMail->defCopieCarboneInvisible(GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN);
	
	$sListeDestinatairesErrones = NULL;
	
	foreach ($HTTP_POST_VARS["destinataireCourriel"] as $sDestinataire)
	{
		if (strstr($sDestinataire,"*"))
			continue;
		
		// Envoy� la liste des personnes qui ONT bien RECU un courriel
		$sListeDestinatairesErrones .= "<input type=\"hidden\" name=\"destinataireCourriel[]\" value=\"{$sDestinataire}\">\n";
		
		$oMail->ajouterDestinataire(urldecode($sDestinataire));
	}
	
	// Envoyer le courriel
	$oMail->envoyer();
	
	echo "<html>\n"
		."<head>\n"
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
// R�cup�rer les variales de l'url
// ---------------------
foreach ($HTTP_GET_VARS as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialiser
// ---------------------
$url_sTypeCourriel = (empty($HTTP_GET_VARS["typeCourriel"]) ? NULL : $HTTP_GET_VARS["typeCourriel"]);

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
	.htmlentities($oProjet->oUtilisateur->retNomComplet()." "."<".$oProjet->oUtilisateur->retEmail().">")
	."</option>"
	.($oProjet->verifAdministrateur()
		? "<option>".htmlentities($oProjet->retNom()." "."<".$oProjet->retEmail().">")."</option>"
		: NULL);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("mail-infos.tpl");

// {{{ Formulaire
$oTpl->remplacer("{form}","<form action=\"".$HTTP_SERVER_VARS["PHP_SELF"]."\" target=\"_self\" method=\"post\">");
$oTpl->remplacer("{html_options}",$sHtmlOptions);
$oTpl->remplacer("{iframe->src}","destinataires-liste.php{$sParamsUrl}");
$oTpl->remplacer("{sujet_courriel}",$sSujetCourriel);
$oTpl->remplacer("{message_courriel}",$sMessageCourriel);
$oTpl->remplacer("{/form}","</form>");
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

