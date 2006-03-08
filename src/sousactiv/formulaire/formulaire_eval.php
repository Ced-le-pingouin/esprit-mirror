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
** Fichier ................: formulaire_eval.php
** Description ............:
** Date de création .......: 05/11/2004
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

$g_iIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

$bAEteEvalue = FALSE;

// ---------------------
// Récupérer les variables de l'url
// ---------------------
if (isset($HTTP_POST_VARS["idFCSousActiv"]))
{
	$url_iIdFCSousActiv    = (empty($HTTP_POST_VARS["idFCSousActiv"]) ? 0 : $HTTP_POST_VARS["idFCSousActiv"]);
	$url_bEvalFC           = (empty($HTTP_POST_VARS["evalFC"]) ? 0 : $HTTP_POST_VARS["evalFC"]);
	$url_sStatut           = (empty($HTTP_POST_VARS["statutFCE"]) ? NULL : $HTTP_POST_VARS["statutFCE"]);
	$url_sAppreciationEval = (empty($HTTP_POST_VARS["appreciationEvalFCE"]) ? NULL : $HTTP_POST_VARS["appreciationEvalFCE"]);
	$url_sCommentaireEval  = (empty($HTTP_POST_VARS["commentaireEvalFCE"]) ? NULL : $HTTP_POST_VARS["commentaireEvalFCE"]);
	
	$oFCE = new CFormulaireComplete_Evaluation($oProjet->oBdd,$url_iIdFCSousActiv,$g_iIdPers);
	$oFCE->ajouter($url_sStatut,$url_sAppreciationEval,$url_sCommentaireEval);
	
	$bAEteEvalue = TRUE;
	
	unset($oFCE);
}
else
{
	$url_iIdFCSousActiv = (empty($HTTP_GET_VARS["idFCSousActiv"]) ? 0 : $HTTP_GET_VARS["idFCSousActiv"]);
	$url_bEvalFC        = (empty($HTTP_GET_VARS["evalFC"]) ? 0 : $HTTP_GET_VARS["evalFC"]);
	$url_iIdPers        = (empty($HTTP_GET_VARS["idPers"]) ? 0 : $HTTP_GET_VARS["idPers"]);
	
	if ($url_iIdPers < 1 && $url_bEvalFC)
		$url_iIdPers = $g_iIdPers;
	else if ($url_iIdPers < 1)
	{
		// Se positionner sur la permière évaluation
		$oProjet->oModuleCourant->initTuteurs();
		$iIdPersDefaut = 0;
		
		foreach ($oProjet->oModuleCourant->aoTuteurs as $oTuteur)
		{
			$iIdPers = $oTuteur->retId();
			
			if ($iIdPersDefaut < 1)
				$iIdPersDefaut = $iIdPers;
			
			$oFCE = new CFormulaireComplete_Evaluation($oProjet->oBdd,$url_iIdFCSousActiv,$iIdPers);
			
			if (strlen($oFCE->retAppreciation()) || strlen($oFCE->retCommentaire()))
			{
				$url_iIdPers = $iIdPers;
				break;
			}
		}
		
		if ($url_iIdPers < 1)
		{
			$url_iIdPers = $iIdPersDefaut;
			$url_bEvalFC = FALSE;
		}
	}
}

// ---------------------
// Initialiser
// ---------------------
$oFC = new CFormulaireComplete($oProjet->oBdd);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("formulaire_eval.tpl");

$sSetMessageConfirmation = $oTpl->defVariable("SET_MESSAGE_ENREGISTRER");
$sSetMessagePasEvalue    = $oTpl->defVariable("SET_MESSAGE_PAS_EVALUE");
$sSetEvaluationTuteur    = $oTpl->defVariable("SET_EVALUATION_TUTEUR");
$sSetEvaluationEtudiant  = $oTpl->defVariable("SET_EVALUATION_ETUDIANT");

