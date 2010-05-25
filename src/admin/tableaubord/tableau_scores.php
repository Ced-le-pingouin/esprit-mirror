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

/**
 * @file	tableau_scores.php
 * 
 * Affiche les scores des étudiants d'une activité en ligne de type auto-corrigé
 * 
 * @date	2006/11/30
 * 
 * @author	Jérôme TOUZE
 */

require_once("globals.inc.php");
require_once(dir_database("formulairecomplete.tbl.php"));
require_once(dir_database("formulaire.tbl.php"));
require_once(dir_database("objetformulaire.tbl.php"));
require_once(dir_database("fonctions_form.inc.php"));
require_once(dir_database("propositionreponse.tbl.php"));
require_once(dir_database("qradio.tbl.php"));
require_once(dir_database("qlistederoul.tbl.php"));
require_once(dir_database("qcocher.tbl.php"));
require_once(dir_database("qtextelong.tbl.php"));
require_once(dir_database("qtextecourt.tbl.php"));
require_once(dir_database("qnombre.tbl.php"));
$oProjet = new CProjet();
if(isset($_GET["IdFormul"]))
	$v_iIdFormul = $_GET["IdFormul"];
else
	$v_iIdFormul = 0;
$oFormul = new CFormulaire($oProjet->oBdd,$v_iIdFormul);
$oFormulComplete = new CFormulaireComplete($oProjet->oBdd);
if(isset($_GET["action"]) && $_GET["action"] == "exportation")
{
	$bExportation = true;
	header("Content-type: application/force-download");
	header("Content-Type: application/octetstream");
	header("Content-Type: application/octet-stream");
	header('Content-Disposition: attachment; filename='.urlencode($oFormul->retTitre()).'.csv');
}
else
{
	$bExportation = false;
	$oTpl = new Template("tableau_scores.tpl");
	$oBlocNoms = new TPL_Block("BLOCK_NOMS",$oTpl);
	$oBlocQuestions = new TPL_Block("BLOCK_QUESTIONS",$oTpl);
}
if(!$bExportation)
{
	$oTpl->remplacer("{Titre}",$oFormul->retTitre());
	$oTpl->remplacer("{IdFormul}",$v_iIdFormul);
}
$aoListeFC = $oFormulComplete->retListeFormulaireComplete($v_iIdFormul);
$NbrePers = count($aoListeFC);
if($NbrePers>0)
{
	$aoObjetFormulaire = $oFormul->retListeObjetFormulaire();
	if($bExportation)
		print "Noms :;";
	else
		$oBlocNoms->beginLoop();
	foreach($aoListeFC AS $oFC)
	{
		if($bExportation)
		{
			print mb_convert_encoding($oFC->Nom." ".$oFC->Prenom,"ISO-8859-1","UTF-8").";";
		}
		else
		{
			$oBlocNoms->nextLoop();
			$oBlocNoms->remplacer("{NOM}",$oFC->Nom." ".$oFC->Prenom);
		}
	}
	if($bExportation)
	{
		print "\n";
	}
	else
	{
		$oBlocNoms->afficher();
		$oBlocQuestions->beginLoop();
	}
	$aiScorePers = array_fill(0,$NbrePers,0);
	$iNumeroQuestion = 0;
	$iScoreMax = 0;
	foreach($aoObjetFormulaire as $oObjetFormulaire)
	{
		if(1<=$oObjetFormulaire->retIdTypeObj() && $oObjetFormulaire->retIdTypeObj()<=3)
		{
			$iNumeroQuestion++;
			if(!$bExportation)
			{
				$oBlocQuestions->nextLoop();
				$oObjetFormulaire->initDetail();
				switch($oObjetFormulaire->retIdTypeObj())
				{
					case OBJFORM_QTEXTELONG:	$sTitre = $oObjetFormulaire->oDetail->retEnonQTL();
												break;
					case OBJFORM_QTEXTECOURT:		$sTitre = $oObjetFormulaire->oDetail->retEnonQTC();
												break;
					case OBJFORM_QNOMBRE:		$sTitre = $oObjetFormulaire->oDetail->retEnonQN();
												break;
					default:					$sTitre = "";
				}
				$oBlocQuestions->remplacer("{Question}","<span title=\"".emb_htmlentities($sTitre)."\">Question ".$iNumeroQuestion."</span>");
				$oBlocQuestions->remplacer("{ClassQuestion}"," class=\"grise\"");
				$oBlocScores = new TPL_Block("BLOCK_SCORES",$oBlocQuestions);
				$oBlocScores->beginLoop();
				for($i=0;$i<$NbrePers;$i++)
				{
					$oBlocScores->nextLoop();
					$oBlocScores->remplacer("{Score}","&nbsp;");
					$oBlocScores->remplacer("{ClassScore}"," class=\"grise\"");
				}
				$oBlocScores->afficher();
			}
		}
		if(4<=$oObjetFormulaire->retIdTypeObj() && $oObjetFormulaire->retIdTypeObj()<=6)
		{
			$iNumeroQuestion++;
			$iScoreMax ++;
			if($bExportation)
			{
				print "Questions ".$iNumeroQuestion.";";
			}
			else
			{
				$oBlocQuestions->nextLoop();
				$oObjetFormulaire->initDetail();
				switch($oObjetFormulaire->retIdTypeObj())
				{
					case OBJFORM_QLISTEDEROUL:	$sTitre = $oObjetFormulaire->oDetail->retEnonQLD();
												break;
					case OBJFORM_QRADIO:		$sTitre = $oObjetFormulaire->oDetail->retEnonQR();
												break;
					case OBJFORM_QCOCHER:		$sTitre = $oObjetFormulaire->oDetail->retEnonQC();
												break;
					default:					$sTitre = "";
				}
				$oBlocQuestions->remplacer("{Question}","<span title=\"".emb_htmlentities($sTitre)."\">Question ".$iNumeroQuestion."</span>");
				$oBlocQuestions->remplacer("{ClassQuestion}","");
				$oBlocScores = new TPL_Block("BLOCK_SCORES",$oBlocQuestions);
				$oBlocScores->beginLoop();
			}
			for($i=0;$i<$NbrePers;$i++)
			{
				if(!$bExportation)
					$oBlocScores->nextLoop();
				$oFC = $aoListeFC[$i];
				$iIdReponseEtu = retReponseEntier($oProjet->oBdd,$aoListeFC[$i]->IdFC,$oObjetFormulaire->retId());
				$oPropositionReponse = new CPropositionReponse($oProjet->oBdd);
				$aoListePropRep = $oPropositionReponse->retListePropRep($oObjetFormulaire->retId());
				if(!empty($aoListePropRep))
				{
					$iNbrePropRep = $iNbrePropRepCorrecte = $iNbrePropRepFausse = 0;
					$iNbreRepCorrecte = $iNbreRepFausse = 0;
					foreach($aoListePropRep AS $oPropRep)
					{
						if(in_array($oPropRep->retId(), $iIdReponseEtu)) 
						{
							switch($oPropRep->retScorePropRep())
							{
								case "-1" :	$iNbreRepFausse++;
											break;
								case "1" :	$iNbreRepCorrecte++;
											break;
							}
						}
						switch($oPropRep->retScorePropRep())
						{
									case "-1" :	$iNbrePropRepFausse++;
												break;
									case "1" :	$iNbrePropRepCorrecte++;
												break;
						}
						$iNbrePropRep++;
					}
				}
				if(4<=$oObjetFormulaire->retIdTypeObj() && $oObjetFormulaire->retIdTypeObj()<=5)
					$fScore = CalculerScore(1,$iNbrePropRepFausse,$iNbreRepCorrecte,$iNbreRepFausse,$oFormul->retMethodeCorrection());
				else
					$fScore = CalculerScore($iNbrePropRepCorrecte,$iNbrePropRepFausse,$iNbreRepCorrecte,$iNbreRepFausse,$oFormul->retMethodeCorrection());
				if(!$bExportation)
				{
					$oBlocScores->remplacer("{Score}",round($fScore,2));
					$oBlocScores->remplacer("{ClassScore}","");
				}
				else
				{
					print round($fScore,2).";";
				}
				$aiScorePers[$i] = $fScore + $aiScorePers[$i];
			}
			if(!$bExportation)
				$oBlocScores->afficher();
			else
				print "\n";
		}
	}
	// affichage des totaux
	if(!$bExportation)
	{
		$oBlocQuestions->nextLoop();
		$oBlocQuestions->remplacer("{Question}","Total : ");
		$oBlocQuestions->remplacer("{ClassQuestion}","");
		$oBlocScores = new TPL_Block("BLOCK_SCORES",$oBlocQuestions);
		$oBlocScores->beginLoop();
		for($i=0;$i<$NbrePers;$i++)
		{
			$oBlocScores->nextLoop();
			$iPourcentage = ($iScoreMax != 0) ? round(($aiScorePers[$i]/$iScoreMax)*100) : "0";
			$oBlocScores->remplacer("{Score}",round($aiScorePers[$i],2)."/$iScoreMax ($iPourcentage%)");
			$oBlocScores->remplacer("{ClassScore}","");
		}
		$oBlocScores->afficher();
	}
	else
	{
		print "Total : ;";
		for($i=0;$i<$NbrePers;$i++)
		{
			$iPourcentage = round(($aiScorePers[$i]/$iScoreMax)*100);
			print round($aiScorePers[$i],2)."/$iScoreMax ($iPourcentage%)".";";
		}
		print "\n";
	}
	if(!$bExportation)
		$oBlocQuestions->afficher();
}
else
{
	if(!$bExportation)
		$oBlocNoms->effacer();
}
if(!$bExportation)
	$oTpl->afficher();
?>
