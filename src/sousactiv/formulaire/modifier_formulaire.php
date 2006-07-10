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
$iIdUtilisateur = $oProjet->oUtilisateur->retId();

//************************************************
//*       Récupération des variables             *
//************************************************

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

//***********************************************************************************
//*   Lecture de la table formulaire pour y récupérer les données de mise en page   *
//***********************************************************************************

$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
$sTitre = convertBaliseMetaVersHtml($oFormulaire->retTitre());
$iEncadrer = $oFormulaire->retEncadrer();
$iLargeur = $oFormulaire->retLargeur();
$iTypeLarg = $oFormulaire->retTypeLarg();	//Pourcentage ou pixel
$iInterElem = $oFormulaire->retInterElem();
$iInterEnonRep = $oFormulaire->retInterEnonRep();
$iRemplirTout = ( $oFormulaire->retRemplirTout()?1:0 );

if ($iTypeLarg=="P")					//ajoute % ou px a la largeur pour ainsi créer une chaine de car
   $sLargeur=$iLargeur."%";
else
   $sLargeur=$iLargeur."px";

if ($iEncadrer==1)						//Vérifie s'il faut encadrer le titre ou non et compose le code html
   $sEncadrer= " style=\"border:1px solid black;\" ";
else
   $sEncadrer="";

echo "<html>\n";
echo "<head>\n";
echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n";
echo "<title>Activité en ligne</title>\n";

//echo "<script src=\"selectionobj.js\" type=\"text/javascript\"></script>\n";
echo "<script src=\"".dir_theme_commun("js/formulaire.js")."\" type=\"text/javascript\"></script>\n";
echo "<script src=\"".dir_code_lib_ced("general.js", FALSE, FALSE)."\" type=\"text/javascript\"></script>\n";

if ($v_iIdFormulaire > 0)
{
	//************************************************
	//*                     CSS                      *
	//************************************************
	
	echo "<style type=\"text/css\">\n";
	echo "<!--\n";
	echo "form";
	echo "  { margin-left:$sLargeur; margin-right:$sLargeur; }\n";
	echo ".p";
	echo "  {line-height:10.5pt font-family:Arial,sans-serif; font-size:10pt; color:black; margin-top:6px; margin-bottom:6px; }\n";
	echo ".InterER";
	echo "  {margin-top:{$iInterEnonRep}px; }\n"; //Espace en pixels séparant les énoncés des réponses
	echo ".InterObj";
	echo "  {margin-top:{$iInterElem}px; }\n"; //Espace en pixels séparant les objets
	echo "-->\n";
	echo "</style>\n";
}

//CSS insertion
echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">\n";
//FIN CSS


echo "</head>\n";

echo "<body class=\"liste\">\n";
echo "<FORM NAME=\"questionnaire\" ACTION=\"{$_SERVER['PHP_SELF']}\" METHOD=\"POST\" ENCTYPE=\"text/html\" CLASS=\"formFormulaire\">";
echo "<input type=\"hidden\" name=\"idFormulaire\" value=\"{$v_iIdFormulaire}\">\n";
if (!empty($iIdSousActiv))
	echo "<input type=\"hidden\" name=\"idSousActiv\" value=\"{$iIdSousActiv}\">\n";
echo "<input type=\"hidden\" name=\"bSoumis\" value=\"1\">\n";
echo "<TABLE $sEncadrer ALIGN=\"center\"><TR><TD><font size=+1><b>$sTitre<b/></font></TD></TR></TABLE>\n";

echo "<br><br><br>";

$hResult = $oProjet->oBdd->executerRequete("SELECT * FROM ObjetFormulaire"
									." WHERE IdForm = '$v_iIdFormulaire'"
									." ORDER by OrdreObjForm");
									
while ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
{
	$oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd);
	$oObjetFormulaire->init($oEnreg);
	
	$iIdObjActuel = $oObjetFormulaire->retId();
	print "<a name=\"ancre{$iIdObjActuel}\"></a>\n";
	//$iOrdreObjForm = $oObjetFormulaire->retOrdreObjForm();
	
	switch($oObjetFormulaire->retIdTypeObj())
	{
		case 1:
			///$oQTexteLong = new CQTexteLong($oProjet->oBdd);
				
			//Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
			//Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
			$oQTexteLong = new CQTexteLong($oProjet->oBdd,$iIdObjActuel);
			if ($bSoumis)
			{
				//echo "<br>Objet de type 1 :";
				//echo $_POST[$iIdObjActuel];
				
				$oQTexteLong->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
				
				//echo "<br>idFC : ".$iIdFC;
			}
			echo $oQTexteLong->cHtmlQTexteLong($iIdFC);
			break;
		
		case 2:
			//$oQTexteCourt = new CQTexteCourt($oProjet->oBdd);
			
			//Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
			//Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
			$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$iIdObjActuel);
			if ($bSoumis)
			{
				//echo "<br>Objet de type 2 :";
				//echo $_POST[$iIdObjActuel];
				
				$oQTexteCourt->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
				
				//echo "<br>idFC : ".$iIdFC;
			}
			echo $oQTexteCourt->cHtmlQTexteCourt($iIdFC);
			break;

		case 3:
			//$oQNombre = new CQNombre($oProjet->oBdd);
			
			//Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
			//Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
			$oQNombre = new CQNombre($oProjet->oBdd,$iIdObjActuel);
			if ($bSoumis)
			{
				//echo "<br>Objet de type 3 :";
				//echo $_POST[$iIdObjActuel];
				
				// Transforme la virgule en point ex: 20,5 -> 20.5
				$_POST[$iIdObjActuel] = str_replace(",", ".", $_POST[$iIdObjActuel]);
				$oQNombre->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
				
				//echo "<br>idFC : ".$iIdFC;
			}
			echo $oQNombre->cHtmlQNombre($iIdFC);
			break;
			
		case 4:
			//$oQListeDeroul = new CQListeDeroul($oProjet->oBdd);
			
			//Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
			//Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
			$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$iIdObjActuel);
			if ($bSoumis)
			{
				//echo "<br>Objet de type 4 :";
				//echo $_POST[$iIdObjActuel];
				
				$oQListeDeroul->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
				
				//echo "<br>idFC : ".$iIdFC;
			}
			echo $oQListeDeroul->cHtmlQListeDeroul($iIdFC);
			break;
			
		case 5:
			//$oQRadio = new CQRadio($oProjet->oBdd);
			
			//Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
			//Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
			$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
			if ($bSoumis)
			{
				//echo "<br>Objet de type 5 :";
				//echo $_POST[$iIdObjActuel];
				
				$oQRadio->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel]);
				
				//echo "<br>idFC : ".$iIdFC;
			}
			echo $oQRadio->cHtmlQRadio($iIdFC);
			break;
			
		case 6:
			//$oQCocher = new CQCocher($oProjet->oBdd);
			
			//Ces 2 lignes ci-dessous permettent de réafficher la réponse fournie
			//Celles-ci serviront pour afficher les questionnaires remplis par les étudiants
			$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
			if ($bSoumis)
			{
				//echo "<br>Objet de type 6 :";
				
				for ($i = 0; $i < count($_POST[$iIdObjActuel]); $i++) 
				{
					//echo $_POST[$iIdObjActuel][$i];
					$oQCocher->enregistrerRep($iIdFC,$iIdObjActuel,$_POST[$iIdObjActuel][$i]);
				}
				
				//echo "<br>idFC : ".$iIdFC;
			}
			echo $oQCocher->cHtmlQCocher($iIdFC);
			break;
			
		case 7:
			//echo "Objet de type 7<br>";
			$oMPTexte = new CMPTexte($oProjet->oBdd,$iIdObjActuel);
			echo $oMPTexte->cHtmlMPTexte();
			break;
			
		case 8:
			//echo "Objet de type 8<br>";
			$oMPSeparateur = new CMPSeparateur($oProjet->oBdd,$iIdObjActuel);
			echo $oMPSeparateur->cHtmlMPSeparateur();
			break;
			
		default:
			echo "Erreur: numéro d'objet de formulaire incorrect !<br>";
			break;
	}
	
	echo "<div class=\"InterObj\"></div>\n";
}

echo "<div align=\"center\">\n";
echo "<INPUT TYPE=\"button\" VALUE=\"Valider\" name=\"soumettre\" onClick=\"validerFormulaire($iRemplirTout);\">\n";
//echo "<input type=\"reset\" value=\"Réinitialiser le formulaire\">\n";
echo "</div>\n";
echo "</FORM>\n";

if ($bSoumis)
{
	$oFormulaireComplete->deverrouillerTables();
	
	echo "<script language=\"javascript\" type=\"text/javascript\">";
	echo "\ttop.opener.location = top.opener.location;\n";
	echo "\ttop.close();\n";
	echo "</script>\n";
}

echo "</body>\n";
echo "</html>\n";
?>

