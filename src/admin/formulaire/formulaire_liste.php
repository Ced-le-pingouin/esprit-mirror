<?php
require_once("globals.inc.php");
$oProjet = new CProjet();
if ($oProjet->verifPermission('PERM_MOD_FORMULAIRES'))
{
	//************************************************
	//*       Récupération des variables             *
	//************************************************
	
	if (isset($HTTP_GET_VARS))
	{
		$v_iIdObjForm = $HTTP_GET_VARS['idobj'];
		$v_iIdFormulaire = $HTTP_GET_VARS['idformulaire'];
	}
	else if (isset($HTTP_POST_VARS))
	{
		$v_iIdObjForm = $HTTP_GET_VARS['idobj'];
		$v_iIdFormulaire = $HTTP_POST_VARS['idformulaire'];
	}
	else
	{
		$v_iIdFormulaire = 0;
		$v_iIdObjForm = 0;
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
	$iIdPersForm = $oFormulaire->retIdPers();
	
	if ($iTypeLarg=="P")					//ajoute % ou px a la largeur pour ainsi créer une chaine de car
		$sLargeur=$iLargeur."%";
	else
		$sLargeur=$iLargeur."px";
	
	if ($iEncadrer==1)						//Vérifie s'il faut encadrer le titre ou non et compose le code html
		$sEncadrer = " style=\"border:1px solid black;\" ";
	else
		$sEncadrer="";
	
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>Mettre en forme les formulaires et les éléments de formulaires avec les CSS</title>\n";
	
	echo "<script src=\"selectionobj.js\" type=\"text/javascript\"></script>\n";
	echo "<script language=\"Javascript\" src=\"/code_lib/general.js\"></script>\n";
	echo "<script language=\"Javascript\">\n";
	echo "function allerAPos()\n";
	echo "{\n";
	echo "\tiPos = retParamUrl(window.location, 'pos');\n";
	echo "\tif (iPos != null)\n";
	echo "\t\tdocument.location = '#' + iPos;\n";
	echo "}\n";
	echo "</script>\n";
	
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
	
	if ($v_iIdFormulaire > 0) //sert uniquement lors du 1er appel de la page (affichage du logo)
	{
		// on vérifie si la personne peut voir OU modifier le formulaire ! les modifs se font a 2 endroits
		$iIdPers = $oProjet->oUtilisateur->retId();
		
		// si $v_iIdObjForm = 0 cela veut dire que l'on vient de selectionner le formulaire via le menu et alors :
		// on charge la modif du titre formulaire dans la frame du dessous[modif] uniquement si on est le propriétaire du formulaire où
		// si l'on est administrateur
		if ($HTTP_GET_VARS["verifUtilisation"] == 1)
		{
			$iNbUtilisations = $oFormulaire->retNbUtilisationsDsSessions();
			$iNbRemplis = $oFormulaire->retNbRemplisDsSessions();
			$sJsVerifUtilisation = " alerteFormulaireUtilise({$iNbUtilisations},{$iNbRemplis});";
		}
		if ( ($oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES')) or ($iIdPersForm == $iIdPers) )
			echo "<body class=\"liste\" onLoad =\"selectionobj($v_iIdObjForm,$v_iIdFormulaire); allerAPos();{$sJsVerifUtilisation}\">\n";
		else 
			echo "<body class=\"liste\" onLoad =\"selectionobj('NULL','NULL')\">\n";
		
		$hResult = $oProjet->oBdd->executerRequete("SELECT * FROM ObjetFormulaire"
											." WHERE IdForm = $v_iIdFormulaire"
											." ORDER by OrdreObjForm");
		
		echo "<FORM NAME=\"selection\" CLASS=\"formFormulaire\">";
		
		if ($v_iIdObjForm == 0) {$sCocher='CHECKED';} else {$sCocher="";}  //utile si on arrive sur la liste après suppression d'un objet par exemple
																								 //cela permet de cocher le bouton radio devant le titre sans intervention de l'utilisateur
		//Si on clique sur le titre on envoie à la page 'formulaire_modif.php' via javascript 
		//idobj=0 et le numéro de formulaire 
		if ( ($oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES')) or ($iIdPersForm == $iIdPers) )
			$sSelectModifTitre="<INPUT TYPE=\"radio\" NAME=\"objet\" VALUE=\"TitreFormulaire\" onClick =\"selectionobj(0,$v_iIdFormulaire)\" $sCocher>\n";
		else
			$sSelectModifTitre="";
		
		echo "$sSelectModifTitre"."<TABLE $sEncadrer ALIGN=\"center\"><TR><TD><font size=+1><b>$sTitre<b/></font></TD></TR></TABLE>\n";
		
		echo "<br>";
		
		while ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
		{
			$oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd);
			$oObjetFormulaire->init($oEnreg);
			
			$iIdObjActuel = $oObjetFormulaire->retId();
			$iOrdreObjForm = $oObjetFormulaire->retOrdreObjForm();
			
			echo "\n<a name=\"$iIdObjActuel\"></a>\n";
			
			if ($iIdObjActuel==$v_iIdObjForm)
				$sCocher='CHECKED';
			else
				$sCocher="";
			
			if ( ($oProjet->verifPermission('PERM_MOD_TOUS_FORMULAIRES')) || ($iIdPersForm == $iIdPers) )
				$sSelectModif = "<INPUT TYPE=\"radio\" NAME=\"objet\" VALUE=\"$iIdObjActuel\" onClick =\"selectionobj($iIdObjActuel,$v_iIdFormulaire)\" $sCocher><b>$iOrdreObjForm</b>";
			else 
				$sSelectModif = "";
			
			switch($oObjetFormulaire->retIdTypeObj())
			{
				case 1:
					//echo "Objet de type 1<br>";
					
					$oQTexteLong = new CQTexteLong($oProjet->oBdd,$iIdObjActuel);
					echo "$sSelectModif".$oQTexteLong->cHtmlQTexteLong()."\n";
					break;
					
				case 2:
					//echo "Objet de type 2<br>";
					
					$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$iIdObjActuel);
					echo "$sSelectModif".$oQTexteCourt->cHtmlQTexteCourt()."\n";					
					break;
					
				case 3:
					//echo "Objet de type 3<br>";
					
					$oQNombre = new CQNombre($oProjet->oBdd,$iIdObjActuel);
					echo "$sSelectModif".$oQNombre->cHtmlQNombre()."\n";					
					break;
					
				case 4:
					//echo "Objet de type 4<br>";
					$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$iIdObjActuel);
					echo "$sSelectModif".$oQListeDeroul->cHtmlQListeDeroul()."\n";
					break;
					
				case 5:
					//echo "Objet de type 5<br>";
					$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
					echo "$sSelectModif".$oQRadio->cHtmlQRadio()."\n";
					break;
					
				case 6:
					//echo "Objet de type 6<br>";
					$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
					echo "$sSelectModif".$oQCocher->cHtmlQCocher()."\n";
					break;
					
				case 7:
					//echo "Objet de type 7<br>";
					$oMPTexte = new CMPTexte($oProjet->oBdd,$iIdObjActuel);
					echo "$sSelectModif".$oMPTexte->cHtmlMPTexte()."\n";
					break;
					
				case 8:
					//echo "Objet de type 8<br>";
					$oMPSeparateur = new CMPSeparateur($oProjet->oBdd,$iIdObjActuel);
					echo "$sSelectModif".$oMPSeparateur->cHtmlMPSeparateur()."\n";
					break;
					
				default:
					echo "Erreur: type d'objet de formulaire inconnu<br>";
			}
			
			echo "<div class=\"InterObj\"></div>\n";
			
		}
		echo "</FORM>\n";
	}
	else
	{
		echo "<body class=\"liste\">";
		echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" height=\"100%\">"
			."<tr><td align=\"center\">"
			."<img src=\"../../images/doc-plein.gif\" border=\"0\"><br>"
			//."e&nbsp;C&nbsp;O&nbsp;N&nbsp;C&nbsp;E&nbsp;P&nbsp;T<font size=\"1\"><sup>&copy;</sup></font><br>"
			."La conception de formulaires totalement en ligne"
			."</td></tr>"
			."<tr><td align=\"center\">Unit&eacute; de Technologie de l'&Eacute;ducation</td></tr>"
			."</table>";
	}
	
	echo "</body>\n";
	echo "</html>\n";
}//Verification de la permission d'utiliser le concepteur de formulaire
?>

