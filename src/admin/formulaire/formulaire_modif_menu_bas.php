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

//Ceci est ajouté uniquement pour pouvoir effectuer un contrôle de l'utilisateur
require_once("globals.inc.php");
$oProjet = new CProjet();
if ($oProjet->verifPermission('PERM_MOD_FORMULAIRES'))
{

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

echo "<html>\n";

echo "<head>\n";
echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n";
echo "<script type=\"text/javascript\">\n";
echo "<!--\n";

echo "\nfunction appliquer()\n";
echo "{if (parent.frames['FORMFRAMEMODIF'].document.forms.length > 0)"; //Teste si le formulaire existe si oui il execute le submit
echo "{ parent.frames['FORMFRAMEMODIF'].document.forms['formmodif'].submit(); } }\n";

echo "\nfunction annuler()\n"; 
echo "{if (parent.frames['FORMFRAMEMODIF'].document.forms.length > 0)"; //Teste si le formulaire existe si oui il execute le submit
echo "{ parent.frames['FORMFRAMEMODIF'].document.forms['formmodif'].reset(); } }\n";
echo "//-->\n";
echo "</script>\n\n";

//CSS
echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">\n";
//FIN CSS
echo "</head>\n";


echo "<body class=\"menumodifbas\">\n";
echo "<TABLE style=\"border-top:1px solid black;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\">\n";

echo "<tr><td style=\"text-align : left\">&nbsp\n";

echo "</td><td style=\"text-align : right\">";


echo "<a href=\"javascript: appliquer();\">Appliquer les changements</a>\n";
echo " | ";

echo "<a href=\"javascript: annuler();\">Annuler</a>\n";

echo "&nbsp</td></tr>\n";
echo "</TABLE>\n";
echo "</body>\n";
echo "</html>\n";
}//Verification de la permission d'utiliser le concepteur de formulaire
?>

