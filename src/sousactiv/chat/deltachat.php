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
** Fichier ................: deltachat.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 04/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once("connecte.class.php");
require_once(dir_database("ids.class.php"));
require_once(dir_database("chat.tbl.php"));
require_once(dir_code_lib("repertoire.class.php"));

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdNiveau   = (empty($_GET["idNiveau"]) ? 0 : $_GET["idNiveau"]);
$url_iTypeNiveau = (empty($_GET["typeNiveau"]) ? 0 : $_GET["typeNiveau"]);
$url_iIdChat     = (empty($_GET["idChat"]) ? 0 : $_GET["idChat"]);
$url_iIdEquipe   = (empty($_GET["idEquipe"]) ? 0 : $_GET["idEquipe"]);

// ---------------------
// Initialiser
// ---------------------
$oIds = new CIds($oProjet->oBdd,$url_iTypeNiveau,$url_iIdNiveau);

$iIdForm = $oIds->retIdForm();

// Configurer le chat
$oChat = new CChat($oProjet->oBdd,$url_iIdChat);

$iIdUniqueChat          = CID.$oChat->retId();
$sNomPlateforme         = $oProjet->retNom();
$sChatNom               = $oChat->retNom();
$sChatCouleur           = $oChat->retValeurCouleur();
$iChatNumPort           = $oProjet->retNumPortChat();
$iUtilisateurId         = $oProjet->retIdUtilisateur();
$sUtilisateurPseudo     = $oProjet->oUtilisateur->retPseudo();
$sUtilisateurNomComplet = $oProjet->oUtilisateur->retNom()." ".$oProjet->oUtilisateur->retPrenom();
$sUtilisateurSexe       = $oProjet->oUtilisateur->retSexe();
$bChatMsgPrive          = ($oChat->retSalonPrive() ? "true" : "false");
$bEnregConversation     = ($oChat->retEnregConversation() ? "true" : "false");
$sEquipeNom             = NULL;
$iChatFenLargeur        = "600";
$iChatFenHauteur        = ($bChatMsgPrive == "true" ? "460" : "312");
$sTailleFenetre         = "{$iChatFenLargeur},".($iChatFenHauteur-15);

if ($oChat->retModalite() == CHAT_PAR_EQUIPE)
{
	if ($url_iIdEquipe > 0)
		$oEquipe = new CEquipe($oProjet->oBdd,$url_iIdEquipe);
	else
	{
		$oProjet->initEquipe();
		$oEquipe = &$oProjet->oEquipe;
	}
	
	if (is_object($oEquipe))
		$sEquipeNom = $oEquipe->retNom();
	else
		$bEnregConversation = "false";
}

// ---------------------
// Définir le répertoire de sauvegarde des archives
// ---------------------
$sRepArchives = NULL;

if ($bEnregConversation)
{
	if ($url_iTypeNiveau == TYPE_SOUS_ACTIVITE)
	{
		$iIdActiv = $oIds->retIdActiv();
		$oRep = new CRepertoire(dir_chat_log($iIdActiv,$iIdForm,NULL,TRUE));
		$sRepArchives = str_replace($_SERVER['DOCUMENT_ROOT'], '', dir_chat_log($iIdActiv,$iIdForm,NULL,TRUE));
	}
	else if ($url_iTypeNiveau == TYPE_RUBRIQUE)
	{
		$oRep = new CRepertoire(dir_formation($iIdForm,"chatlog",TRUE));
		$sRepArchives = str_replace($_SERVER['DOCUMENT_ROOT'], '', dir_formation($iIdForm,"chatlog/",TRUE));
	}
	
	if (isset($sRepArchives) &&
		!$oRep->existe() &&
		!$oRep->creer())
	{
		$bEnregConversation = "false";
		$sRepArchives = NULL;
	}
}

$oProjet->terminer();

?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html;  charset=utf-8">
<title><?php echo "{$sChatNom} [{$sUtilisateurNomComplet}".(isset($sEquipeNom) ? " - {$sEquipeNom}" : NULL)."]"?></title>
</head>
<body style="background-color: black;" topmargin="1" leftmargin="1" rightmargin="1" bottommargin="1">
<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
<tr>
<td align="center" valign="middle">
<applet codebase="client" code="ChatCli.class" archive="DeltaChatClient.jar" width="<?php echo $iChatFenLargeur?>" height="<?php echo $iChatFenHauteur?>">
<param name="command" value="">
<param name="ID" value="<?php echo $iIdUniqueChat?>">
<param name="plateform" value="<?php echo rawurlencode($sNomPlateforme)?>">
<param name="room" value="<?php echo rawurlencode($sChatNom)?>">
<param name="group" value="<?php echo rawurlencode($sEquipeNom)?>">
<param name="user_id" value="<?php echo $iUtilisateurId?>">
<param name="nickname" value="<?php echo $sUtilisateurPseudo?>">
<param name="user" value="<?php echo $sUtilisateurNomComplet?>">
<param name="sex" value="<?php echo $sUtilisateurSexe?>">
<param name="hostname" value="<?php echo $_SERVER['SERVER_ADDR']?>">
<param name="port" value="<?php echo $iChatNumPort?>">
<param name="height_room" value="<?php echo $iChatFenHauteur?>">
<param name="color_room" value="<?php echo $sChatCouleur?>">
<param name="to_file_conversation" value="<?php echo $bEnregConversation?>">
<param name="dir_client_log" value="<?php echo $sRepArchives?>">
<param name="use_private_room" value="<?php echo $bChatMsgPrive?>">
<param name="language" value="Fra">
<param name="id_session" value="<?php echo $iIdForm?>">
<param name="location" value="test">
<param name="port_spy" value="0">
</applet>
<!--<?php echo $_SERVER['DOCUMENT_ROOT'] ?>-->
</td>
</tr>
</table>
</body>
</html>

