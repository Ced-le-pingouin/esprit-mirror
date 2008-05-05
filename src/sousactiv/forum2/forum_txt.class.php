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
** Fichier ................: forum_txt.class.php
** Description ............:
** Date de création .......: 25/10/2005
** Dernière modification ..: 31/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_database("forum.tbl.php"));

define("CRLN","\r\n");

class CForumTXT extends CForum
{
	var $aoEquipes;
	
	function CForumTXT (&$v_oBdd,$v_iId)
	{
		$this->CForum($v_oBdd,$v_iId);
		$this->oIds= new CIds($v_oBdd,$this->retTypeNiveau(),$this->retIdNiveau());
	}
	
	function initEquipes ()
	{
		$this->aoEquipes = array();
		$oEquipe = new CEquipe($this->oBdd);
		$iNbEquipes = $oEquipe->initEquipesEx($this->retIdNiveau(),$this->retTypeNiveau(),FALSE);
		$this->aoEquipes = $oEquipe->aoEquipes;
		return $iNbEquipes;
	}
	
	// {{{ Envoyer les informations
	function envoyerNomForum () { echo "+".$this->retNom().CRLN.CRLN.CRLN; }
	
	function envoyerSujets ($v_iIdEquipe=NULL)
	{
		$iIdMod = $this->oIds->retIdMod();
		
		$this->initSujets($v_iIdEquipe);
		
		foreach ($this->aoSujets as $oSujet)
		{
			echo CRLN
				."+++"
				.$oSujet->retNom()
				.CRLN
				.CRLN;
			
			// {{{ Envoyer les message de ce sujet
			$oSujet->initMessages($v_iIdEquipe);
			
			// Inverser l'ordre des messages (ordre chronologique)
			$oSujet->aoMessages = array_reverse($oSujet->aoMessages);
			
			foreach ($oSujet->aoMessages as $oMessage)
			{
				$oMessage->initAuteur();
				$bTuteur = $oMessage->oAuteur->verifTuteur($iIdMod);
				
				echo "*"
					.($bTuteur ? "T" : "E")
					."-"
					.strtoupper($oMessage->oAuteur->retNom())
					." "
					.ucfirst(mb_strtolower($oMessage->oAuteur->retPrenom(),"UTF-8"))
					.CRLN;
				
				echo $oMessage->retDate("d/m/y (H:i:s)")
					.CRLN
					.CRLN;
				
				echo enleverBaliseMeta($oMessage->retMessage())
					.CRLN;
				
				echo CRLN;
			}
			// }}}
		}
	}
	// }}}
	
	function exporter ()
	{
		if (MODALITE_POUR_TOUS != $this->retModalite())
			$this->initEquipes();
		
		// Envoyer le nom du forum
		$this->envoyerNomForum();
		
		// Envoyer les sujets du forum
		if (empty($this->aoEquipes))
			$this->envoyerSujets();
		else
			foreach ($this->aoEquipes as $oEquipe)
			{
				// Envoyer le nom de l'équipe
				echo CRLN."++"
					.$oEquipe->retNom()
					.CRLN;
				
				$this->envoyerSujets($oEquipe->retId());
			}
	}
}

?>
