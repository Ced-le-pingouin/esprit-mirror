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
$oTpl = new Template("modifier_formulaire.tpl");
$oBlockFormulaire = new TPL_Block("BLOCK_FORMULAIRE",$oTpl);
$oBlockFermer = new TPL_Block("BLOCK_FERMER",$oTpl);
$iIdUtilisateur = $oProjet->oUtilisateur->retId();

//	Récupération des variables
$v_iIdFormulaire = ( isset($_GET["idFormulaire"])?$_GET["idFormulaire"]:($_POST["idFormulaire"]?$_POST["idFormulaire"]:NULL) );
$iIdSousActiv = ( isset($_GET["idSousActiv"])?$_GET["idSousActiv"]:($_POST["idSousActiv"]?$_POST["idSousActiv"]:NULL) );

if (isset($_POST['bSoumis']))
{
	$bSoumis = TRUE;
	$oFormulaireComplete = new CFormulaireComplete($oProjet->oBdd);
	$oFormulaireComplete->verrouillerTables();
	$iIdFC = $oFormulaireComplete->ajouter($iIdUtilisateur, $v_iIdFormulaire);
	if (isset($iIdSousActiv))
	{
		$oSousActiv = new CSousActiv($oProjet->oBdd, $iIdSousActiv);
		list($sLien, $iMode, $sIntitule) = explode(";",$oSousActiv->retDonnees());
		if ($iMode == SOUMISSION_AUTOMATIQUE)
			$oFormulaireComplete->deposerDansSousActiv($iIdSousActiv, STATUT_RES_SOUMISE);
		else
			$oFormulaireComplete->deposerDansSousActiv($iIdSousActiv, STATUT_RES_EN_COURS);
	}
}
else
{
	$bSoumis = FALSE;
	if (isset($_GET["idFC"]))
	{
		$iIdFC = $_GET["idFC"];
		$oFormulaireComplete = new CFormulaireComplete($oProjet->oBdd, $iIdFC);
		$v_iIdFormulaire = $oFormulaireComplete->retIdForm();
	}
	else
	{
		$iIdFC = NULL;
	}
}

if ($v_iIdFormulaire > 0)
{
	if($bSoumis)
		$oBlockFormulaire->effacer();
	else
		$oBlockFormulaire->afficher();
	// Lecture de la table formulaire pour y récupérer les données de mise en page
	$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
	$sTitre = convertBaliseMetaVersHtml($oFormulaire->retTitre());
	$iInterElem = $oFormulaire->retInterElem();
	$iInterEnonRep = $oFormulaire->retInterEnonRep();
	$iIdPersForm = $oFormulaire->retIdPers();
	$iRemplirTout = ( $oFormulaire->retRemplirTout()?1:0 );
	if ($oFormulaire->retEncadrer() == 1)				//Vérifie s'il faut encadrer le titre ou non et compose le code html
		$sEncadrer = "style=\"border:1px solid black;\"";
	else
		$sEncadrer = "";
	$iLargeur = $oFormulaire->retLargeur();
	if ($oFormulaire->retTypeLarg() == "P") //Pourcentage ou pixel
		$sLargeur = $iLargeur."%";
	else
		$sLargeur = $iLargeur."px";
	$oTpl->remplacer("{sLargeur}",$sLargeur);
	$oTpl->remplacer("{iInterEnonRep}",$iInterEnonRep);
	$oTpl->remplacer("{iInterElem}",$iInterElem);
	if($oProjet->verifPermission("PERM_EVALUER_FORMULAIRE"))
		$oTpl->remplacer("{bouton_valider}","");
	else
		$oTpl->remplacer("{bouton_valider}","<input type=\"button\" value=\"Valider\" name=\"soumettre\" onclick=\"validerFormulaire($iRemplirTout);\" />");
		
	$oTpl->remplacer("{general_js_php}",dir_code_lib_ced("general.js.php", FALSE, FALSE));
	$oTpl->remplacer("{formulaire_js}",dir_theme_commun("js/formulaire.js"));
	$oTpl->remplacer("{sEncadrer}",$sEncadrer);
	$oTpl->remplacer("{sTitre}",$sTitre);
	
	$oTpl->remplacer("{iIdFormulaire}",$v_iIdFormulaire);
	if(!empty($iIdSousActiv))
		$oTpl->remplacer("{input_ss_activ}","<input type=\"hidden\" name=\"idSousActiv\" value=\"{$iIdSousActiv}\" />\n");
	else
		$oTpl->remplacer("{input_ss_activ}","");
	$aoObjetFormulaire = $oFormulaire->retListeObjetFormulaire();
	$sHtmlListeObjForm = "";
	foreach($aoObjetFormulaire as $oObjetFormulaire)
	{
		$iIdObjActuel = $oObjetFormulaire->retId();
		$sHtmlListeObjForm .= "<a name=\"ancre{$iIdObjActuel}\"></a>\n";
		switch($oObjetFormulaire->retIdTypeObj())
		{
			case 1:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQTexteLong = new CQTexteLong($oProjet->oBdd,$iIdObjActuel);
					if ($bSoumis)
						$oQTexteLong->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					else
						$sHtmlListeObjForm .= $oQTexteLong->cHtmlQTexteLong($iIdFC);
					break;
			
			case 2:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$iIdObjActuel);
					if ($bSoumis)
						$oQTexteCourt->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					else
						$sHtmlListeObjForm .= $oQTexteCourt->cHtmlQTexteCourt($iIdFC);
					break;
			
			case 3:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQNombre = new CQNombre($oProjet->oBdd,$iIdObjActuel);
					if ($bSoumis)
					{
						// Transforme la virgule en point ex: 20,5 -> 20.5
						$_POST[$iIdObjActuel] = str_replace(",", ".", $_POST[$iIdObjActuel]);
						$oQNombre->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					}
					else
						$sHtmlListeObjForm .= $oQNombre->cHtmlQNombre($iIdFC);
					break;
			
			case 4:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$iIdObjActuel);
					if ($bSoumis)
						$oQListeDeroul->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					else
						$sHtmlListeObjForm .= $oQListeDeroul->cHtmlQListeDeroul($iIdFC);
					break;
			
			case 5:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
					if ($bSoumis)
						$oQRadio->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
					else
						$sHtmlListeObjForm .= $oQRadio->cHtmlQRadio($iIdFC);
					break;
			
			case 6:	// Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
					// Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
					$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
					if ($bSoumis)
					{
						for ($i = 0; $i < count($_POST[$iIdObjActuel]); $i++) 
						{
							$oQCocher->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel][$i]);
						}
					}
					else
						$sHtmlListeObjForm .= $oQCocher->cHtmlQCocher($iIdFC);
					break;
			
			case 7:	$oMPTexte = new CMPTexte($oProjet->oBdd,$iIdObjActuel);
					if (!$bSoumis)
						$sHtmlListeObjForm .= $oMPTexte->cHtmlMPTexte();
					break;
			
			case 8:	$oMPSeparateur = new CMPSeparateur($oProjet->oBdd,$iIdObjActuel);
					if (!$bSoumis)
						$sHtmlListeObjForm .= $oMPSeparateur->cHtmlMPSeparateur();
					break;
		}
		$sHtmlListeObjForm .= "<div class=\"InterObj\"></div>\n";
	}
	$oTpl->remplacer("{ListeObjetFormul}",$sHtmlListeObjForm);
}
else
{
	$oBlockFormulaire->effacer();
}
if($bSoumis)
{
	$oFormulaireComplete->deverrouillerTables();
	$oBlockFermer->afficher();
}
else
{
	$oBlockFermer->effacer();
}
$oTpl->afficher(); 
?>
