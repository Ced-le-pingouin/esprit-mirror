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

require_once("globals.inc.php");
$oProjet = new CProjet();

function formaterChaineCsv($v_sChaine)
{
	return str_replace("\"", "\"\"", $v_sChaine);
}

$iIdSousActiv = $HTTP_GET_VARS["idSousActiv"];
//$iIdFormulaire = ( isset($HTTP_GET_VARS["idformulaire"])?$HTTP_GET_VARS["idformulaire"]:118 );
//$iIdFc = $HTTP_GET_VARS["idfc"]; // 3,4,49,52,54 = Form118 | 42 = Form2 | 59 = Form126

$asTypes = array(1=>"Q texte long", "Q texte court", "Q nombre", "Q liste", "Q radio", "Q case", "texte", "séparateur");
$iValeurNeutreMin = 0;
$iValeurNeutreMax = 0;

$oSousActiv = new CSousActiv($oProjet->oBdd, $iIdSousActiv);
list($iIdFormulaire,$iDeroulement,$sIntituleLien) = explode(";",$oSousActiv->retDonnees());
$iNbFormulairesAcceptes = $oSousActiv->initFormulairesCompletes(NULL, STATUT_RES_ACCEPTEE);

$oFormulaire = new CFormulaire($oProjet->oBdd, $iIdFormulaire); // init objet Formulaire
$oFormulaire->initAxes(); // quels axes pour le formulaire
$oFormulaire->initObjets(TRUE, TRUE); // on init aussi les questions, les réponses possibles si choix multiples, et les valeurs de réponses (par axe)
$oFormulaire->determinerDonneesAExporter($iValeurNeutreMin, $iValeurNeutreMax);
$osFormulaire = serialize($oFormulaire);

// préparation des en-têtes de données exportées PAR AXE
$asColonnesExportees[0][] = '"Nom"';
$asColonnesExportees[0][] = '"Prenom"';
$asColonnesExportees[0][] = '"Pseudo"';
$asColonnesExportees[0][] = '"N° d\'ordre"';
if (count($oFormulaire->oExportation->abExporterAxeObjet))
{
	foreach ($oFormulaire->oExportation->abExporterAxeObjet as $iAxeExporte=>$abObjetsPourAxe)
	{
		foreach ($abObjetsPourAxe as $iObjetExporte=>$bAExporter)
		{
			if ($bAExporter)
				$asColonnesExportees[0][] = '"Axe '.$iAxeExporte.'/Elément n°'.$oFormulaire->aoObjets[$iObjetExporte]->retOrdre().'"';
		}
		
		$asColonnesExportees[0][] = '"Moyenne Axe '.$iAxeExporte.' ('.$oFormulaire->aoAxes[$iAxeExporte]->retNom().')"';
	}
}

// préparation des en-têtes de données exportées SANS AXE
$asColonnesExporteesSansAxe[0][] = '"Nom"';
$asColonnesExporteesSansAxe[0][] = '"Prenom"';
$asColonnesExporteesSansAxe[0][] = '"Pseudo"';
$asColonnesExporteesSansAxe[0][] = '"N° d\'ordre"';
if (count($oFormulaire->oExportation->abExporterObjetSansAxe))
{
	foreach ($oFormulaire->oExportation->abExporterObjetSansAxe as $iObjetExporte=>$bAExporter)
	{
		if ($bAExporter)
			$asColonnesExporteesSansAxe[0][] = '"Elément n°'.$oFormulaire->aoObjets[$iObjetExporte]->retOrdre().'"';
	}
}

