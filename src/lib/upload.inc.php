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
** Fichier ................: upload.inc.php
** Description ............: 
** Date de création .......: 06/10/2004
** Dernière modification ..: 20/10/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

function retNomFichierUnique ($v_sNomFichier,$v_sNomRepertoire=NULL,$v_bAjouterZero=TRUE,$v_bAssocierRepertoire=FALSE)
{ // v1.01
	// Nettoyer le fichier de caractères indésirable
	$v_sNomFichier = retNomFichierPropre($v_sNomFichier);
	
	$pos  = strrpos($v_sNomFichier,".");
	
	if ($pos>0)
	{
		$sNomFichier = substr($v_sNomFichier,0,$pos);
		$sExtFichier = substr($v_sNomFichier,$pos,(strlen($v_sNomFichier)-$pos));
	}
	else
	{
		$sNomFichier = $v_sNomFichier;
		$sExtFichier = "";
	}
	
	$iIdxNumFichier = 1;
	
	while (1)
	{
		$sNbZeros = "";
		
		if ($v_bAjouterZero)
		{
			if ($iIdxNumFichier < 10) $sNbZeros = "000";
			else if ($iIdxNumFichier < 100) $sNbZeros = "00";
			else if ($iIdxNumFichier < 1000) $sNbZeros = "0";
		}
		
		// Composer le nom unique du fichier
		$sNomFichierUnique = ($v_bAssocierRepertoire ? $v_sNomRepertoire : NULL)
			.$sNomFichier."-".$sNbZeros.$iIdxNumFichier.$sExtFichier;
		
		if (!file_exists($v_sNomRepertoire.$sNomFichierUnique))
			break;
		
		$iIdxNumFichier++;
	}
	
	return ($sNomFichierUnique);
}

function retNomFichierReel ($v_sNomFichier) { return ereg_replace("-([0-9]){4}\.",".",$v_sNomFichier); }
function retNomFichierPropre ($v_sNomFichier) { return stripslashes(trim($v_sNomFichier)); }

?>

