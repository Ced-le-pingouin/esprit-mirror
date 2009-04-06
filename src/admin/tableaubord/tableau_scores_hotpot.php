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
//                          UniversitÃ©s Joseph Fourier.

/**
 * @file	tableau_scores_hotpot.php
 * 
 * Affiche les scores des Ã©tudiants pour un exercice hotpotatoes
 */

require_once("globals.inc.php");
require_once(dir_formation()."hotpotatoes.inc.php");

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

/**
 * le nom de fichier n'était pas pris en compte :
 * urlencode($oSousActiv->retNom()) ne retournait aucune valeur lors de l'exportation.
 * 
 * Ajout du nom de fichier dans lien "exporter"
 * 
 */
isset($_GET["fichier"]) ? $sNomFichier = $_GET["fichier"] : $sNomFichier = "temp";

if (isset($_GET["action"]) && $_GET["action"] == "exportation")
{
	$bExportation = true;
	header("Content-Type: application/excel; charset=utf-8");
	header("Content-Type: text/csv; charset=utf-8");
	header('Content-Disposition: attachment; filename='.urlencode($sNomFichier).'.csv');
}
else
{
	$bExportation = false;
	$oTpl = new Template("tableau_scores_hotpot.tpl");

/*
 * BLOCK_TITRE contient les balises <table> et <tr>
 * Celà permet de gérer l'affichage si il n'y a aucun score dans une activité 
 */
	$oBlocTitre = new TPL_Block("BLOCK_TITRE",$oTpl);
	$oBlocNoms = new TPL_Block("BLOCK_NOMS",$oTpl);
	$oBlocEssais = new TPL_Block("BLOCK_ESSAIS",$oTpl);
	$oBlocDetails = new TPL_Block("BLOCK_DETAILS",$oTpl);
	$oTpl->remplacer("{Titre}",$oSousActiv->retNom(true));
	$oTpl->remplacer("{IdHotpot}",$v_iIdHotpot);
	$oTpl->remplacer("{NomFichierHP}", urlencode($oSousActiv->retNom()));
}

$aoPers = $oHotpotatoes->etudiants();
$NbrePers = count($aoPers);

