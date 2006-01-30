<?php

/*
** Fichier ................: template.inc
** Description ............: Déclaration des classes pour les templates.
** Date de création .......: 21/03/2002
** Dernière modification ..: 04/11/2005
** Auteurs ................: Jérôme TOUZÉ <webmaster@guepard.org>
**                           Cédric FLOQUET <cedric.floquet@umh.ac.be>
**                           Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

class Template
{
	var $data = NULL;
	
	function Template ($v_sTplFichier)
	{
		if ($v_sTplFichier && is_file($v_sTplFichier))
		{
			if ($fp = @fopen($v_sTplFichier,"r"))
			{
				while ($sLigne = fgets($fp,4096))
					$this->data .= $sLigne;
				fclose($fp);
			}
		}
		else
			$this->data = "";
	}
	
	function ajouterTemplate ($v_sTplFichier) { $this->Template($v_sTplFichier); }
	
	function defVariable ($looptag)
	{
		$sVariable = NULL;
		
		if(strpos($this->data,"[$looptag-]"))
		{
			$debut = strpos($this->data,"[$looptag+]");
			$fin = strpos($this->data,"[$looptag-]") + strlen("[$looptag-]"); // + taille de la balise finale
			$sVariable = substr($this->data,$debut,($fin-$debut));
			//effacement du bloc
			$this->data = str_replace($sVariable,"",$this->data);
			// enlevement des balises + et - ds le tableau
			$sVariable = str_replace("[$looptag+]","",$sVariable);
			$sVariable = str_replace("[$looptag-]","",$sVariable);
			$sVariable = trim($sVariable);
		}
		
		return $sVariable;
	}
	
	function remplacer ($in,$out) { $this->data = str_replace($in,$out,$this->data); }
	
	function afficher ()
	{
		// {{{ Ajouter par Fil
		$asRechercher = array("racine://","admin://","commun://","theme://","javascript://","lib://");
		$asRemplacer = array(dir_root_plateform(NULL,FALSE),dir_admin(),dir_theme_commun(),dir_theme(),dir_javascript(),dir_lib());
		$this->data = str_replace($asRechercher,$asRemplacer,$this->data);
		// }}}
		echo $this->retDonnees();
	}
	
	// {{{ Ajouter par Fil
	function retDonnees () { return trim($this->data); }
	function caracteres () { return strlen($this->data); }
	// }}}
}

class TPL_Block
{
	var $looptag;
	var $data;
	var $template_parent;
	var $copy_data;
	var $iNbLoops;
	var $asData;
	
	function TPL_Block ($looptag,&$template)
	{
		$this->looptag = $looptag;
		$this->template_parent = &$template;
		$this->extraire();
	}
	
	function beginLoop () { $this->copy_data = $this->data; $this->data = ""; $this->iNbLoops = 0; }
	
	function nextLoop ()
	{
		if ($this->data != "")
			$this->asData[] = $this->data;
		$this->data = $this->copy_data;
		$this->iNbLoops++;
	}
	
	function countLoops () { return $this->iNbLoops; }
	
	function remplacer ($in,$out) { $this->data = str_replace($in,$out,$this->data); }
	
	function extraire ()
	{
		if (strpos($this->template_parent->data,"[$this->looptag-]"))
		{
			$debut = strpos($this->template_parent->data,"[".$this->looptag."+]");
			$fin = strpos($this->template_parent->data,"[".$this->looptag."-]",$debut) + strlen("[".$this->looptag."-]"); // + taille de la balise finale
			$this->data = substr($this->template_parent->data,$debut,($fin-$debut));
			$this->template_parent->data = str_replace($this->data,"[$this->looptag"."_tmp]",$this->template_parent->data);
			// enlevement des balises + et - ds le tableau
			$this->data = str_replace("[$this->looptag+]","",$this->data);
			$this->data = str_replace("[$this->looptag-]","",$this->data);
		}
	}
	
	// {{{ Ajouter par Fil
	function cycle ($v_iCycle=NULL)
	{
		if (strpos($this->data,"[CYCLE:"))
		{
			$iNbBoucles = $this->iNbLoops-1;
			$debut = strpos($this->data,"[CYCLE:");
			$fin = strpos($this->data,"]",$debut);
			$sVariable = substr($this->data,$debut,($fin-$debut)+1);
			$asListeCycles = explode("|",substr($sVariable,7,-1));
			$iNbCycles = count($asListeCycles);
			$iCycle = (isset($v_iCycle) ? $v_iCycle : $iNbBoucles%$iNbCycles);
			$this->data = str_replace($sVariable,$asListeCycles[$iCycle],$this->data);
		}
	}
	
	/**
	 * Cette méthode récupère les variables situées à l'intérieur d'un bloc
	 * @param string $looptag
	 * @param boolean $v_bRetTableau
	 * @return Retourne une chaîne de caractères ou un tableau de chaîne de caractères
	 */
	function defVariable ($looptag,$v_bRetTableau=FALSE,$v_sSeparateur="###")
	{
		$sVariable = NULL;
		
		if (strpos($this->data,"[$looptag-]"))
		{
			$debut = strpos($this->data,"[$looptag+]");
			$fin = strpos($this->data,"[$looptag-]") + strlen("[$looptag-]"); // + taille de la balise finale
			$sVariable = substr($this->data,$debut,($fin-$debut));
			//effacement du bloc
			$this->data = str_replace($sVariable,"",$this->data);
			// enlevement des balises + et - ds le tableau
			$sVariable = str_replace("[$looptag+]","",$sVariable);
			$sVariable = str_replace("[$looptag-]","",$sVariable);
			$sVariable = trim($sVariable);
		}
		
		return ($v_bRetTableau && isset($sVariable) ? explode($v_sSeparateur,$sVariable) : $sVariable);
	}
	function defTableau ($looptag,$v_sSeparateur=",") { return $this->defVariable($looptag,TRUE,$v_sSeparateur); }
	function effacerVariable ($looptag) { $this->defVariable($looptag); }
	function retDonnees () { return $this->data; }
	function defDonnees ($v_sDonnees) { $this->data = $v_sDonnees; }
	function caracteres () { return strlen($this->data); }
	// }}}
	
	function ajouter ($sTexteAjout) { $this->data = $this->data.$sTexteAjout; }
	function effacer () { $this->template_parent->data = str_replace("[$this->looptag"."_tmp]","",$this->template_parent->data); }
	
	function afficher ()
	{
		if (count($this->asData))
			$this->data = implode("",$this->asData)
				.$this->data;
		$this->template_parent->data = str_replace("[$this->looptag"."_tmp]",$this->data,$this->template_parent->data);
	}
}

?>
