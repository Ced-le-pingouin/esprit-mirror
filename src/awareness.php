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
** Fichier ................: awareness.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 01/03/2005
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Nettoyer le répertoire tmp
// ---------------------
$sRepTmp = dir_tmp(NULL,TRUE);

if (is_dir($sRepTmp))
{
	$sPseudoFichier = "-".$oProjet->oUtilisateur->retPseudo().".";
	
	if ($hReps = opendir($sRepTmp))
	{
		while (($sFichier = readdir($hReps)) !== FALSE)
			if (is_file($sRepTmp.$sFichier) && strstr($sFichier,$sPseudoFichier))
				unlink($sRepTmp.$sFichier);
		
		closedir($hReps);
	}
	
	$hReps = NULL;
	$sPseudoFichier = NULL;
}

$sRepTmp = NULL;

include_once(dir_admin("awareness","awareness.inc.php",TRUE));

$sAwarenessClient = NULL;

if (isset($oProjet->oUtilisateur) && 
	!stristr($HTTP_SERVER_VARS["HTTP_USER_AGENT"],"Netscape"))
{
	$sAwarenessClient = "<applet"
		." name=\"AwarenessClient\""
		." width=\"1\" height=\"1\""
		." codebase=\"".dir_admin("awareness/client")."\""
		." code=\"AwarenessClient.class\""
		.">\n" // <applet ...>
		."<param name=\"mode\" value=\"client\">\n"
		."<param name=\"hostname\" value=\"".$HTTP_SERVER_VARS["SERVER_ADDR"]."\">\n"
		."<param name=\"port\" value=\"2501\">\n"
		."<param name=\"nickname\" value=\"".urlencode($oProjet->oUtilisateur->retPseudo())."\">\n"
		."<param name=\"username\" value=\""
			.urlencode($oProjet->oUtilisateur->retPrenom()." ".$oProjet->oUtilisateur->retNom())
			."\">\n"
		."<param name=\"sex\" value=\"".$oProjet->oUtilisateur->retSexe()."\">\n"
		."<param name=\"team\" value=\"".urlencode($oProjet->retTexteStatutUtilisateur())."\">\n"
		."<param name=\"language\" value=\"Fra\">\n"
		."<param name=\"session\" value=\"".retNomUniqueAwareness()."\">\n"
		."</applet>\n";
}

$oProjet->terminer();
?>
<html>
<head>
<script type="text/javascript" language="javascript">
<!--
function voir_liste_connectes()
{
	if (document.applets.length > 0 && document.applets["AwarenessClient"])
		document.applets["AwarenessClient"].openListConnected();
}
function deconnexion() { self.location = "deconnexion.php"; }
//-->
</script>
</head>
<body style="background-color: rgb(0,0,0);">
<?=$sAwarenessClient?>
</body>
</html>
