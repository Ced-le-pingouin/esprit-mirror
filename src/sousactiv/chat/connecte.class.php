<?php

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
