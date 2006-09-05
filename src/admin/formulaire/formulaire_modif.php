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
if ($oProjet->verifPermission('PERM_MOD_FORMULAIRES'))
{
	//************************************************
	//*       Récupération des variables             *
	//************************************************
	
	if (isset($_GET['idobj']))
	{
		$v_iIdObjForm = $_GET['idobj'];
		$v_iIdFormulaire = $_GET['idformulaire'];
	}
	else if (isset($_POST['idobj']))
	{
		$v_iIdObjForm = $_POST['idobj'];
		$v_iIdFormulaire = $_POST['idformulaire'];
	
	}
	else
	{
		$v_iIdObjForm = 0;
		$v_iIdFormulaire = 0;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="<?=dir_theme("formulaire/formulaire.css");?>">
<title>Modification des formulaires </title>
<script src="selectionobj.js" type="text/javascript">
</script>
<script type="text/javascript">
<!--
function soumettre(TypeAct,Parametre)
{
	document.forms['formmodif'].typeaction.value=TypeAct;
	
	if (TypeAct == 'supprimer')
		document.forms['formmodif'].parametre.value=Parametre;
	else
		document.forms['formmodif'].parametre.value="";
	
	document.forms['formmodif'].submit();
}
//-->
</script>
</head>
<body class="modif">
<?php
	if ($v_iIdObjForm > 0)
	{
		$oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd,$v_iIdObjForm);
		$iIdObjActuel = $oObjetFormulaire->retId();
		
		switch($oObjetFormulaire->retIdTypeObj())
		{
			case 1:
				$oQTexteLong = new CQTexteLong($oProjet->oBdd,$v_iIdObjForm);
				echo $oQTexteLong->cHtmlQTexteLongModif($v_iIdObjForm,$v_iIdFormulaire);
				break;
				
			case 2:
				$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$iIdObjActuel);
				echo $oQTexteCourt->cHtmlQTexteCourtModif($v_iIdObjForm,$v_iIdFormulaire);
				break;
				
			case 3:
				$oQNombre = new CQNombre($oProjet->oBdd,$iIdObjActuel);
				echo $oQNombre->cHtmlQNombreModif($v_iIdObjForm,$v_iIdFormulaire);
				break;
				
			case 4:
				$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$iIdObjActuel);
				echo $oQListeDeroul->cHtmlQListeDeroulModif($v_iIdObjForm,$v_iIdFormulaire);
				break;
				
			case 5:
				$oQRadio = new CQRadio($oProjet->oBdd,$iIdObjActuel);
				echo $oQRadio->cHtmlQRadioModif($v_iIdObjForm,$v_iIdFormulaire);
				break;
				
			case 6:
				$oQCocher = new CQCocher($oProjet->oBdd,$iIdObjActuel);
				echo $oQCocher->cHtmlQCocherModif($v_iIdObjForm,$v_iIdFormulaire);
				break;
				
			case 7:
				$oMPTexte = new CMPTexte($oProjet->oBdd,$iIdObjActuel);
				echo $oMPTexte->cHtmlMPTexteModif($v_iIdObjForm,$v_iIdFormulaire);
				break;
				
			case 8:
				$oMPSeparateur = new CMPSeparateur($oProjet->oBdd,$iIdObjActuel);
				echo $oMPSeparateur->cHtmlMPSeparateurModif($v_iIdObjForm,$v_iIdFormulaire);
				break;
				
			default:
				echo "Erreur: numéro d'objet d'activité en ligne incorrect.<br />";
		}
	}
	else 
	{
		if ($v_iIdFormulaire != 0 ) //Cas où on a cliqué sur le titre du formulaire
		{
			$oFormulaire = new CFormulaire($oProjet->oBdd,$v_iIdFormulaire);
			echo $oFormulaire->cHtmlFormulaireModif($v_iIdObjForm,$v_iIdFormulaire);
		}
		else	//Cas où aucune valeur n'a encore été envoyée (c-à-d chargement de la page)
		{
?>
			<div id="titrepagevierge">
			<img src="../../images/doc-vide.gif" alt="logo" />
			Générateur d'activités en ligne
			<span id="ute">Unit&eacute; de Technologie de l'&Eacute;ducation</span>
			</div>
<?php
		}
	}
} //Verification de la permission d'utiliser le concepteur de formulaire
$oProjet->terminer();
?>
</body>
</html>
