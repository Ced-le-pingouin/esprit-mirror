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
** Fichier ................: mail_erreur.php
** Description ............:
** Date de création .......: 17/12/2004
** Dernière modification ..: 20/12/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$bEnvoiCourrielReussi = (empty($HTTP_GET_VARS["erreur"]) ? TRUE : FALSE);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("mail_erreur.tpl");

$sVarEnvoiCourrielReussi = $oTpl->defVariable("SET_ENVOI_COURRIEL_REUSSI");
$sVarEnvoiCourrielEchoue = $oTpl->defVariable("SET_ENVOI_COURRIEL_ECHOUE");

if ($bEnvoiCourrielReussi)
{
	$oTpl->remplacer("{envoi_courriel->message}",$sVarEnvoiCourrielReussi);
}
else
{
	$bErreurPartielle = TRUE;
	
	$oTpl->remplacer("{envoi_courriel->message}",$sVarEnvoiCourrielEchoue);
	
	$sVarErreurPartielle = $oTpl->defVariable("VAR_ERREUR_PARTIELLE");
	$sVarErreurComplete  = $oTpl->defVariable("VAR_ERREUR_COMPLETE");
	
	if ($bErreurPartielle)
		$oTpl->remplacer("{envoi_courriel->message}",$sVarErreurPartielle);
	else
		$oTpl->remplacer("{envoi_courriel->message}",$sVarErreurComplete);
}

$oTpl->afficher();

?>

