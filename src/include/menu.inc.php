<?php

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
