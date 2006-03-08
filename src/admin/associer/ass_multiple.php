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

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForm   = (empty($HTTP_GET_VARS["ID_FORM"]) ? 0 : $HTTP_GET_VARS["ID_FORM"]);
$url_iIdStatut = (empty($HTTP_GET_VARS["STATUT_PERS"]) ? 0 : $HTTP_GET_VARS["STATUT_PERS"]);

// ---------------------
// Initialisations
// ---------------------
switch ($url_iIdStatut)
{
	case STATUT_PERS_CONCEPTEUR:
		$sTitreOngletPersonnes = _("Liste&nbsp;des&nbsp;concepteurs");
		break;
	case STATUT_PERS_TUTEUR:
		$sTitreOngletPersonnes = _("Liste&nbsp;des&nbsp;tuteurs");
		break;
	case STATUT_PERS_ETUDIANT:
		$sTitreOngletPersonnes = _("Liste&nbsp;des&nbsp;&eacute;tudiants");
		break;
		
	default:
		$sTitreOngletPersonnes = "&nbsp;";
}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("ass_multiple.tpl");

$oTpl->remplacer("{formation->id}",$url_iIdForm);
$oTpl->remplacer("{statut->id}",$url_iIdStatut);

$oTpl->remplacer("{titre->onglet->personnes}",$sTitreOngletPersonnes);

$oTpl->afficher();

?>

