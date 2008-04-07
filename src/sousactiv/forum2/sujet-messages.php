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
** Fichier ................: sujet-messages.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 21/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/
require_once("globals.inc.php");
require_once(dir_lib("systeme_fichiers.lib.php",TRUE,TRUE));

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdSujet    = (empty($_GET["idSujet"]) ? 0 : $_GET["idSujet"]);
$url_iIdNiveau   = (empty($_GET["idNiveau"]) ? 0 : $_GET["idNiveau"]);
$url_iTypeNiveau = (empty($_GET["typeNiveau"]) ? 0 : $_GET["typeNiveau"]);
$url_iIdEquipe   = (empty($_GET["idEquipe"]) ? 0 : $_GET["idEquipe"]);

// ---------------------
// Initialiser
// ---------------------

$iIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);
$oIds    = new CIds($oProjet->oBdd,$url_iTypeNiveau,$url_iIdNiveau);

$sRepAbsRessourcesForum = dir_forum_ressources($oIds,$url_iTypeNiveau,NULL,TRUE);

// ---------------------
// Permissions
// ---------------------
$bPeutGererTousMessages  = $oProjet->verifPermission("PERM_MOD_MESSAGES_FORUMS");
$bPeutGererTousMessages |= ($oProjet->verifPermission("PERM_MOD_MESSAGES_FORUM") && $oProjet->verifModifierModule());

$bPeutGererSonMessage  = $oProjet->verifPermission("PERM_MOD_MESSAGE_FORUM");
$bPeutGererSonMessage |= $oProjet->verifPermission("PERM_SUP_MESSAGE_FORUM");

//echo "<span style=\"font-size: 7pt;\">[ Peut gérer tous les messages: {$bPeutGererTousMessages} | Peut gérer que son message: {$bPeutGererSonMessage} ]</span>";

// ---------------------
// Initialiser le sujet du forum
// ---------------------
$oSujetForum = new CSujetForum($oProjet->oBdd,$url_iIdSujet);
$iNbMessages = $oSujetForum->initMessages($url_iIdEquipe);
$oSujetForum->initAuteur();		// Initialiser l'auteur du sujet

$oForum = new CForum($oProjet->oBdd,$oSujetForum->retIdParent());

// ---------------------
// Template
// ---------------------
$oTpl = new Template("sujet-messages.tpl");

$oTpl->remplacer("{form->action}",$_SERVER["PHP_SELF"]);

$oBloc_Message = new TPL_Block("BLOCK_MESSAGE",$oTpl);

$oSet_Message               = $oTpl->defVariable("SET_MESSAGE");
$oSet_FichierAttache        = $oTpl->defVariable("SET_FICHIER_ATTACHE");
$oSet_LigneSeparationSujets = $oTpl->defVariable("SET_LIGNE_SEPARATION_SUJETS");
$oSet_Message_Selectionner  = $oTpl->defVariable("SET_SELECTIONNER_MESSAGE");
$oSetEmail                  = $oTpl->defVariable("SET_EMAIL");
$oSetSansEmail              = $oTpl->defVariable("SET_SANS_EMAIL");
$oSet_Image_Homme           = $oTpl->defVariable("SET_IMAGE_HOMME");
$oSet_Image_Femme           = $oTpl->defVariable("SET_IMAGE_FEMME");

// Liste des messages
$iIdxMessage = 0;
$aiNbMessagesDeposesPersonne = array();
$sTelechargerRessource = dir_lib("download.php?f=",FALSE)
	.rawurlencode(dir_forum_ressources($oIds,$url_iTypeNiveau,NULL,FALSE));

