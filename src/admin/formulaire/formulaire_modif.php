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
		$v_iIdObjForm = $HTTP_POST_VARS['idobj'];
		$v_iIdFormulaire = $HTTP_POST_VARS['idformulaire'];
	
	}
	else
	{
		$v_iIdObjForm = -1;
		$v_iIdFormulaire = -1;
	}
?>
	
<html>
	
<head>
<link type="text/css" rel="stylesheet" href="<?=dir_theme("formulaire/formulaire.css");?>">

<script src="selectionobj.js" type="text/javascript">
</script>

<script type="text/javascript">
<!--

// tableau qui contiendra les objets SELECT permettant de modifier l'ordre
// des propositions dans les questions de type Liste/Radios/Cases
var g_aoSelects = new Array();

// initialiser le tableau de SELECT en fonction du nom
function init()
{
	var i, j;
	var sNomSelectOrdre = 'selOrdreProposition';
	var iTailleNomSelectOrdre = sNomSelectOrdre.length;
	
	aoSelects = document.getElementsByTagName('select');
	for (i = 0, j = 0; i < aoSelects.length; i++)
	{
		if (aoSelects[i].name.substr(0, iTailleNomSelectOrdre) == sNomSelectOrdre)
		{
			g_aoSelects[j] = aoSelects[i];
			g_aoSelects[j].onchange = changerOrdreProposition;
			g_aoSelects[j].selectedIndexCopy = g_aoSelects[j].selectedIndex;
			j++;
		}
	}
}

// quand l'ordre d'une proposition est modifié, il faut s'assurer que la nouvelle 
// valeur n'existe pas pour une autre proposition (auquel cas on lui attribue l'ancienne 
// (valeur de la position qui vient d'être modifiée)
function changerOrdreProposition()
{
	var i;
	
	for (i = 0; i < g_aoSelects.length; i++)
	{
		if (g_aoSelects[i] != this && g_aoSelects[i].selectedIndex == this.selectedIndex)
		{
			g_aoSelects[i].selectedIndex = this.selectedIndexCopy;
			g_aoSelects[i].selectedIndexCopy = this.selectedIndexCopy;
			break;
		}
	}
	
	this.selectedIndexCopy = this.selectedIndex;
}


function soumettre(TypeAct,Parametre)
{
	document.forms['formmodif'].typeaction.value=TypeAct;
	
	if (TypeAct == 'supprimer')
		document.forms['formmodif'].parametre.value=Parametre;
	else
		document.forms['formmodif'].parametre.value="";
	
	document.forms['formmodif'].submit();
}

function afficherAxes(v_iIdProposition, v_iAffichage)
{
	sIdDiv = 'divPanneauAxes' + v_iIdProposition;
	oObj = document.getElementById(sIdDiv);
	
	if (v_iAffichage == 1)
		oObj.style.display = "block";
	else if (v_iAffichage == 0)
		oObj.style.display = "none";
	else if (v_iAffichage == -1)
	{
		if (oObj.style.display == "block")
			oObj.style.display = "none"
		else if (oObj.style.display == "none")
			oObj.style.display = "block"
	}
}
//-->
</script>

</head>

<body class="modif" onLoad="init();">
	
<?php
	//echo "Idobj = ".$v_iIdObjForm;
	//echo "<br>IdFormulaire =".$v_iIdFormulaire;
	
	if ($v_iIdObjForm > 0)
	{
		$oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd,$v_iIdObjForm);
		$iIdObjActuel = $oObjetFormulaire->retId();
		
		switch($oObjetFormulaire->retIdTypeObj())
		{
			case 1:
				//echo "Objet de type 1<br>";
				$oQTexteLong = new CQTexteLong($oProjet->oBdd,$v_iIdObjForm);
				echo $oQTexteLong->cHtmlQTexteLongModif($v_iIdObjForm,$v_iIdFormulaire);
				
				break;
				
			case 2:
				//echo "Objet de type 2<br>";
				$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$iIdObjActuel);
				echo $oQTexteCourt->cHtmlQTexteCourtModif($v_iIdObjForm,$v_iIdFormulaire);
				
				break;
				
			case 3:
				//echo "Objet de type 3<br>";
				$oQNombre = new CQNombre($oProjet->oBdd,$iIdObjActuel);
				echo $oQNombre->cHtmlQNombreModif($v_iIdObjForm,$v_iIdFormulaire);
				
				break;
				
			case 4:
				//echo "Objet de type 4<br>";
				$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$iIdObjActuel);
				echo $oQListeDeroul->cHtmlQListeDeroulModif($v_iIdObjForm,$v_iIdFormulaire);
				
				break;
				
			case 5:
				//echo "Objet de type 5<br>";
				$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
				echo $oQRadio->cHtmlQRadioModif($v_iIdObjForm,$v_iIdFormulaire);
				
				break;
				
			case 6:
				//echo "Objet de type 6<br>";
				$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
				echo $oQCocher->cHtmlQCocherModif($v_iIdObjForm,$v_iIdFormulaire);
				
				break;
				
			case 7:
				//echo "Objet de type 7<br>";
				$oMPTexte = new CMPTexte($oProjet->oBdd,$iIdObjActuel);
				echo $oMPTexte->cHtmlMPTexteModif($v_iIdObjForm,$v_iIdFormulaire);
				
				break;
				
			case 8:
				//echo "Objet de type 8<br>";
				$oMPSeparateur = new CMPSeparateur($oProjet->oBdd,$iIdObjActuel);
				echo $oMPSeparateur->cHtmlMPSeparateurModif($v_iIdObjForm,$v_iIdFormulaire);
				
				break;
				
			default:
				echo "Erreur: numéro d'objet d'activité en ligne incorrect.<br>";
		}
	}
	else if ($v_iIdFormulaire != 0 ) //Cas où on a cliqué sur le titre du formulaire
	{
		$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
		echo $oFormulaire->cHtmlFormulaireModif($v_iIdObjForm,$v_iIdFormulaire);
	}
	else	//Cas où aucune valeur n'a encore été envoyée (c-à-d chargement de la page)
	{
		echo "<table border=\"10\" cellspacing=\"10\" cellpadding=\"10\" width=\"100%\" height=\"100%\">"
			."<tr><td align=\"center\">"
			."<img src=\"../../images/doc-vide.gif\" border=\"0\"><br>"
			//."e&nbsp;C&nbsp;O&nbsp;N&nbsp;C&nbsp;E&nbsp;P&nbsp;T<font size=\"1\"><sup>&copy;</sup></font><br>"
			."Générateur d'activités en ligne"
			."</td></tr>"
			."<tr><td align=\"center\">Unit&eacute; de Technologie de l'&Eacute;ducation</td></tr>"
			."</table>";
	}
	echo "\n</body>\n";
	echo "</html>";
} //Verification de la permission d'utiliser le concepteur de formulaire
?>

