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
** Fichier ................: tchatche_connectes.swf.php
** Description ............:
** Date de cr�ation .......: 20/01/2005
** Derni�re modification ..: 20/01/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$sAdresseComplete = dir_root_plateform($HTTP_POST_VARS["adresseComplete"]);

echo "&log={$sAdresseComplete}&";

if (is_file("{$sAdresseComplete}/delta_chat_58"))
{
	echo "&log=";
	$fp = fopen("{$sAdresseComplete}/delta_chat_58","r");
	fpassthru($fp);
	flush();
	echo "&";
}
?>
