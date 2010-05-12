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

define("LIRE_EVALUATION", 0);
define("AJOUTER_EVALUATION", 1);
define("LIRE_COMMENTAIRE", 2);
define("AJOUTER_COMMENTAIRE", 3);

$oProjet = new CProjet();
$oProjet->initModuleCourant();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdActiv     = (empty($_GET["idActiv"]) ? 0 : $_GET["idActiv"]);
$url_iIdSousActiv = (empty($_GET["idSousActiv"]) ? 0 : $_GET["idSousActiv"]);
$url_iIdFC        = (empty($_GET["idFC"]) ? 0 : $_GET["idFC"]);

// Variable url facultative
$url_iIdPers = (empty($_GET["idPers"]) ? 0 : $_GET["idPers"]);

// ---------------------
// Initialiser
// ---------------------
$oSousActiv = new CSousActiv($oProjet->oBdd,$url_iIdSousActiv);
$oOptionsFormulaire = new CSousActivFormulOptions($oProjet->oBdd, $url_iIdSousActiv);

/*
 * On vérifie si le formulaire doit être affiché dans la page ou en popup
 * par défaut : popup (comportement habituel)
 */
$sAffichageFormulaire = "popup";

if ($oProjet->retReelStatutUtilisateur() == STATUT_PERS_ETUDIANT)
    $sAffichageFormulaire = $oOptionsFormulaire->retAffichageEtudiant();
else
    $sAffichageFormulaire = "popup";

$iMonIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

// Vérifier que cette personne a le droit d'évaluer les formulaires soumis
$bPeutEvaluerFormulaires    = $oProjet->verifModifierModule() && $oProjet->verifPermission("PERM_EVALUER_FORMULAIRE");
$bPeutModifierArchive       = $oProjet->verifPermission("PERM_MOD_SESSION_ARCHIVES");

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
//$oBlocFormulaire->beginLoop();

($oProjet->retReelStatutUtilisateur() == STATUT_PERS_ETUDIANT) ? 
    $oBlocFormulaire->remplacer("{utilisateur->nomComplet}", "(" . $oProjet->oUtilisateur->retNomComplet() . ")") :
    $oBlocFormulaire->remplacer("{utilisateur->nomComplet}", "");

$oBlocFormulaireInline = new TPL_Block("BLOCK_FORM_INLINE", $oTpl);

// {{{ Liste des icônes
$sSetListeIcones = $oTpl->defVariable("SET_LISTE_ICONES");

//$oBlocFormulaire->nextLoop();
$oBlocFormulaire->remplacer("{formulaire->listeIcones}",$sSetListeIcones);

// Exporter
$oBlocFormulaire->remplacer("{a.exporter.href}","formulaire_export.php?idSousActiv={$url_iIdSousActiv}");

// Envoi courriel
$oBlocFormulaire->remplacer("{a.choix_courriel.href}","choix_courriel('?typeCourriel=courriel-Activit&eacute;%20en%20ligne&idStatuts=".STATUT_PERS_TUTEUR."x".STATUT_PERS_ETUDIANT."')");

$asListeIcones = $oBlocFormulaire->defTableau("ARRAY_LISTE_ICONES","#@#");

if (!$bPeutEvaluerFormulaires)
    $asListeIcones[0] = NULL;

$sListeIcones = NULL;

foreach ($asListeIcones as $sIcone)
    $sListeIcones .= $sIcone;

$oBlocFormulaire->remplacer("{liste_icones}",$sListeIcones);

unset($sSetListeIcones);
// fin Liste des icônes }}}


// {{{ Description
$oSetDescription = $oTpl->defVariable("SET_DESCRIPTION");

$sDescription = $oSousActiv->retDescr();

if (strlen($sDescription) > 0)
{
//    $oBlocFormulaire->nextLoop();
//    $oBlocFormulaire->remplacer("{formulaire->element}",$oSetDescription);
    $oBlocFormulaire->remplacer("{formulaire->description}",$oSetDescription);
    $oBlocFormulaire->remplacer("{description->texte}",convertBaliseMetaVersHtml($sDescription));
}
else {
    $oBlocFormulaire->remplacer("{formulaire->description}", "");
}

unset($oSetDescription,$sDescription);
// fin Description }}}

// {{{ Document de base
list($iIdFormulaire,$iDeroulement,$sIntituleLien) = explode(";",$oSousActiv->retDonnees());

$sSetDocBase = $oTpl->defVariable("SET_DOCUMENT_DE_BASE");

//$oBlocFormulaire->nextLoop();
//$oBlocFormulaire->remplacer("{formulaire->element}",$sSetDocBase);
$oBlocFormulaire->remplacer("{formulaire->docBase}",$sSetDocBase);

