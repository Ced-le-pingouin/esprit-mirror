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
** Fichier ................: nvfrm_pg2.php
** Description ............: 
** Date de création .......: 04-06-2002
** Dernière modification ..: 17-04-2003
** Auteurs ................: Filippo Porco
** Emails .................: <ute@umh.ac.be>
**
*/
  
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"2\" width=\"100%\" style=\"font-size : 10pt;\">\n"
	."<tr><td><h5>Etape 2&nbsp;: Choisissez la formation &agrave; copier</h5></td></tr>"
	."<tr>\n"
	."<td align=\"right\" valign=\"middle\">"
	."<b>Filtre&nbsp;:</b>&nbsp;"
	."<select name=\"filtre\" onchange=\"javascript: rechargerListe(this,'filtre');\">\n";

$asFiltre = array("Toutes les formations"
	,"Une formation par type"
	,"Année en cours (".date("01-01-Y")." au ".date("31-12-Y").")"
	,"Deux dernières années (".date("01-01-".(date("Y")-1))." au ".date("31-12-Y").")");

for ($i=0; $i<count($asFiltre); $i++)
	echo "<option name=\"CHOIX_FILTRE\" value=\"".($i+1)."\"".(($filtre == $i+1) ? " selected": NULL).">{$asFiltre[$i]}</option>\n";

$sParamsUrl = "";

echo "</select>\n"
	."</td>\n"
	."</tr>\n"
	."<tr><td>"
	."<iframe name=\"IFRAME_LISTE\" src=\"nvfrm_lst.php\" width=\"100%\" height=\"220px\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"0\" scrolling=\"yes\"></iframe>"
	."</td></tr>\n"
	."</table>\n";
?>
