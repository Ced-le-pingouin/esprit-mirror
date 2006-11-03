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
** Fichier ................: modifier_sujet.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 02/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Jérôme TOUZE <>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Gestion
// ---------------------
if (isset($_POST["modaliteFenetre"]))
{
	$bOk = FALSE;
	
	$url_sModaliteFenetre = $_POST["modaliteFenetre"];
	$url_iIdForum         = $_POST["idForum"];
	$url_iIdSujet         = $_POST["idSujet"];
	$url_iIdNiveau        = (empty($_POST["idNiveau"]) ? 0 : $_POST["idNiveau"]);
	$url_iTypeNiveau      = (empty($_POST["typeNiveau"]) ? 0 : $_POST["typeNiveau"]);
	$url_iIdEquipe        = (empty($_POST["idEquipe"]) ? 0 : $_POST["idEquipe"]);
	
	// Répertoire contenant les ressources du forum
	$oIds = new CIds($oProjet->oBdd,$url_iTypeNiveau,$url_iIdNiveau);
	$sRepFichiersForum = dir_forum_ressources($oIds,$url_iTypeNiveau,NULL,TRUE);
	
	if ("ajouter" == $url_sModaliteFenetre || "modifier" == $url_sModaliteFenetre)
	{
		$url_sTitreSujet   = (empty($_POST["titreSujet"]) ? NULL : trim($_POST["titreSujet"]));
		$url_sMessageSujet = (empty($_POST["messageSujet"]) ? NULL : trim($_POST["messageSujet"]));
		
		$iIdPers = $oProjet->oUtilisateur->retId();
		
		if (strlen($url_sTitreSujet) > 0)
		{
			if ("ajouter" == $url_sModaliteFenetre)
			{
				$oSujetForum = new CSujetForum($oProjet->oBdd);
				$url_iIdSujet = $oSujetForum->ajouter($url_sTitreSujet,NULL,NULL,NULL,$url_iIdForum,$iIdPers);
				
				// Associer ce sujet à l'équipe
				if ($url_iIdSujet > 0 && $url_iIdEquipe > 0)
					$oSujetForum->associerEquipe($url_iIdEquipe);
				
				// Enregistrer le message du sujet
				if ($url_iIdSujet > 0 && strlen($url_sMessageSujet) > 0)
				{
					$oMessageForum = new CMessageForum($oProjet->oBdd);
					$oMessageForum->ajouter($url_sMessageSujet,$url_iIdSujet,$iIdPers);
					
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
							
							echo $sNomFichierUnique = retNomFichierUnique($_FILES["fichierMessage"]["name"],$sRepFichiersForum);
							
							if (move_uploaded_file($_FILES["fichierMessage"]["tmp_name"],($sRepFichiersForum.$sNomFichierUnique)))
							{
								$oMessageForum->defRepRessources($sRepFichiersForum);
								$oMessageForum->ajouterRessource($_FILES["fichierMessage"]["name"],$sNomFichierUnique,$oProjet->oUtilisateur->retId());
							}
						}
					}
					
					// {{{ Copie courriel
					include_once("copie_courriel-mail.inc.php");
					// }}}
				}
			}
			else
			{
				$oSujetForum = new CSujetForum($oProjet->oBdd,$url_iIdSujet);
				$oSujetForum->defTitre($url_sTitreSujet);
				$oSujetForum->enregistrer();
			}
			
			$bOk = TRUE;
		}
	}
	else if ($url_sModaliteFenetre == "supprimer")
	{
		$oSujetForum = new CSujetForum($oProjet->oBdd,$url_iIdSujet);
		$oSujetForum->defRepRessources($sRepFichiersForum);
		$oSujetForum->verrouillerTables();
		$oSujetForum->effacer();
		$oSujetForum->verrouillerTables(FALSE);
		$oSujetForum = NULL;
		
		// Lorsqu'on supprime un sujet nous devons afficher le dernier sujet
		// entré
		$oForum = new CForum($oProjet->oBdd,$url_iIdForum);
		$oSujetForum = $oForum->retDernierSujets();
		if (is_object($oSujetForum))
			$url_iIdSujet = $oSujetForum->retId();
		else
			$url_iIdSujet = 0;
		
		$bOk = TRUE;
	}
	
	if ($bOk)
	{
		echo "<html>\n"
			."<head>\n"
		        ."<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n"
			."<script type=\"text/javascript\" language=\"javascript\"><!--\n"
			."function fermer() { top.opener.rafraichir_liste_sujets('{$url_iIdSujet}','$url_sModaliteFenetre'); top.close(); }\n"
			."//--></script>\n"
			."</head>\n"
			."<body onload=\"fermer()\"></body>\n"
			."</html>\n";
		
		$oProjet->terminer();
		
		exit();
	}
}

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sModaliteFenetre = (empty($_GET["modaliteFenetre"]) ? NULL : $_GET["modaliteFenetre"]);
$url_iIdForum         = (empty($_GET["idForum"]) ? 0 : $_GET["idForum"]);
$url_iIdSujet         = (empty($_GET["idSujet"]) ? 0 : $_GET["idSujet"]);
$url_iIdNiveau        = (empty($_GET["idNiveau"]) ? 0 : $_GET["idNiveau"]);
$url_iTypeNiveau      = (empty($_GET["typeNiveau"]) ? 0 : $_GET["typeNiveau"]);
$url_iIdEquipe        = (empty($_GET["idEquipe"]) ? 0 : $_GET["idEquipe"]);