//$oBlocFormulaire->remplacer("{document_de_base}",$sSetOnglet);
//$oBlocFormulaire->remplacer("{onglet->titre}",$oBlocFormulaire->defVariable("VAR_TITRE"));
$oBlocFormulaire->defVariable("VAR_TITRE");
$sOngletTexte = $oBlocFormulaire->defVariable("VAR_DOCUMENT_URL");
$oBlocFormulaire->defVariable("VAR_CONSIGNE");
//$oBlocFormulaire->remplacer("{onglet->texte}",$sOngletTexte);
$oBlocFormulaire->remplacer("{document_de_base}",$sOngletTexte);

$oBlocFormulaire->remplacer("{a->label}",$sIntituleLien);

if ($sAffichageFormulaire != "inline")
{
    $oBlocFormulaire->remplacer("{a->onclick}","return formulaire('?idSousActiv={$url_iIdSousActiv}&idFormulaire={$iIdFormulaire}','winFormulaire')");
    $oBlocFormulaire->remplacer("{a->href}", "javascript: void(0);");
    $oBlocFormulaire->remplacer("{a->target}", "");
}
else
{
    $oBlocFormulaire->remplacer("{a->href}",dir_sousactiv() . "formulaire/formulaire.php?idActiv={$url_iIdActiv}&amp;idSousActiv={$url_iIdSousActiv}#FormulaireInline");
    $oBlocFormulaire->remplacer("{a->onclick}","");
    $oBlocFormulaire->remplacer("{a->target}", "Principal");
}

if ($url_iIdFC == 0)
    $s_gIntituleLien = $sIntituleLien;

unset($sSetDocBase);
// fin Document de base }}}

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

//        $oBlocFormulaire->nextLoop();
        
//        $oBlocFormulaire->remplacer("{formulaire->element}",$sSetTravauxEnCours);
        $oBlocFormulaire->remplacer("{formulaire->travauxEnCours}",$sSetTravauxSoumis);
        
//        $oBlocFormulaire->remplacer("{onglet}",$sSetOnglet);
//        $oBlocFormulaire->remplacer("{onglet->titre}",$oBlocFormulaire->defVariable("VAR_TITRE"));
        
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
//        $oBlocFormulaire->remplacer("{liste_documents}",$sListeDocuments);
        $oBlocFormulaire->remplacer("{onglet}", $sListeDocuments);
    }
}
else {
    $oBlocFormulaire->remplacer("{formulaire->travauxEnCours}", "");
}


unset($sSetTravauxEnCours,$sListeDocuments);
// fin Travaux en cours }}}

// {{{ Travaux soumis
$sSetTravauxSoumis = $oTpl->defVariable("SET_TRAVAUX_SOUMIS");
$sSetPasActivite = $oTpl->defVariable("SET_PAS_ACTIVITE_REALISEE");
$sVarPasEtudiantTrouve = $oTpl->defVariable("SET_PAS_ETUDIANT_TROUVE");

$sListeTravauxSoumis = NULL;

$asInfosPersonne = array("{personne_infos.id}","{personne_infos.sexe}","{personne_infos.nom_complet}","{personne_infos.pseudo}","{personne_infos.email}","{personne->email}");
$asFormationComplete = array("{document->selectionner}","{document->titre}","{document->personne_complet}","{document->date}","{document->evalue}","{document->fini}",
                             "{a->onclick}","{a->href}","{a->target}","{radio->name}","{radio->value}", "{evaluer->bouton}");

