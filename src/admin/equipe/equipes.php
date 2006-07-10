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
** Fichier ................: equipes.php
** Description ............: 
** Date de création .......: Décembre 2002
** Dernière modification ..: 23/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// *************************************
//
// *************************************

if (is_object($oProjet->oFormationCourante))
	$bAutoInscrit = $oProjet->oFormationCourante->retInscrAutoModules();
else
	$bAutoInscrit = FALSE;

if ($bAutoInscrit)
{
	$iTypeNiveauFin = TYPE_FORMATION;
	$iId = $oProjet->oFormationCourante->retId();
}
else
{
	$iNbModules = $oProjet->oFormationCourante->initModules();
	
	$iTypeNiveauFin = TYPE_MODULE;
	$iId = ($iNbModules > 0 ? $oProjet->oFormationCourante->aoModules[0]->retId() : 0);
}

$url_iNiveau   = (empty($_POST["NIVEAU"]) ? $iTypeNiveauFin : $_POST["NIVEAU"]);
$url_iIdNiveau = (empty($_POST["ID_NIVEAU"]) ? $iId : $_POST["ID_NIVEAU"]);

$url_iIdEquipe = (empty($_POST["ID_EQUIPE"]) ? 0 : $_POST["ID_EQUIPE"]);
$url_iFiltre   = (empty($_POST["FILTRE_PERSONNES"]) ? PERSONNE_SANS_EQUIPE : $_POST["FILTRE_PERSONNES"]);

// *************************************
// Boite de sélection contenant toutes les équipes de la formation
// *************************************

$sSelectEquipes = "<select name=\"ID_EQUIPE\" onchange=\"afficherMembres(value)\">\n";

include_once(dir_database("ids.class.php"));

$oEquipe = new CEquipe($oProjet->oBdd);

$oIds = new CIds($oProjet->oBdd,$url_iNiveau,$url_iIdNiveau);

for ($iIdxNiveau=$url_iNiveau; $iIdxNiveau>=$iTypeNiveauFin; $iIdxNiveau--)
{
	$iNiveau = $iIdxNiveau;
	
	switch ($iIdxNiveau)
	{
		case TYPE_RUBRIQUE:
			$iIdNiveau = $oIds->retIdRubrique();
			$iNbEquipes = $oEquipe->initEquipes(NULL,NULL,$iIdNiveau,0);
			break;
			
		case TYPE_MODULE:
			$iIdNiveau = $oIds->retIdMod();
			$iNbEquipes = $oEquipe->initEquipes(NULL,$iIdNiveau,0);
			break;
			
		default:
			$iIdNiveau = $oIds->retIdForm();
			$iNbEquipes = $oEquipe->initEquipes($iIdNiveau,0);
	}
	
	// Remplir le combobox avec les noms des équipes
	for ($iIdxEquipe=0; $iIdxEquipe<$iNbEquipes; $iIdxEquipe++)
	{
		if ($url_iIdEquipe == 0)
			$url_iIdEquipe = $oEquipe->aoEquipes[$iIdxEquipe]->retId();
		
		$sNomEquipe = $oEquipe->aoEquipes[$iIdxEquipe]->retNom();
		
		if (strlen($sNomEquipe) > 35)
			$sNomEquipeCourt = substr($sNomEquipe,0,30)."...";
		else
			$sNomEquipeCourt = $sNomEquipe;
		
		$sSelectEquipes .= "<option"
			." value=\"".$oEquipe->aoEquipes[$iIdxEquipe]->retId()."\""
			.($url_iIdEquipe == $oEquipe->aoEquipes[$iIdxEquipe]->retId() ? " selected" : NULL)
			.">{$sNomEquipeCourt}</option>";
	}
	
	if ($iNbEquipes>0 || $iIdxNiveau <= 0)
		break;
}

if ($iIdxEquipe <= 0)
	$sSelectEquipes .= "<option value=\"0\">Aucune &eacute;quipe</option>\n";
else if ($iNiveau <> $url_iNiveau)
	$url_iIdEquipe = $oEquipe->aoEquipes[0]->retId();
else if ($url_iIdEquipe <= 0)
	$url_iIdEquipe = $oEquipe->aoEquipes[0]->retId();

