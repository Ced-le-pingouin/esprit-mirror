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
** Fichier ................: formulaire.php
** Description ............:
** Date de création .......: 26/10/2004
** Dernière modification ..: 22/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initModuleCourant();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdActiv     = (empty($HTTP_GET_VARS["idActiv"]) ? 0 : $HTTP_GET_VARS["idActiv"]);
$url_iIdSousActiv = (empty($HTTP_GET_VARS["idSousActiv"]) ? 0 : $HTTP_GET_VARS["idSousActiv"]);

// Variable url facultative
$url_iIdPers = (empty($HTTP_GET_VARS["idPers"]) ? 0 : $HTTP_GET_VARS["idPers"]);

// ---------------------
// Initialiser
// ---------------------
$oSousActiv = new CSousActiv($oProjet->oBdd,$url_iIdSousActiv);

$iMonIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

// Vérifier que cette personne a le droit d'évaluer les formulaires soumis
$bPeutEvaluerFormulaires  = $oProjet->verifModifierModule();
$bPeutEvaluerFormulaires &= $oProjet->verifPermission("PERM_EVALUER_FORMULAIRE");

if ($bPeutEvaluerFormulaires)
{
	// Obtenir la liste des étudiants de ce module
	$oProjet->initInscritsModule();
	
	$aiIdPers = array();
	
	foreach ($oProjet->aoInscrits as $oInscrit)
		$aiIdPers[] = $oInscrit->retId();
	
	unset($oProjet->aoInscrits);
}
else
{
	// Afficher les formulaires de cette personne
	$aiIdPers = array($iMonIdPers);
}

// ---------------------
// Template globale
// ---------------------
$oTpl = new Template(dir_theme("globals.inc.tpl",FALSE,TRUE));

$asTplGlobale = array(
	  "personne_infos" => $oTpl->defVariable("SET_PERSONNE_INFOS")
	, "personne->sexe->m" => $oTpl->defVariable("SET_SEXE_MASCULIN")
	, "personne->sexe->f" => $oTpl->defVariable("SET_SEXE_FEMININ")
	, "mail->actif" => $oTpl->defVariable("SET_MAIL_ACTIF")
	, "mail->passif" => $oTpl->defVariable("SET_MAIL_PASSIF")
	, "icone->favori" => $oTpl->defVariable("SET_ICONE_FAVORI")
	, "input->radio" => $oTpl->defVariable("SET_INPUT_RADIO")
);

// ---------------------
// Template de l'onglet
// ---------------------
$oTpl       = new Template(dir_theme("onglet/onglet_tab.tpl",FALSE,TRUE));
$sSetOnglet = $oTpl->defVariable("SET_ONGLET");

// ---------------------
// Template
// ---------------------
$oTpl = new Template("formulaire.tpl");

$oBlocFormulaire = new TPL_Block("BLOCK_FORMULAIRE",$oTpl);
$oBlocFormulaire->beginLoop();

// {{{ Liste des icônes
$sSetListeIcones = $oTpl->defVariable("SET_LISTE_ICONES");

$oBlocFormulaire->nextLoop();
$oBlocFormulaire->remplacer("{formulaire->element}",$sSetListeIcones);

// Exporter
$oBlocFormulaire->remplacer("{a.exporter.href}","formulaire_export.php?idSousActiv={$url_iIdSousActiv}");

// Envoi courriel
$oBlocFormulaire->remplacer("{a.choix_courriel.href}","choix_courriel('?typeCourriel=courriel-unite&idStatuts=".STATUT_PERS_TUTEUR."x".STATUT_PERS_ETUDIANT."')");

$asListeIcones = $oBlocFormulaire->defTableau("ARRAY_LISTE_ICONES","#@#");

if (!$bPeutEvaluerFormulaires)
	$asListeIcones[0] = NULL;

$sListeIcones = NULL;

foreach ($asListeIcones as $sIcone)
	$sListeIcones .= $sIcone;

$oBlocFormulaire->remplacer("{liste_icones}",$sListeIcones);

unset($sSetListeIcones);
// }}}


// {{{ Description
$oSetDescription = $oTpl->defVariable("SET_DESCRIPTION");

$sDescription = $oSousActiv->retDescr();

if (strlen($sDescription) > 0)
{
	$oBlocFormulaire->nextLoop();
	$oBlocFormulaire->remplacer("{formulaire->element}",$oSetDescription);
	$oBlocFormulaire->remplacer("{description->texte}",convertBaliseMetaVersHtml($sDescription));
}

unset($oSetDescription,$sDescription);
// }}}

// {{{ Document de base
list($iIdFormulaire,$iDeroulement,$sIntituleLien) = explode(";",$oSousActiv->retDonnees());

$sSetDocBase = $oTpl->defVariable("SET_DOCUMENT_DE_BASE");

$oBlocFormulaire->nextLoop();
$oBlocFormulaire->remplacer("{formulaire->element}",$sSetDocBase);

