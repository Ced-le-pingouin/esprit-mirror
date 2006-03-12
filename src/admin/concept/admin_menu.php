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
** Fichier ................: admin_menu.php
** Description ............: 
** Date de création .......: 01/02/2002
** Dernière modification ..: 21/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once("admin_globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Déclaration des constantes
// ---------------------
define("AJT_FORMATION",1);
define("SUP_FORMATION",2);
define("AJT_MODULE",3);
define("SUP_MODULE",4);
define("AJT_RUBRIQUE",5);
define("SUP_RUBRIQUE",6);
define("AJT_UNITE",7);
define("SUP_UNITE",8);
define("AJT_ACTIVITE",9);
define("SUP_ACTIVITE",10);
define("AJT_SOUS_ACTIVITE",11);
define("SUP_SOUS_ACTIVITE",12);

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$type = 0; $params = 0; $act = NULL;

if (!empty($HTTP_GET_VARS))
{
	$type = $HTTP_GET_VARS["type"];
	$params = $HTTP_GET_VARS["params"];
}
else if (!empty($HTTP_POST_VARS))
{
	$type = $HTTP_POST_VARS["type"];
	$params = $HTTP_POST_VARS["params"];
	$act = $HTTP_POST_VARS["act"];
}

$g_iFormation = $g_iModule = $g_iRubrique = $g_iUnite = $g_iActiv = $g_iSousActiv = 0;

if ($params)
	list($g_iFormation,$g_iModule,$g_iRubrique,$g_iUnite,$g_iActiv,$g_iSousActiv) = explode(":",$params);

$g_iIdPers = (isset($oProjet->oUtilisateur) && is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

// ---------------------
// Initialiser la formation courante
// ---------------------
if ($g_iFormation > 0)
	$oProjet->defFormationCourante($g_iFormation);

// Les titres des tooltips
$asIntitule = array(
		"",
		"Ajouter une nouvelle ".strtolower(INTITULE_FORMATION),
		"Supprimer cette ".strtolower(INTITULE_FORMATION),
		"Ajouter un nouveau ".strtolower(INTITULE_MODULE),
		"Supprimer ce ".strtolower(INTITULE_MODULE),
		"Ajouter une nouvelle ".strtolower(INTITULE_RUBRIQUE),
		"Supprimer cette ".strtolower(INTITULE_RUBRIQUE),
		"",
		"",
		"Ajouter un nouveau ".strtolower(INTITULE_ACTIV),
		"Supprimer ce ".strtolower(INTITULE_ACTIV),
		"Ajouter une nouvelle ".strtolower(INTITULE_SOUS_ACTIV),
		"Supprimer cette ".strtolower(INTITULE_SOUS_ACTIV),
	);

// ---------------------
// Ajouter, modifier et supprimer un élément
// ---------------------
if (isset ($act))
{
	if (is_object($oProjet->oFormationCourante))
		$oProjet->oFormationCourante->verrouillerTables();
	
	switch ($act)
	{
		case AJT_FORMATION:
			break;
			
		case SUP_FORMATION:
			effacer_formation();
			break;
			
		case AJT_MODULE:
			ajouter_module();
			break;
			
		case SUP_MODULE:
			effacer_module();
			break;
			
		case AJT_RUBRIQUE:
			ajouter_rubrique();
			break;
			
		case SUP_RUBRIQUE:
			effacer_rubrique();
			break;
			
		case AJT_ACTIVITE:
			ajouter_activite();
			break;
			
		case SUP_ACTIVITE:
			effacer_activite();
			break;
			
		case AJT_SOUS_ACTIVITE:
			ajouter_sous_activite();
			break;
			
		case SUP_SOUS_ACTIVITE:
			effacer_sous_activite();
			break;
	}
	
	$oProjet->oBdd->deverrouillerTables();
}

// ---------------------
//
// ---------------------
$asNoms = array("...","...","...","...","...");

// {{{ Formation
if ($g_iFormation > 0)
{
	$type = TYPE_FORMATION;
	$asNoms[0] = $oProjet->oFormationCourante->retNom();
}
// }}}

// ---------------------
// Module
// ---------------------
if ($g_iModule > 0)
{
	$oProjet->defModuleCourant($g_iModule,TRUE);
	
	$type = TYPE_MODULE;
	$asNoms[1] = $oProjet->oModuleCourant->retNom();
}

// ---------------------
// Rubrique
// ---------------------
if ($g_iRubrique > 0)
{
	$oProjet->defRubriqueCourante($g_iRubrique);
	
	$type = (($oProjet->oRubriqueCourante->retType() == LIEN_UNITE ) ? TYPE_UNITE : TYPE_RUBRIQUE);
	$asNoms[2] = $oProjet->oRubriqueCourante->retNom();
}

// ---------------------
// Activité
// ---------------------
if ($g_iActiv > 0)
{
	$oProjet->defActivCourante($g_iActiv);
	
	$type      = TYPE_ACTIVITE;
	$asNoms[3] = $oProjet->oActivCourante->retNom();
}

// ---------------------
// Sous-activité
// ---------------------
if ($g_iSousActiv > 0)
{
	$oProjet->defSousActivCourante($g_iSousActiv);
	
	$type = TYPE_SOUS_ACTIVITE;
	$asNoms[4] = $oProjet->oSousActivCourante->retNom();
}

// include_once("econcept.liste.forms.php");

// ---------------------
// Permissions
// ---------------------
$bPeutModifierForm = $oProjet->verifModifierFormation();

// Formation
$bPeutAjouterForm = $oProjet->verifPermission("PERM_AJT_SESSION");

$bPeutSupprimerForm  = $oProjet->verifPermission("PERM_SUP_TOUTES_SESSIONS");
$bPeutSupprimerForm |= $oProjet->verifPermission("PERM_SUP_SESSION");
$bPeutSupprimerForm &= ($g_iModule == 0 && $bPeutModifierForm);

// {{{ Module
$bPeutModifierMod = $bPeutModifierForm & $oProjet->verifModifierModule();
$bPeutAjouterMod  = $oProjet->verifPermission("PERM_AJT_COURS");
$bPeutAjouterMod &= $bPeutModifierMod;

$bPeutSupprimerMod  = $oProjet->verifPermission("PERM_SUP_TOUS_COURS");
$bPeutSupprimerMod |= $oProjet->verifPermission("PERM_SUP_COURS");
$bPeutSupprimerMod &= ($g_iRubrique == 0 && $bPeutModifierMod);
// }}}

// Rubrique
$bPeutAjouterRub  = $oProjet->verifPermission("PERM_AJT_RUBRIQUE");
$bPeutAjouterRub &= $bPeutModifierMod;

$bPeutSupprimerRub  = $oProjet->verifPermission("PERM_SUP_RUBRIQUE");
$bPeutSupprimerRub &= ($g_iActiv == 0 && $bPeutModifierMod);

// Activité
$bPeutAjouterActiv  = $oProjet->verifPermission("PERM_AJT_BLOC");
$bPeutAjouterActiv &= $bPeutModifierMod;

$bPeutSupprimerActiv  = $oProjet->verifPermission("PERM_SUP_BLOC");
$bPeutSupprimerActiv &= ($g_iSousActiv == 0 && $bPeutModifierMod);

// Sous-activité
$bPeutAjouterSousActiv  = $oProjet->verifPermission("PERM_AJT_ELEMENT_ACTIF");
$bPeutAjouterSousActiv &= $bPeutModifierMod;

$bPeutSupprimerSousActiv  = $oProjet->verifPermission("PERM_SUP_ELEMENT_ACTIF");
$bPeutSupprimerSousActiv &= $bPeutModifierMod;

// ---------------------
// Mettre à jour
// ---------------------
$params = implode(":",array($g_iFormation,$g_iModule,$g_iRubrique,0,$g_iActiv,$g_iSousActiv));

if (isset($act) && $act != AJT_FORMATION)
{
	// Mettre à jour
	echo "<html><head>\n"
		."<script type=\"text/javascript\" language=\"javascript\">\n"
		."<!--\n\n"
		."function reafficher()\n"
		."{\ntop.frames['ADMINFRAMELISTE'].location.replace(\"admin_liste.php?type={$type}&params={$params}\");\n}\n"
		."{\ntop.frames['ADMINFRAMELISTE'].location.reload();\n}\n"
		."\n//-->\n"
		."</script>"
		."</head><body onload=\"reafficher()\"></body></html>";
	
	$oProjet->terminer();
	
	exit();
}

// *************************************
// Déclaration des fonctions locales
// *************************************

function dessinerMenu ($v_asMenu,$v_sStyleExt=NULL)
{
	if (empty($v_asMenu))
		return FALSE;
	
	// *************************************
	// Afficher le titre du menu
	// *************************************
	
	echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" width=\"190px\" align=\"center\">\n"
		."<tr><td class=\"concept_menu_titre_principal{$v_sStyleExt}\"><b>".$v_asMenu[0][0]."</b></td></tr>\n";
	
	// *************************************
	// Afficher les articles du menu
	// *************************************
	
	echo "<tr>\n"
		."<td class=\"concept_menu_fond{$v_sStyleExt}\">\n"
		."<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\">\n"
		."<tr"
		.(isset($v_asMenu[0][2]) ? " style=\"cursor: pointer;\" onclick=\"top.frames['ADMINFRAMELISTE'].location.replace('admin_liste.php{$v_asMenu[0][2]}');\"" : NULL)
		.">"
		."<td class=\"concept_menu_sous_titre{$v_sStyleExt}\">"
		.$v_asMenu[0][1]
		."</td>"
		."</tr>\n";
	
	echo "<tr><td class=\"concept_menu{$v_sStyleExt}\">";
	
	for ($i=1; $i<count ($v_asMenu); $i++)
		if ($v_asMenu[$i][3] != NULL)
			echo (($i>1 && !empty($v_asMenu[$i-1][3])) ? "&nbsp;|&nbsp;" : NULL)
				."<a href=\"javascript: Envoyer('".$v_asMenu[$i][1]."'); void(0);\""
				." title=\"".$v_asMenu[$i][2]."\""
				.">".$v_asMenu[$i][0]."</a>";
		/*else
			echo (($i>1) ? "&nbsp;|&nbsp;" : NULL)
				."<span class=\"lien_passif\">".$v_asMenu[$i][0]."</span>";*/
		
	echo "</td>"
		."</tr>\n"
		."</table>\n"
		."</td>\n"
		."</tr>\n"
		."</table><br>\n";
	
	return TRUE;
}

// ---------------------
// Composer les menus
// ---------------------
$aoElementMenu = array();

// Bloc Formation
// --------------
$sTmpParams = ($g_iFormation > 0 ? "?type=".TYPE_FORMATION."&params={$g_iFormation}:0:0:0:0:0" : NULL);
$aoElementMenu[0][0] = array("Niv.&nbsp;1 (".INTITULE_FORMATION.")",$asNoms[0],$sTmpParams);

// Ajouter une formation
$aoElementMenu[0][] = array(
	"Ajouter"
	,AJT_FORMATION
	,$asIntitule[AJT_FORMATION]
	,$bPeutAjouterForm
);
	
if ($g_iFormation > 0)
{
	// Supprimer une formation
	$aoElementMenu[0][] = array(
		"Supprimer"
		,SUP_FORMATION
		,$asIntitule[SUP_FORMATION]
		,$bPeutSupprimerForm
	);
	
	// Bloc module
	// -----------
	$sTmpParams = ($g_iModule > 0 ? "?type=".TYPE_MODULE."&params={$g_iFormation}:{$g_iModule}:0:0:0:0" : NULL);
	$aoElementMenu[1][0] = array("Niv.&nbsp;2 (".INTITULE_MODULE.")",$asNoms[1],$sTmpParams);
	
	// Ajouter un module
	$aoElementMenu[1][] = array(
		"Ajouter"
		,AJT_MODULE
		,$asIntitule[AJT_MODULE]
		,$bPeutAjouterMod
	);
}

if ($g_iModule > 0)
{
	// Supprimer un module
	$aoElementMenu[1][] = array(
		"Supprimer"
		,SUP_MODULE
		,$asIntitule[SUP_MODULE]
		,$bPeutSupprimerMod
	);
	
	// Bloc rubrique/unité
	// -------------------
	$sTmpParams = ($g_iRubrique > 0 ? "?type=".TYPE_RUBRIQUE."&params={$g_iFormation}:{$g_iModule}:{$g_iRubrique}:0:0:0" : NULL);
	$aoElementMenu[2][0] = array("Niv.&nbsp;3 (".INTITULE_RUBRIQUE.")",$asNoms[2],$sTmpParams);
	
	// Ajouter une rubrique/unité
	$aoElementMenu[2][] = array(
		"Ajouter"
		,AJT_RUBRIQUE
		,$asIntitule[AJT_RUBRIQUE]
		,$bPeutAjouterRub
	);
}

// Supprimer une rubrique/unité
if (isset($type) && $type >= TYPE_RUBRIQUE)
	$aoElementMenu[2][] = array(
		"Supprimer"
		,SUP_RUBRIQUE
		,$asIntitule[SUP_RUBRIQUE]
		,$bPeutSupprimerRub
	);

// ---------------------
// Seules les rubriques de type "Unité"
// peuvent ajouter/modifier/supprimer une activité
// ou une sous-activité
// ---------------------
if (isset($oProjet->oRubriqueCourante) &&
	$oProjet->oRubriqueCourante->retType() == LIEN_UNITE)
{
	// Bloc activité
	// -------------
	$sTmpParams = ($g_iActiv > 0 ? "?type=".TYPE_ACTIVITE."&params={$g_iFormation}:{$g_iModule}:{$g_iRubrique}:0:{$g_iActiv}:0" : NULL);
	$aoElementMenu[4][0] = array("Niv.&nbsp;4 (".INTITULE_ACTIV.")",$asNoms[3],$sTmpParams);
	
	// Ajouter une activité
	$aoElementMenu[4][] = array(
		"Ajouter"
		,AJT_ACTIVITE
		,$asIntitule[AJT_ACTIVITE]
		,$bPeutAjouterActiv
	);

	if ($g_iActiv > 0)
	{
		// Supprimer une activité
		$aoElementMenu[4][] = array(
			"Supprimer"
			,SUP_ACTIVITE
			,$asIntitule[SUP_ACTIVITE]
			,$bPeutSupprimerActiv
		);
		
		// Bloc sous-activité
		// ------------------
		$sTmpParams = ($g_iSousActiv > 0 ? "?type=".TYPE_SOUS_ACTIVITE."&params={$g_iFormation}:{$g_iModule}:{$g_iRubrique}:0:{$g_iActiv}:{$g_iSousActiv}" : NULL);
		$aoElementMenu[5][0] = array("Niv.&nbsp;5 (".INTITULE_SOUS_ACTIV.")",$asNoms[4],$sTmpParams);
		
		// Ajouter sous-activité
		$aoElementMenu[5][] = array(
			"Ajouter"
			,AJT_SOUS_ACTIVITE
			,$asIntitule[AJT_SOUS_ACTIVITE]
			,$bPeutAjouterSousActiv
		);
		
		// Supprimer sous-activité
		if (isset ($g_iSousActiv) && $g_iSousActiv>0)
			$aoElementMenu[5][] = array(
				"Supprimer"
				,SUP_SOUS_ACTIVITE
				,$asIntitule[SUP_SOUS_ACTIVITE]
				,$bPeutSupprimerSousActiv
			);
	}
}

?>
<html>
<head>
<?php inserer_feuille_style("dialog.css; concept.css"); ?>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('window.js')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_admin('concept','admin_modif.js')?>"></script>
<script type="text/javascript" language="javascript">
<!--

var asElement = new Array (
	""
	,""
	,"cette <?=strtolower(INTITULE_FORMATION)?>"
	,""
	,"ce <?=strtolower(INTITULE_MODULE)?>"
	,""
	,"cette <?=strtolower(INTITULE_RUBRIQUE)?>"
	,""
	,""
	,""
	,"ce <?=strtolower(INTITULE_ACTIV)?>"
	,""
	,"cette <?=strtolower(INTITULE_SOUS_ACTIV)?>");

function Envoyer(v_iNum)
{
	if (v_iNum == '<?php echo AJT_FORMATION; ?>')
	{
		ajouterFormation('<?=$g_iFormation?>');
		return;
	}
	
	var bEnvoyer = true;
	var bEffacer = false;
	
	switch(v_iNum)
	{
		case '<?php echo SUP_FORMATION; ?>' :
		case '<?php echo SUP_MODULE; ?>' :
		case '<?php echo SUP_RUBRIQUE; ?>' :
		case '<?php echo SUP_UNITE; ?>' :
		case '<?php echo SUP_ACTIVITE; ?>' :
		case '<?php echo SUP_SOUS_ACTIVITE; ?>' :
			
			bEffacer = true;
			break;		
	}
	
	if (bEffacer)
		bEnvoyer = confirm("Etes-vous certain de vouloir supprimer " + asElement[parseInt(v_iNum)] + " ?");
	
	if (bEnvoyer)
	{
		document.forms["menuForm"].act.value = v_iNum;
		document.forms["menuForm"].submit();
	}
}

function ChangerFormation()
{
	top.oListe().location = "admin_liste.php" + document.forms[0].elements["intitule_rubrique"].value;
}

//-->
</script>
<style type="text/css">
<!--
html { border: rgb(240,240,240) none 1px; border-right-style: solid; }
-->
</style>
</head>
<body class="gauche">
<form name="menuForm" action="<?php echo $HTTP_SERVER_VARS['PHP_SELF']; ?>" method="post">
<table border="0" cellpadding="0" celspacing="1" width="100%">
<tr><td align="center"><?=$sSelectFormations?></td></tr>
<tr><td><img src="<?=dir_theme_commun('espacer.gif')?>" width="1" height="1" border="0"></td></tr>
<tr>
<td>
<?php

// *************************************
// Afficher les différents menu
// *************************************

for ($i=0; $i<=$type; $i++)
{
	if (empty($aoElementMenu[$i]))
		continue;
	
	$iTmpType = ($type == TYPE_UNITE ? $type-2 : $type-1);
	dessinerMenu($aoElementMenu[$i],($i == $iTmpType ? NULL : "_2"));
}

unset($asNoms,$aoElementMenu);

?>
</td></tr></table>
<input type="hidden" name="act" value="">
<input type="hidden" name="type" value="<?php echo $type; ?>">
<input type="hidden" name="params" value="<?php echo $params; ?>">
</form>
</body>
</html>
<?php $oProjet->Terminer(); ?>