if ($iNbFormulairesAcceptes)
{
	$iIndexEtudiant = 1;
	// !!! Il faut qu'il y ait un seul formulaire accepté par étudiant, ET que les formulaires soient COMPLETEMENT remplis
	// !!! pour que l'exportation fonctionne
	foreach ($oSousActiv->aoFormulairesCompletes as $oFc)
	{
		//$asColonnesExportees[$iIndexEtudiant] = NULL;
		
		$oFc->initAuteur();
		$oFc->initFormulaireModele(unserialize($osFormulaire)); $oFc->oFormulaireModele->oBdd =& $oFc->oBdd;
		//$oFc->oFormulaireModele->initAxes();
		//$oFc->oFormulaireModele->initObjets(TRUE, TRUE);
		//$oFc->oFormulaireModele->determinerDonneesAExporter($iValeurNeutreMin, $iValeurNeutreMax);
		$oFc->initReponses();
		
		// on exporte les données AVEC AXE
		if (count($oFc->oFormulaireModele->oExportation->abExporterAxeObjet))
		{
			//$asColonnesExportees[$iIndexEtudiant][] = '"'.$oFc->retId().'"';////
			$asColonnesExportees[$iIndexEtudiant][] = '" '.formaterChaineCsv($oFc->oAuteur->retNom()).'"';
			$asColonnesExportees[$iIndexEtudiant][] = '" '.formaterChaineCsv($oFc->oAuteur->retPrenom()).'"';
			$asColonnesExportees[$iIndexEtudiant][] = '" '.formaterChaineCsv($oFc->oAuteur->retPseudo()).'"';
			$asColonnesExportees[$iIndexEtudiant][] = $iIndexEtudiant;
			
			foreach ($oFc->oFormulaireModele->oExportation->abExporterAxeObjet as $iAxeExporte=>$abObjetsPourAxe)
			{
				$iNbObjetsPourAxe = 0;
				$iTotalPourAxe = 0;
				foreach ($abObjetsPourAxe as $iObjetExporte=>$bAExporter)
				{
					if ($bAExporter)
					{
						$iNbObjetsPourAxe++;
						if (isset($oFc->oFormulaireModele->aoObjets[$iObjetExporte]->sReponse))
						{
							/*print "FC: ".$oFc->retId();
							print " - FM: ".$oFc->oFormulaireModele->retId();
							print " - OBJ: ".$oFc->oFormulaireModele->aoObjets[$iObjetExporte]->retId();
							print " - REPS POS: ";
							foreach ($oFc->oFormulaireModele->aoObjets[$iObjetExporte]->aoReponsesPossibles as $oReponsePossible)
								print $oReponsePossible->retId().",";
							print " - REP DONNEE: ";
							print_r($oFc->oFormulaireModele->aoObjets[$iObjetExporte]->sReponse);
							print "<br>";*/
							
							$iIdReponse = $oFc->oFormulaireModele->aoObjets[$iObjetExporte]->sReponse[0];
							$iValeur = $oFc->oFormulaireModele->aoObjets[$iObjetExporte]->aoReponsesPossibles[$iIdReponse]->aiValeurAxe[$iAxeExporte];
							$asColonnesExportees[$iIndexEtudiant][] = $iValeur;
						}
						else
						{
							$iValeur = $oFc->oFormulaireModele->oExportation->iValeurAxeNeutre;
							$asColonnesExportees[$iIndexEtudiant][] = $iValeur;
						}
						$iTotalPourAxe += $iValeur;
					}
				}
				if ($iNbObjetsPourAxe != 0)
					$iMoyennePourAxe = $iTotalPourAxe / $iNbObjetsPourAxe;
				else
					$iMoyennePourAxe = 0;
				
				$asColonnesExportees[$iIndexEtudiant][] = str_replace('.', ',', (string)$iMoyennePourAxe);
			}
		}
		
		// ensuite, on exporte les données SANS AXE
		if (count($oFc->oFormulaireModele->oExportation->abExporterObjetSansAxe))
		{
			//$asColonnesExportees[$iIndexEtudiant][] = '"'.$oFc->retId().'"';////
			$asColonnesExporteesSansAxe[$iIndexEtudiant][] = '" '.formaterChaineCsv($oFc->oAuteur->retNom()).'"';
			$asColonnesExporteesSansAxe[$iIndexEtudiant][] = '" '.formaterChaineCsv($oFc->oAuteur->retPrenom()).'"';
			$asColonnesExporteesSansAxe[$iIndexEtudiant][] = '" '.formaterChaineCsv($oFc->oAuteur->retPseudo()).'"';
			$asColonnesExporteesSansAxe[$iIndexEtudiant][] = $iIndexEtudiant;
			
			foreach ($oFc->oFormulaireModele->oExportation->abExporterObjetSansAxe as $iObjetExporte=>$bAExporter)
			{
				if ($bAExporter)
				{
					$oObjet = $oFc->oFormulaireModele->aoObjets[$iObjetExporte];
					if ($oObjet->retIdType() == OBJFORM_QLISTEDEROUL || $oObjet->retIdType() == OBJFORM_QRADIO || $oObjet->retIdType() == OBJFORM_QCOCHER)
					{
						$asReponseComplete = NULL;
						
						foreach($oFc->oFormulaireModele->aoObjets[$iObjetExporte]->sReponse as $sReponseCourante)
						{
							//$iIdReponse = $oFc->oFormulaireModele->aoObjets[$iObjetExporte]->sReponse[0];
							
							$iIdReponse = $sReponseCourante;
							$sTexteReponse = $oFc->oFormulaireModele->aoObjets[$iObjetExporte]->aoReponsesPossibles[$iIdReponse]->retTexte();
							$asReponseComplete[] = $sTexteReponse;
							
							//$asColonnesExporteesSansAxe[$iIndexEtudiant][] = '" '.formaterChaineCsv($sTexteReponse).'"';
						}
						
						$asColonnesExporteesSansAxe[$iIndexEtudiant][] = '" '.formaterChaineCsv(implode(', ', $asReponseComplete)).'"';
					}
					else
					{
						$asColonnesExporteesSansAxe[$iIndexEtudiant][] = '" '.formaterChaineCsv($oFc->oFormulaireModele->aoObjets[$iObjetExporte]->sReponse[0]).'"';
					}
				}
			}
		}
		
		$iIndexEtudiant++;
		//print "<br>";
	}
}

//header('Cache-Control: no-store, no-cache, must-revalidate');
header("Content-type: application/force-download");
header("Content-Type: application/octetstream");
header("Content-Type: application/octet-stream");
header('Content-Disposition: attachment; filename='.urlencode($oFormulaire->retTitre()).'.csv');

//print "Sous-activité: ".$oSousActiv->retId();
//print "<br>";

print "Formulaire: ".$oFormulaire->retTitre();
print "\n";

print "Nombre d'axes exportés: ".count($oFormulaire->oExportation->abExporterAxeObjet);
print "\n";

print "Nombre d'éléments/questions: ".count($oFormulaire->aoObjets);
print "\n";

print "Nombre de formulaires traités: ".$iNbFormulairesAcceptes;
print "\n";

print "\n";
print "\n";

print "Les données ci-dessous proviennent des questions qui font usage des axes :";
print "\n";
print "\n";

for ($i = 0; $i < count($asColonnesExportees); $i++)
	print implode(';', $asColonnesExportees[$i])."\n";


print "\n";
print "\n";
print "\n";

print "Les données (réponses) ci-dessous proviennent des questions pour lesquelles les axes ne sont pas pris en compte"
	." (le contenu/texte de la réponse est donc exporté) :"
	;
print "\n";
print "\n";

for ($i = 0; $i < count($asColonnesExporteesSansAxe); $i++)
	print implode(';', $asColonnesExporteesSansAxe[$i])."\n";
?>
