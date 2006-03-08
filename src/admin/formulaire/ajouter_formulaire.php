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
$oProjet = new CProjet();

//************************************************
//*       Récupération des variables             *
//************************************************

if (isset($HTTP_GET_VARS))
{
	$v_iIdFormulaire = $HTTP_GET_VARS['idform'];
}
else if (isset($HTTP_POST_VARS))
{
	$v_iIdFormulaire = $HTTP_POST_VARS['idform'];
}
else
{
	echo "Erreur dans le passage des paramètres";
}

$iIdPers = $oProjet->oUtilisateur->retId();

$oFormulaire = new CFormulaire($oProjet->oBdd);
$v_iIdFormulaire = $oFormulaire->ajouter($iIdPers);


echo "<html>";
echo "<head>";
echo "<script src=\"selectionobj.js\" type=\"text/javascript\">";
echo "</script>";
echo "</head>";

//Le javascript permet de recharger les frames FORMFRAMELISTE et FORMFRAMEMENU sans intervention de l'utilisateur
echo "<body onLoad=\"rechargerliste(0,$v_iIdFormulaire)\" onunload=\"rechargermenugauche()\">";
echo "</body></html>";
?>
