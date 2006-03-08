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
** Fichier ................: equipes-formation.php
** Description ............: 
** Date de création .......: 01/01/2003
** Dernière modification ..: 16/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

$bInscritAuto = $oProjet->oFormationCourante->retInscrAutoModules();
$iNbrModules = $oProjet->oFormationCourante->initModules();

if (isset($HTTP_POST_VARS["APPLIQUER"]))
{
	if (is_array($oProjet->oFormationCourante->aoModules))
	{
		settype($HTTP_POST_VARS["NIVEAU_2"],"array");
		
		$poModules = &$oProjet->oFormationCourante->aoModules;
		
		for ($iIdxModule=0; $iIdxModule<count($poModules); $iIdxModule++)
		{
			$poModules[$iIdxModule]->initRubriques(LIEN_UNITE);
			
			$poRubriques = &$poModules[$iIdxModule]->aoRubriques;
			
			for ($iIdxRubrique=0; $iIdxRubrique<count($poRubriques); $iIdxRubrique++)
				if (in_array($poRubriques[$iIdxRubrique]->retId(),$HTTP_POST_VARS["NIVEAU_2"]))
					$poRubriques[$iIdxRubrique]->ajouterEquipes();
				else
					$poRubriques[$iIdxRubrique]->effacerEquipes();
		}
	}
}

// *************************************
//
// *************************************

$sSousTitre = rawurlencode($oProjet->oFormationCourante->retNom());

if ($bInscritAuto)
{
	$iNiveau = TYPE_FORMATION;
}
else if ($iNbrModules > 0)
{
	$sSousTitre = rawurlencode($oProjet->oFormationCourante->aoModules[0]->retNom());
	$iNiveau = TYPE_MODULE;
}

$oTpl = new Template(dir_theme("equipes-formation.tpl",FALSE,TRUE));

// ---------------------
// Formation
// ---------------------
$oBlock_Formation_Ouvert = new TPL_Block("BLOCK_FORMATION_OUVERT",$oTpl);
$oBlock_Formation_Fermer = new TPL_Block("BLOCK_FORMATION_FERMER",$oTpl);

$sNom = $oProjet->oFormationCourante->retNom();

if ($iNiveau == TYPE_FORMATION)
{

	$oBlock_Formation_Ouvert->remplacer("{nom_formation}",htmlentities($sNom));
	$oBlock_Formation_Ouvert->remplacer("{nom_formation_encoder}",rawurlencode($sNom));
	$oBlock_Formation_Ouvert->remplacer("{id_formation}",$oProjet->oFormationCourante->retId());
	$oBlock_Formation_Ouvert->remplacer("{type_formation}",TYPE_FORMATION);
	
	$oBlock_Formation_Ouvert->afficher();
	$oBlock_Formation_Fermer->effacer();
}
else
{
	$oBlock_Formation_Fermer->remplacer("{nom_formation}",htmlentities($sNom));
	$oBlock_Formation_Fermer->remplacer("{nom_formation_encoder}",rawurlencode($sNom));
	
	$oBlock_Formation_Ouvert->effacer();
	$oBlock_Formation_Fermer->afficher();
}

$sCocherVide = dir_theme("cocher-vide-0.gif");

// ---------------------
//Modules
// ---------------------
$oBlock_Module = new TPL_Block("BLOCK_MODULE",$oTpl);

$oBlock_Module->beginLoop();

for ($iIdxModule=0; $iIdxModule<$iNbrModules; $iIdxModule++)
{
	$oBlock_Module->nextLoop();
	
	$poModule = &$oProjet->oFormationCourante->aoModules[$iIdxModule];
	
	$sNom = $poModule->retNom();
	
	$oBlock_Module->remplacer("{ordre_module}",($iIdxModule+1));
	$oBlock_Module->remplacer("{id_module}",$poModule->retId());
	$oBlock_Module->remplacer("{type_module}",TYPE_MODULE);
	$oBlock_Module->remplacer("{nom_module}",htmlentities($sNom));
	$oBlock_Module->remplacer("{nom_module_encoder}",rawurlencode($sNom));
	
	if ($iIdxModule == 0 && $iNiveau == TYPE_MODULE)
		$oBlock_Module->remplacer("{select_module}"," checked");
	else
		$oBlock_Module->remplacer("{select_module}",NULL);
	
	// ---------------------
	// Rubriques
	// ---------------------
	$iNbrRubriques = $poModule->initRubriques();
	
	$iCompteurRubrique = 1;
	
	$oBlock_Unite = new TPL_Block("BLOCK_UNITE",$oBlock_Module);
	
	$oBlock_Unite->beginLoop();
	
	for ($iIdxRubrique=0; $iIdxRubrique<$iNbrRubriques; $iIdxRubrique++)
	{
		$poRubrique = &$poModule->aoRubriques[$iIdxRubrique];
		
		if ($poRubrique->retType() == LIEN_UNITE)
		{
			$oBlock_Unite->nextLoop();
			
			//$bChecked = ($poRubrique->retNbrEquipes() > 0 ? " checked" : NULL);
			
			$sNom = $poRubrique->retNom();
			
			$oBlock_Unite->remplacer("{ordre_unite}",$iCompteurRubrique++);
			$oBlock_Unite->remplacer("{id_unite}",$poRubrique->retId());
			$oBlock_Unite->remplacer("{type_unite}",TYPE_RUBRIQUE);
			$oBlock_Unite->remplacer("{nom_unite}",htmlentities($sNom));
			$oBlock_Unite->remplacer("{nom_unite_encoder}",rawurlencode($sNom));
		}
	}
	
	$oBlock_Unite->afficher();
}

