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

/*
** Fichier ................: nouv_form.php
** Description ............:
** Date de création .......: 03/06/2002
** Dernière modification ..: 06/06/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once("admin_globals.inc.php");
require_once(dir_include("dialog.class.php"));

$oProjet = new CProjet();

$oProjet->verifPeutUtiliserOutils("PERM_AJT_SESSION");

// ---------------------
// Déclarer les constantes locales
// ---------------------
define("NOUVELLE_FORMATION",0);
define("COPIER_FORMATION",1);

// ---------------------
// Récupération des valeurs des formulaires ou des l'urls
// ---------------------
$etape    = isset($_POST["ETAPE"]) ? $_POST["ETAPE"] : "1";
$filtre   = isset($_POST["FILTRE"]) ? $_POST["FILTRE"] : NULL;
$fonction = isset($_POST["FONCTION"]) ? $_POST["FONCTION"] : NULL;
$bInit    = isset($_POST["INIT"]) ? 0 : 1;

// Informations de la formation
$type     = isset($_POST["TYPE"]) ? $_POST["TYPE"] : NOUVELLE_FORMATION;
$iIdForm  = (empty($_POST["ID_FORM"]) ? (empty($_GET["ID_FORM"]) ? 0 : $_GET["ID_FORM"]) : $_POST["ID_FORM"]);

$url_iInscrSpontForm = isset($_POST["InscrSpontForm"]) ? $_POST["InscrSpontForm"] : 1;

$url_sNomForm = empty($_POST["formation_nom"])
	? (empty($_POST["NOM_FORM"])
		? INTITULE_FORMATION." sans nom"
		: $_POST["NOM_FORM"])
	: $_POST["formation_nom"];

$url_sDescrForm = empty($_POST["formation_description"])
	? (empty($_POST["DESCR_FORM"])
		? NULL
		: $_POST["DESCR_FORM"])
	: $_POST["formation_description"];

if ($etape < 3 && $type == COPIER_FORMATION)
{
	$url_sNomForm = $url_sDescrForm = NULL;
}
else
{
	$url_sNomForm   = mb_convert_encoding(stripslashes($url_sNomForm),"HTML-ENTITIES","UTF-8");
	$url_sDescrForm = mb_convert_encoding(stripslashes($url_sDescrForm),"HTML-ENTITIES","UTF-8");
}

// ---------------------
// Initialisations
// ---------------------
$bConfirmation = FALSE;

$MAX_PALLIER = ($type == NOUVELLE_FORMATION ? 3 : 4);

if (isset($fonction))
{
	switch ($fonction)
	{
		case "precedent":
			$etape--;
			if ($etape < 1)
				$etape = "1";
			break;
			
		case "suivant":
			$etape++;
			if ($etape > $MAX_PALLIER)
				$etape = $MAX_PALLIER;
			break;
			
		case "valider":
			$bConfirmation = TRUE;
			break;
	}
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Ajouter une nouvelle formation</title>
<?php inserer_feuille_style("dialog.css; ajouter_formation.css"); ?>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('globals.js.php')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('window.js')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('outils_admin.js')?>"></script>
<script type="text/javascript" language="javascript">
<!--

var g_iFiltre = 1;
var g_sTri = null;
var g_iSensTri = false;

function init(v_iLargeurFenetre,v_iHauteurFenetre,v_bInitFen,v_bCentrerFen)
{ // v2.0
	if (v_bInitFen)
	{
		if (v_iHauteurFenetre < 1)
			v_iHauteurFenetre = self.document.height+25;
		
		self.resizeTo(v_iLargeurFenetre,v_iHauteurFenetre);
		
		if (v_bCentrerFen)
		{
			var iCentrerHorizontalement = (screen.width-v_iLargeurFenetre)/2;
			var iCentrerVerticalement = (screen.height-v_iHauteurFenetre)/2;
			
			self.moveTo(iCentrerHorizontalement,iCentrerVerticalement);
		}
		
		self.focus();
	}
}

function changerType(v_sType)
{
	document.forms["FRM_GENERAL"].TYPE.value = v_sType;
}

function rechargerListe(v_oObject,v_sType)
{
	var sParamsUrl = "";
	
	switch(v_sType)
	{
		case "filtre":
			g_iFiltre = v_oObject.options[v_oObject.selectedIndex].value;
			sParamsUrl = '?FILTRE=' + g_iFiltre;
			break;
		
		case "trier_types":
			if (g_sTri != "trier_types")
			{
				g_sTri = "trier_types";
				g_iSensTri = false;
			}
			else
				g_iSensTri = !g_iSensTri;
				
			sParamsUrl = '?FILTRE=' + g_iFiltre + '&TRI=types&SENS_TRI=' + (g_iSensTri ? 'DESC' : 'ASC');
			break;
			
		case "trier_noms":
			if (g_sTri != "trier_noms")
			{
				g_sTri = "trier_noms";
				g_iSensTri = false;
			}
			else
				g_iSensTri = !g_iSensTri;
			sParamsUrl = '?FILTRE=' + g_iFiltre + '&TRI=noms&SENS_TRI=' + (g_iSensTri ? 'DESC' : 'ASC');
			break;
	}
	
	top.frames['main'].frames['IFRAME_LISTE'].location = 'nvfrm_lst.php' + sParamsUrl;
}

function afficherMenu()
{
	top.frames['menu'].location = "nouv_form_menu.php"
		+ "<?php echo ($bConfirmation ? "?fin=1" : "?etape={$etape}&etapes={$MAX_PALLIER}"); ?>";
}

function editeur_callback(v_sForm,v_sElem,v_sTexte) { document.forms[v_sForm].elements[v_sElem].value = v_sTexte; }
//-->
</script>
</head>
<body onload="afficherMenu()">
<form name="FRM_GENERAL" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td align="left" valign="top">
<?php
switch ($etape)
{
	case "4":
		include_once("nvfrm_pg4.inc.php");
		break;
		
	case "3":
		if ($type == COPIER_FORMATION)
			include_once("nvfrm_cp_pg3.php");
		else
			include_once("nvfrm_pg4.inc.php");
		break;
		
	case "2":
		if ($type == NOUVELLE_FORMATION)
			include_once("nvfrm_nv_pg3.php");
		else
			include_once("nvfrm_pg2.php");
		break;
		
	default:
		include_once("nvfrm_pg1.php");
}

?>
</td>
</tr>
</table>
<input type="hidden" name="ETAPE" value="<?=$etape?>">
<input type="hidden" name="FONCTION" value="">
<input type="hidden" name="INIT" value="0">

<input type="hidden" name="ID_FORM" value="<?=$iIdForm?>">
<input type="hidden" name="TYPE" value="<?=$type?>">
<input type="hidden" name="NOM_FORM" value="<?=$url_sNomForm?>">
<input type="hidden" name="DESCR_FORM" value="<?=$url_sDescrForm?>">

</form>
</body>
</html>
<?php $oProjet->terminer(); ?>
