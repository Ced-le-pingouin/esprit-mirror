<?php
require_once(dirname(__FILE__).'/OO.php');

class AfficheurPage
{
	var $fichierTpl;
	var $tpl;
	var $aErreursPossibles = array();
	var $aErreurs = array();
	var $iNbErreursFatales = 0;
	
	function demarrer($fichierTpl = NULL)
	{
		$this->recupererDonnees();
		$this->validerDonnees();
		if ($this->retNbErreursFatales() == 0)
			$this->gererActions();
		$this->defTpl($fichierTpl);
		$this->detecterErreursPossibles();
		$this->afficher();
	}
	
	function detecterErreursPossibles()
	{
		preg_match_all('/\[(erreur[^]]+)\+\].*\[\1\-\]/s', $this->tpl->data, $aListeErreurs);
		if (count($aListeErreurs[0]) > 0)
			$this->aErreursPossibles = $aListeErreurs[1];
	}
	
	function recupererDonnees()
	{
		OO::abstraite();
	}
	
	function validerDonnees()
	{
		OO::abstraite();
	}
	
	function gererActions()
	{
		OO::abstraite();
	}
	
	function defTpl($fichierTpl)
	{
		if (empty($fichierTpl))
		{
			$asTraces = debug_backtrace();
			$this->fichierTpl = preg_replace('/\.[^.]*$/', '.html', basename($asTraces[1]['file']));
		}
		else
		{
			$this->fichierTpl = $fichierTpl;
		}
			
		$this->tpl = new Template($this->fichierTpl);
	}
	
	function rediriger($sUrl)
	{
		header("Location: $sUrl\n");
	}
	
	function afficher()
	{
		$this->afficherErreurs();
		if ($this->retNbErreursFatales() == 0)
			$this->afficherParties();
		
		$this->tpl->afficher();
	}
	
	function afficherErreurs()
	{
		// afficher les blocs correspondant aux erreurs qui se sont produites
		foreach ($this->aErreurs as $sNomErreur=>$bFatale)
		{
			if (($cle = array_search($sNomErreur, $this->aErreursPossibles)) !== FALSE)
			{
				$tplErreur = new TPL_Block($sNomErreur, $this->tpl);
				$tplErreur->afficher();
				
				unset($this->aErreursPossibles[$cle]);
			}
		}
		
		// effacer les blocs d'erreurs se trouvant sur la page, pour celles qui ne se sont pas produites
		foreach ($this->aErreursPossibles as $sNomErreur)
		{
			$tplErreur = new TPL_Block($sNomErreur, $this->tpl);
			$tplErreur->effacer();
		}
		
		// si pas d'erreur(s) fatale(s), on affiche le bloc "pasErreur", sinon on l'efface => pour bien faire, les blocs
		// représentant les erreurs fatales devraient se trouver en dehors du bloc "pasErreur", sinon on ne verra pas 
		// leur texte
		$tplPasErreur = new TPL_Block('pasErreur', $this->tpl);
		if ($this->iNbErreursFatales == 0)
			$tplPasErreur->afficher();
		else
			$tplPasErreur->effacer();
	}
	
	function afficherParties()
	{
		OO::abstraite();
	}
	
	function declarerErreur($sNomErreur, $bFatale = FALSE)
	{
		$this->aErreurs[$sNomErreur] = $bFatale;
		if ($bFatale) $this->iNbErreursFatales++;
	}
	
	function erreurDeclaree($sNomErreur)
	{
		return isset($this->aErreurs[$sNomErreur]);
	}
	
	function retNbErreurs()
	{
		return count($this->aErreurs);
	}
	
	function retNbErreursNonFatales()
	{
		return count($this->aErreurs) - $this->iNbErreursFatales;
	}
	
	function retNbErreursFatales()
	{
		return $this->iNbErreursFatales;
	}
}

OO::defClasseAbstraite();
?>
