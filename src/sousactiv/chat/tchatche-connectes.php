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
** Fichier ................: tchatche-connectes.php
** Description ............:
** Date de création .......: 01/03/2001
** Dernière modification ..: 03/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("ids.class.php"));
require_once(dir_database("chat.tbl.php"));
require_once("archive.class.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdNiveau   = (empty($_GET["idNiveau"]) ? 0 : $_GET["idNiveau"]);
$url_iTypeNiveau = (empty($_GET["typeNiveau"]) ? 0 : $_GET["typeNiveau"]);

if ($url_iIdNiveau < 1 || $url_iTypeNiveau < 1)
	exit();

// ---------------------
// Initialiser
// ---------------------
$bHautStatut = retHautStatut($oProjet->retStatutUtilisateur());
$sMonEquipe = NULL;
$sNomEquipe = NULL;

if (!$bHautStatut)
{
	$oProjet->initEquipe();
	echo $sMonEquipe = $oProjet->oEquipe->retNom();
}

$oIds = new CIds($oProjet->oBdd,$url_iTypeNiveau,$url_iIdNiveau);

$amParams = array("idForm" => $oIds->retIdForm()
	, "idActiv" => $oIds->retIdActiv());

switch ($url_iTypeNiveau)
{
	case TYPE_SOUS_ACTIVITE:
	//   ------------------
		$oParent = new CSousActiv($oProjet->oBdd,$url_iIdNiveau);
		break;
		
	case TYPE_RUBRIQUE:
	//   -------------
		$oParent = new CModule_Rubrique($oProjet->oBdd,$url_iIdNiveau);
		break;
}

$iTotalChats = $oParent->initChats();
$aoChats = $oParent->aoChats;
$oParent->initEquipes();
$aoEquipes = &$oParent->aoEquipes;

// Rechercher le répertoire des archives
$sRepArchives = dir_chat_archives($url_iTypeNiveau,$amParams,NULL,TRUE);
unset($amParams);

$asListeConnectes = NULL;

$asPseudo = NULL;

$asPseudo = array();
$asNomEquipe = array_keys($asPseudo);

$oArchives = new CArchives($sRepArchives);
$asNombreArchives = NULL;

foreach ($oParent->aoChats as $oChat)
{
	$iIdChat = $oChat->retId();
	$sIdArchive = CID.$iIdChat;
	$sListePseudos = NULL;
	$asListePseudos = NULL;
	
	if (is_file($sRepArchives.$sIdArchive))
		$asListePseudos = file($sRepArchives.$sIdArchive);
	
	$iModaliteChat = $oChat->retModalite();
	$sTableau = NULL;
	
	if (gettype($asListePseudos) == "array")
	{
		while (list($iNumeroLigne,$sLigne) = each($asListePseudos))
		{
			$sLigne = trim($sLigne);
			list($sPlateforme,$sIdUnique,$sNomEquipe,$sPseudo) = explode(":",$sLigne);
			if (CHAT_PAR_EQUIPE == $iModaliteChat)
				echo $sListePseudos .= (isset($sListePseudos) ? ", " : NULL)
					."\"{$sPseudo}:$sNomEquipe\"";
			else
				$sListePseudos .= (isset($sListePseudos) ? ", " : NULL)
					.$sPseudo;
		}
	}
	
	if (CHAT_PAR_EQUIPE == $iModaliteChat)
	{
		$asListeConnectes .= "var g_idListeConnectes".$oChat->retId()
			."=".(isset($sListePseudos) ? "new Array({$sListePseudos})" : "null").";\n";
		
		// Compter le nombre d'archives
		foreach ($aoEquipes as $oEquipe)
		{
			if (!$bHautStatut && $sMonEquipe != $oEquipe->retNom())
				continue;
			
			$sNomEquipe = $oEquipe->retNom();
			$oArchives->defFiltre(retIdUniqueChat($iIdChat,urlencode($sNomEquipe)));
			$asNombreArchives .= (isset($asNombreArchives) ? ", ": NULL)
				."new Array(\"".$oChat->retId()."\""
				.",\"".urlencode($sNomEquipe)."\""
				.",\"".count($oArchives->retArchives())."\""
				.")";
		}
	}
	else
	{
		$asListeConnectes .= "var g_idListeConnectes".$oChat->retId()
			."=".(isset($sListePseudos) ? "\"".$sListePseudos."\"" : "null").";\n";
		
		// Compter le nombre d'archives
		$oArchives->defFiltre(retIdUniqueChat($iIdChat));
		$asNombreArchives .= (isset($asNombreArchives) ? ", ": NULL)
			."new Array(\"".$oChat->retId()."\""
			.",\"".urlencode($sNomEquipe)."\""
			.",\"".count($oArchives->retArchives())."\""
			.")";
	}
}

?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html;  charset=utf-8">
<script type="text/javascript" language="javascript">
<!--

<?php echo $asListeConnectes?>
var g_aListeChats = new Array();
var g_aaNombreArchives = new Array(<?php echo $asNombreArchives?>);

function afficher_liste_connectes(v_iListeConnectes,v_sListeConnectes)
{
	// id_liste_connectes{chat.id}_{equipe.id}
	var idListeConnectes = "id_liste_connectes"
		+ g_aListeChats[v_iListeConnectes][0]	// Identifiant du chat
		+ "_"
		+ g_aListeChats[v_iListeConnectes][1];	// Identifiant de l'équipe
	
	if (top.oPrincipal().document &&
		top.oPrincipal().document.getElementById(idListeConnectes))
	{
		top.oPrincipal().document.getElementById(idListeConnectes).innerHTML = v_sListeConnectes;
	}
}

function recharger_liste_connectes(v_iListeConnectes)
{
	var i;
	var sListePseudos = new String();
	
	if (g_aListeChats[v_iListeConnectes][2].length > 0)
	{
		var tableau = eval("g_idListeConnectes" + g_aListeChats[v_iListeConnectes][0]);
		
		if (tableau != null)
		{
			for (i=0; i<tableau.length; i++)
			{
				str = tableau[i].split(":");
				if (str[1] == g_aListeChats[v_iListeConnectes][2])
					sListePseudos += (sListePseudos.length > 0 ? ", " : "") + str[0];
			}
		}
	}
	else
		sListePseudos = eval("g_idListeConnectes" + g_aListeChats[v_iListeConnectes][0]);
	
	if (sListePseudos == null || sListePseudos.length < 1)
		sListePseudos = "Pas d'utilisateur connect&eacute;";
	
	afficher_liste_connectes(v_iListeConnectes,sListePseudos);
}

function afficher_nombre_archives()
{
	var i;
	var j = 0;
	
	for (i=0; i<g_aListeChats.length; i++)
	{
		/*if (g_aListeChats[i][0] == g_aaNombreArchives[j][0] &&
			g_aListeChats[i][2] == g_aaNombreArchives[j][1])
		{*/
			var sIdNombreArchives = "id_nombre_archives"
				+ g_aListeChats[i][0]
				+ "_"
				+ g_aListeChats[i][1];
			if (top.oPrincipal().document &&
				top.oPrincipal().document.getElementById(sIdNombreArchives))
			{
				top.oPrincipal().document.getElementById(sIdNombreArchives).innerHTML = g_aaNombreArchives[j][2];
				j++;
			}
		//}
		
	}
}

function init()
{
	g_aListeChats = top.oPrincipal().g_aListeChats;
	
	if (typeof(g_aListeChats) != "undefined")
	{
		for (var iIdxChat=0; iIdxChat<g_aListeChats.length; iIdxChat++)
			recharger_liste_connectes(iIdxChat);
		
		afficher_nombre_archives();
	}
}

//-->
</script>
</head>
<body onload="init()" style="background-color: rgb(255,255,255); color: rgb(0,0,0);">&nbsp;</body>
</html>

