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
** Fichier ................: info_bulle.php
** Description ............:
** Date de création .......: 10/06/2004
** Dernière modification ..: 10/06/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education (UTE)
**
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

$url_iType      = $HTTP_GET_VARS["type"];
$url_iIdType    = $HTTP_GET_VARS["idType"];
$url_sInfoBulle = (empty($HTTP_GET_VARS["info_bulle"]) ? NULL : trim($HTTP_GET_VARS["info_bulle"]));

switch ($url_iType)
{
	case TYPE_SOUS_ACTIVITE:
		$oSousActiv = new CSousActiv($oProjet->oBdd,$url_iIdType);
		
		if (isset($url_sInfoBulle))
		{
			$oSousActiv->defInfoBulle($url_sInfoBulle);
			include_once(dir_include("fermer_fenetre.html"));
			exit();
		}
		else
		{
			$sInfoBulle = $oSousActiv->retInfoBulle();
		}
		
		unset($oSousActiv);
		
		break;
}

$oTpl = new Template("info_bulle.tpl");

$oTpl->remplacer("{menu}",($url_iType > 0 && $url_iIdType > 0 ? "?menu=1" : NULL));
$oTpl->remplacer("{type->id}",$url_iType);
$oTpl->remplacer("{idType->id}",$url_iIdType);

$oBloc_Info_Bulle = new TPL_Block("BLOCK_INFO_BULLE",$oTpl);
$oSet_Info_Bulle = $oTpl->defVariable("SET_INFO_BULLE");

$oTplOnglet = new Template(dir_theme("onglet/onglet_tab.tpl",FALSE,TRUE));
$oSet_Onglet = $oTplOnglet->defVariable("SET_ONGLET");

$oBloc_Info_Bulle->ajouter($oSet_Onglet);
$oBloc_Info_Bulle->remplacer("{onglet->titre}","Info bulle");
$oBloc_Info_Bulle->remplacer("{onglet->texte}",$oSet_Info_Bulle);
$oBloc_Info_Bulle->remplacer("{info_bulle->texte}",$sInfoBulle);
$oBloc_Info_Bulle->afficher();

$oTpl->afficher();

$oProjet->terminer();
?>

