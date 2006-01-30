<?php
require_once("globals.inc.php");
$oProjet = new CProjet();
$iIdUtilisateur = $oProjet->oUtilisateur->retId();

//************************************************
//*       R�cup�ration des variables             *
//************************************************

$v_iIdFormulaire = ( isset($HTTP_GET_VARS["idFormulaire"])?$HTTP_GET_VARS["idFormulaire"]:($HTTP_POST_VARS["idFormulaire"]?$HTTP_POST_VARS["idFormulaire"]:NULL) );
$iIdSousActiv = ( isset($HTTP_GET_VARS["idSousActiv"])?$HTTP_GET_VARS["idSousActiv"]:($HTTP_POST_VARS["idSousActiv"]?$HTTP_POST_VARS["idSousActiv"]:NULL) );
if (isset($HTTP_POST_VARS['bSoumis']))
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

	if (isset($HTTP_GET_VARS["idFC"]))
	{
		$iIdFC = $HTTP_GET_VARS["idFC"];
		$oFormulaireComplete = new CFormulaireComplete($oProjet->oBdd, $iIdFC);
		$v_iIdFormulaire = $oFormulaireComplete->retIdForm();
	}
	else
	{
		$iIdFC = NULL;
	}
}

//***********************************************************************************
//*   Lecture de la table formulaire pour y r�cup�rer les donn�es de mise en page   *
//***********************************************************************************

$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
$sTitre = convertBaliseMetaVersHtml($oFormulaire->retTitre());
$iEncadrer = $oFormulaire->retEncadrer();
$iLargeur = $oFormulaire->retLargeur();
$iTypeLarg = $oFormulaire->retTypeLarg();	//Pourcentage ou pixel
$iInterElem = $oFormulaire->retInterElem();
$iInterEnonRep = $oFormulaire->retInterEnonRep();
$iRemplirTout = ( $oFormulaire->retRemplirTout()?1:0 );

if ($iTypeLarg=="P")					//ajoute % ou px a la largeur pour ainsi cr�er une chaine de car
   $sLargeur=$iLargeur."%";
else
   $sLargeur=$iLargeur."px";

if ($iEncadrer==1)						//V�rifie s'il faut encadrer le titre ou non et compose le code html
   $sEncadrer= " style=\"border:1px solid black;\" ";
else
   $sEncadrer="";

