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
** Fichier ................: collecticiel.php
** Description ............:
** Date de création .......: 11/04/2005
** Dernière modification ..: 21/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

error_reporting(E_ALL);

require_once("globals.inc.php");
require_once(dir_root_plateform("globals.icones.php"));

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdPersEquipe       = (empty($_GET["sltPersEquipe"]) ? 0 : $_GET["sltPersEquipe"]);
$url_iIdStatutDocument   = (empty($_GET["sltStatutDoc"]) ? 0 : $_GET["sltStatutDoc"]);
$url_sDateDocument       = (empty($_GET["sltDateDoc"]) ? 0 : $_GET["sltDateDoc"]);
$url_bAfficherBlocsVides = (empty($_GET["cbBlocsVides"]) ? ($url_iIdPersEquipe > 0) : $_GET["cbBlocsVides"]);
$url_sTri                = (empty($_GET["tri"]) ? "date" : $_GET["tri"]);
$url_iTypeTri            = (empty($_GET["typeTri"]) ? TRI_DECROISSANT : $_GET["typeTri"]);

// ---------------------
// Initialiser
// ---------------------
$g_iIdPers = $oProjet->retIdUtilisateur();
$g_bPeutEvaluer = $oProjet->verifPermission("PERM_EVALUER_COLLECTICIEL");
$g_bResponsable = $g_bPeutEvaluer | $oProjet->verifPermission("PERM_VOIR_TOUS_COLLECTICIELS");
$g_iIdEquipe = 0;

$g_iIdModalite = $oProjet->oSousActivCourante->retModalite(TRUE);

$g_sRepRessources = $oProjet->dir_ressources(NULL,FALSE);
$g_sFichierTelecharger = dir_lib("download.php");

$aaCollecticiels = array();

if (MODALITE_PAR_EQUIPE == $g_iIdModalite)
{
	if ($oProjet->initEquipe())
		$g_iIdEquipe = $oProjet->oEquipe->retId();
	
	if ($g_iIdEquipe == 0 && !$g_bResponsable)
	{
		// Cette personne n'est pas inscrite pour ce collecticiel
		$sErreur = emb_htmlentities("Vous ne pouvez pas réaliser cette activité pour le moment car vous n'êtes pas encore inscrit dans une équipe.");
		$oTplErreur = new Template(dir_theme_commun("erreur.tpl",FALSE,TRUE));
		$oTplErreur->remplacer("{message_erreur.texte}",$sErreur);
		$oTplErreur->afficher();
		
		exit();
	}
	
	$oProjet->oActivCourante->initEquipes();
	
	foreach ($oProjet->oActivCourante->aoEquipes as $oEquipe)
		$aaCollecticiels[] = array("id" => $oEquipe->retId(), "nom" => $oEquipe->retNom());
}
else
{
	if ($g_bResponsable)
	{
		$oProjet->initInscritsModule();
		
		foreach ($oProjet->aoInscrits as $oInscrit)
			$aaCollecticiels[] = array("id" => $oInscrit->retId(), "nom" => $oInscrit->retNom()." ".$oInscrit->retPrenom());
	}
	else if ($oProjet->verifEtudiant())
		$aaCollecticiels[] = array("id" => $g_iIdPers, "nom" => $oProjet->oUtilisateur->retNom()." ".$oProjet->oUtilisateur->retPrenom());
	else
	{
		// Cette personne n'est pas inscrite dans cette formation
		$sErreur = emb_htmlentities("Vous ne pouvez pas réaliser cette activité pour le moment car vous n'êtes pas encore inscrit comme étudiant dans cette formation.");
		$oTplErreur = new Template(dir_theme_commun("erreur.tpl",FALSE,TRUE));
		$oTplErreur->remplacer("{message_erreur.texte}",$sErreur);
		$oTplErreur->afficher();
		
		exit();
	}
}

if (!$g_bResponsable)
{
	// Un étudiant ne voit que son collecticiel
	$url_iIdPersEquipe       = (MODALITE_PAR_EQUIPE == $g_iIdModalite ? $g_iIdEquipe : $g_iIdPers);
	$url_bAfficherBlocsVides = ($g_iIdPers > 0);
}