$oBlock_Module->afficher();

$oProjet->terminer();

?>
<html>
<head>
<?php inserer_feuille_style(); ?>
<script type="text/javascript" language="javascript">
<!--

var sAncienParentNode = null;
var sAncienEnfantNode = null;

var asSousTitre = new Array(
	null,
	"&Eacute;quipes de la formation",
	"&Eacute;quipes du cours",
	"&Eacute;quipes de l'unit&eacute;");

function init()
{
	changerSousTitre(<?="'{$iNiveau}','{$sSousTitre}'"?>);
}

function StatusBar(v_oObj,v_sTexteStatusBar)
{
	var sTexteStatusBar = (typeof(v_oObj) == "object" && v_oObj != null ? v_oObj.innerHTML + '&nbsp;:&nbsp;' : "")
		+ v_sTexteStatusBar;
	
	top.oMenu().StatusBar(sTexteStatusBar);
}

function changerSousTitre(v_iNiveau,v_sSousTitre)
{
	if (top.oTitre && top.oTitre().changerSousTitre)
	{
		sSousTitre = asSousTitre[parseInt(v_iNiveau)]
			+ "&nbsp;&raquo;&nbsp;"
			+ unescape(v_sSousTitre);
		
		top.changerSousTitre(escape(sSousTitre));
	}
	else
		setTimeout("changerSousTitre('" + v_iNiveau + "','" + v_sSousTitre + "')",1000);
}

function changerComposition(v_iNiveau,v_iIdNiveau,v_sSousTitre)
{
	changerSousTitre(v_iNiveau,v_sSousTitre);
	
	top.oEquipes().defNiveau(v_iNiveau,v_iIdNiveau);
	top.oEquipes().envoyer();
}

function designerSonParent(v_iParentNiveau,v_iParentIdNiveau,v_iEnfantNiveau,v_iEnfantIdNiveau)
{
	var sParentNode = sEnfantNode = null;
	
	switch (v_iParentNiveau)
	{
		case "<?=TYPE_FORMATION?>":
			sParentNode = "id_form"
			break;
			
		case "<?=TYPE_MODULE?>":
			sParentNode = "id_mod_" + v_iParentIdNiveau;
			break;
			
		case "<?=TYPE_RUBRIQUE?>":
			sParentNode = "id_rubrique_" + v_iParentIdNiveau;
			break;
	}
	
	switch (v_iEnfantNiveau)
	{
		case "<?=TYPE_MODULE?>":
			sEnfantNode = "id_mod_" + v_iEnfantIdNiveau;
			break;
			
		case "<?=TYPE_RUBRIQUE?>":
			sEnfantNode = "id_rubrique_" + v_iEnfantIdNiveau;
			break;
	}
	
	if (sAncienEnfantNode != null && document.getElementById(sAncienEnfantNode))
		document.getElementById(sAncienEnfantNode).childNodes[0].src = "<?=dir_theme('blank.gif')?>";
		
	if (sAncienParentNode != sParentNode)
	{
		if (document.getElementById(sAncienParentNode))
			document.getElementById(sAncienParentNode).childNodes[0].src = "<?=dir_theme('blank.gif')?>";
		
		if (sParentNode != null && document.getElementById(sParentNode))
			document.getElementById(sParentNode).childNodes[0].src = "<?=dir_theme('fleche-parent.gif')?>";
		
		sAncienParentNode = sParentNode;
	}
	
	if (sEnfantNode != null &&
		sEnfantNode != sParentNode &&
		typeof(document.getElementById(sEnfantNode)) == "object")
	{
		document.getElementById(sEnfantNode).childNodes[0].src = "<?=dir_theme('fleche-enfant.gif')?>";
		sAncienEnfantNode = sEnfantNode;
	}
}

function envoyer()
{
	document.forms[0].submit();
}

//-->
</script>
<style type="text/css">
<!--
body { background-image: none; }
-->
</style>
</head>
<body class="gauche" onload="init()">
<form action="<?=$HTTP_SERVER_VARS['PHP_SELF']?>" method="post">
<?php $oTpl->afficher(); ?>
<input type="hidden" name="APPLIQUER" value="1">
</form>
</body>
</html>