foreach ($oSujetForum->aoMessages as $oMessageForum)
{
	$oMessageForum->initAuteur();
	
	$iIdPersMsg = $oMessageForum->oAuteur->retId();
	$sPseudo    = $oMessageForum->oAuteur->retPseudo();
	$sEmail     = $oMessageForum->oAuteur->retEmail();
	
	// Sauvegarder dans un tableau le nombre de messages par personne
	if (!array_key_exists($sPseudo,$aiNbMessagesDeposesPersonne))
		$aiNbMessagesDeposesPersonne[$sPseudo] = $oSujetForum->retNbMessagesDeposesPersonne($iIdPersMsg,$url_iIdEquipe);
	
	$oBloc_Message->ajouter($oSet_Message);
	
	// Bouton permettant de sélectionner le message qui devra être modifié ou
	// supprimé
	if ($bPeutGererTousMessages || ($bPeutGererSonMessage && $iIdxMessage == 0 && $iIdPersMsg == $iIdPers))
	{
		$oBloc_Message->remplacer("{message->bouton_selection}",$oSet_Message_Selectionner);
		$oTpl->remplacer("{nombre_messages}",1);
	}
	else
	{
		if ($iIdxMessage == 0)
			$oTpl->remplacer("{nombre_messages}",0);
		$oBloc_Message->remplacer("{message->bouton_selection}","&nbsp;");
	}
	
	$oBloc_Message->remplacer("{message->id}",$oMessageForum->retId());
	
	// Afficher l'image d'un monsieur ou d'une madame :-)
	$oBloc_Message->remplacer("{personne->sexe}",($oMessageForum->oAuteur->retSexe() == "M" ? $oSet_Image_Homme : $oSet_Image_Femme));
	$oBloc_Message->remplacer("{personne->nom_complet}",emb_htmlentities($oMessageForum->oAuteur->retNomComplet()));
	
	// Email de l'auteur
	if (strlen($sEmail) > 0)
	{
		$oBloc_Message->remplacer("{personne->email}",$oSetEmail);
		$oBloc_Message->remplacer("{a.choix_courriel.href}","?idPers=".$oMessageForum->oAuteur->retId()."&select=".$oMessageForum->oAuteur->retId()."&typeCourriel=courriel-forum@".$oForum->retId());
		//$oBloc_Message->remplacer("{personne->email}",$oMessageForum->oAuteur->retEmail());
	}
	else
		$oBloc_Message->remplacer("{personne->email}",$oSetSansEmail);
	
	$oBloc_Message->remplacer("{personne->pseudo}",$sPseudo);
	$oBloc_Message->remplacer("{personne->nb_messages_deposes}",$aiNbMessagesDeposesPersonne[$sPseudo]);
	$oBloc_Message->remplacer("{message->date}",$oMessageForum->retDate("d/m/y | H:i"));
	
	// Message
	$oBloc_Message->remplacer("{message->texte}",convertBaliseMetaVersHtml($oMessageForum->retMessage()));
	
	// Affichier le/les fichiers attachés au message
	$iNbFichiersAttaches = $oMessageForum->initRessources();
	$sListeFichiersAttaches = NULL;
	
	if ($iNbFichiersAttaches > 0)
	{
		//$sRepAbsRessourcesForum = dir_forum_ressources($oIds,$url_iTypeNiveau,NULL,TRUE);
		
		foreach ($oMessageForum->aoRessources as $oRes)
		{
			$sFichier = $oRes->retUrl();
			
			if (!is_file($sRepAbsRessourcesForum.$sFichier))
				continue;
			
			$sTailleFichier = ret_taille_fichier(filesize($sRepAbsRessourcesForum.$sFichier));
			
			$sHref = $sTelechargerRessource
				.rawurlencode($sFichier)
				."&fn=1";
			$sListeFichiersAttaches .= $oSet_FichierAttache;
			$sListeFichiersAttaches = str_replace("{a['fichier_attache']->href}",$sHref,$sListeFichiersAttaches);
			$sListeFichiersAttaches = str_replace("{a['fichier_attache']->text}",emb_htmlentities($oRes->retNom()." ({$sTailleFichier})"),$sListeFichiersAttaches);
		}
	}
	
	$oBloc_Message->remplacer("{message->ressources}",$sListeFichiersAttaches);
	
	// Ajouter une ligne de séparation entre deux sujets
	$oBloc_Message->remplacer("{ligne_separation_sujets}",(++$iIdxMessage < $iNbMessages ? $oSet_LigneSeparationSujets : NULL));
}

$oBloc_Message->afficher();

if ($url_iIdSujet < 1)
	$oTpl->remplacer("{nombre_messages}",0);

// {{{ Tableau de bord
$oTpl->remplacer(
		array("{tableaudebord.niveau.id}","{tableaudebord.niveau.type}"),
		array($oIds->retIdRubrique(),TYPE_RUBRIQUE)
	);
// }}}

$oTpl->remplacer("{sujet->id}",$url_iIdSujet);
$oTpl->remplacer("{equipe->id}",$url_iIdEquipe);

$oTpl->afficher();

$oProjet->terminer();

?>

