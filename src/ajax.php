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
//                          Grenoble UniversitÃ©s.

/**
 * @file	ajax.php
 * 
 * RÃ©agit aux appels AJAX
 * 
 */

require_once("globals.inc.php");
require_once(dir_database("hotpotatoes_score.tbl.php"));

$oProjet = new CProjet();

$action = stripslashes($_REQUEST['action']);
if (function_exists($action)) {
	call_user_func($action);
}

/** Affecte un score Ã  un utilisateur pour un exo Hotpot.
 @details Les donnÃ©es sont lues dans $_REQUEST
 @return FALSE en cas d'erreur
*/
function hotpotScore( ) {
	global $oProjet;
	if (empty($_REQUEST['IdHotpot']) || !ctype_digit($_REQUEST['IdHotpot']))
		return FALSE;
	$IdHotpot = $_REQUEST['IdHotpot'];
	if (!isset($_REQUEST['Score']) || !preg_match('/^-?\d+$/',$_REQUEST['Score']))
		return FALSE;
	$Score = $_REQUEST['Score'];
	if (!isset($_REQUEST['IdPers']) || !ctype_digit($_REQUEST['IdPers']))
		return FALSE;
	$DateDebut = 0;
	if (!empty($_REQUEST['DateDebut']) && ctype_digit($_REQUEST['DateDebut']) )
		$DateDebut = date('Y-m-d H:i:s',$_REQUEST['DateDebut']/1000);
	if (!empty($_REQUEST['DateFin']) && ctype_digit($_REQUEST['DateFin']) )
		$DateFin = date('Y-m-d H:i:s',$_REQUEST['DateFin']/1000);
	else
		$DateFin = date('Y-m-d H:i:s');

	if (empty($_REQUEST['IdSessionExercice']))
		return false;
	$IdSessionExercice = $_REQUEST['IdSessionExercice'];

	if (empty($_REQUEST['NumeroPage']))
		return false;
	$iNumeroPage = $_REQUEST['NumeroPage'];

	/**
	 * Le nombre d'exercice est défini par une variable javascript utilisée dans le fichier HP.
	 * Cette variable est présente en particulier dans les exercices de type Quizz (1 fichier HTML, mais plusieurs questions)
	 * Si elle n'est pas définie (ou égale à 0), on insérera -1 comme valeur.
	 */
	if (empty($_REQUEST['NombreTotal']) || !ctype_digit($_REQUEST['NombreTotal']) || $_REQUEST['NombreTotal'] == 0)
		$iNombreQuestions = -1;
	else $iNombreQuestions = $_REQUEST['NombreTotal'];
echo "test";
	$IdPers = $_REQUEST['IdPers'];
	$oHotpotScore = new CHotpotatoesScore($oProjet->oBdd);
	$oHotpotScore->defIdHotpot($IdHotpot);
	$oHotpotScore->defIdPers($IdPers);
	$oHotpotScore->defScore($Score);
	$oHotpotScore->defDateDebut($DateDebut);
	$oHotpotScore->defDateFin($DateFin);
	$oHotpotScore->defIdSessionExercice($IdSessionExercice);
	$oHotpotScore->defNombreQuestion($iNombreQuestions);
	$oHotpotScore->defNumeroPage($iNumeroPage);
	if ($oHotpotScore->ExerciceFait($IdPers,$IdHotpot,$IdSessionExercice,$iNumeroPage) == NULL)
		$oHotpotScore->enregistrer();
}

?>
