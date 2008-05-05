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
** Fichier ................: equipes-membres.php
** Description ............: 
** Date de création .......: 01/01/2003
** Dernière modification ..: 15/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
if (!empty($_GET["ID_EQUIPE"]))
	$url_iIdEquipe = $_GET["ID_EQUIPE"];
else if (!empty($_POST["ID_EQUIPE"]))
	$url_iIdEquipe = $_POST["ID_EQUIPE"];
else
	$url_iIdEquipe = 0;

if (!empty($_GET["NIVEAU"]))
	$url_iNiveau = $_GET["NIVEAU"];
else if (!empty($_POST["NIVEAU"]))
	$url_iNiveau = $_POST["NIVEAU"];
else
	$url_iNiveau = 0;

//echo "ID_EQUIPE={$url_iIdEquipe}<br>NIVEAU={$url_iNiveau}<hr>";

// *************************************
// Retirer un membre de l'équipe
// *************************************

$sCorpFonctionInit = NULL;

if (isset($_POST["ID_PERS"]) && $url_iIdEquipe > 0)
{
	$oEquipeMembre = new CEquipe_Membre($oProjet->oBdd,$url_iIdEquipe);
	$oEquipeMembre->effacerMembres($_POST["ID_PERS"]);
	$sCorpFonctionInit = "\n\ttop.oEtudiants().envoyer();\n";
}

// *************************************
//
// *************************************

$oEquipe = new CEquipe($oProjet->oBdd,$url_iIdEquipe);

$iNbMembres = $oEquipe->initMembres();

$sCorpHtml = NULL;
$sRepIcone = dir_theme_commun("icones");

for ($iIdxMembre=0; $iIdxMembre<$iNbMembres; $iIdxMembre++)
	$sCorpHtml .= "<tr>"
		."<td>"
		."<input type=\"radio\""
		." name=\"ID_PERS\""
		." value=\"".$oEquipe->aoMembres[$iIdxMembre]->retId()."\""
		." onfocus=\"blur()\""
		.">"
		."</td>"
		."<td><img src=\"{$sRepIcone}/".($oEquipe->aoMembres[$iIdxMembre]->retSexe() == "F" ? "girl.gif" : "boy.gif")."\" border=\"0\"></td>"
		."<td width=\"99%\" class=\"bloc_personne\">"
		."&nbsp;&nbsp;"
		."<a"
		." href=\"javascript: profil('?idPers=".$oEquipe->aoMembres[$iIdxMembre]->retId()."'); void(0);\""
		." onclick=\"blur()\""
		.">".$oEquipe->aoMembres[$iIdxMembre]->retNomComplet(TRUE)."</a>"
		."<br>&nbsp;&nbsp;"
		."<small>".$oEquipe->aoMembres[$iIdxMembre]->retPseudo()."</small>"
		."</td>"
		."</tr>\n";

if (empty($sCorpHtml))
{
	$asTypes = array(NULL,"cette formation","ce cours",NULL,"cette unit&eacute;");
	
	$sCorpHtml .= "<tr>"
			."<td>"
			."<div class=\"Attention\" align=\"center\">"
			.($url_iIdEquipe < 1 && $url_iNiveau < 1 ? "Il n'y a pas d'&eacute;quipe pour l'instant &agrave; ce niveau" : "Il n'y aucun &eacute;tudiant d'affect&eacute; dans cette &eacute;quipe")
			."</div>"
			."</td>"
			."</tr>\n";
}

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<META HTTP-EQUIV="Pragma" CONTENT="NO-CACHE">
<?php inserer_feuille_style("admin/personnes.css"); ?>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript('globals.js.php')?>"></script>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript('window.js')?>"></script>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript('outils_admin.js')?>"></script>
<script type="text/javascript" language="javascript">
<!--

function init()
{<?php echo $sCorpFonctionInit; ?>
}

function enlever() { document.forms[0].submit(); }

//-->
</script>

</head>
<body class="membres" onload="init()">
<form method="post">
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<?php echo $sCorpHtml?>
</table>
<input type="hidden" name="ID_EQUIPE" value="<?php echo $url_iIdEquipe?>">
<input type="hidden" name="NIVEAU" value="<?php echo $url_iNiveau?>">
</form>
</body>
</html>
