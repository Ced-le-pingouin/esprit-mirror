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
** Fichier ................: liste_personnes.php
** Description ............:
** Date de création .......: 16/09/2002
** Dernière modification ..: 27/01/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Initialiser
// ---------------------
$iIdFormCourante = (isset($_GET["idform"]) ? $_GET["idform"] : 0);
$iFiltre         = (isset($_GET["FILTRE"]) ? $_GET["FILTRE"] : 0);
$iStatutPers     = (isset($_GET["STATUT_PERS"]) ? $_GET["STATUT_PERS"] : STATUT_PERS_ETUDIANT);
$iIdMod          = (isset($_GET["ID_MOD"]) ? $_GET["ID_MOD"] : 0);
$iIdForm         = (isset($_GET["FORMATION"]) && $_GET["FORMATION"] > 0 ? 1 : 0);

$sErrPers = NULL;

$sMajListeInscrit = NULL;

// ---------------------
// Ajouter des personnes
// ---------------------
if (isset($_GET["IDPERS"]) && count($_GET["IDPERS"]) > 0)
{
	$sMajListeInscrit = "oFrmInscrit().document.location = 'liste_inscrits.php?idform=$iIdFormCourante&STATUT_PERS={$iStatutPers}";
	
	switch ($iStatutPers)
	{
		case STATUT_PERS_RESPONSABLE_POTENTIEL:
		case STATUT_PERS_RESPONSABLE:
			
			if ($iStatutPers == STATUT_PERS_RESPONSABLE)
				$oResp = new CFormation_Resp($oProjet->oBdd,$iIdFormCourante);
			else
				$oResp = new CProjet_Resp($oProjet->oBdd);
			
			foreach ($_GET["IDPERS"] as $iIdPers)
				$oResp->ajouter($iIdPers);
			
			$oResp = NULL;
			
			break;
			
		case STATUT_PERS_CONCEPTEUR_POTENTIEL:
		case STATUT_PERS_CONCEPTEUR:
			
			if ($iStatutPers == STATUT_PERS_CONCEPTEUR)
			{
				include_once(dir_database("module_concepteur.tbl.php"));
				$oConcepteur = new CFormation_Concepteur($oProjet->oBdd,$iIdFormCourante);
			}
			else
			{
				include_once(dir_database("projet_concepteur.tbl.php"));
				$oConcepteur = new CProjet_Concepteur($oProjet->oBdd);
			}
			$oConcepteur->ajouterConcepteurs($_GET["IDPERS"]);
			break;
			
		case STATUT_PERS_TUTEUR:
			
			include_once(dir_database("module_tuteur.tbl.php"));
			$oTuteur = new CFormation_Tuteur($oProjet->oBdd,$iIdFormCourante);
			$oTuteur->ajouterTuteurs($_GET["IDPERS"]);
			break;
			
		case STATUT_PERS_ETUDIANT:
			
			$oFormInscrit = new CFormation_Inscrit($oProjet->oBdd,$iIdFormCourante);
			foreach ($_GET["IDPERS"] as $iIdPers)
				$oFormInscrit->ajouter($iIdPers);
			unset($oFormInscrit);
			break;
	}
	
	$sMajListeInscrit .= "';";
}

// ---------------------
// Rechercher les personnes
// ---------------------
$i = ($iIdForm > 0 ? $iIdFormCourante : 0);

if (($iNbrPers = $oProjet->initPersonnes($iFiltre,$i)) < 1)
{
	switch ($iFiltre)
	{
		case STATUT_PERS_RESPONSABLE:
			$sErrPers = "Pas de "
				.mb_strtolower($oProjet->retTexteStatutUtilisateur(STATUT_PERS_RESPONSABLE,"M"),"UTF-8")
				." trouvé";
			break;
			
		case STATUT_PERS_CONCEPTEUR:
			$sErrPers = "Pas de "
				.mb_strtolower($oProjet->retTexteStatutUtilisateur(STATUT_PERS_CONCEPTEUR,"M"),"UTF-8")
				." trouvé";
			break;
			
		case STATUT_PERS_TUTEUR:
			$sErrPers = "Pas de "
				.mb_strtolower($oProjet->retTexteStatutUtilisateur(STATUT_PERS_TUTEUR,"M"),"UTF-8")
				." trouvé";
			break;
			
		case STATUT_PERS_ETUDIANT:
			$sErrPers = "Pas d'"
				.mb_strtolower($oProjet->retTexteStatutUtilisateur(STATUT_PERS_ETUDIANT,"M"),"UTF-8")
				." trouvé";
			break;
			
		default:
			$sErrPers = _("La table contenant les personnes est vide");
	}
}

?>
<html>
<head>
<?php inserer_feuille_style("associer_personnes.css"); ?>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript"  src="<?php echo dir_javascript('globals.js.php')?>" ></script>
<script type="text/javascript" language="javascript" src="globals.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript('window.js')?>"></script>
<script type="text/javascript" language="javascript"  src="<?php echo dir_javascript('outils_admin.js')?>" ></script>
<script language="javascript" type="text/javascript">
<!--

// {{{ Ces variables globales sont utilisées par la fonction "rechPersonne"
var g_sRech = null;
var g_asListeRech = null;
var g_iPosDerniereRech = -1;
// }}}

function init()
{
<?php echo $sMajListeInscrit; ?>
	top.frames['Principal'].rechPersonne(top.frames['Principal'].document.forms[0].elements['nomPersonneRech'].value,self,'nom[]');
}

//-->
</script>
</head>
<body onload="init()" style="background-color: #FFFFFF;">
<a name="top"></a>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get" target="_self">
<table border="0" cellspacing="1" cellpadding="1" width="100%">
<?php
$sClassTR = (" style=\"border: #EFEFEF none 1px; border-bottom-style: solid;font-size: 10pt;\"");

for ($i=0; $i<$iNbrPers; $i++)
	echo "<tr>"
		."<td{$sClassTR}>"
		."<input type=\"checkbox\" name=\"IDPERS[$i]\" onfocus=\"blur()\" value=\"".$oProjet->aoPersonnes[$i]->retId()."\">"
		."</td>"
		."<td width=\"98%\"{$sClassTR}>"
		."<a name=\"pos".($i+1)."\"></a>"
		."<a href=\"javascript: profil('?idPers=".$oProjet->aoPersonnes[$i]->retId()."'); void(0);\" onclick=\"blur()\">"
		."<span name=\"nom[]\" id=\"nom[]\">".$oProjet->aoPersonnes[$i]->retNomComplet(TRUE)
		.((defined('UNICITE_NOM_PRENOM') && UNICITE_NOM_PRENOM===FALSE)?
		  '&nbsp;<em>('.$oProjet->aoPersonnes[$i]->retPseudo().')</em>':'')
		."</span>"
		."</a>"
		."</td>\n"
		."<td{$sClassTR}>&nbsp;"._("Infos")."&nbsp;</td>"
		."</tr>\n";

if ($i < 1)
	echo "<tr><td style=\"text-align: center;\" class=\"Infos\">&#8250;&nbsp;$sErrPers</td></tr>\n";
?>
</table>
<input type="hidden" name="FILTRE" value="<?php echo $iFiltre?>">
<input type="hidden" name="STATUT_PERS" value="<?php echo $iStatutPers?>">
<input type="hidden" name="FORMATION" value="<?php echo $iIdForm?>">
<input type="hidden" name="ID_MOD" value="<?php echo $iIdMod?>">
<input type="hidden" name="idform" value="<?php echo $iIdFormCourante?>">
</form>
</body>
</html>

