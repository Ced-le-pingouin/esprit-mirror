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

require_once ("globals.inc.php");

if (isset($_GET["corriger"]))
	$sMenu = "<td"
		." align=\"right\">"
		."<a"
		." href=\"javascript: top.location=top.location; void(0);\""
		." onclick=\"blur()\""
		.">Recommencer</a>"
		."&nbsp;|&nbsp;"
		."<a href=\"javascript: top.close();\">Annuler</a>"
		."</td>";
else
	$sMenu = "<td"
		." align=\"right\">"
		."<a"
		." href=\"javascript: top.frames['Principal'].Attente_pour_Envoi(); top.frames['Principal'].document.forms[0].submit(); top.opener.top.frames['Principal'].location.reload(true); void(0);\""
		." onclick=\"blur()\""
		.">Valider</a>"
		."&nbsp;|&nbsp;"
		."<a href=\"javascript: top.close();\">Annuler</a>"
		."</td>";

$oTpl = new Template(dir_theme("dialog-menu.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->effacer();

$oTpl->remplacer("{menu}",$sMenu);

$oTpl->afficher();

?>

