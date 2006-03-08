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
** Fichier ................: archive.class.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 07/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

// [Esprit:Chat par équipe:Equipe 1 - BA:delta_chat_14][09/12/2003][17:16:59]
// Esprit:Chat par équipe:Equipe 1 - BA:delta_chat_14

function retParticipantsUnique ($v_aoParticipants)
{
	$asParticipants = array();
	
	foreach ($v_aoParticipants as $aoParticipants)
	{
		$bAjouter = TRUE;
		
		foreach ($asParticipants as $oParticipants)
			if ($oParticipants == $aoParticipants)
			{
				$bAjouter = FALSE;
				break;
			}
		
		if ($bAjouter)
			$asParticipants[] = $aoParticipants;
	}
	
	return $asParticipants;
}

class CArchives
{
	var $sRepertoire;
	var $sFiltre;
	
	var $aoArchives;
	var $asPseudos;
	
	function CArchives ($v_sRepArchives,$v_bInitArchives=FALSE,$v_bInitMessages=FALSE)
	{
		$this->sRepertoire = $v_sRepArchives;
		$this->asPseudos = array();
		
		if ($v_bInitArchives)
			$this->initArchives($v_bInitMessages);
	}
	
	function retArchives ($v_bInitMessages=FALSE)
	{
		$iIdxArchive = 0;
		
		$aoArchives = array();
		
		if (!is_dir($this->sRepertoire))
			return $aoArchives;
		
		$handle = opendir($this->sRepertoire);
		
		$sFiltre         = $this->retFiltre();
		$bRechParPseudos = (count($this->asPseudos) > 0);
		
		while (($file = readdir($handle)))
		{
			if ($file == "." || $file == "..")
				continue;
			
			// Ouvrir le fichier
			$sArchive = $this->sRepertoire."/".$file;
			
			if ($fp = fopen($sArchive,"r"))
			{
				// La première ligne doit correspondre au filtre
				if (strstr(fgets($fp,255),$sFiltre))
				{
					// {{{ Rechercher les archives d'une/des personne(s) en particulier
					if ($bRechParPseudos)
					{
						$bOk = FALSE;
						
						while (!feof($fp))
						{
							if (strstr(fgets($fp,100),"[".$this->asPseudos[0]."]"))
							{
								$bOk = !$bOk;
								break;
							}
						}
						
						if (!$bOk)
							continue;
					}
					// }}}
					
					$aoArchives[$iIdxArchive] = new CArchive($sArchive,$v_bInitMessages);
					$iIdxArchive++;
				}
				
				fclose($fp);
			}
		}
		
		closedir($handle);
		
		return $aoArchives;
	}
	
	function initArchives ($v_bInitMessages=FALSE)
	{
		$this->aoArchives = $this->retArchives($v_bInitMessages);
		return $this->retNbArchives();
	}
	
	function retNbArchives ($v_sPseudo=NULL)
	{
		$iNbArchives = (is_array($this->aoArchives) ? count($this->aoArchives) : 0);
		
		if ($iNbArchives > 0 && isset($v_sPseudo))
			$iNbArchives = 0;
		
		return $iNbArchives;
	}
	
	function ajouterPseudo ($v_sPseudo) { $this->asPseudos[] = $v_sPseudo; }
	
	function defFiltre ($v_sFiltre) { $this->sFiltre = $v_sFiltre; }
	function retFiltre () {	return $this->sFiltre; }
	
	function retRepertoire () { return $this->sRepertoire; }
}

class CArchive
{
	var $sNomFichier,$sNomArchive;
	var $aoMessages;
	
	var $sNomPlateforme, $sNomSalon, $sNomEquipe, $sIdentifiant;
	
	var $sHeurePremierMessage, $sHeureDernierMessage;
	
	var $asParticipants, $iNombreParticipants;
	
	function CArchive ($v_sArchive,$v_bInitMessages=FALSE)
	{
		$this->sNomArchive = $v_sArchive;
		$this->sNomFichier = basename($this->sNomArchive);
		
		$this->init();
		
		if ($v_bInitMessages)
			$this->initMessages();
	}
	
	function init ()
	{
		if ($fp = fopen($this->sNomArchive,"r"))
		{
			$sLigne = fgets($fp,4096);
			
			$tmp = explode("]",$sLigne);
			
			@list($this->sNomPlateforme,$this->sNomSalon,$this->sNomEquipe,$this->sIdentifiant) = explode(":",substr($tmp[0],1));
			$this->sDate = substr($tmp[1],1);
			$this->sHeure = substr($tmp[2],1);
			
			fclose($fp);
		}
		
		$this->asParticipants = array();
	}
	
