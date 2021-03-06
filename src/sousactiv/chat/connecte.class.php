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

class CConnecte
{
	var $sPlateforme,$sNomSalon,$sNomEquipe;
	
	var $sNomFichier = "connecte.txt";
	var $sRecherche;
	
	function CConnecte($v_sPlateforme,$v_sNomSalon,$v_sNomEquipe=NULL)
	{
		$this->sPlateforme = $v_sPlateforme;
		$this->sNomSalon = $v_sNomSalon;
		$this->sNomEquipe = $v_sNomEquipe;
		
		$this->sRecherche = $this->sPlateforme
			.":".$this->sNomSalon
			.":".$this->sNomEquipe
			.":";
	}
	
	function rechercher()
	{
		$sLigne = NULL;
		
		if (is_file($this->sNomFichier))
		{
			$fp = fopen($this->sNomFichier,"r");
			
			while (!feof($fp))
			{
				$sLigne = fgets($fp,1024);
				if (strstr($sLigne,$this->sRecherche))
					break;
				$sLigne = NULL;
			}
			
			fclose($fp);
		}
		
		return $sLigne;
	}
	
	function ajouterLigne($v_iIdPers)
	{
		$sTexte = $this->sRecherche."{$v_iIdPers}\n";
		
		$fp = fopen($this->sNomFichier,"a");
		fwrite($fp,$sTexte);
		fclose($fp);
	}
	
	function supprimerConnecte($v_iIdPers)
	{
		$asLignes = file($this->sNomFichier);
		
		$fp = fopen($this->sNomFichier,"w");
		
		for ($i=0; $i<count($asLignes); $i++)
		{
			$sLigne = trim($asLignes[$i]);
			
			if (empty($sLigne))
				continue;
			
			if (strstr($sLigne,$this->sRecherche))
			{
				$iIdPers = substr($sLigne,strrpos($sLigne,":")+1);
				$aiIdPers = explode(",",$iIdPers);
				
				$sIdsPers = NULL;
				
				foreach ($aiIdPers as $iIdPers)
					if ($iIdPers != $v_iIdPers)
						$sIdsPers .= (isset($sIdsPers) ? "," : NULL).$iIdPers;
				
				// Dans le cas où il n'y a plus de personne connectée
				// nous devons supprimer la ligne du salon du fichier
				if (!isset($sIdsPers))
					continue;
				
				$sLigne = $this->sRecherche.$sIdsPers;
			}
			
			fwrite($fp,$sLigne."\n");
		}
		
		fclose($fp);
	}
	
	function ajouterConnecte($v_iIdPers)
	{
		if ($this->rechercher() != NULL)
		{
			$asLignes = file($this->sNomFichier);
			
			$fp = fopen($this->sNomFichier,"w");
			
			for ($i=0; $i<count($asLignes); $i++)
			{
				$sLigne = trim($asLignes[$i]);
				
				if (empty($sLigne))
					continue;
				
				if (strstr($sLigne,$this->sRecherche) &&
					!ereg("([:|,]$v_iIdPers)",$sLigne))
					$sLigne .= ",".$v_iIdPers;
				
				fwrite($fp,$sLigne."\n");
			}
			
			fclose($fp);
		}
		else
		{
			$this->ajouterLigne($v_iIdPers);
		}
	}
	
	function retListeConnectes()
	{
		$sLigne = $this->rechercher();
		return explode(",",substr($sLigne,strrpos($sLigne,":")+1));
	}
}

?>
