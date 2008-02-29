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
// Copyright (C) 2001-2008  Unite de Technologie de l'Education, 
//                           Universite de Mons-Hainaut, Belgium ;
//                          Universités Joseph Fourier.

/**
 * @file	tableau_scores_hotpot.php
 * 
 * Affiche les scores des étudiants pour un exercice hotpotatoes
 */

require_once("globals.inc.php");

if (isset($_GET["IdHotpot"]))
	$v_iIdHotpot = $_GET["IdHotpot"];
else
	$v_iIdHotpot = 0;
if (isset($_GET["IdSousActiv"]))
	$v_idISousActiv = $_GET["IdSousActiv"];
else
	$v_iIdSousActiv = 0;

$oProjet = new CProjet();
$oSousActiv = new CSousActiv($oProjet->oBdd,$v_idISousActiv);
$oHotpotatoes = new CHotpotatoes($oProjet->oBdd,$v_iIdHotpot);

if (isset($_GET["action"]) && $_GET["action"] == "exportation")
{
	$bExportation = true;
	header("Content-type: application/force-download");
	header("Content-Type: application/octetstream");
	header("Content-Type: application/octet-stream");
	header('Content-Disposition: attachment; filename='.urlencode($oSousActiv->retNom()).'.csv');
}
else
{
	$bExportation = false;
	$oTpl = new Template("tableau_scores_hotpot.tpl");
	$oBlocNoms = new TPL_Block("BLOCK_NOMS",$oTpl);
	$oBlocEssais = new TPL_Block("BLOCK_ESSAIS",$oTpl);
	$oTpl->remplacer("{Titre}",$oSousActiv->retNom(true));
	$oTpl->remplacer("{IdHotpot}",$v_iIdHotpot);
}

$aoPers = $oHotpotatoes->etudiants();
$NbrePers = count($aoPers);
if ($NbrePers>0)
{
	$iNbEssais = $oHotpotatoes->retMaxEssais();

	if ($bExportation) {
		foreach ($aoPers as $oPers) {
			$ligne[] = $oPers->retNom()." ".$oPers->retPrenom();
			$aoScores[$oPers->retId()] = $oHotpotatoes->scores_par_etudiant( $oPers->retId() );
		}
		print join(' ; ',$ligne)."\n"; unset($ligne);
		for ($iEssai = 1; $iEssai <= $iNbEssais; $iEssai++) {
			foreach ($aoScores as $aoScoresIndiv) {
				if (isset($aoScoresIndiv[$iEssai-1])) {
					$ligne[] = $aoScoresIndiv[$iEssai-1]->retScore()." - "
					           .retDateFormatter( $aoScoresIndiv[$iEssai-1]->retDateModif() );
				} else {
					$ligne[] = ' ';
				}
			}
			print join(' ; ',$ligne)."\n"; unset($ligne);
		}
		exit();
	}

	$oBlocNoms->beginLoop();
	foreach ($aoPers as $oPers) {
		$oBlocNoms->nextLoop();
		$oBlocNoms->remplacer("{NOM}",$oPers->retNom()." ".$oPers->retPrenom());
		$aoScores[$oPers->retId()] = $oHotpotatoes->scores_par_etudiant( $oPers->retId() );
	}
	$oBlocNoms->afficher();
	$oBlocEssais->beginLoop();
	$iEssai = 1;
	while ($iEssai <= $iNbEssais) {
		$oBlocEssais->nextLoop();
		$oBlocEssais->remplacer("{EssaiNb}",$iEssai);
		$oBlocScores = new TPL_Block("BLOCK_SCORES",$oBlocEssais);
		$oBlocScores->beginLoop();
		foreach ($aoScores as $aoScoresIndiv) {
			$oBlocScores->nextLoop();
			if (isset($aoScoresIndiv[$iEssai-1])) {
				$oBlocScores->remplacer("{Score}",$aoScoresIndiv[$iEssai-1]->retScore()."&nbsp;%<br /><em>"
				                        .retDateFormatter($aoScoresIndiv[$iEssai-1]->retDateModif())."</em>");
			} else {
				$oBlocScores->remplacer("{Score}",'-');
			}
		}
		$oBlocScores->afficher();
		$iEssai++;
	}
	$oBlocEssais->afficher();
}
else
{
	$oBlocEssais->effacer();
}
$oTpl->afficher();
?>


