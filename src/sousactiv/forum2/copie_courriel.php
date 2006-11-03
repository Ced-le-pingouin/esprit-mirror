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
** Fichier ................: copie_courriel.php
** Description ............:
** Date de création .......: 29/11/2004
** Dernière modification ..: 21/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

$bPeutGererTousMessages  = $oProjet->verifPermission("PERM_MOD_MESSAGES_FORUMS");
$bPeutGererTousMessages |= ($oProjet->verifPermission("PERM_MOD_MESSAGES_FORUM") && $oProjet->verifModifierModule());

$iMonIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);
$sMonEmail  = ($iMonIdPers > 0 ? $oProjet->oUtilisateur->retEmail() : NULL);

// ---------------------
// Mise à jour
// ---------------------
if (is_array($_POST) && count($_POST) > 0)
{
	$url_iIdForum  = (empty($_POST["idForum"]) ? 0 : $_POST["idForum"]);
	
	if ($url_iIdForum > 0 && $iMonIdPers > 0)
	{
		$url_aiIdsEquipes   = (empty($_POST["idEquipes"]) ? NULL : $_POST["idEquipes"]);
		$iNbrEquipes        = count($url_aiIdsEquipes);
		$url_iCopieCourriel = (empty($_POST["copieCourriel"]) ? ($iNbrEquipes > 0 ? "1" : "0") : ("on" == $_POST["copieCourriel"]));
		
		$oForumPrefs = new CForumPrefs($oProjet->oBdd);
		
		if ($oForumPrefs->initForumPrefs($url_iIdForum,$iMonIdPers))
		{
			// Mise à jour de la table "ForumPrefs"
			$oForumPrefs->defCopieCourriel($url_iCopieCourriel);
			$oForumPrefs->enregistrer();
		}
		else
		{
			// Ajouter un enregistrement dans la table "ForumPrefs"
			$oForumPrefs->ajouter($url_iIdForum,$iMonIdPers,$url_iCopieCourriel);
		}
		
		if ($oForumPrefs->estForumParEquipe() && empty($url_aiIdsEquipes) && !$bPeutGererTousMessages)
		{
			$url_aiIdsEquipes = array();
			
			if ($url_iCopieCourriel)
			{
				if (MODALITE_PAR_EQUIPE == $oForumPrefs->retModalite())
				{
					if ($oProjet->initEquipe())
						$url_aiIdsEquipes[] = $oProjet->oEquipe->retId();
				}
				else
				{
					// Dans le cas où, la modalité du forum est un forum par équipe
					// autre que "Equipe isolée", dès lors la personne doit être au
					// courant (par mail) des messages déposés par n'importe quelles
					// équipes.
					$oProjet->initEquipes();
					
					foreach ($oProjet->aoEquipes as $oEquipe)
						$url_aiIdsEquipes[] = $oEquipe->retId();
				}
			}
		}
		
		$oForumPrefs->defEquipes($url_aiIdsEquipes);
		
		fermerBoiteDialogue("top.opener.location=top.opener.location;");
		
		exit();
	}
}
else
{
	// Récupérer les variables de l'url
	$url_iIdForum  = (empty($_GET["idForum"]) ? 0 : $_GET["idForum"]);
}

// ---------------------
// Initialiser
// ---------------------
$bPeutUtiliserCopieCourriel = $oProjet->verifPermission("PERM_COPIE_COURRIEL_FORUM");

$oForumPrefs = new CForumPrefs($oProjet->oBdd);
$oForumPrefs->initForumPrefs($url_iIdForum,$iMonIdPers);

$iModaliteForum  = $oForumPrefs->retModalite();
$bForumParEquipe = (MODALITE_POUR_TOUS != $iModaliteForum);

$sFrameMenuSrc = "copie_courriel-menu.php";

// ---------------------
// Template
// ---------------------
$oTpl = new Template("copie_courriel.tpl");