echo "<html>\n";
echo "<head>\n";
echo "<title>Activit� en ligne</title>\n";

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
	echo "  {margin-top:{$iInterEnonRep}px; }\n"; //Espace en pixels s�parant les �nonc�s des r�ponses
	echo ".InterObj";
	echo "  {margin-top:{$iInterElem}px; }\n"; //Espace en pixels s�parant les objets
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
				
			//Ces 2 lignes ci-dessous permettent de r�afficher la r�ponse fournie
			//Celles-ci serviront pour afficher les questionnaires remplis par les �tudiants
			$oQTexteLong = new CQTexteLong($oProjet->oBdd,$iIdObjActuel);
			if ($bSoumis)
			{
				//echo "<br>Objet de type 1 :";
				//echo $HTTP_POST_VARS[$iIdObjActuel];
				
				$oQTexteLong->enregistrerRep($iIdFC,$iIdObjActuel,$HTTP_POST_VARS[$iIdObjActuel]);
				
				//echo "<br>idFC : ".$iIdFC;
			}
			echo $oQTexteLong->cHtmlQTexteLong($iIdFC);
			break;
		
		case 2:
			//$oQTexteCourt = new CQTexteCourt($oProjet->oBdd);
			
			//Ces 2 lignes ci-dessous permettent de r�afficher la r�ponse fournie
			//Celles-ci serviront pour afficher les questionnaires remplis par les �tudiants
			$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$iIdObjActuel);
			if ($bSoumis)
			{
				//echo "<br>Objet de type 2 :";
				//echo $HTTP_POST_VARS[$iIdObjActuel];
				
				$oQTexteCourt->enregistrerRep($iIdFC,$iIdObjActuel,$HTTP_POST_VARS[$iIdObjActuel]);
				
				//echo "<br>idFC : ".$iIdFC;
			}
			echo $oQTexteCourt->cHtmlQTexteCourt($iIdFC);
			break;

		case 3:
			//$oQNombre = new CQNombre($oProjet->oBdd);
			
			//Ces 2 lignes ci-dessous permettent de r�afficher la r�ponse fournie
			//Celles-ci serviront pour afficher les questionnaires remplis par les �tudiants
			$oQNombre = new CQNombre($oProjet->oBdd,$iIdObjActuel);
			if ($bSoumis)
			{
				//echo "<br>Objet de type 3 :";
				//echo $HTTP_POST_VARS[$iIdObjActuel];
				
				// Transforme la virgule en point ex: 20,5 -> 20.5
				$HTTP_POST_VARS[$iIdObjActuel] = str_replace(",", ".", $HTTP_POST_VARS[$iIdObjActuel]);
				$oQNombre->enregistrerRep($iIdFC,$iIdObjActuel,$HTTP_POST_VARS[$iIdObjActuel]);
				
				//echo "<br>idFC : ".$iIdFC;
			}
			echo $oQNombre->cHtmlQNombre($iIdFC);
			break;
			
		case 4:
			//$oQListeDeroul = new CQListeDeroul($oProjet->oBdd);
			
			//Ces 2 lignes ci-dessous permettent de r�afficher la r�ponse fournie
			//Celles-ci serviront pour afficher les questionnaires remplis par les �tudiants
			$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$iIdObjActuel);
			if ($bSoumis)
			{
				//echo "<br>Objet de type 4 :";
				//echo $HTTP_POST_VARS[$iIdObjActuel];
				
				$oQListeDeroul->enregistrerRep($iIdFC,$iIdObjActuel,$HTTP_POST_VARS[$iIdObjActuel]);
				
				//echo "<br>idFC : ".$iIdFC;
			}
			echo $oQListeDeroul->cHtmlQListeDeroul($iIdFC);
			break;
			
		case 5:
			//$oQRadio = new CQRadio($oProjet->oBdd);
			
			//Ces 2 lignes ci-dessous permettent de r�afficher la r�ponse fournie
			//Celles-ci serviront pour afficher les questionnaires remplis par les �tudiants
			$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
			if ($bSoumis)
			{
				//echo "<br>Objet de type 5 :";
				//echo $HTTP_POST_VARS[$iIdObjActuel];
				
				$oQRadio->enregistrerRep($iIdFC,$iIdObjActuel,$HTTP_POST_VARS[$iIdObjActuel]);
				
				//echo "<br>idFC : ".$iIdFC;
			}
			echo $oQRadio->cHtmlQRadio($iIdFC);
			break;
			
		case 6:
			//$oQCocher = new CQCocher($oProjet->oBdd);
			
			//Ces 2 lignes ci-dessous permettent de r�afficher la r�ponse fournie
			//Celles-ci serviront pour afficher les questionnaires remplis par les �tudiants
			$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
			if ($bSoumis)
			{
				//echo "<br>Objet de type 6 :";
				
				for ($i = 0; $i < count($HTTP_POST_VARS[$iIdObjActuel]); $i++) 
				{
					//echo $HTTP_POST_VARS[$iIdObjActuel][$i];
					$oQCocher->enregistrerRep($iIdFC,$iIdObjActuel,$HTTP_POST_VARS[$iIdObjActuel][$i]);
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
			echo "Erreur: num�ro d'objet de formulaire incorrect !<br>";
			break;
	}
	
	echo "<div class=\"InterObj\"></div>\n";
}

echo "<div align=\"center\">\n";
echo "<INPUT TYPE=\"button\" VALUE=\"Valider\" name=\"soumettre\" onClick=\"validerFormulaire($iRemplirTout);\">\n";
//echo "<input type=\"reset\" value=\"R�initialiser le formulaire\">\n";
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

