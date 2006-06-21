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
	
	function envoyerNomForum ()
	{
		$oFormation = new CFormation($this->oBdd,$this->oIds->retIdForm());
		$oModule = new CModule($this->oBdd,$this->oIds->retIdMod());
		
		$oRubrique = (TYPE_SOUS_ACTIVITE == $this->oForum->retTypeNiveau()
			? new CModule_Rubrique($this->oBdd,$this->oIds->retIdRubrique())
			: NULL);
			
		echo "\""
			.$this->doubler_guillemets($oFormation->retNom())
			." / "
			.$this->doubler_guillemets($oModule->retNom())
			.(isset($oRubrique)
				? " / ".$this->doubler_guillemets($oRubrique->retNom())
				: NULL)
			."\""
			."\n"
			."\""
			.$this->doubler_guillemets($this->oForum->retNom())
			."\""
			."\n";
	}
	
	function envoyerEntetes ()
	{
		echo "\n"
			."\"ID forum\""
			.";\"Nom du forum (".$this->doubler_guillemets($this->oForum->retTexteModalite()).")\""
			.";\"ID sujet\""
			.";\"Sujet\""
			.";\"ID auteur\""
			.";\"Nom\""
			.";\"Prénom\""
			.";\"ID statut\""
			.";\"Statut\""
			.";\"Date\""
			.";\"Heure\""
			.";\"N° du message\""
			.";\"ID message\""
			.";\"Message\""
			."\n";
	}
	
	function envoyerSujets ($v_oEquipe=NULL)
	{
		$iIdMod = $this->oIds->retIdMod();
		$iIdEquipe = (isset($v_oEquipe) ? $v_oEquipe->retId() : NULL);
		
		$iIdForum = $this->oForum->retId();
		$sNomForum = $this->doubler_guillemets($this->oForum->retNom());
		
		$this->oForum->initSujets($iIdEquipe);
		
		foreach ($this->oForum->aoSujets as $oSujet)
		{
			$iIdSujet = $oSujet->retId();
			$sNomSujet = $this->doubler_guillemets($oSujet->retNom());
			
			// {{{ Messages du sujet
			$oSujet->initMessages($iIdEquipe);
			$oSujet->aoMessages = array_reverse($oSujet->aoMessages);
			
			foreach ($oSujet->aoMessages as $iNumMessage => $oMessage)
			{
				$oMessage->initAuteur();
				$bTuteur = $oMessage->oAuteur->verifTuteur($iIdMod);
				
				echo "\"{$iIdForum}\""
					.";\"".(isset($v_oEquipe) ? $this->doubler_guillemets($v_oEquipe->retNom()) : NULL)."\""
					.";\"{$iIdSujet}\""
					.";\"{$sNomSujet}\""
					.";\"".$oMessage->oAuteur->retId()."\""
					.";\"".$oMessage->oAuteur->retNom()."\""
					.";\"".$oMessage->oAuteur->retPrenom()."\""
					.";\"".($bTuteur ? STATUT_PERS_TUTEUR : STATUT_PERS_ETUDIANT)."\""
					.";\"".(PERSONNE_SEXE_FEMININ == $oMessage->oAuteur->retSexe()
						? ($bTuteur ? TXT_STATUT_TUTEUR_F : TXT_STATUT_ETUDIANT_F)
						: ($bTuteur ? TXT_STATUT_TUTEUR_M : TXT_STATUT_ETUDIANT_M))
					."\""
					.";\"".$oMessage->retDate("d/m/y")."\""
					.";\"".$oMessage->retDate("H:i")."\""
					.";\"".($iNumMessage+1)."\""
					.";\"".$oMessage->retId()."\"";
				
				// {{{ Messages
				/*foreach (preg_split("/\015\012|\015|\012/",$oMessage->retMessage()) as $iNbMessages => $sMessage)
				{
					$sMessage = enleverBaliseMeta($sMessage);
					
					if (strlen($sMessage))
						echo ($iNbMessages > 0 ? "\"\";\"\";\"\";\"\";\"\"" : NULL)
							.";\""
							.$this->doubler_guillemets($sMessage)
							."\"\n";
				}*/
				// }}}
				
				// {{{ Messages
				$sMessage = enleverBaliseMeta(preg_replace("/\015\012|\015/","\012",$oMessage->retMessage()));
				
				if (strlen($sMessage))
					echo ";\" "
						.$this->doubler_guillemets($sMessage)
						."\"\n";
				// }}}
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
		$this->envoyerEntetes();
		
		// {{{ Sujets du forum
		if ($bModaliteParEquipe)
			foreach ($this->aoEquipes as $oEquipe)
				$this->envoyerSujets($oEquipe);
		else
			$this->envoyerSujets();
		// }}}
	}
}

?>