// ---------------------
// Template onglet
// ---------------------
$oTplOnglet = new Template(dir_theme("onglet/onglet_tab.tpl",FALSE,TRUE));
$sOnglet = $oTplOnglet->defVariable("SET_ONGLET");

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_sousactiv(LIEN_COLLECTICIEL,"collecticiel.tpl",TRUE));

$asSetTpl = array(
	"choisir_tri" => $oTpl->defVariable("SET_CHOISIR_TRI")
	, "tri_croissant" => $oTpl->defVariable("SET_TRI_CROISSANT")
	, "tri_decroissant" => $oTpl->defVariable("SET_TRI_DECROISSANT")
	, "liste_votants" => $oTpl->defVariable("SET_LISTE_VOTANTS")
	, "ressource_en_cours" => $oTpl->defVariable("SET_RESSOURCE_EN_COURS")
	, "ressource_soumise" => explode("|",$oTpl->defVariable("SET_RESSOURCE_SOUMISE"))
	, "ressource_evaluation" => $oTpl->defVariable("SET_RESSOURCE_EVALUATION")
);

// {{{ Zone de description 
$oBlocConsigne = new TPL_Block("BLOCK_CONSIGNE",$oTpl);

$sConsigne = (!$g_bResponsable || $url_iIdPersEquipe < 1 ? $oProjet->oSousActivCourante->retDescr() : NULL);

if (strlen($sConsigne) > 0)
{
	$oBlocConsigne->remplacer("{consigne}",convertBaliseMetaVersHtml($sConsigne));
	$oBlocConsigne->remplacer(
		array("{tableaudebord.niveau.id}","{tableaudebord.niveau.type}"),
		array($oProjet->oRubriqueCourante->retId(),TYPE_RUBRIQUE));
	$oBlocConsigne->afficher();
}
else
	$oBlocConsigne->effacer();

unset($oBlocConsigne,$sConsigne);
// }}}

// {{{ Document de base + Barre d'outils
$oBlocDocumentDeBase = new TPL_Block("BLOCK_FICHIER_DE_BASE",$oTpl);

$oBlocTransfererDocuments = new TPL_Block("BLOCK_ICONE_TRANSFERER_DOCUMENTS",$oBlocDocumentDeBase);
$oBlocCourriel = new TPL_Block("BLOCK_ICONE_COURRIEL",$oBlocDocumentDeBase);

$oBlocDocumentTelecharger = new TPL_Block("BLOCK_ICONE_DOCUMENT_TELECHARGER",$oBlocDocumentDeBase);
list($sUrlDocumentBase,,$sIntituleDocumentBase) = explode(";",$oProjet->oSousActivCourante->retDonnees());

if ((!$g_bResponsable || $url_iIdPersEquipe < 1) && strlen($sUrlDocumentBase))
{
	$sUrl = rawurlencode($oProjet->dir_cours($sUrlDocumentBase));
	$oBlocDocumentTelecharger->remplacer("{fichier_de_base.href}",dir_lib("download.php?f={$sUrl}"));
	$oBlocDocumentTelecharger->remplacer("{fichier_de_base.label}",emb_htmlentities($sIntituleDocumentBase));
	$oBlocDocumentTelecharger->afficher();
}
else
	$oBlocDocumentTelecharger->effacer();

if ($g_bResponsable)
{
	$oBlocTransfererDocuments->remplacer("{transfert_fichiers.paramsUrl}",($url_iIdPersEquipe > 0 ? "'idPers={$url_iIdPersEquipe}&idModalite={$g_iIdModalite}'" : NULL));
	$oBlocTransfererDocuments->afficher();
	
	if ($url_iIdPersEquipe > 0)
		$oBlocCourriel->effacer();
	else
	{
		$oBlocCourriel->remplacer("{fichier_de_base.courriel}",retLienEnvoiCourriel("?idStatuts=".STATUT_PERS_TUTEUR."x".STATUT_PERS_ETUDIANT));
		$oBlocCourriel->afficher();
	}
}
else
{
	$oBlocTransfererDocuments->effacer();
	$oBlocCourriel->effacer();
}

