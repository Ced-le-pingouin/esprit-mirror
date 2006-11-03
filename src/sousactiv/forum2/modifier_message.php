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
** Fichier ................: modifier_message.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 29/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

if (!is_object($oProjet->oUtilisateur))
	exit();

// ---------------------
// Appliquer les changements
// ---------------------
if (isset($_POST["modaliteFenetre"]))
{
	$url_sModaliteFenetre = $_POST["modaliteFenetre"];
	$url_iIdSujet         = $_POST["idSujet"];
	$url_iIdMessage       = $_POST["idMessage"];
	$url_iIdNiveau        = (empty($_POST["idNiveau"]) ? 0 : $_POST["idNiveau"]);
	$url_iTypeNiveau      = (empty($_POST["typeNiveau"]) ? 0 : $_POST["typeNiveau"]);
	$url_iIdEquipe        = (empty($_POST["idEquipe"]) ? 0 : $_POST["idEquipe"]);
	
	if ("ajouter" == $url_sModaliteFenetre || "modifier" == $url_sModaliteFenetre)
	{
		$url_sMessage = trim($_POST["messageSujet"]);
		
		// Numéro d'identifiant de la personne
		$iIdPers = $oProjet->oUtilisateur->retId();
		
		if (strlen($url_sMessage) > 0)
		{
			if ("ajouter" == $url_sModaliteFenetre)
			{
				$oMessageForum = new CMessageForum($oProjet->oBdd);
				$oMessageForum->ajouter($url_sMessage,$url_iIdSujet,$iIdPers,$url_iIdEquipe);
				
				// {{{ Copie courriel
				include_once("copie_courriel-mail.inc.php");
				// }}}
			}
			else
			{
				$oMessageForum = new CMessageForum($oProjet->oBdd,$url_iIdMessage);
				$oMessageForum->defMessage($url_sMessage);
				$oMessageForum->enregistrer();
			}
			
			// Répertoire contenant les ressources du forum
			$oIds = new CIds($oProjet->oBdd,$url_iTypeNiveau,$url_iIdNiveau);
			$sRepFichiersForum = dir_forum_ressources($oIds,$url_iTypeNiveau,NULL,TRUE);
			$oMessageForum->defRepRessources($sRepFichiersForum);
			
			// Effacer l'ancien fichier attaché
			if ($_POST["effacerFichierMessage"] == "on")
				$oMessageForum->effacerRessources();
			
			// Déposer le fichier attaché sur le serveur
			if (!empty($_FILES["fichierMessage"]["name"]) &&
				$url_iIdNiveau > 0 &&
				$url_iTypeNiveau > 0)
			{
				include_once(dir_lib("systeme_fichiers.lib.php",TRUE));
				
				mkdirr($sRepFichiersForum);
				
				if (is_dir($sRepFichiersForum))
				{
					// Donner un nom unique au fichier
					include_once(dir_lib("upload.inc.php",TRUE));
					
					// Effacer l'ancienne ressource
					$oMessageForum->effacerRessources();
					
					$sNomFichierUnique = retNomFichierUnique($_FILES["fichierMessage"]["name"],$sRepFichiersForum);
					
					if (move_uploaded_file($_FILES["fichierMessage"]["tmp_name"],($sRepFichiersForum.$sNomFichierUnique)))
						$oMessageForum->ajouterRessource($_FILES["fichierMessage"]["name"],$sNomFichierUnique,$oProjet->oUtilisateur->retId());
				}
			}
		}
	}
	else if ($url_sModaliteFenetre == "supprimer")
	{
		$oIds = new CIds($oProjet->oBdd,$url_iTypeNiveau,$url_iIdNiveau);
		$oMessageForum = new CMessageForum($oProjet->oBdd,$url_iIdMessage);
		$oMessageForum->defRepRessources(dir_forum_ressources($oIds,$url_iTypeNiveau,NULL,TRUE));
		$oMessageForum->effacer();
	}
	
	unset($oMessageForum);
	
	fermerBoiteDialogue("top.opener.rafraichir_liste_sujets('{$url_iIdSujet}','{$url_sModaliteFenetre}');");
	
	exit();
}

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sModaliteFenetre = (empty($_GET["modaliteFenetre"]) ? NULL : $_GET["modaliteFenetre"]);
$url_iIdSujet         = (empty($_GET["idSujet"]) ? 0 : $_GET["idSujet"]);
$url_iIdMessage       = (empty($_GET["idMessage"]) ? 0 : $_GET["idMessage"]);
$url_iIdNiveau        = (empty($_GET["idNiveau"]) ? 0 : $_GET["idNiveau"]);
$url_iTypeNiveau      = (empty($_GET["typeNiveau"]) ? 0 : $_GET["typeNiveau"]);
$url_iIdEquipe        = (empty($_GET["idEquipe"]) ? 0 : $_GET["idEquipe"]);

// ---------------------
// Template de la barre de progression
// ---------------------
$oTplBarreDeProgression = new Template(dir_theme("barre_de_progression.inc.tpl",FALSE,TRUE));

// ---------------------
// Template du message important
// ---------------------
$oTplMessageImportant = new Template(dir_theme("dialogue/dialog-important.inc.tpl",FALSE,TRUE));

// ---------------------
// Template de l'éditeur
// ---------------------
$oTplEditeur = new Template(dir_admin("commun","editeur.inc.tpl",TRUE));