foreach ($aiIdPers as $iIdPers)
{
    if ($url_iIdPers > 0 && $url_iIdPers != $iIdPers)
        continue;

    $iNbrFormulairesCompletes = $oSousActiv->initFormulairesCompletes($iIdPers,array(STATUT_RES_SOUMISE,STATUT_RES_APPROF,STATUT_RES_ACCEPTEE,STATUT_RES_AUTOCORRIGEE_NOCOMMENT,STATUT_RES_AUTOCORRIGEE));
    
    // Si l'utilisateur est un étudiant alors il ne faut pas afficher l'onglet
    // si celui-ci n'a pas soumis des documents
    // [EDIT] il faut quand même afficher qu'il n'y a pas de document trouvés ainsi que la consigne
    $sAucuneActiviteParams = $sAffichageFormulaire == "inline" ? null : "Cliquez sur le lien rouge pour commencer.";
    if ($iNbrFormulairesCompletes < 1 && !$bPeutEvaluerFormulaires)
    {
        $oBlocFormulaire->remplacer("{formulaire->travauxSoumis}", $sSetPasActivite);
        $oBlocFormulaire->remplacer("{activite->params}", $sAucuneActiviteParams);
        break;
    }

    // Si l'utilisateur est un visiteur, il ne peut voir les travaux d�pos�s.
    if ($iMonIdPers == 0)
        break;

    if (empty($sListeTravauxSoumis))
    {
        // Ajouter une nouvelle liste de formulaires

//        $oBlocFormulaire->nextLoop();

//        $oBlocFormulaire->remplacer("{formulaire->element}",$sSetTravauxSoumis);
        $oBlocFormulaire->remplacer("{formulaire->travauxSoumis}",$sSetTravauxSoumis);

//        $oBlocFormulaire->remplacer("{onglet}",$sSetOnglet);
//        $oBlocFormulaire->remplacer("{onglet->titre}",$oBlocFormulaire->defVariable("VAR_TITRE"));
//        $oBlocFormulaire->remplacer("{titreTravauxFinis}",$oBlocFormulaire->defVariable("VAR_TITRE"));
        $oBlocFormulaire->defVariable("VAR_TITRE");

        $sVarListeDocuments = $oBlocFormulaire->defVariable("VAR_LISTE_DOCUMENTS");
        $sVarLigneDocument = $oBlocFormulaire->defVariable("VAR_LIGNE_DOCUMENT");
        $asVarBoutonEvaluer = $oBlocFormulaire->defVariable("VAR_BOUTON_EVALUER",TRUE);
        $sVarPasDocumentTrouve = $oBlocFormulaire->defVariable("VAR_PAS_DOCUMENT_TROUVE");
        $asVarConsignes = $oBlocFormulaire->defVariable("VAR_CONSIGNE",TRUE);
        $asVarConsigneGlobale = $oBlocFormulaire->defVariable("VAR_CONSIGNE_GLOBALE");
        $asVarFormulaireEvaluer = $oBlocFormulaire->defVariable("VAR_FORMULAIRE_EVALUATION",TRUE);
        $sVarButonSelectionnerFormulaire = $oBlocFormulaire->defVariable("VAR_BOUTON_SELECTIONNER_FORMULAIRE");
    }

    if ($bPeutEvaluerFormulaires)
    {
        $oPersonne = new CPersonne($oProjet->oBdd,$iIdPers);
        $amRemplacer = array(
            "id_pers_{$iIdPers}"
            , $asTplGlobale[($oPersonne->retSexe() == "F" ? "personne->sexe->f" : "personne->sexe->m")]
            , emb_htmlentities($oPersonne->retNomComplet()).($iIdPers == $iMonIdPers ? $asTplGlobale["icone->favori"] : NULL)
            , emb_htmlentities($oPersonne->retPseudo())
            , $asTplGlobale[(strlen($oPersonne->retEmail()) ? "mail->actif" : "mail->passif")]
            , "?idStatuts=".STATUT_PERS_TUTEUR."&idPers=".$oPersonne->retId()."&select=".$oPersonne->retId()."&typeCourriel=courriel-Activit&eacute;%20en%20ligne"
        );
        
        $sListeTravauxSoumis .= str_replace($asInfosPersonne,$amRemplacer,$asTplGlobale["personne_infos"]);
    }

    $sListeDocuments = NULL;

    if ($iNbrFormulairesCompletes > 0)
    {
        $sBoutonEvaluer = NULL;
        $iPlusHautStatut = 0;
        $sDocumentFini = null;

        foreach ($oSousActiv->aoFormulairesCompletes as $oFormulaireComplete)
        {
            $iIdFCSA   = $oFormulaireComplete->retIdFCSA();
            $iStatutFC = $oFormulaireComplete->retStatut();
            $iIdFC     = $oFormulaireComplete->retId();

            $sDocumentFini = ($iStatutFC == STATUT_RES_AUTOCORRIGEE_NOCOMMENT || $iStatutFC == STATUT_RES_AUTOCORRIGEE)
                                ? "r&eacute;alis&eacute;e le"
                                : "soumise pour &eacute;valuation le";

            // on récupère le statut le plus élevé
            if ($iStatutFC > $iPlusHautStatut) $iPlusHautStatut = $iStatutFC;

            if ($url_iIdFC == $iIdFC)
                $s_gIntituleLien = "test";

            if ($sAffichageFormulaire != "inline")
            {
                $sAOnclick = "return formulaire('?idSousActiv={$url_iIdSousActiv}&idFC={$iIdFC}','winFormulaire')";
                $sAHref = "javascript: void(0);";
                $sATarget = "";
            }
            else
            {
                $sAOnclick = "";
                $sAHref = dir_sousactiv() . "formulaire/formulaire.php?idActiv={$url_iIdActiv}&amp;idSousActiv={$url_iIdSousActiv}&idFC={$iIdFC}#FormulaireInline";
                $sATarget = "Principal";
            }

            // Initialiser l'auteur du formulaire
            $oFormulaireComplete->initAuteur();

            // Liste des éléments à remplacer
            $amRemplacer = array(
                (($bPeutEvaluerFormulaires || (STATUT_RES_SOUMISE != $iStatutFC && STATUT_RES_AUTOCORRIGEE_NOCOMMENT != $iStatutFC)) ? $sVarButonSelectionnerFormulaire : "&nbsp;")
                , $oFormulaireComplete->retTitre()
                , $oFormulaireComplete->oAuteur->retNomComplet()
                , $oFormulaireComplete->retDate()
                , $asVarFormulaireEvaluer[$iStatutFC]
                , $sDocumentFini
                , $sAOnclick
                , $sAHref
                , $sATarget
                , "idFCSousActiv"
                , $iIdFCSA
//                , $sBoutonEvaluer);
                );
            $sListeDocuments .= str_replace($asFormationComplete,$amRemplacer,$sVarLigneDocument);
        }

            // Pour pouvoir afficher le bouton "Evaluer/Obtenir l'évaluation"
            // il faut que la personne est un tuteur ou que l'étudiant a dans sa
            // liste un document qui a été évalué par son tuteur
            // On ajoute le texte "commenter" ou "obtenir un commentaire" pour les formulaires autocorrigés
            if (empty($sBoutonEvaluer) &&
                ($bPeutEvaluerFormulaires || $iPlusHautStatut != STATUT_RES_SOUMISE))
            {
                switch($iPlusHautStatut)
                {
                    case STATUT_RES_AUTOCORRIGEE:
                    case STATUT_RES_AUTOCORRIGEE_NOCOMMENT:
                        $sBoutonEvaluer = $bPeutEvaluerFormulaires ? $asVarBoutonEvaluer[AJOUTER_COMMENTAIRE] : $asVarBoutonEvaluer[LIRE_COMMENTAIRE];
                        $sVarConsignes = $bPeutEvaluerFormulaires ? $asVarConsignes[AJOUTER_COMMENTAIRE] : $asVarConsignes[LIRE_COMMENTAIRE];
                        break;
                    case STATUT_RES_SOUMISE:
                    case STATUT_RES_ACCEPTEE:
                    case STATUT_RES_APPROF:
                        $sBoutonEvaluer = $bPeutEvaluerFormulaires ? $asVarBoutonEvaluer[AJOUTER_EVALUATION] : $asVarBoutonEvaluer[LIRE_EVALUATION];
                        $sVarConsignes = $bPeutEvaluerFormulaires ? $asVarConsignes[AJOUTER_EVALUATION] : $asVarConsignes[LIRE_EVALUATION];
                        break;
                    default:
                        $sBoutonEvaluer = "&nbsp;";
                        $sVarConsignes = NULL;
                        break;
                }
            }

        $sListeTravauxSoumis .= $sVarListeDocuments;
        $iStatutFormation = (is_object($oProjet->oFormationCourante) ? $oProjet->oFormationCourante->retStatut() : 0);
        $sListeTravauxSoumis = str_replace("{evaluer->bouton}",(!$bPeutModifierArchive && $iStatutFormation ==STATUT_ARCHIVE) ? "" : $sBoutonEvaluer,$sListeTravauxSoumis);
        $sListeTravauxSoumis = str_replace("{liste_documents}",$sListeDocuments,$sListeTravauxSoumis);
    }
    else
        $sListeTravauxSoumis .= $sVarPasDocumentTrouve;

    $sListeTravauxSoumis = str_replace("{personne->id}",$iIdPers,$sListeTravauxSoumis);

    if ($url_iIdPers > 0 && $url_iIdPers == $iIdPers)
        break;
}