$sSelectEquipes .= "</select><br>"
	."<a href=\"javascript: void(0);\" onclick=\"equipe('ajout',".($url_iNiveau == $iNiveau ? "false" : "true").")\" onfocus=\"blur()\" title=\"Ajouter une &eacute;quipe\">Ajouter</a>"
	."&nbsp;&nbsp;"
	.($iIdxEquipe > 0 && $url_iNiveau == $iNiveau ? "<a href=\"javascript: void(0);\" onclick=\"equipe('modif',false)\" onfocus=\"blur()\">Modifier</a>" : "<span class=\"lien_passif\">Modifier</span>")
	."&nbsp;&nbsp;"
	.($iIdxEquipe > 0 && $url_iNiveau == $iNiveau ? "<a href=\"javascript: void(0);\" onclick=\"equipe('sup',false)\" onfocus=\"blur()\">Supprimer</a>" : "<span class=\"lien_passif\">Supprimer</span>")
	."\n";

// *************************************
//
// *************************************

$asNiveau = array(NULL,"cette formation","ce cours","cette unité");

$amFiltres = array(
	array("non encore affectés",PERSONNE_SANS_EQUIPE),
	array("déjà affectés",PERSONNE_DANS_EQUIPE),
	array("inscrits à ".$asNiveau[$url_iNiveau],PERSONNE_INSCRITE));

$sSelectFiltrePersonnes = "<select name=\"FILTRE_PERSONNES\" onchange=\"afficherPersonnes(value)\">";

for ($i=0; $i<count($amFiltres); $i++)
	$sSelectFiltrePersonnes .= "<option value=\"".$amFiltres[$i][1]."\""
		.($amFiltres[$i][1] == $url_iFiltre ? " selected" : NULL)
		.">".$amFiltres[$i][0]."</option>";

$sSelectFiltrePersonnes .= "</select>";

unset($asNiveau);

$sMessageAvertissement = "&nbsp;";

$oProjet->terminer();

// :DEBUG:
//echo "gestion_equipes-pers.php?NIVEAU={$iNiveau}&ID_NIVEAU={$iIdNiveau}&FILTRE_PERSONNES={$url_iFiltre}&ID_EQUIPE={$url_iIdEquipe}";

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style("gestion_equipes.css"); ?>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('globals.js')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('window.js')?>"></script>
<script type="text/javascript" language="javascript">
<!--

var g_sRech = null;

function init()
{
	if (top.oFormation &&
		top.oFormation() &&
		top.oFormation().designerSonParent)
	{
		desactiverBoutons();
		afficherMembres("<?=$url_iIdEquipe?>");
		top.oFormation().designerSonParent(<?php echo "\"{$iNiveau}\",\"{$iIdNiveau}\",\"{$url_iNiveau}\",\"{$url_iIdNiveau}\""; ?>);
		changerTailleFrames();
	}
	else
		setTimeout("init()",1000);
}

function envoyer()
{
	document.forms[0].submit();
}

function valider(v_iIdEquipe)
{
	with (document.forms[0])
	{
		if (typeof(v_iIdEquipe) != "undefined")
			elements["ID_EQUIPE"].options[elements["ID_EQUIPE"].selectedIndex].value = v_iIdEquipe;
		
		elements["ACTION"].value = "rafraichir";
		target = "EQUIPES";
		action = "<?=$_SERVER['PHP_SELF']?>";
		method="post"
		submit();
	}
}

function oForm()
{
	return document.forms[0];
}

function defNiveau(v_iNiveau,v_iIdNiveau)
{
	with (oForm())
	{
		elements["NIVEAU"].value = v_iNiveau;
		elements["ID_NIVEAU"].value = v_iIdNiveau;
		elements["ID_EQUIPE"].options[elements["ID_EQUIPE"].selectedIndex].value = "0";
		elements["FILTRE_PERSONNES"].value = "<?=PERSONNE_SANS_EQUIPE?>";
		
		target = "EQUIPES";
		action = "<?=$_SERVER['PHP_SELF']?>";
		method = "post";
	}
}

function desactiverBoutons()
{
	document.forms[0].elements["btnAjouter"].disabled = ((document.forms[0].elements["FILTRE_PERSONNES"].value < <?=PERSONNE_SANS_EQUIPE?>) || (<?="{$url_iNiveau}!={$iNiveau}"?>) || (<?="{$iIdxEquipe}==0"?>));
	document.forms[0].elements["btnRetirer"].disabled = (<?="{$url_iNiveau}!={$iNiveau} || {$iIdxEquipe}==0"?>);
}

function enregistrer()
{
	var iLargeur = 620;
	var iHauteur = 460;
	var sCaracteristiques = "left=" + ((screen.width-iLargeur)/2) + ",width=" + iLargeur
		+ ",top=" + ((screen.height-iHauteur)/2) + ",height=" + iHauteur
		+ ",resizable=1";
	
	var w = window.open("sauver_modele_index.php?NIVEAU=<?=$iNiveau?>&ID_NIVEAU=<?=$iIdNiveau?>","WinSauverModele",sCaracteristiques);
	
	w.focus();
}