$oBlocFormulaire->remplacer("{document_de_base}",$sSetOnglet);
$oBlocFormulaire->remplacer("{onglet->titre}",$oBlocFormulaire->defVariable("VAR_TITRE"));
$sOngletTexte = $oBlocFormulaire->defVariable("VAR_DOCUMENT_URL")
	.$oBlocFormulaire->defVariable("VAR_CONSIGNE");
$oBlocFormulaire->remplacer("{onglet->texte}",$sOngletTexte);

$oBlocFormulaire->remplacer("{a->label}",$sIntituleLien);
$oBlocFormulaire->remplacer("{a->href}","return formulaire('?idSousActiv={$url_iIdSousActiv}&idFormulaire={$iIdFormulaire}','winFormulaire')");

unset($sSetDocBase);
// }}}

// {{{ Travaux en cours
$sSetTravauxEnCours = $oTpl->defVariable("SET_TRAVAUX_EN_COURS");

if (empty($iDeroulement) || $iDeroulement != SOUMISSION_AUTOMATIQUE)
{
	foreach ($aiIdPers as $iIdPers)
	{
		$iNbrFormulairesCompletes = $oSousActiv->initFormulairesCompletes($iIdPers,STATUT_RES_EN_COURS);
		
		// Si l'utilisateur est un étudiant alors il ne faut pas afficher l'onglet
		// si celui-ci n'a pas soumis des documents
		if ($iNbrFormulairesCompletes < 1 && !$bPeutEvaluerFormulaires)
			break;
		
		$oBlocFormulaire->nextLoop();
		
		$oBlocFormulaire->remplacer("{formulaire->element}",$sSetTravauxEnCours);
		
		$oBlocFormulaire->remplacer("{onglet}",$sSetOnglet);
		$oBlocFormulaire->remplacer("{onglet->titre}",$oBlocFormulaire->defVariable("VAR_TITRE"));
		
		$oBlocFormulaire->remplacer("{onglet->texte}",
			$oBlocFormulaire->defVariable("VAR_LISTE_DOCUMENTS")
			.$oBlocFormulaire->defVariable("VAR_CONSIGNE"));
		
		// Ligne d'une table pour un document
		$sVarLigneDocument = $oBlocFormulaire->defVariable("VAR_LIGNE_DOCUMENT");
		
		// Composer la liste des documents
		$asRechercher = array(
			"{document->selectionner}"
			, "{document->titre}"
			, "{document->personne_complet}"
			, "{document->date}"
			, "{input->name}");
		
		$sListeDocuments = NULL;
		
		foreach ($oSousActiv->aoFormulairesCompletes as $oFormulaireComplete)
		{
			$oFormulaireComplete->initAuteur();
			
			$amRemplacer = array(
				($oFormulaireComplete->retStatut() == STATUT_RES_EN_COURS ? $asTplGlobale["input->radio"] : NULL)
				, $oFormulaireComplete->retTitre()
				, $oFormulaireComplete->oAuteur->retNomComplet()
				, $oFormulaireComplete->retDate()
				, "idFC");
			
			$sListeDocuments .= str_replace($asRechercher,$amRemplacer,$sVarLigneDocument);
		}
		
		// Ajouter dans le template la liste des documents trouvés
		$oBlocFormulaire->remplacer("{liste_documents}",$sListeDocuments);
	}
}

unset($sSetTravauxEnCours,$sListeDocuments);
// }}}

// {{{ Travaux soumis
$sSetTravauxSoumis = $oTpl->defVariable("SET_TRAVAUX_SOUMIS");

$sListeTravauxSoumis = NULL;

$asInfosPersonne = array("{personne_infos.id}","{personne_infos.sexe}","{personne_infos.nom_complet}","{personne_infos.pseudo}","{personne_infos.email}","{personne->email}");
$asFormationComplete = array("{document->selectionner}","{document->titre}","{document->personne_complet}","{document->date}","{document->evalue}","{a->href}","{radio->name}","{radio->value}");

