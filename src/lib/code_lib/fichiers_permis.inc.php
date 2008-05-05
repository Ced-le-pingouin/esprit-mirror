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
** Fichier ................: fichiers_permis.inc.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 13/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

define("LISTE_EXTENSIONS_AUTORISEES","exe,zip,txt,doc,dot,xls,ppt,pdf,jpg,bmp,gif,png,mot,chat,rtf,html,htm,js,css,pps,ins,aam,a4w,a5w,a6p,a7p,sdr,wf1,csv,xml,xsl,wav,swf,jqz,jbc,dwg,mp3");

function validerFichier ($v_sFichierDeposer,$v_sValidExt=NULL,$v_sSeparateurExt=",")
{
	if (empty($v_sValidExt))
		$v_sValidExt = LISTE_EXTENSIONS_AUTORISEES;
	
	if (strstr(substr($v_sValidExt,0,1),"+"))
		$v_sValidExt = LISTE_EXTENSIONS_AUTORISEES
			.$v_sSeparateurExt
			.substr($v_sValidExt,1,strlen ($v_sValidExt));
	
	$asListeExtentions = explode($v_sSeparateurExt,$v_sValidExt);
	
	$iPos = strrpos($v_sFichierDeposer,".");
	
	if ($iPos === FALSE || $iPos == (strlen($v_sFichierDeposer)-1))
		return FALSE;
	
	$sExt = mb_strtolower(substr($v_sFichierDeposer,$iPos+1,strlen($v_sFichierDeposer)),"UTF-8");
	
	return in_array($sExt,$asListeExtentions);
}

?>
