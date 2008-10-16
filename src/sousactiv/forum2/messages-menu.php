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
** Fichier ................: messages-menu.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 14/04/2005
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
$url_iIdSujet    = (empty($_GET["idSujet"]) ? 0 : $_GET["idSujet"]);
$url_iIdEquipe   = (empty($_GET["idEquipe"]) ? 0 : $_GET["idEquipe"]);
$url_iNbMessages = (empty($_GET["nbMessages"]) ? 0 : $_GET["nbMessages"]);

// ---------------------
// Initialiser
// ---------------------
$oSujet = new CSujetForum($oProjet->oBdd,$url_iIdSujet);

$iIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

// ---------------------
// Permissions
// ---------------------
$bPeutGererTousMessages  = $oProjet->verifPermission("PERM_MOD_MESSAGES_FORUMS");
$bPeutGererTousMessages |= ($oProjet->verifPermission("PERM_MOD_MESSAGES_FORUM") && $oProjet->verifModifierModule());

$bPeutAjtMessage = FALSE;

// si la formation est archiv�e et que l'utilisateur n'a pas les droits de modification
if (($oProjet->oFormationCourante->retStatut()== STATUT_ARCHIVE) &&(!$oProjet->verifPermission("PERM_MOD_SESSION_ARCHIVES")))
{
	$bPeutGererTousMessages = $bPeutAjtMessage = $bPeutModifierMessage = FALSE; 
}
else $bPeutAjtMessage = $oProjet->verifPermission("PERM_AJT_MESSAGE_FORUM");

if (!$bPeutGererTousMessages)
{
	if (($bPeutAjtMessage) &&
		$url_iIdEquipe > 0)
	{
		$oForum = new CForum($oProjet->oBdd,$oSujet->retIdParent());
		
		if (MODALITE_PAR_EQUIPE_COLLABORANTE == $oForum->retModalite())
		{
			// Vérifier que cette personne est inscrite dans une équipe
			$bPeutAjtMessage = $oProjet->verifEquipe($url_iIdEquipe);
		}
		else
		{
			// Si c'est un forum par équipe, vérifier que cette personne
			// est inscrite dans cette équipe
			$oEquipeMembre = new CEquipe_Membre($oProjet->oBdd,$url_iIdEquipe);
			$bPeutAjtMessage = $oEquipeMembre->verifMembre($iIdPers);
			unset($oEquipeMembre);
		}
		
		unset($oForum);
	}
}

// ---------------------
// Template
// ---------------------
$sMenuMessages = NULL;

$oTpl = new Template("messages-menu.tpl");

$oSetAjtMessageEquipes = $oTpl->defVariable("SET_MENU_AJOUTER_EQUIPES");
$oSetAjtMessage        = $oTpl->defVariable("SET_MENU_AJOUTER");
$oSetMdfMessage        = $oTpl->defVariable("SET_MENU_MODIFIER");
$oSetSupMessage        = $oTpl->defVariable("SET_MENU_SUPPRIMER");
$oSetSansMenu          = $oTpl->defVariable("SET_SANS_MENU");
$oSetMenuSeparateur    = $oTpl->defVariable("SET_MENU_SEPARATEUR");

if ($url_iIdSujet > 0)
{
	if ($bPeutGererTousMessages && $oSujet->estPourTous())
		$sMenuMessages .= $oSetAjtMessageEquipes;
	
	if ($bPeutGererTousMessages || $bPeutAjtMessage)
		$sMenuMessages .= $oSetAjtMessage;
	
	if ($url_iNbMessages > 0 && $bPeutGererTousMessages)
	{
		$sMenuMessages .= (isset($sMenuMessages) ? $oSetMenuSeparateur : NULL)
			.$oSetMdfMessage;
		
		$sMenuMessages .= (isset($sMenuMessages) ? $oSetMenuSeparateur : NULL)
			.$oSetSupMessage;
	}
}
else
{
	$sMenuMessages = $oSetSansMenu;
}

$oTpl->remplacer("{menu_messages}",$sMenuMessages);
$oTpl->remplacer("{message->equipe->id}",($bPeutGererTousMessages || $bPeutAjtMessage ? 0 : $url_iIdEquipe));

$oTpl->afficher();

$oProjet->terminer();

?>