function ouvrir()
{
	var iLargeur = 620;
	var iHauteur = 460;
	var sCaracteristiques = "left=" + ((screen.width-iLargeur)/2) + ",width=" + iLargeur
		+ ",top=" + ((screen.height-iHauteur)/2) + ",height=" + iHauteur
		+ ",resizable=1";
	
	var w = window.open("ouvrir_modele_index.php?NIVEAU=<?=$url_iNiveau?>&ID_NIVEAU=<?=$url_iIdNiveau?>","WinOuvrirModele",sCaracteristiques);
	
	w.focus();
}

function afficherPersonnes(v_iFiltre)
{
	desactiverBoutons();
	
	top.oEtudiants().defFiltre(v_iFiltre);
	top.oEtudiants().envoyer();
}

function afficherMembres(v_iIdEquipe)
{
	sEquipe = "";
	
	if (parseInt(v_iIdEquipe) > 0)
		sEquipe = "?NIVEAU=<?=$iNiveau?>&ID_EQUIPE=" + v_iIdEquipe;
	
	top.oEtudiants().setIdEquipe(v_iIdEquipe);
	
	top.oMembres().location.replace("equipes-membres.php" + sEquipe);
}

function equipe(v_sAction,v_bConfirmerAjouter)
{
	if (v_bConfirmerAjouter && !confirm("Attention, en créant une nouvelle équipe vous modifierez la composition des équipes héritée de la formation.\n\nVoulez-vous continuer ?"))
		return;
	
	var sCaracteristiques = centrerFenetre(430,150);
	
	var w = window.open("","WINGESTIONEQUIPE",sCaracteristiques);
	
	document.forms[0].elements["ACTION"].value = v_sAction;
	document.forms[0].action = "<?=dir_admin('equipe','equipe-index.php')?>";
	document.forms[0].target = "WINGESTIONEQUIPE";
	document.forms[0].method = "post";
	
	w.focus();
	
	if (w.onload)
		w.onload = document.forms[0].submit();
	else
		document.forms[0].submit();
}

function changerTailleFrames() {
	var iHauteurFrame = 500;
	
	if (window.innerHeight)
		iHauteurFrame = window.innerHeight;
	else if (document.body)
		iHauteurFrame = document.body.clientHeight;
	
	if (iHauteurFrame > 150)
		document.getElementById("id_frame_etudiants").style.height = (iHauteurFrame - 100) + "px";
	
	if (iHauteurFrame > 130)
		document.getElementById("id_frame_membres").style.height = (iHauteurFrame - 80) + "px";
}
//-->
</script>
<style type="text/css">
<!--
select { width: 100%; }
-->
</style>
</head>
<body class="principal" onload="init()" onresize="changerTailleFrames()">
<form>
<table border="0" cellspacing="0" cellpadding="3" width="100%">
<tr>
<td class="intitule" width="1%" valign="bottom">
&nbsp;&#8250;&nbsp;Liste des &eacute;tudiants
<br>
<?=$sSelectFiltrePersonnes?>
</td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td valign="bottom"><?=$sSelectEquipes?></td>
</tr>
<tr>
<td valign="top" width="50%">
<iframe src="equipes-etudiants.php<?="?NIVEAU={$iNiveau}&ID_NIVEAU={$iIdNiveau}&FILTRE_PERSONNES={$url_iFiltre}&ID_EQUIPE={$url_iIdEquipe}"?>" name="ETUDIANTS" id="id_frame_etudiants" width="100%" height="400px" frameborder="0"></iframe>
<div class="intitule" align="right">Rechercher&nbsp;:&nbsp;<input type="text" size="20" onkeyup="sePlacerPersonne(value,top.oEtudiants())"></div>
</td>
<td align="left">
<input type="button" name="btnAjouter" onclick="top.oEtudiants().ajouter()" value="&nbsp;&raquo;&nbsp;" disabled>
<br>
<input type="button" name="btnRetirer" onclick="top.oMembres().enlever()" value="&nbsp;&laquo;&nbsp;" disabled>
</td>
<td valign="top" width="50%">
<iframe src="" name="MEMBRES" id="id_frame_membres" width="100%" height="420px" frameborder="0"></iframe>
</td>
</tr>
</table>
<input type="hidden" name="ACTION" value="rafraichir">
<input type="hidden" name="NIVEAU" value="<?=$url_iNiveau?>">
<input type="hidden" name="ID_NIVEAU" value="<?=$url_iIdNiveau?>">
</form>
</body>
</html>
