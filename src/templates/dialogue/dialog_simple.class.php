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
** Fichier ................: dialog_simple.class.php
** Description ............:
** Date de création .......: 17/12/2004
** Dernière modification ..: 17/12/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_template("dialogue/dialog_base.class.php"));

$sPageIndex = <<<BLOC_PAGE_INDEX
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
   "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>{html_title}</title>
{html_head}
<script type="text/javascript" language="javascript">
<!--
function oPrincipale() { return top.frames["PRINCIPALE"]; }
function oMenu() { return top.frames["MENU"]; }
//-->
</script>
</head>
<frameset rows="*,23" border="0">
<frame name="PRINCIPALE" src="{frame_src_principale}" frameborder="0" marginwidth="5" marginheight="5" scrolling="no" noresize="noresize">
<frame name="MENU" src="{frame_src_menu}" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</html>
BLOC_PAGE_INDEX;

class CDialogSimple extends CDialogBase
{
	function CDialogSimple ($v_sTitreFenetre)
	{
		CDialogBase::CDialogBase($v_sTitreFenetre);
	}
	
	function afficher ()
	{
		CDialogBase::afficher();
	}
}

?>