// {{{ Icones du tableau de bord
$oBlocTableauDeBord = new TPL_Block("BLOCK_TABLEAU_DE_BORD",$oTplEditeur);

if ($oProjet->retStatutUtilisateur() < STATUT_PERS_ETUDIANT)
	$oBlocTableauDeBord->afficher();
else
	$oBlocTableauDeBord->effacer();
// }}}

$oBlocVisualiseur = new TPL_Block("BLOCK_VISUALISEUR",$oTplEditeur);
$oBlocVisualiseur->effacer();
$oSetEditeur = $oTplEditeur->defVariable("SET_EDITEUR");

// ---------------------
// Template principale
// ---------------------
$oTpl = new Template("modifier_message.tpl");

$oBlockStyleSheetErreur = new TPL_Block("BLOCK_STYLESHEET_ERREUR",$oTpl);
$oBlockMessage          = new TPL_Block("BLOCK_MESSAGE",$oTpl);

$oSetMessageSupprimerSujet        = $oTpl->defVariable("SET_MESSAGE_SUPPRIMER_SUJET");
$oSetMessageSupprimerSujetEquipes = $oTpl->defVariable("SET_MESSAGE_SUPPRIMER_SUJET_EQUIPES");
$oSetQuestionSupprimerSujet       = $oTpl->defVariable("SET_QUESTION_SUPPRIMER_SUJET");

$oSetFichierAttache               = $oTpl->defvariable("SET_FICHIER_ATTACHE");

$oTpl->remplacer("{form->action}","modifier_message.php");
$oTpl->remplacer("{fenetre->modalite}",$url_sModaliteFenetre);
$oTpl->remplacer("{sujet->id}",$url_iIdSujet);
$oTpl->remplacer("{message->id}",$url_iIdMessage);
$oTpl->remplacer("{niveau->id}",$url_iIdNiveau);
$oTpl->remplacer("{niveau->type}",$url_iTypeNiveau);
$oTpl->remplacer("{equipe->id}",$url_iIdEquipe);

// Onglet
$oTplOnglet = new Template(dir_theme("onglet/onglet_tab.tpl",FALSE,TRUE));
$oSetOnglet = $oTplOnglet->defVariable("SET_ONGLET");

if ($url_sModaliteFenetre == "ajouter")
{
	$oBlockStyleSheetErreur->effacer();
	
	$oBlockMessage->ajouter($oSetOnglet);
	
	// Onglet "Message"
	$oBlockMessage->remplacer("{onglet->message}",$oSetOnglet);
	$oBlockMessage->remplacer("{onglet->titre}","Message");
	$oBlockMessage->remplacer("{onglet->texte}",$oSetEditeur.$oSetFichierAttache);
	$oBlockMessage->remplacer("{message->texte}",NULL);
}
else if ($url_sModaliteFenetre == "modifier")
{
	$oBlockStyleSheetErreur->effacer();
	
	$oMessageForum = new CMessageForum($oProjet->oBdd,$url_iIdMessage);
	
	$oBlockMessage->ajouter($oSetOnglet);
	
	// Onglet "Message"
	$oBlockMessage->remplacer("{onglet->message}",NULL);
	$oBlockMessage->remplacer("{onglet->titre}","Message");
	$oBlockMessage->remplacer("{onglet->texte}",$oSetEditeur.$oSetFichierAttache);
	
	$oBlocEffacerFichier = new TPL_Block("BLOCK_EFFACER_FICHIER_ATTACHE",$oBlockMessage);
	
	if ($oMessageForum->initRessources() > 0)
	{
		$oBlocEffacerFichier->remplacer("{fichier_attache->nom}",mb_convert_encoding($oMessageForum->aoRessources[0]->retNom(),"HTML-ENTITIES","UTF-8"));
		$oBlocEffacerFichier->afficher();
	}
	
	$oTpl->remplacer("{editeur->sauvegarde}",$oMessageForum->retMessage());
	
	$oMessageForum = NULL;
}
else if ($url_sModaliteFenetre == "supprimer")
{
	$oSujetForum = new CSujetForum($oProjet->oBdd,$url_iIdSujet);
	$oBlockStyleSheetErreur->afficher();
	$oBlockMessage->ajouter($oTplMessageImportant->defVariable("SET_MESSAGE_IMPORTANT"));
	$oBlockMessage->remplacer("{important->message}",($oSujetForum->estPourTous() ? $oSetMessageSupprimerSujetEquipes : $oSetMessageSupprimerSujet));
	$oBlockMessage->remplacer("{important->question}",$oSetQuestionSupprimerSujet);
	unset($oSujetForum);
}

$oBlocEffacerFichier = new TPL_Block("BLOCK_EFFACER_FICHIER_ATTACHE",$oBlockMessage);
$oBlocEffacerFichier->effacer();

// Afficher le bloc
$oBlockMessage->afficher();

// Barre de progression
$oTpl->remplacer("{barre_de_progression}",$oTplBarreDeProgression->defVariable("SET_BARRE_DE_PROGRESSION"));
$oTpl->remplacer("{barre_de_progression->message}",$oTpl->defVariable("SET_BARRE_DE_PROGRESSION_MESSAGE"));

// Editeur
$oTpl->remplacer("{editeur->sauvegarde}",NULL);

$oTpl->remplacer("{editeur->nom}","messageSujet");

$oTpl->remplacer("editeur://",dir_admin("commun"));
$oTpl->remplacer("icones://",dir_icones());

$oTpl->afficher();

$oProjet->terminer();

?>