// ---------------------
// Template de l'éditeur
// ---------------------
$oTplEditeur = new Template(dir_admin("commun","editeur.inc.tpl",TRUE));
$oBlocVisualiseur = new TPL_Block("BLOCK_VISUALISEUR",$oTplEditeur);
$oBlocVisualiseur->effacer();
$sSetEditeur = $oTplEditeur->defVariable("SET_EDITEUR");

// ---------------------
// Template du message important
// ---------------------
$oTplMessageImportant = new Template(dir_theme("dialogue/dialog-important.inc.tpl",FALSE,TRUE));

// ---------------------
// Template de la barre de progression
// ---------------------
$oTplBarreDeProgression = new Template(dir_theme("barre_de_progression.inc.tpl",FALSE,TRUE));

// ---------------------
// Template principal
// ---------------------
$oTpl = new Template("modifier_sujet.tpl");

$oBlockStyleSheetErreur = new TPL_Block("BLOCK_STYLESHEET_ERREUR",$oTpl);
$oBlockSujet            = new TPL_Block("BLOCK_SUJET",$oTpl);

$sSetModifierSujet                = $oTpl->defVariable("SET_MODIFIER_SUJET");
$sSetTitreSujet                   = $oTpl->defVariable("SET_TITRE_SUJET");
$sSetMessageSupprimerSujet        = $oTpl->defVariable("SET_MESSAGE_SUPPRIMER_SUJET");
$sSetMessageSupprimerSujetEquipes = $oTpl->defVariable("SET_MESSAGE_SUPPRIMER_SUJET_EQUIPES");
$sSetQuestionSupprimerSujet       = $oTpl->defVariable("SET_QUESTION_SUPPRIMER_SUJET");

// Onglet
$oTplOnglet = new Template(dir_theme("onglet/onglet_tab.tpl",FALSE,TRUE));
$oSet_Onglet = $oTplOnglet->defVariable("SET_ONGLET");

