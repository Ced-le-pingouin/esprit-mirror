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

require_once (dirname (__FILE__)."/fichiers_permis.inc.php");

/*************************************************************************
** Script permettant de télécharger n'importe quel fichier, quel que soit
** son type (MIME), et ce même dans Internet Explorer (IE).
**
** Testé avec les navigateurs suivants:
**	IE 5.5:	OK
**	IE 5.0:	- Après téléchargement, IE continue à 'chercher' (pointeur 
**		  sablier & terre tournante), mais le fichier est OK 
**		  (il faut cliquer 'Arrêter' dans la barre de navigation)
**		- En plus, si le fichier n'a pas une extension reconnue
**		  par Windows, son extension sera doublée (bug!): par ex.
**		  'archive.tar..tar'
**	IE 4:	Impossible de forcer le download d'un fichier reconnu
**		(.doc, peut-être aussi .pdf et autres): il est affiché
**		directement.
**	N 4.7x:	OK
**	N 4.61:	OK
**	N 4.5:	OK
*************************************************************************/

/*************************************************************************
** FONCTIONS
*************************************************************************/

/*
** Fonction 		: retExt
** Description		: renvoie l'extension d'un fichier. Cette fonction devrait
**					  être améliorée, peut-être en utilisant les fonctions 
**					  pathinfo() et/ou realpath() de PHP 4.0.3+
** Entrée			:
**					v_sNomFichier	: nom du fichier.
**									  Attention ! Cette version n'est pas
**									  protégée contre les noms de fichiers
**									  contenant des '..' et '.' dans le 
**									  chemin, et dont le fichier n'a pas
**									  d'extension. Si on passe 
**									  'res/../res/fichier' comme paramètre, 
**									  la fonction va retourner '/res/fichier'
**									  comme extension
**					v_bMinuscules	: renvoie l'extension en minuscules
** Sortie			: l'extension du fichier (sans le point)
*/

function retExt ($v_sNomFichier, $v_bMinuscules = FALSE)
{
	$asMorceaux = explode(".", $v_sNomFichier);
	
	if (count($asMorceaux) > 1)
		$r_sExt = $asMorceaux[count($asMorceaux) - 1];
	else
		$r_sExt = "";
		
	if ($v_bMinuscules)
		return strtolower($r_sExt);
	else
		return $r_sExt;
}


function retVraiNomFichier ($v_sNomComplet)
{
	return ereg_replace("-([0-9]){4}\.",".",$v_sNomComplet);
}

// Attention ! le nom du fichier passé en paramètre de l'URL est contenu
// dans la variable 'f' (f=...), mais il a dû être auparavant encodé par la
// fonction PHP 'rawurlencode'. C'est pour éviter le plantage avec les noms 
// de fichier contenant des espaces et autres caractères spéciaux 

// FILIPPO : tu dois remplacer le 'f' entre [] par 'fd' (et enlever 'rawurldecode' ???)
$nomComplet = stripslashes(rawurldecode($HTTP_GET_VARS["f"]));

$sErreur = NULL;

// Si on essaye de downloader un fichier en partant du root ('/'), ou en 
// utilisant des '..' pour remonter dans l'arborescence, ou un fichier 
// auquel on n'a pas accès, il y aura un message d'erreur
/*
if ($nomComplet[0] == "/")
{
	$sErreur = "Utilisation non autorisée du caractère '/'";
}
else
*/
if (!(strpos($nomComplet, "..") === false))
{
	$sErreur = "Utilisation non autorisée de la chaine '..'";
}
else if (!is_readable($HTTP_SERVER_VARS["DOCUMENT_ROOT"].$nomComplet))
{
	$sErreur = "Lecture impossible";
}
else
{
	// on récupère le nom de fichier, sans le chemin
	$nomSimple = stripslashes(basename($nomComplet));
	$extension = retExt($nomSimple, TRUE);
	
	// on vérifie que l'extension est autorisée
	if (!validerFichier($nomSimple))
		$sErreur = "Le téléchargement de ce type de fichier ($nomSimple) n'est pas autorisé";
}

/*
	header("Content-Type: application/msword")
	
	application/msword
	application/ms-excel
*/

// s'il n'y a pas eu d'erreur, le téléchargement du fichier débute
if (empty($sErreur))
{
	// FILIPPO : enlève les commentaires des 2 lignes suivantes si tu veux
	// utiliser la variable 'filename' de l'URL pour nommer le fichier envoyé
	// à l'utilisateur. Si 'filename' n'a pas été spécifié, le fichier aura le
	// même nom que sur le serveur
	
	if (isset ($HTTP_GET_VARS["fn"]))
	{
		if ($HTTP_GET_VARS["fn"] === "1") 
			$nomSimple = retVraiNomFichier($nomSimple);
		else
			$nomSimple = rawurldecode($HTTP_GET_VARS["fn"]).".".$extension;
	}
	
	//header ("Content-Type: application/force-download");
	header ("Content-Type: application/octet-stream");
	//header ("Content-Length: ".filesize($nomComplet));
	header ("Content-Disposition: attachment; filename=".str_replace(" ","_",$nomSimple));
	//header("Content-Transfer-Encoding: binary"); 
	readfile ($HTTP_SERVER_VARS["DOCUMENT_ROOT"].$nomComplet);
}
else
{
	// s'il y a eu erreur, on en affiche la raison
	print "<html>\n"
		."<head>\n"
	        ."<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n"
                ."<title>Téléchargement impossible !</title>\n</head>\n"
		."<div style=\"text-align: center;\">\n"
		."<center>\n"
		."<h4>\n"
		."<br><br>ERREUR<br><br><br>"
		."Fichier '$nomComplet'<br><br>$sErreur"
		."</h4>\n"
		."<a href=\"javascript: history.back();\">Retour</a>"
		."</div>\n"
		."</body>\n"
		."</html>\n";
}

?>
