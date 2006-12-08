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
	foreach($aoObjetFormulaire as $oObjetFormulaire)
	{
		if(4<=$oObjetFormulaire->retIdTypeObj() && $oObjetFormulaire->retIdTypeObj()<=6)
		{
			if($bExportation)
			{
				print "Questions ".$oObjetFormulaire->retOrdre().";";
			}
			else
			{
				$oBlocQuestions->nextLoop();
				$oBlocQuestions->remplacer("{Question}","Question ".$oObjetFormulaire->retOrdre());
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
				$fScore = CalculerScore($iNbrePropRepCorrecte,$iNbrePropRepFausse,$iNbreRepCorrecte,$iNbreRepFausse);
				if(!$bExportation)
					$oBlocScores->remplacer("{Score}",round($fScore,2));
				else
					print round($fScore,2).";";
			}
			if(!$bExportation)
				$oBlocScores->afficher();
			else
				print "\n";
		}
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
