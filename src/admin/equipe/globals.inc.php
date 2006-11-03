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

require_once ("../../globals.inc.php"); 

define("PERSONNE_INSCRITE",0);
define("PERSONNE_DANS_EQUIPE",1);
define("PERSONNE_SANS_EQUIPE",2);

function format_sous_titre ($v_sType,$v_sSousTitre)
{
	return "&nbsp;&nbsp;"
		."<b>".mb_convert_encoding($v_sType,"HTML-ENTITIES","UTF-8")."</b>"
		."&nbsp;"
		."<img src=\"".dir_theme("signet-2.gif")."\" border=\"0\">"
		."&nbsp;"
		.mb_convert_encoding($v_sSousTitre,"HTML-ENTITIES","UTF-8");
}

// *************************************
// Classe pour lire/enregistrer les modéles équipes
// *************************************

class CModele 
{
	var $sAuteur;
	var $sDateCreation;
	var $sDescription;
	var $aiIdPers;
	var $asNomEquipe;
	
	function CModele ($v_sAuteur=NULL,$v_sDateCreation=NULL)
	{
		$this->sAuteur = $v_sAuteur;
		$this->sDateCreation = $v_sDateCreation;
		$this->asNomEquipe = array();
		$this->aiIdPers = array();
	}
		
	function ajouterEquipe ($v_sNomEquipe)
	{
		$this->asNomEquipe[] = $v_sNomEquipe;
	}
	
	function ajouterMembres ($v_aiIdPers)
	{
		$this->aiIdPers[] = $v_aiIdPers;
	}
}

?>