if ($bAEteEvalue)
{
	$oTpl->remplacer("{evaluation->corp}",$sSetMessageConfirmation);
	$oTpl->remplacer("{fonction->init->corp}","top.opener.location = top.opener.location; top.close();");
}
else if ($url_bEvalFC)
{
	$oTpl->remplacer("{evaluation->corp}",$sSetEvaluationTuteur);
	
	// Globals
	$oTpl->remplacer("{form->action}",$HTTP_SERVER_VARS["PHP_SELF"]);
	$oTpl->remplacer("{formulaire_eval->id}",$url_iIdFCSousActiv);
	$oTpl->remplacer("{personne->peutEvaluer}",$url_bEvalFC);
	
	// Initialiser le formulaire complété et évalué
	$oFCE = new CFormulaireComplete_Evaluation($oProjet->oBdd,$url_iIdFCSousActiv,$url_iIdPers);
	$oFCE->initEvaluateur();
	
	// Composer la liste des statuts du formulaire
	if (STATUT_RES_SOUMISE == ($iStatutFCE = $oFCE->retStatut()))
		$iStatutFCE = STATUT_RES_ACCEPTEE;
	
	$sListeEtats = "<select name=\"statutFCE\">"
		."<option value=\"".STATUT_RES_APPROF."\"".(STATUT_RES_APPROF == $iStatutFCE ? " selected" : NULL).">".htmlentities($oFC->retTexteStatut(STATUT_RES_APPROF))."</option>"
		."<option value=\"".STATUT_RES_ACCEPTEE."\"".(STATUT_RES_ACCEPTEE == $iStatutFCE ? " selected" : NULL).">".htmlentities($oFC->retTexteStatut(STATUT_RES_ACCEPTEE))."</option>"
		."</select>\n";
	$oTpl->remplacer("{etat->liste}",$sListeEtats);
	
	// Informations à propos de l'évaluateur
	$oTpl->remplacer("{tuteur->nom_complet}",htmlentities($oFCE->oEvaluateur->retNomComplet()));
	$oTpl->remplacer("{formulaire_eval->date}",$oFCE->retDate());
	
	// Appréciation
	$oTpl->remplacer("{appreciation->input->name}","appreciationEvalFCE");
	$oTpl->remplacer("{appreciation->texte}",htmlentities($oFCE->retAppreciation()));
	
	// Commentaire
	$oTpl->remplacer("{commentaire->textarea->name}","commentaireEvalFCE");
	$oTpl->remplacer("{commentaire->texte}",$oFCE->retCommentaire());
	
	$oTpl->remplacer("{fonction->init->corp}","top.frames['MENU'].location='formulaire_eval-menu.php?evalFC=1';");
}
else
{
	// Initialiser le formulaire complété et évalué
	$oFCE = new CFormulaireComplete_Evaluation($oProjet->oBdd,$url_iIdFCSousActiv,$url_iIdPers);
	$oFCE->initEvaluateur();
	
	if (is_object($oFCE->oEnregBdd))
	{
		$oTpl->remplacer("{evaluation->corp}",$sSetEvaluationEtudiant);
		
		// Statut du document
		if (STATUT_RES_SOUMISE == ($iStatutFCE = $oFCE->retStatut()))
			$iStatutFCE = STATUT_RES_ACCEPTEE;
		
		$oTpl->remplacer("{etat->liste}",str_replace(" ","&nbsp;",htmlentities($oFC->retTexteStatut($iStatutFCE))));
		
		// Informations à propos de l'évaluateur
		$oTpl->remplacer("{tuteur->nom_complet}",htmlentities($oFCE->oEvaluateur->retNomComplet()));
		$oTpl->remplacer("{formulaire_eval->date}",$oFCE->retDate());
		
		// Appréciation
		$oTpl->remplacer("{appreciation->input->name}","appreciationEvalFCE");
		$oTpl->remplacer("{appreciation->texte}",htmlentities($oFCE->retAppreciation()));
		
		// Commentaire
		$oTpl->remplacer("{commentaire->textarea->name}","commentaireEvalFCE");
		$oTpl->remplacer("{commentaire->texte}",convertBaliseMetaVersHtml($oFCE->retCommentaire()));
	}
	else
		$oTpl->remplacer("{evaluation->corp}",$sSetMessagePasEvalue);
}

$oTpl->remplacer("{fonction->init->corp}","top.frames['MENU'].location = 'formulaire_eval-menu.php';");

$oTpl->afficher();

$oProjet->terminer();

?>

