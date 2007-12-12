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
// Copyright (C) 2001-2007  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium,
//                          Grenoble Universités.

/**
 * @file	ajax.php
 * 
 * Réagit aux appels AJAX
 * 
 */

require_once("globals.inc.php");
require_once(dir_database("hotpotatoes_score.tbl.php"));

$oProjet = new CProjet();

$action = stripslashes($_REQUEST['action']);
if (function_exists($action)) {
	call_user_func($action);
}

/** Affecte un score à un utilisateur pour un exo Hotpot.
 @details Les données sont lues dans $_REQUEST
 @return FALSE en cas d'erreur
*/
function hotpotScore( ) {
	global $oProjet;
	if (empty($_REQUEST['IdHotpot']) || !ctype_digit($_REQUEST['IdHotpot']))
		return FALSE;
	$IdHotpot = $_REQUEST['IdHotpot'];
	if (!isset($_REQUEST['Score']) || !ctype_digit($_REQUEST['Score']))
		return FALSE;
	$Score = $_REQUEST['Score'];
	if (!isset($_REQUEST['IdPers']) || !ctype_digit($_REQUEST['IdPers']))
		return FALSE;
	$Fini = 0;
	if (!empty($_REQUEST['Fini']))
		$Fini = 1;
	$DateDebut = 0;
	if (!empty($_REQUEST['DateDebut']) && ctype_digit($_REQUEST['DateDebut']) )
		$DateDebut = date('Y-m-d H:i:s',$_REQUEST['DateDebut']/1000);
	$IdPers = $_REQUEST['IdPers'];
	$oHotpotScore = new CHotpotatoesScore($oProjet->oBdd);
	$oHotpotScore->defIdHotpot($IdHotpot);
	$oHotpotScore->defIdPers($IdPers);
	$oHotpotScore->defFini($Fini);
	$oHotpotScore->defScore($Score);
	$oHotpotScore->defDateDebut($DateDebut);
	$oHotpotScore->enregistrer();
}

?>