foreach ($aiIdPers as $iIdPers)
{
	if ($url_iIdPers > 0 && $url_iIdPers != $iIdPers)
		continue;
	
	$iNbrFormulairesCompletes = $oSousActiv->initFormulairesCompletes($iIdPers,array(STATUT_RES_SOUMISE,STATUT_RES_APPROF,STATUT_RES_ACCEPTEE));
	
	// Si l'utilisateur est un étudiant alors il ne faut pas afficher l'onglet
	// si celui-ci n'a pas soumis des documents
	if ($iNbrFormulairesCompletes < 1 && !$bPeutEvaluerFormulaires)
		break;
	
	if (empty($sListeTravauxSoumis))
	{
		// Ajouter une nouvelle liste de formulaires
		$oBlocFormulaire->nextLoop();
		
		$oBlocFormulaire->remplacer("{formulaire->element}",$sSetTravauxSoumis);
		
		$oBlocFormulaire->remplacer("{onglet}",$sSetOnglet);
		$oBlocFormulaire->remplacer("{onglet->titre}",$oBlocFormulaire->defVariable("VAR_TITRE"));
		
		$sVarListeDocuments = $oBlocFormulaire->defVariable("VAR_LISTE_DOCUMENTS");
		$sVarLigneDocument = $oBlocFormulaire->defVariable("VAR_LIGNE_DOCUMENT");
		$asVarBoutonEvaluer = $oBlocFormulaire->defVariable("VAR_BOUTON_EVALUER",TRUE);
		$sVarPasDocumentTrouve = $oBlocFormulaire->defVariable("VAR_PAS_DOCUMENT_TROUVE");
		$asVarConsignes = $oBlocFormulaire->defVariable("VAR_CONSIGNE",TRUE);
		$asVarFormulaireEvaluer = $oBlocFormulaire->defVariable("VAR_FORMULAIRE_EVALUATION",TRUE);
		$sVarButonSelectionnerFormulaire = $oBlocFormulaire->defVariable("VAR_BOUTON_SELECTIONNER_FORMULAIRE");
	}
	
	if ($bPeutEvaluerFormulaires)
	{
		$oPersonne = new CPersonne($oProjet->oBdd,$iIdPers);
		$amRemplacer = array(
			"id_pers_{$iIdPers}"
			, $asTplGlobale[($oPersonne->retSexe() == "F" ? "personne->sexe->f" : "personne->sexe->m")]
			, htmlentities($oPersonne->retNomComplet()).($iIdPers == $iMonIdPers ? $asTplGlobale["icone->favori"] : NULL)
			, htmlentities($oPersonne->retPseudo())
			, $asTplGlobale[(strlen($oPersonne->retEmail()) ? "mail->actif" : "mail->passif")]
			, $oPersonne->retEmail()
		);
		
		$sListeTravauxSoumis .= str_replace($asInfosPersonne,$amRemplacer,$asTplGlobale["personne_infos"]);
	}
	
	$sListeDocuments = NULL;
	
	if ($iNbrFormulairesCompletes > 0)
	{
		$sBoutonEvaluer = NULL;
		
		foreach ($oSousActiv->aoFormulairesCompletes as $oFormulaireComplete)
		{
			$iIdFCSA   = $oFormulaireComplete->retIdFCSA();
			$iStatutFC = $oFormulaireComplete->retStatut();
			$iIdFC     = $oFormulaireComplete->retId();
			
			// Initialiser l'auteur du formulaire
			$oFormulaireComplete->initAuteur();
			
			// Pour pouvoir afficher le bouton "Evaluer/Obtenir l'évaluation"
			// il faut que la personne est un tuteur ou que l'étudiant a dans sa
			// liste un document qui a été évalué par son tuteur
			if (empty($sBoutonEvaluer) &&
				($bPeutEvaluerFormulaires || STATUT_RES_SOUMISE != $iStatutFC))
				$sBoutonEvaluer = $asVarBoutonEvaluer[$bPeutEvaluerFormulaires];
			
			// Liste des éléments à remplacer
			$amRemplacer = array(
				  ($bPeutEvaluerFormulaires || STATUT_RES_SOUMISE != $iStatutFC ? $sVarButonSelectionnerFormulaire : "&nbsp;")
				, $oFormulaireComplete->retTitre()
				, $oFormulaireComplete->oAuteur->retNomComplet()
				, $oFormulaireComplete->retDate()
				, $asVarFormulaireEvaluer[$iStatutFC]
				, "return formulaire('?idSousActiv={$url_iIdSousActiv}&idFC={$iIdFC}','winFormulaire{$iIdFC}')"
				, "idFCSousActiv", $iIdFCSA);
			
			$sListeDocuments .= str_replace($asFormationComplete,$amRemplacer,$sVarLigneDocument);
		}
		
		$sListeTravauxSoumis .= $sVarListeDocuments;
		$sListeTravauxSoumis = str_replace("{evaluer->bouton}",$sBoutonEvaluer,$sListeTravauxSoumis);
		$sListeTravauxSoumis = str_replace("{liste_documents}",$sListeDocuments,$sListeTravauxSoumis);
	}
	else
		$sListeTravauxSoumis .= $sVarPasDocumentTrouve;
	
	$sListeTravauxSoumis = str_replace("{personne->id}",$iIdPers,$sListeTravauxSoumis);
	
	if ($url_iIdPers > 0 && $url_iIdPers == $iIdPers)
		break;
}

if (isset($sListeTravauxSoumis))
	$oBlocFormulaire->remplacer("{onglet->texte}",$sListeTravauxSoumis
		.(isset($iIdFC) && $iIdFC > 0 ? $asVarConsignes[$bPeutEvaluerFormulaires] : NULL));

unset($sSetTravauxSoumis,$sListeTravauxSoumis);
// }}}

$oBlocFormulaire->afficher();

$oTpl->afficher();

$oProjet->terminer();

?>

