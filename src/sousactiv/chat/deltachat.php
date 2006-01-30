<?php

/*
** Fichier ................: deltachat.php
** Description ............:
** Date de cr�ation .......:
** Derni�re modification ..: 04/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
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
// R�cup�rer les variables de l'url
// ---------------------
$url_iIdNiveau   = (empty($HTTP_GET_VARS["idNiveau"]) ? 0 : $HTTP_GET_VARS["idNiveau"]);
$url_iTypeNiveau = (empty($HTTP_GET_VARS["typeNiveau"]) ? 0 : $HTTP_GET_VARS["typeNiveau"]);
$url_iIdChat     = (empty($HTTP_GET_VARS["idChat"]) ? 0 : $HTTP_GET_VARS["idChat"]);
$url_iIdEquipe   = (empty($HTTP_GET_VARS["idEquipe"]) ? 0 : $HTTP_GET_VARS["idEquipe"]);

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
// D�finir le r�pertoire de sauvegarde des archives
// ---------------------
$sRepArchives = NULL;

if ($bEnregConversation)
{
	if ($url_iTypeNiveau == TYPE_SOUS_ACTIVITE)
	{
		$iIdActiv = $oIds->retIdActiv();
		$oRep = new CRepertoire(dir_chat_log($iIdActiv,$iIdForm,NULL,TRUE));
		$sRepArchives = dir_chat_log($iIdActiv,$iIdForm);
	}
	else if ($url_iTypeNiveau == TYPE_RUBRIQUE)
	{
		$oRep = new CRepertoire(dir_formation($iIdForm,"chatlog",TRUE));
		$sRepArchives = dir_formation($iIdForm,"chatlog/",FALSE);
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
<title><?="{$sChatNom} [{$sUtilisateurNomComplet}".(isset($sEquipeNom) ? " - {$sEquipeNom}" : NULL)."]"?></title>
</head>
<body style="background-color: black;" topmargin="1" leftmargin="1" rightmargin="1" bottommargin="1">
<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
<tr>
<td align="center" valign="middle">
<applet codebase="client" code="ChatCli.class" archive="DeltaChatClient.jar" width="<?=$iChatFenLargeur?>" height="<?=$iChatFenHauteur?>">
<param name="command" value="">
<param name="ID" value="<?=$iIdUniqueChat?>">
<param name="plateform" value="<?=rawurlencode($sNomPlateforme)?>">
<param name="room" value="<?=rawurlencode($sChatNom)?>">
<param name="group" value="<?=rawurlencode($sEquipeNom)?>">
<param name="user_id" value="<?=$iUtilisateurId?>">
<param name="nickname" value="<?=$sUtilisateurPseudo?>">
<param name="user" value="<?=$sUtilisateurNomComplet?>">
<param name="sex" value="<?=$sUtilisateurSexe?>">
<param name="hostname" value="<?=$HTTP_SERVER_VARS['SERVER_ADDR']?>">
<param name="port" value="<?=$iChatNumPort?>">
<param name="height_room" value="<?=$iChatFenHauteur?>">
<param name="color_room" value="<?=$sChatCouleur?>">
<param name="to_file_conversation" value="<?=$bEnregConversation?>">
<param name="dir_client_log" value="<?=$sRepArchives?>">
<param name="use_private_room" value="<?=$bChatMsgPrive?>">
<param name="language" value="Fra">
<param name="id_session" value="<?=$iIdForm?>">
<param name="location" value="test">
<param name="port_spy" value="0">
</applet>
</td>
</tr>
</table>
</body>
</html>

