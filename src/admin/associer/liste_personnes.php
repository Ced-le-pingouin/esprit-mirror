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
// Ajouter/enlever des personnes
// ---------------------
if (isset($_GET["IDPERS"]) && count($_GET["IDPERS"]) > 0)
{
	include_once(dir_database("module_tuteur.tbl.php"));
	include_once(dir_database("module_concepteur.tbl.php"));
	include_once(dir_database("projet_concepteur.tbl.php"));

	// ---------------------
	// Enlever des personnes de la formation, pas de la PF.
	// ---------------------
	if (isset($_GET["Action_Pers"]) && ($_GET["Action_Pers"] === "enlever")) {
		$sMajListeInscrit = "oFrmInscrit().document.location = 'liste_inscrits.php?idform=$iIdFormCourante&STATUT_PERS={$iStatutPers}";
		$oRespForm 	= new CFormation_Resp($oProjet->oBdd,$iIdFormCourante);
		$oResp		= new CProjet_Resp($oProjet->oBdd);
	
		foreach ($_GET["IDPERS"] as $iIdPers) {
			$oConcepteurForm	= new CFormation_Concepteur($oProjet->oBdd,$iIdFormCourante,$iIdPers);
			$oConcepteur		= new CProjet_Concepteur($oProjet->oBdd,$iIdPers);
			$oTuteur			= new CFormation_Tuteur($oProjet->oBdd,$iIdFormCourante,$iIdPers);
			$oFormInscrit		= new CFormation_Inscrit($oProjet->oBdd,$iIdFormCourante,$iIdPers);
					
			$oRespForm->effacer($iIdPers);
			$oResp->effacer($iIdPers);
			$oConcepteurForm->effacerConcepteur();$oConcepteurForm = NULL;
			$oConcepteur->effacerConcepteur();$oConcepteur = NULL;
			$oTuteur->effacerTuteur();$oTuteur = NULL;
			$oFormInscrit->effacer();$oFormInscrit = NULL;
		}
		$oRespForm = $oResp = NULL;
		$sMajListeInscrit .= "';";
	}
	// ---------------------
	// Ajouter des personnes
	// ---------------------
	else {
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<?php inserer_feuille_style("admin/personnes.css"); ?>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>test</title>
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<script type="text/javascript" language="javascript"  src="<?php echo dir_javascript('globals.js.php')?>" ></script>
<script type="text/javascript" language="javascript" src="globals.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript('window.js')?>"></script>
<script type="text/javascript" language="javascript"  src="<?php echo dir_javascript('outils_admin.js')?>" ></script>
<script type="text/javascript" language="javascript"  src="<?php echo dir_javascript('selection_multiple.js')?>" ></script>
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
<style type="text/css">
<!--
td#id_table_entete_1 { width: 1%; }
td#id_table_entete_2 { width: 99%; text-align: left; }
-->
</style>
</head>
<body onload="init()" class="associer_personnes">
<a name="top"></a>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get" target="_self">
<table border="0" cellspacing="1" cellpadding="1" width="100%" class="liste_personnes">
<tr><td><input type="checkbox" name="Selectionner_tous" onclick="selectionner(this,'Selectionner_tous',-1,true,false)" onfocus="blur()" value="-1"></td>
<td class="intitule" id="id_table_entete_2">&nbsp;<b>S&eacute;lectionner toutes les personnes</b></td></tr>
<tr><td> </td><td>
<div style="margin: 0px 0px 0pt; background-color: rgb(255, 255, 255);">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody>
<?php
$lettre = "";
$j=0;
for ($i=0; $i<$iNbrPers; $i++)
{
	$sPremiereLettre = mb_strtolower(substr($oProjet->aoPersonnes[$i]->retNom(),0,1),"UTF-8");
	if ($lettre < $sPremiereLettre)
	{
		$j=0;
		$lettre = $sPremiereLettre;
		$lien = "<a id=\"lettre_{$lettre}\"></a>";
	}
	else $j++;
	
$sPosition = ($j==0) ? "pos".$lettre : "pos".$lettre.$j;

	echo "<tr>"
		."<td>".$lien
		."<input type=\"checkbox\" name=\"IDPERS[$i]\" id=\"IDPERS[$i]\" onfocus=\"blur()\" value=\"".$oProjet->aoPersonnes[$i]->retId()."\">"
		."</td>"
		."<td style=\"border: rgb(180,180,180) none 1px; border-bottom-style: dashed; width: 98%; font-size: 9pt\">"
		//."<a name=\"pos".($i+1)."\"></a>"
		."<a name=\"".$sPosition."\" id=\"".$sPosition."\"></a>"
		."<a href=\"javascript: profil('?idPers=".$oProjet->aoPersonnes[$i]->retId()."'); void(0);\" onclick=\"blur()\">"
		."<span name=\"".$lettre."\" id=\"".$lettre."\">".$oProjet->aoPersonnes[$i]->retNomComplet(TRUE)
		.((defined('UNICITE_NOM_PRENOM') && UNICITE_NOM_PRENOM===TRUE)?
		  '&nbsp;<em>('.$oProjet->aoPersonnes[$i]->retPseudo().')</em>':'')
		."</span>"
		."</a>"
		."</td>\n"
		."<td>&nbsp;"._("Infos")."&nbsp;</td>"
		."</tr>\n";
}

if ($i < 1)
	echo "<tr><td class=\"Infos\">&#8250;&nbsp;$sErrPers</td></tr>\n";
?>
</tbody></table>
</div></td></tr>
</table>
<input type="hidden" name="FILTRE" value="<?php echo $iFiltre?>">
<input type="hidden" name="STATUT_PERS" value="<?php echo $iStatutPers?>">
<input type="hidden" name="FORMATION" value="<?php echo $iIdForm?>">
<input type="hidden" name="ID_MOD" value="<?php echo $iIdMod?>">
<input type="hidden" name="idform" value="<?php echo $iIdFormCourante?>">
<input type="hidden" name="Action_Pers" id="Action_Pers" value="aucune">
</form>
</body>
</html>

