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

if (isset($_GET))
{
	$v_iIdObjForm = $_GET['idobj'];
	$v_iIdFormulaire = $_GET['idformulaire'];
}
else if (isset($_POST))
{
	$v_iIdObjForm = $_POST['idobj'];
	$v_iIdFormulaire = $_POST['idformulaire'];
}
else
{
	$v_iIdObjForm = 0;
	$v_iIdFormulaire = 0;
}

//echo "<br>v_iIdTypeObj".$v_iIdTypeObj;
//echo "<br>v_iIdFormulaire : ".$v_iIdFormulaire;

echo "<html>\n";
echo "<head>\n";
echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n";
echo "<script src=\"selectionobj.js\" type=\"text/javascript\">";
echo "</script>\n";

//CSS
echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">";
//FIN CSS
echo "</head>\n";

echo "<body class=\"modif\">";

if ($v_iIdObjForm > 0)
{
	$iIdNvObjForm = CopierUnObjetFormulaire($oProjet->oBdd, $v_iIdObjForm, $v_iIdFormulaire, "max");
	
	echo "<script>\n";
	echo "rechargerliste($iIdNvObjForm,$v_iIdFormulaire)\n";
	echo "</script>\n";

}
else
{
	echo "Erreur: Impossible de copier l'objet";
}
echo "</body>\n";
echo "</html>\n";
?>