if ($url_sModaliteFenetre == "ajouter")
{
	$oBlockStyleSheetErreur->effacer();
	
	// Template du fichier attaché
	$oTplFichierAttache = new Template("fichier_attache.inc.tpl");
	$oTplFichierAttache->remplacer("{input['file']->name}","fichierMessage");
	
	$oBlockEffacerFichierAttache = new TPL_Block("BLOCK_EFFACER_FICHIER_ATTACHE",$oTplFichierAttache);
	$oBlockEffacerFichierAttache->effacer();
	
	$oBlockSujet->ajouter($sSetModifierSujet);
	
	// {{{ Onglet "Sujet"
	$oBlockSujet->remplacer("{onglet->sujet}",$oSet_Onglet);
	$oBlockSujet->remplacer("{onglet->titre}","Sujet");
	$oBlockSujet->remplacer("{onglet->texte}",$sSetTitreSujet);
	
	// Titre
	$oBlockSujet->remplacer("{titre->valeur}","");
	
	// Afficher un message à l'utilisateur
	$oBlocTitreMessage = new TPL_Block("BLOCK_TITRE_SUJET_MESSAGE",$oBlockSujet);
	$oBlocTitreMessage->afficher();
	// }}}
	
	// {{{ Onglet "Message"
	/*
	$oBlockSujet->remplacer("{onglet->message}",$oSet_Onglet);
	$oBlockSujet->remplacer("{onglet->titre}","Message");
	$oBlockSujet->remplacer("{onglet->texte}",$sSetEditeur.$oTplFichierAttache->defVariable("SET_FICHIER_ATTACHE"));
	
	// Editeur
	$oBlockSujet->remplacer("{edition->style}","width: 100%; height: 200px;");
	*/
	$oBlockSujet->remplacer("{onglet->message}",NULL);
	// }}}
}
else if ($url_sModaliteFenetre == "modifier")
{
	$oSujetForum = new CSujetForum($oProjet->oBdd,$url_iIdSujet);
	
	$oBlockStyleSheetErreur->effacer();
	
	$oBlockSujet->ajouter($sSetModifierSujet);
	
	// {{{ Onglet "Sujet"
	$oBlockSujet->remplacer("{onglet->sujet}",$oSet_Onglet);
	$oBlockSujet->remplacer("{onglet->titre}","Sujet");
	$oBlockSujet->remplacer("{onglet->texte}",$sSetTitreSujet);
	
	// Titre
	$oBlockSujet->remplacer("{titre->valeur}",mb_convert_encoding($oSujetForum->retTitre(),"HTML-ENTITIES","UTF-8"));
	
	// Ne pas afficher le message à l'utilisateur
	$oBlocTitreMessage = new TPL_Block("BLOCK_TITRE_SUJET_MESSAGE",$oBlockSujet);
	$oBlocTitreMessage->effacer();
	// }}}
	
	// {{{ Onglet "Message"
	$oBlockSujet->remplacer("{onglet->message}",NULL);
	// }}}
}
else if ($url_sModaliteFenetre == "supprimer")
{
	$oBlockStyleSheetErreur->afficher();
	
	$oSujetForum = new CSujetForum($oProjet->oBdd,$url_iIdSujet);
	
	$oBlockSujet->ajouter($oTplMessageImportant->defVariable("SET_MESSAGE_IMPORTANT"));
	$oBlockSujet->remplacer("{important->message}",($oSujetForum->estPourTous() ? $sSetMessageSupprimerSujetEquipes : $sSetMessageSupprimerSujet));
	$oBlockSujet->remplacer("{important->question}",$sSetQuestionSupprimerSujet);
	//$oBlockSujet->remplacer("{sujet->titre}",mb_convert_encoding($oSujetForum->retTitre(),"HTML-ENTITIES","UTF-8"));
	//$oBlockSujet->remplacer("{messages->total}",$oSujetForum->retNombreMessages());
}

// Afficher le bloc
$oBlockSujet->afficher();

// Barre de progression
$oTpl->remplacer("{barre_de_progression}",$oTplBarreDeProgression->defVariable("SET_BARRE_DE_PROGRESSION"));
$oTpl->remplacer("{barre_de_progression->message}",$oTpl->defVariable("SET_BARRE_DE_PROGRESSION_MESSAGE"));

// Remplir les éléments cachés du formation
$oTpl->remplacer("{fenetre->modalite}",$url_sModaliteFenetre);
$oTpl->remplacer("{forum->id}",$url_iIdForum);
$oTpl->remplacer("{sujet->id}",$url_iIdSujet);
$oTpl->remplacer("{niveau->id}",$url_iIdNiveau);
$oTpl->remplacer("{niveau->type}",$url_iTypeNiveau);
$oTpl->remplacer("{equipe->id}",$url_iIdEquipe);

// Editeur
$oTpl->remplacer("{editeur->nom}","messageSujet");
$oTpl->remplacer("editeur://",dir_admin("commun"));
$oTpl->remplacer("icones://",dir_icones());

$oTpl->afficher();

$oProjet->terminer();

?>