if (empty($aiIdPers))
{
    $sListeTravauxSoumis .= $sVarPasEtudiantTrouve;
    $oBlocFormulaire->remplacer("{formulaire->travauxSoumis}", $sListeTravauxSoumis);
}

if (isset($sListeTravauxSoumis))
{
//    $oBlocFormulaire->remplacer("{onglet->texte}",$sListeTravauxSoumis
    $oBlocFormulaire->remplacer("{onglet}", $sListeTravauxSoumis);
    $oBlocFormulaire->remplacer("{consigne}",$asVarConsigneGlobale . (isset($iIdFC) && $iIdFC > 0 ? $sVarConsignes : NULL));
}

unset($sSetTravauxSoumis,$sListeTravauxSoumis,$sSetPasActivite,$sVarPasEtudiantTrouve);
// fin Travaux soumis }}}

$oBlocFormulaire->afficher();

if ($sAffichageFormulaire == "inline")
{
//    $url_iIdFC != 0 ? $oTpl->remplacer("{document->titre}", $url_iIdFC) : $oTpl->remplacer("{document->titre}", NULL);
    $oBlocFormulaireInline->afficher();
    include("modifier_formulaire.php");
}
else
    $oBlocFormulaireInline->effacer();

$oTpl->afficher();

$oProjet->terminer();

?>