if ($g_bResponsable || strlen($sUrlDocumentBase))
	$oBlocDocumentDeBase->afficher();
else
	$oBlocDocumentDeBase->effacer();

unset($sUrl,$oBlocCourriel,$oBlocTransfererDocuments,$oBlocDocumentTelecharger,$oBlocDocumentDeBase);
// }}}

$oBlocCollecticiel     = new TPL_Block("BLOCK_COLLECTICIEL",$oTpl);
$oBlocSansCollecticiel = new TPL_Block("BLOCK_SANS_COLLECTICIEL",$oTpl);

$sSetNote = $oBlocCollecticiel->defVariable("SET_COLLECTICIEL_NOTE");
$asSetSelection = $oBlocCollecticiel->defTableau("ARRAY_COLLECTICIEL_SELECTIONNER","###");

$iNbCollecticielsReel = 0;

if (count($aaCollecticiels) > 0)
{
	$oBlocCollecticiel->beginLoop();
	
	$asRechTplDocument = array("{document.titre}","{a.titre.href}","{document.auteur}","{document.date}","{document.heure}","{document.statut}","{document.evalue}","{document.texte_associe}","{document.selection}","{document.id}");
	
	// {{{ Liste des collecticiels
	foreach ($aaCollecticiels as $amCollecticiel)
	{
		if ($url_iIdPersEquipe > 0 && $url_iIdPersEquipe != $amCollecticiel["id"])
			continue;
		
		$iNbRessources = $oProjet->oSousActivCourante->initRessources($url_sTri,$url_iTypeTri,$g_iIdModalite,$amCollecticiel["id"],$url_iIdStatutDocument,$url_sDateDocument);
		
		if ($iNbRessources < 1 && !$url_bAfficherBlocsVides)
			continue;
		
		$iNbCollecticielsReel++;
		
		$oBlocCollecticiel->nextLoop();
		
		$sSetDocument = $oBlocCollecticiel->defVariable("SET_DOCUMENTS");
		
		$oBlocCollecticiel->remplacer("{documents}",$sOnglet);
		$oBlocCollecticiel->remplacer("{onglet->titre}",$amCollecticiel["nom"]);
		$oBlocCollecticiel->remplacer("{onglet->texte}",$sSetDocument);
		
		// {{{ Les en-têtes de la table des documents
		$asRech = array(
			"titre" => "{entete.tri_titre}"
			, "auteur" => "{entete.tri_auteur}"
			, "date" => "{entete.tri_date}"
			, "statut" => "{entete.tri_statut}"
			, "evalue" => "{entete.tri_evalue}"
		);
		
		foreach ($asRech as $sCle => $sValeur)
		{
			if ($sCle == $url_sTri)
			{
				if (TRI_DECROISSANT == $url_iTypeTri)
				{
					$oBlocCollecticiel->remplacer($sValeur,$asSetTpl["tri_decroissant"]);
					$oBlocCollecticiel->remplacer("{html.a.type_tri}",TRI_CROISSANT);
				}
				else
				{
					$oBlocCollecticiel->remplacer($sValeur,$asSetTpl["tri_croissant"]);
					$oBlocCollecticiel->remplacer("{html.a.type_tri}",TRI_DECROISSANT);
				}
				
				$oBlocCollecticiel->remplacer("{html.a.tri}",$sCle);
			}
			else
			{
				$oBlocCollecticiel->remplacer($sValeur,$asSetTpl["choisir_tri"]);
				$oBlocCollecticiel->remplacer("{html.img.usemap}",$sCle."_".$amCollecticiel["id"]);
				
				$oBlocCollecticiel->remplacer("{html.area.tri}",$sCle);
				$oBlocCollecticiel->remplacer("{html.area.type_tri.croissant}",TRI_CROISSANT);
				$oBlocCollecticiel->remplacer("{html.area.type_tri.decroissant}",TRI_DECROISSANT);
			}
		}
		
		$oBlocCollecticiel->remplacer("{entete.selection}",$asSetSelection[$g_bResponsable]);
		// }}}
		
		// {{{ Liste des documents déposés
		$iNbResEnCours = 0;
		
		$oBlocDocument     = new TPL_Block("BLOCK_DOCUMENT",$oBlocCollecticiel);
		$oBlocSansDocument = new TPL_Block("BLOCK_SANS_DOCUMENTS",$oBlocCollecticiel);
		
		$iNbDocsEnCours = 0;
		if ($iNbRessources > 0)
		{
			//$iNbDocsEnCours = 0;
			
			$oBlocDocument->beginLoop();
			
			foreach ($oProjet->oSousActivCourante->aoRessources as $oRessource)
			{
				if (STATUT_RES_EN_COURS == ($iResStatut = $oRessource->retStatut()))
					$iNbResEnCours++;
				
				$oBlocDocument->nextLoop();
				
				if (STATUT_RES_EN_COURS == $iResStatut)
				{
					// Voir la liste des votants
					if ($oRessource->retNbVotants() > 0)
						$oBlocDocument->remplacer("{document.statut}",$asSetTpl["liste_votants"]);
					
					$iNbDocsEnCours++;
				}
				
				// {{{ Accéder ou pas la fenêtre de l'évaluation
				if ($oRessource->retEstSoumise())
				{
					if ($oRessource->retEstEvaluee())
						$sEvaluer = $asSetTpl["ressource_evaluation"];
					else if ($g_bPeutEvaluer)
						$sEvaluer = $asSetTpl["ressource_soumise"][1];
					else
						$sEvaluer = $asSetTpl["ressource_soumise"][0];
				}
				else
					$sEvaluer = $asSetTpl["ressource_en_cours"];
				// }}}
				
				$amReplTplDocument = array(
					emb_htmlentities($oRessource->retNom())
					, "{$g_sFichierTelecharger}?f=".rawurlencode($g_sRepRessources.$oRessource->retUrl())
						."&fn=1"
					, emb_htmlentities($oRessource->oExpediteur->retNom()." ".$oRessource->oExpediteur->retPrenom())
					, retDateFormatter($oRessource->retDate())
					, retDateFormatter($oRessource->retDate(),"H:i:s")
					, $oRessource->retTexteStatut()
					, $sEvaluer
					, (strlen($oRessource->retDescr()) ? $sSetNote : "-")
					, ($g_bResponsable ? $asSetSelection[3] : ($url_iIdPersEquipe > 0 && $iResStatut == STATUT_RES_EN_COURS ? $asSetSelection[2] : $asSetSelection[0]))
					, $oRessource->retId()
				);
				
				$oBlocDocument->cycle();
				$oBlocDocument->remplacer($asRechTplDocument,$amReplTplDocument);
			}
			
			$oBlocDocument->afficher();
			$oBlocSansDocument->effacer();
		}
		else
		{
			$oBlocDocument->effacer();
			$oBlocSansDocument->afficher();
		}
		// }}}
		
		// {{{ Barre outils
		$oBlocBarreOutils = new TPL_Block("BLOCK_BARRE_OUTILS",$oBlocCollecticiel);
		
		if ($g_iIdPers > 0)
		{
			$sBarreOutils = NULL;
			
			$sSetSeparateurOutils = $oBlocBarreOutils->defVariable("SET_SEPARATEUR_ICONES");
			$asTableOutils = $oBlocBarreOutils->defTableau("ARRAY_BARRE_OUTILS","###");
			
			$bPeutAjouterDocuments = FALSE;
			$sParamsUrlCourriel = "&idStatuts=".STATUT_PERS_TUTEUR;
			
			if (MODALITE_PAR_EQUIPE == $g_iIdModalite)
			{
				$sParamsUrlCourriel .= "&idEquipes=".$amCollecticiel["id"];
				$sParamsUrlCourriel .= "&selectEquipe=".$amCollecticiel["id"];
				
				if ($g_iIdEquipe == $amCollecticiel["id"])
					$bPeutAjouterDocuments = TRUE;
				
				$sBarreOutils .= $asTableOutils[1]; 			// Liste des équipes
			}
			else
			{
				$sParamsUrlCourriel .= "&idPers=".$amCollecticiel["id"];
				$sParamsUrlCourriel .= "&selectEquipe=".$amCollecticiel["id"];
				
				if ($g_iIdPers == $amCollecticiel["id"])
					$bPeutAjouterDocuments = TRUE;
				
				$sBarreOutils .= $asTableOutils[0];	 			// Profil
			}
			
			$sBarreOutils .= $asTableOutils[2];					// Boîte courrielle
			$sBarreOutils = str_replace("{courriel.modalite}",$sParamsUrlCourriel,$sBarreOutils);
			
			$oBlocBarreOutils->remplacer("{barre_outils}",$sBarreOutils);
			$oBlocBarreOutils->afficher();
		}
		else
			$oBlocBarreOutils->effacer();
		// }}}
		
		// {{{ Gestion des documents
		$oBlocGestionDocuments = new TPL_Block("BLOCK_GESTION_DOCUMENTS",$oBlocCollecticiel);
		
		$sVarSupprimer = $oBlocGestionDocuments->defVariable("VAR_SUPPRIMER");
		$sVarDeposer   = $oBlocGestionDocuments->defVariable("VAR_DEPOSER");
		$sVarSoumettre = $oBlocGestionDocuments->defVariable("VAR_SOUMETTRE_POUR_EVALUATION");
		$sVarVoter     = $oBlocGestionDocuments->defVariable("VAR_VOTER_POUR_SOUMETTRE");
		
		if ($g_iIdPers > 0)
		{
			$oBlocDeposer   = new TPL_Block("BLOCK_DEPOSER",$oBlocGestionDocuments);
			$oBlocSupprimer = new TPL_Block("BLOCK_SUPPRIMER",$oBlocGestionDocuments);
			$oBlocSoumettre = new TPL_Block("BLOCK_SOUMETTRE_POUR_EVALUATION",$oBlocGestionDocuments);
			$oBlocVoter     = new TPL_Block("BLOCK_VOTER_POUR_SOUMETTRE",$oBlocGestionDocuments);
			
			if ($g_bPeutEvaluer && $iNbRessources > 0)
				$oBlocSupprimer->defDonnees($sVarSupprimer);
			
			$oBlocSupprimer->afficher();
			
			if ($bPeutAjouterDocuments)
			{
				$oBlocDeposer->defDonnees($sVarDeposer);
				$oBlocDeposer->afficher();
				
				if (MODALITE_PAR_EQUIPE == $g_iIdModalite)
				{
					$oBlocSoumettre->effacer();
					
					if ($iNbDocsEnCours > 0)
						$oBlocVoter->defDonnees($sVarVoter);
					
					$oBlocVoter->afficher();
				}
				else
				{
					if ($iNbDocsEnCours > 0)
						$oBlocSoumettre->defDonnees($sVarSoumettre);
					
					$oBlocSoumettre->afficher();
					$oBlocVoter->effacer();
				}
			}
			else
			{
				$oBlocDeposer->effacer();
				$oBlocSoumettre->effacer();
				$oBlocVoter->effacer();
			}
			
			$oBlocGestionDocuments->afficher();
		}
		else
			$oBlocGestionDocuments->effacer();
		// }}}
		
		$oBlocCollecticiel->remplacer(array("{collecticiel.id}","{equipe.id}","{personne.id}"),$amCollecticiel["id"]);
	}
	// }}}
}

if ($iNbCollecticielsReel > 0)
{
	$oBlocCollecticiel->afficher();
	$oBlocSansCollecticiel->effacer();
}
else
{
	$oBlocCollecticiel->effacer();
	$oBlocSansCollecticiel->afficher();
}

$oTpl->afficher();

$oProjet->terminer();

?>

