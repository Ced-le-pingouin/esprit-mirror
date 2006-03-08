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
** Fichier ................: forum_csv.class.php
** Description ............:
** Date de création .......: 11/10/2005
** Dernière modification ..: 25/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once(dir_include("csv.class.php"));
require_once(dir_locale("globals.lang"));

class CForumCSV extends CCSV
{
	var $oBdd;
	var $oIds;
	
	var $oForum;
	
	var $aoEquipes;
	
	function CForumCSV (&$v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->oForum = new CForum($this->oBdd,$v_iId);
		$this->oIds= new CIds($this->oBdd,$this->oForum->retTypeNiveau(),$this->oForum->retIdNiveau());
	}
	
	function initEquipes ()
	{
		$this->aoEquipes = array();
		$oEquipe = new CEquipe($this->oForum->oBdd);
		$iNbEquipes = $oEquipe->initEquipesEx($this->oForum->retIdNiveau(),$this->oForum->retTypeNiveau(),FALSE);
		$this->aoEquipes = $oEquipe->aoEquipes;
		return $iNbEquipes;
	}
	
	function envoyerNomForum () { echo "\"".$this->doubler_guillemets($this->oForum->retNom())."\"\n"; }
	
	function envoyerSujets ($v_iIdEquipe=NULL)
	{
		$iIdMod = $this->oIds->retIdMod();
		
		$this->oForum->initSujets($v_iIdEquipe);
		
		foreach ($this->oForum->aoSujets as $oSujet)
		{
			echo "\n";
			echo "\"".$this->doubler_guillemets($oSujet->retNom())."\""
				."\n";
			
			// {{{ Messages du sujet
			$oSujet->initMessages($v_iIdEquipe);
			$oSujet->aoMessages = array_reverse($oSujet->aoMessages);
			
			echo "\"N° du message\";\"Pseudo\";\"Statut\";\"Date\";\"Heure\";\"Message\"\n";
			
			foreach ($oSujet->aoMessages as $iNumMessage => $oMessage)
			{
				$oMessage->initAuteur();
				$bTuteur = $oMessage->oAuteur->verifTuteur($iIdMod);
				
				echo "\"".($iNumMessage+1)."\";"
					."\"".$oMessage->oAuteur->retPseudo()."\";"
					."\"".(PERSONNE_SEXE_FEMININ == $oMessage->oAuteur->retSexe()
						? ($bTuteur ? TXT_STATUT_TUTEUR_F : TXT_STATUT_ETUDIANT_F)
						: ($bTuteur ? TXT_STATUT_TUTEUR_M : TXT_STATUT_ETUDIANT_M))
					."\";"
					."\"".$oMessage->retDate("d/m/y")."\";"
					."\"".$oMessage->retDate("H:i")."\"";
				
				// {{{ Messages
				foreach (preg_split("/\015\012|\015|\012/",$oMessage->retMessage()) as $iNbMessages => $sMessage)
				{
					$sMessage = enleverBaliseMeta($sMessage);
					
					if (strlen($sMessage))
						echo ($iNbMessages > 0 ? "\"\";\"\";\"\";\"\";\"\"" : NULL)
							.";\""
							.$this->doubler_guillemets($sMessage)
							."\"\n";
				}
				// }}}
				
				echo "\n";
			}
		}
	}
	
	function exporter ()
	{
		$bModaliteParEquipe = (MODALITE_POUR_TOUS != $this->oForum->retModalite());
		
		if ($bModaliteParEquipe)
			$this->initEquipes();
		
		// Nom du forum
		$this->envoyerNomForum();
		
		// {{{ Sujets du forum
		if ($bModaliteParEquipe)
		{
			foreach ($this->aoEquipes as $oEquipe)
			{
				echo "\n\n\"".$this->doubler_guillemets($oEquipe->retNom())."\"\n";
				$this->envoyerSujets($oEquipe->retId());
			}
		}
		else
			$this->envoyerSujets();
		// }}}
	}
}

?>