if ($NbrePers>0)
{
	$iNbEssais = $oHotpotatoes->retMaxEssais();

	//@{ Début Exportation
	if ($bExportation) {
		print "nom : ".$sNomFichier."\n\n";
		print "Nom-prénom (ordre alphabétique);N° essai (ordre n°);Score moyen (%);Nbre d'ex. réalisés;Dates;Heures\n";

		foreach ($aoPers as $oPers) {
			$lignePersonne[$oPers->retId()] = $oPers->retNom()." ".$oPers->retPrenom();
			$aoScores[$oPers->retId()] = $oHotpotatoes->scores_par_etudiant( $oPers->retId() );
			$iEssaiReel = 1;

			for ($iEssaiExport=1; $iEssaiExport <= count($aoScores[$oPers->retId()]); $iEssaiExport++) {
				//print "test$iNumeroEssai \n";
				if (isset($aoScores[$oPers->retId()][$iEssaiExport-1])) {
					$bScoreDejaInscrit = false;
					$iNbScoreParId 		= $aoScores[$oPers->retId()][$iEssaiExport-1]->NbScoreParId();
					$iNombreReelScore	= $aoScores[$oPers->retId()][$iEssaiExport-1]->NbReelScoresParId();
					$iIdExercice		= $aoScores[$oPers->retId()][$iEssaiExport-1]->retIdSessionExercice();
					$iIdHotpotatoe		= $aoScores[$oPers->retId()][$iEssaiExport-1]->retIdHotPot();
					$sNomPersonne		= utf8_decode($oPers->retNom());
					$sPrenomPersonne	= utf8_decode($oPers->retPrenom());
					/**
					 * Si on a plusieurs scores pour 1 id de session d'exercice (différent de 1),
					 * On récupère la moyenne de ces scores.
					 * 
					 * Cas d'un exercice HotPotatoes sur plusieurs fichiers html liés entre eux par le bouton "=>"
					 */
					//@{{
					if (isset($iNbScoreParId) && $iNbScoreParId > 1) // nombre de score pour 1 id de session
					{
						$aSauvegardeValeurs[$oPers->retId()][$iEssaiExport]		= $oPers->retId()."-".$iIdHotpotatoe."-".$iIdExercice;
						if (isset($aSauvegardeValeurs[$oPers->retId()][$iEssaiExport-1])) {
							if ($aSauvegardeValeurs[$oPers->retId()][$iEssaiExport-1] == $aSauvegardeValeurs[$oPers->retId()][$iEssaiExport]) {
								$bScoreDejaInscrit = true;
							}
							else $bScoreDejaInscrit = false;
						}
						//$iEssaiReel += $iNbScoreParId - 1;
					}
					else 
					{
						$iEssaiReel++;
					}

					$iScore			= $aoScores[$oPers->retId()][$iEssaiExport-1]->CalculMoyenne();
					$DateFormat		= retDateFormatter($aoScores[$oPers->retId()][$iEssaiExport-1]->retDateModif(), "d/m/Y");
					$HeureFormat	= retHeureFormatter($aoScores[$oPers->retId()][$iEssaiExport-1]->retDateModif());

					if (!$bScoreDejaInscrit) {
						print "$sNomPersonne $sPrenomPersonne; $iEssaiReel; $iScore; $iNombreReelScore; $DateFormat; $HeureFormat; \n";
					}
					//@}}
				}
				else print "\n erreur $iEssaiExport $iEssaiReel ".count($aoScores[$oPers->retId()]);
				//$iNumeroEssai++;
				//if ($iNumeroEssai > count($aoScores[$oPers->retId()])) break;
			}
			print "\n";
		}
		exit();
	}
	//@} Fin Exportation

	$oBlocTitre->remplacer("{TitreNom}","<table><tr><th class=\"titrenom\">Noms : </th>");
	$oBlocTitre->afficher();

	$oBlocNoms->beginLoop();
	foreach ($aoPers as $oPers) {
		$oBlocNoms->nextLoop();
		$oBlocNoms->remplacer("{NOM}","<th>".$oPers->retNom()." ".$oPers->retPrenom()."</th>");
		$aoScores[$oPers->retId()] = $oHotpotatoes->scores_par_etudiant( $oPers->retId() );
		$aEssaiPersonne[$oPers->retId()] = 1;
		$bScoreDejaAffiche[$oPers->retId()] = false;
	}
	$oBlocNoms->afficher();
	$oBlocEssais->beginLoop();
	$oBlocDetails->beginLoop();
	$iEssai = 1;

	while ($iEssai <= $iNbEssais) {
		$oBlocEssais->nextLoop();
		$oBlocEssais->remplacer("{EssaiNb}",$iEssai);

		$oBlocScores = new TPL_Block("BLOCK_SCORES",$oBlocEssais);
		$oBlocScores->beginLoop();

		foreach ($aoScores as $aoScoresIndiv) {
			$oBlocScores->nextLoop();
			if (isset($aoScoresIndiv[$iEssai-1])) {
				$oBlocDetails->nextLoop();
				$iIdPersonne 		= $aoScoresIndiv[$iEssai-1]->retIdPers();
				$iNbScoreParId 		= $aoScoresIndiv[$iEssai-1]->NbScoreParId();

				/**
				 * Si on a plusieurs scores pour 1 id de session d'exercice (différent de 1),
				 * On récupère la moyenne de ces scores et on l'affiche.
				 * 
				 * Cas d'un exercice HotPotatoes sur plusieurs fichiers html liés entre eux par le bouton "=>"
				 * 
				 */
				if (isset($iNbScoreParId) && $iNbScoreParId > 1 && $bScoreDejaAffiche[$iIdPersonne] == false)
				{
					$aEssaiPersonne[$iIdPersonne] += $iNbScoreParId;
					$bScoreDejaAffiche[$iIdPersonne] = true;
				}
				else {
					$aEssaiPersonne[$iIdPersonne] ++;
					$bScoreDejaAffiche[$iIdPersonne] = false;
				}

				$temp = $aEssaiPersonne[$iIdPersonne]-1;
				if (isset($aoScoresIndiv[$temp-1])) {
				$sNumeroDetails 	= $iIdPersonne."_".$iEssai;
				$iIdExercice		= $aoScoresIndiv[$temp-1]->retIdSessionExercice();
				$iIdHotpotatoe		= $aoScoresIndiv[$temp-1]->retIdHotPot();
				$iScore				= $aoScoresIndiv[$temp-1]->CalculMoyenne();
				$DateFormat			= retDateFormatter($aoScoresIndiv[$temp-1]->retDateModif());
				$iNombreReelScore	= $aoScoresIndiv[$temp-1]->NbReelScoresParId();
				$iScore				= $aoScoresIndiv[$temp-1]->CalculMoyenne();
				$DateFormat			= retDateFormatter($aoScoresIndiv[$temp-1]->retDateModif());

				$StyleCSS = "";

				$oBlocDetails->remplacer("{idexercice}",$sNumeroDetails);
				if ($iIdExercice == 0) {
					$oBlocDetails->remplacer("{nombre_exercice}", "<strong>Attention!</strong><p>ce score n'a aucune valeur (ancien syst&egrave;me HP.</p>");
					$StyleCSS = "class=\"Ancien_systemeHP\"";
				}
				else
					$oBlocDetails->remplacer("{nombre_exercice}", $iNombreReelScore > 1 ?  "$iNombreReelScore exercices r&eacute;alis&eacute;s"
																						: "$iNombreReelScore exercice r&eacute;alis&eacute;");

				if (isset($iScore)) 				
					$oBlocScores->remplacer("{Score}","<td $StyleCSS onmouseover=\"Montrer_Details(event,'$sNumeroDetails')\" onmouseout=\"Cacher_Details('$sNumeroDetails')\">".$iScore."&nbsp;%<br /><em>"
						.$DateFormat."</em></td>");
				else $oBlocScores->remplacer("{Score}","<td>???<br /><em>???</em></td>");
				}
				else $oBlocScores->remplacer("{Score}",'<td>-</td>');
			}
			else {
				$oBlocScores->remplacer("{Score}",'<td>-</td>');
			}
		}
		$oBlocScores->afficher();
		$iEssai++;
	}
	$oBlocDetails->afficher();
	$oBlocEssais->afficher();
}
else
{
	$oBlocTitre->remplacer("{TitreNom}","<table class=\"AucunEtudiant\"><tr>");
	$oBlocTitre->afficher();
	$oBlocNoms->remplacer("{NOM}","<th class=\"AucunEtudiant\"><strong>Aucune personne dans cette formation</strong></th>");
	$oBlocNoms->afficher();
	$oBlocDetails->effacer();
	$oBlocEssais->effacer();
}
$oTpl->afficher();
?>