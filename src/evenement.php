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

require_once("globals.inc.php");

$url_bMAJ = (isset($_GET["maj"]) ? $_GET["maj"] : FALSE);

if ($url_bMAJ)
	$oProjet = new CProjet();

if (isset($oProjet->oUtilisateur))
	$oProjet->ecrireEvenement(TYPE_EVEN_DECONNEXION);

if (isset($oProjet->oFormationCourante) && is_object($oProjet->oFormationCourante))
{
	include_once(dir_database("evenement.tbl.php"));
	$oEvenDetail = new CEvenement_Detail($oProjet->oBdd,$oProjet->retNumeroUniqueSession(),$oProjet->oFormationCourante->retId());
	$oEvenDetail->sortirFormation();
	$oEvenDetail = NULL;
}

?>