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
** Fichier ................: 
** Description ............: 
** Date de création .......: 11-07-2002
** Dernière modification ..: 17-07-2002
** Auteur .................: Fili//0: Porco
** Email ..................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

require_once("globals.inc.php");

function menu_admin ($sConteneur="&nbsp;")
{
	$dir = dir_theme();

	echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\">\n"
		."<tr>\n"
		."<td background=\"{$dir}top_left.gif\"><img src='{$dir}top_left.gif' width='10' height='10'></td>\n"
		."<td valign=\"bottom\" background=\"{$dir}inner-bdr-top.gif\"><img src='{$dir}inner-bdr-top.gif' width='10' height='10'></td>\n"
		."<td background=\"{$dir}top_right.gif\" width=\"10\"><img src='{$dir}top_right.gif' width='10' height='10'></td>\n"
		."</tr>"
		."<tr>"
		."<td background=\"{$dir}inner-bdr-left.gif\" width=\"10\"><img src='{$dir}inner-bdr-left.gif' width='10' height='10'></td>\n"
		."<td bgcolor=\"#FFFFE7\">{$sConteneur}</td>\n"
		."<td background=\"{$dir}inner-bdr-right.gif\" width=\"10\"><img src='{$dir}inner-bdr-right.gif' width='10' height='10'></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td background=\"{$dir}bottom_left.gif\"><img src='{$dir}bottom_left.gif' width='10' height='10'></td>\n"
		."<td valign=\"bottom\" background=\"{$dir}inner-bdr-bottom.gif\"><img src='{$dir}inner-bdr-bottom.gif' width='10' height='10'></td>\n"
		."<td background=\"{$dir}bottom_right.gif\" width=\"10\"><img src='{$dir}bottom_right.gif' width='10' height='10'></td>\n"
		."</tr>"
		."</table>\n";
}

?>
