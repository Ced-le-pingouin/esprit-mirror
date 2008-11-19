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
** Fichier ................: liste_roles.php
** Description ............:
** Date de création .......: 14/12/2006
** Dernière modification ..: 17/12/2006
** Auteurs ................: Cécile Guilloux
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
if (isset($_GET["IDPERS"]) && count($_GET["IDPERS"]) > 0){

   $sMajListeInscrit = "oFrmInscrit().document.location = 'liste_roles.php?idform=$iIdFormCourante&STATUT_PERS={$iStatutPers}";			
   $oResp = new CProjet_Admin($oProjet->oBdd);
			
   foreach ($_GET["IDPERS"] as $iIdPers)
      $oResp->ajouter($iIdPers);
			
   $oResp = NULL;

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
<?php inserer_feuille_style("admin/personnes.css"); ?>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript"  src="<?=dir_javascript('globals.js.php')?>" ></script>
<script type="text/javascript" language="javascript" src="globals.js"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('window.js')?>"></script>
<script type="text/javascript" language="javascript"  src="<?=dir_javascript('outils_admin.js')?>" ></script>
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
	top.frames['Principale'].rechPersonne(top.frames['Principale'].document.forms[0].elements['nomPersonneRech'].value,self,'nom[]');
}

//-->
</script>
</head>
<body onload="init()" style="background-color: #FFFFFF;" class="associer_personnes">
<a name="top"></a>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get" target="_self">
<table border="0" cellspacing="1" cellpadding="1" width="100%">
<?php
$sClassTR = (" style=\"border: #EFEFEF none 1px; border-bottom-style: solid;\"");

$lettre = 96;
$j=0;
for ($i=0; $i<$iNbrPers; $i++)
{
	$sPremiereLettre = mb_strtolower(substr($oProjet->aoPersonnes[$i]->retNom(),0,1),"UTF-8");
	if ($lettre < ord($sPremiereLettre))
	{
		$j=0;
		$lettre = $sPremiereLettre;
		$lien = "<a id=\"{$lettre}\"></a>";
	}
	else $j++;

	$sPosition = ($j==0) ? "pos".$lettre : "pos".$lettre.$j;

	echo "<tr>"
		."<td{$sClassTR}>".$lien
		."<input type=\"checkbox\" name=\"IDPERS[$i]\" onfocus=\"blur()\" value=\"".$oProjet->aoPersonnes[$i]->retId()."\">"
		."</td>"
		."<td width=\"98%\"{$sClassTR}>"
		//."<a name=\"pos".($i+1)."\"></a>"
		."<a name=\"".$sPosition."\"></a>"
		."<a href=\"javascript: profil('?idPers=".$oProjet->aoPersonnes[$i]->retId()."'); void(0);\" onclick=\"blur()\">"
		."<span name=\"".$lettre."\" id=\"nom[]\">".$oProjet->aoPersonnes[$i]->retNomComplet(TRUE)."</span>"
		."</a>"
		."</td>\n"
		."<td{$sClassTR}>&nbsp;"._("Infos")."&nbsp;</td>"
		."</tr>\n";
}

if ($i < 1)
	echo "<tr><td style=\"text-align: center;\">$sErrPers</td></tr>\n";
?>
</table>
<input type="hidden" name="FILTRE" value="<?=$iFiltre?>">
<input type="hidden" name="STATUT_PERS" value="<?=$iStatutPers?>">
<input type="hidden" name="FORMATION" value="<?=$iIdForm?>">
<input type="hidden" name="ID_MOD" value="<?=$iIdMod?>">
<input type="hidden" name="idform" value="<?=$iIdFormCourante?>">
</form>
</body>
</html>