$oBlocJavascriptFunctionValider = new TPL_Block("BLOCK_JAVASCRIPT_FUNCTION_VALIDER",$oTpl);

$oBlocCopieCourriel = new TPL_Block("BLOCK_COPIE_COURRIEL",$oTpl);

// Variables du template
$sVarSansEmail            = $oTpl->defVariable("SET_SANS_EMAIL");
$sVarEmailErrone          = $oTpl->defVariable("SET_EMAIL_ERRONE");
$sVarMessageCommun        = $oTpl->defVariable("SET_MESSAGE_COMMUN");
$sVarCopieCourriel        = $oTpl->defVariable("SET_COPIE_COURRIEL");
$sVarCopieCourrielEquipes = $oTpl->defVariable("SET_COPIE_COURRIEL_EQUIPES");

$sFormulaire = "<form>";
$sJavascriptFunctionValider = NULL;

if (empty($sMonEmail))
{
	// L'utilisateur n'a pas d'adresse électronique
	$sFrameMenuSrc .= "?menu=profil";
	$oBlocJavascriptFunctionValider->effacer();
	$oBlocCopieCourriel->ajouter($sVarSansEmail);
}
else if (!emailValide($sMonEmail))
{
	// L'adresse électronique de l'utilisateur n'est pas valide
	$sFrameMenuSrc .= "?menu=profil";
	$oBlocJavascriptFunctionValider->effacer();
	$oBlocCopieCourriel->ajouter($sVarEmailErrone);
}
else if ($iMonIdPers > 0 && $bPeutUtiliserCopieCourriel)
{
	$sFrameMenuSrc .= "?menu=valider";
	$asVarCopieCourrielValider = $oBlocJavascriptFunctionValider->defVariable("VAR_COPIE_COURRIEL_VALIDER",TRUE);
	
	if ($bForumParEquipe && ($bPeutGererTousMessages || MODALITE_PAR_EQUIPE != $iModaliteForum))
	{
		$oBlocJavascriptFunctionValider->remplacer("{valider}",$asVarCopieCourrielValider[1]);
		$oBlocCopieCourriel->ajouter($sVarCopieCourrielEquipes);
	}
	else
	{
		$oBlocJavascriptFunctionValider->remplacer("{valider}",$asVarCopieCourrielValider[0]);
		$sFormulaire = "<form action=\"copie_courriel.php\" target=\"_self\" method=\"post\">";
		$oBlocCopieCourriel->ajouter($sVarCopieCourriel);
	}
	
	$oBlocCopieCourriel->remplacer("{message_commun}",$sVarMessageCommun);
	
	$oBlocCopieCourriel->remplacer("{personne->email}",$sMonEmail);
	//$oBlocCopieCourriel->remplacer("{personne->email}","<a href=\"mailto:{$sMonEmail}\" target=\"_self\" onfocus=\"blur()\">".mb_convert_encoding($sMonEmail,"HTML-ENTITIES","UTF-8")."</a>");
	
	$oBlocCopieCourriel->remplacer("{iframe->src}","copie_courriel-equipes.php?idForum={forum->id}");
	
	$oBlocJavascriptFunctionValider->afficher();
}

$oBlocCopieCourriel->remplacer("{copieCourriel->selectionne}",($oForumPrefs->retCopieCourriel() ? " checked=\"checked\"" : NULL));

// Afficher la liste des équipes

$oBlocCopieCourriel->afficher();

$oTpl->remplacer("{javascript_function_valider}",$sJavascriptFunctionValider);

$oTpl->remplacer("{frames['menu']->url}",$sFrameMenuSrc);

// Formulaire
$oTpl->remplacer("{html_form}",$sFormulaire);
$oTpl->remplacer("{forum->id}",$url_iIdForum);
$oTpl->remplacer("{/html_form}","</form>");

$oTpl->afficher();

$oProjet->terminer();

?>