	function initMessages ($v_sPseudo=NULL)
	{
		$iIdxMessage = 0;
		$this->aoMessages = array();
		
		$this->asParticipants = array();
		
		if ($fp = fopen($this->sNomArchive,"r"))
		{
			fgets($fp,4096);
			
			while (!feof($fp))
			{
				$sLigne = fgets($fp,255);
				
				if (empty($sLigne))
					continue;
				
				if (substr($sLigne,0,1) == "[")
				{
					if (isset($v_sPseudo) && !strstr($sLigne,"[{$v_sPseudo}]"))
						continue;
					
					$this->aoMessages[$iIdxMessage] = new CMessageArchive($sLigne);
					
					if (!isset($this->sHeurePremierMessage))
						$this->sHeurePremierMessage = $this->aoMessages[$iIdxMessage]->retHeure();
					
					$this->asParticipants[] = array( 
						$this->aoMessages[$iIdxMessage]->retPseudo(),
						$this->aoMessages[$iIdxMessage]->retNomComplet()
					);
					
					$iIdxMessage++;
				}
				else if ($iIdxMessage > 0 && is_object($this->aoMessages[$iIdxMessage-1]))
					$this->aoMessages[$iIdxMessage-1]->completerMessage($sLigne);
			}
			
			if ($iIdxMessage > 0 && is_object($this->aoMessages[$iIdxMessage-1]))
				$this->sHeureDernierMessage = $this->aoMessages[$iIdxMessage-1]->retHeure();
			
			$this->calculDuree();
			
			$this->asParticipants = retParticipantsUnique($this->asParticipants);
			
			fclose($fp);
		}
		
		return $iIdxMessage;
	}
	
	function calculDuree ()
	{
		$h1 = strtotime($this->sHeureDernierMessage);
		$h2 = strtotime($this->sHeurePremierMessage);
		$iTotal = $h1-$h2;
		$this->sDuree = ($iTotal > 0 ? GMDate("H:i:s",$iTotal) : "< 1 min");
	}
	
	function retNomArchive () { return $this->sNomFichier; }
	function retPlateforme () { return $this->sNomPlateforme; }
	function retSalon () { return (empty($this->sNomSalon) ? "Chat" : $this->sNomSalon); }
	function retEquipe () { return $this->sNomEquipe; }
	function retIdentifiant () { return $this->sIdentifiant; }
	function retDate () { return $this->sDate; }
	function retHeure () { return $this->sHeure; }
	
	function retHeureCourte () { return ereg_replace("([0-9]{2}):([0-9]{2}):([0-9]{2})","\\1:\\2",$this->sHeure); }
	function retHeurePremierMessage () { return $this->sHeurePremierMessage; }
	function retHeureDernierMessage () { return $this->sHeureDernierMessage; }
	function retDuree () { return $this->sDuree; }
	function retNbParticipants () { return (is_array($this->asParticipants) ? count($this->asParticipants) : 0); }
	function retNbMessages () { return (is_array($this->aoMessages) ? count($this->aoMessages) : 0); }
	
	function retParticipants ()
	{
		$sParticipants = NULL;
		
		for ($i=0; $i<count($this->asParticipants); $i++)
		{
			if (($sPseudo = $this->asParticipants[$i][0]) == "-")
				continue;
			
			$sNomComplet = $this->asParticipants[$i][1];
			
			$sParticipants .= (isset($sParticipants) ? ", " : NULL)
				."<span"
				." title=\"{$sNomComplet}\""
				." style=\"cursor: help;\""
				.">{$sPseudo}</span>";
		}
		
		return $sParticipants;
	}
}

/**
 * @class CMessageArchive
 */
class CMessageArchive
{
	var $sHeure, $sNomComplet, $sPseudo, $sMessage=NULL;
	
	function CMessageArchive ($v_sMessageBrut)
	{
		$tmp = explode("]",$v_sMessageBrut);
		
		$this->sHeure = substr($tmp[0],1);
		$this->sNomComplet = trim(substr($tmp[1],1));
		$this->sPseudo = trim(substr($tmp[2],1));
		$this->sMessage = trim($tmp[3]);
		
		if (strlen($this->sPseudo) == 0)
			$this->sNomComplet = $this->sPseudo = "-";
	}
	
	function completerMessage ($v_sMessage)	{ $this->sMessage .= $v_sMessage; }
	function retHeure () { return $this->sHeure; }
	function retHeureCourte () { return ereg_replace("([0-9]{2}):([0-9]{2}):([0-9]{2})","\\1:\\2",$this->sHeure); }
	function retNomComplet ($v_bNoWrap=FALSE) {
		if ($this->sNomComplet == "-")
			return "inconnu";
		else
			return ($v_bNoWrap ? str_replace(" ","&nbsp;",$this->sNomComplet) : $this->sNomComplet);
	}
	function retPseudo () { return $this->sPseudo; }
	function retMessage () { return $this->sMessage; }
}

?>
