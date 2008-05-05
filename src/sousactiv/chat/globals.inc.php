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

require_once("../../globals.inc.php");

define("CID","delta_chat_");

function retIdUniqueChat ($v_iIdChat,$v_sNomEquipe=NULL) { return ":{$v_sNomEquipe}:".CID."{$v_iIdChat}"; }

function retHautStatut ($v_iStatutUtilisateur)
{
	if ($v_iStatutUtilisateur == STATUT_PERS_ADMIN ||
		$v_iStatutUtilisateur == STATUT_PERS_RESPONSABLE_POTENTIEL ||
		$v_iStatutUtilisateur == STATUT_PERS_RESPONSABLE ||
		$v_iStatutUtilisateur == STATUT_PERS_TUTEUR)
		return TRUE;
	else
		return FALSE;
}
?>
