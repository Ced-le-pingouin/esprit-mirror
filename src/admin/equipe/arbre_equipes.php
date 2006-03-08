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
** Date de création .......: 11-02-2003
** Dernière modification ..: 11-02-2003
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

require_once("globals.inc.php");

require_once(dir_database("ids.class.php"));

// *************************************
//
// *************************************

$oProjet = new CProjet();

// *************************************
//
// *************************************

$url_iNiveau   = (empty($HTTP_GET_VARS["NIVEAU"]) ? 0 : $HTTP_GET_VARS["NIVEAU"]);
$url_iIdNiveau = (empty($HTTP_GET_VARS["ID_NIVEAU"]) ? 0 : $HTTP_GET_VARS["ID_NIVEAU"]);

$oIds = new CIds($oProjet->oBdd,$url_iNiveau,$url_iIdNiveau);

$oObj = new CFormation($oProjet->oBdd,$oIds->retIdForm());
$iNbr = $oObj->initModules();

recursive($oObj->aoModules,$url_iNiveau+1);

for ($i=$url_iNiveau+1; $i<=TYPE_SOUS_ACTIV; $i++)
{
	switch ($i)
	{
		case TYPE_MODULE:
			$oObj->initModules();
			
			break;

		case TYPE_UNITE:
			continue;

		case TYPE_SOUS_ACTIV:
		case TYPE_ACTIV:
		case TYPE_RUBRIQUE:
		
		
			break;
		
			break;
		
			break;
	}
	
	echo $oObj->retNom();
}

$oProjet->terminer();

?>
