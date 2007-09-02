<?php
require_once(dirname(__FILE__).'/OO.php');

class AfficheurPage
{
	var $aDonneesUrl;
	var $aDonneesForm;
	var $aDonneesPersist;
	
	var $fichierTpl;
	var $tpl;
	
	var $aErreursPossibles = array();
	var $aErreurs = array();
	var $aErreursFatales = array();
	
	function demarrer($fichierTpl = NULL)
	{
		// récup des données
		$this->aDonneesUrl     =  $_GET;
		$this->aDonneesForm    =  $_POST;
		if (is_null(session_id()) || session_id() == '') session_start();
		$this->aDonneesPersist =& $_SESSION;
				
		$this->recupererDonnees();
		$this->validerDonnees();
		if ($this->retNbErreursFatales() == 0)
			$this->gererActions();
		$this->defTpl($fichierTpl);
		$this->detecterErreursPossibles();
		$this->afficher();
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
			// trouver le .html du même nom que le fichier qui a instancié l'objet
			//$asTraces = debug_backtrace();
			//$this->fichierTpl = preg_replace('/\.[^.]*$/', '.html', basename($asTraces[1]['file']));
			
			// trouver le .tpl qui porte le même nom que la classe (fille) à afficher
			$this->fichierTpl = get_class($this).'.tpl';
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
		$tplErreurs = new TPL_Block('erreurs', $this->tpl);
		
		// afficher les blocs correspondant aux erreurs qui se sont produites
		foreach ($this->aErreurs as $sNomErreur=>$sTexte)
		{
			if (($cle = array_search($sNomErreur, $this->aErreursPossibles)) !== FALSE)
			{
				if (empty($sTexte))
					$this->tpl->activerBloc($sNomErreur);
				unset($this->aErreursPossibles[$cle]);
			}
			else
			{
				if (!empty($sTexte))
					$tplErreurs->ajouter("<p>$sTexte</p>");
			}
		}
		
		foreach ($this->aErreursFatales as $sNomErreur=>$sTexte)
		{
			if (($cle = array_search($sNomErreur, $this->aErreursPossibles)) !== FALSE)
			{
				if (empty($sTexte))
					$this->tpl->activerBloc($sNomErreur);
				unset($this->aErreursPossibles[$cle]);
			}
			else
			{
				if (!empty($sTexte))
					$tplErreurs->ajouter("<p>$sTexte</p>");
			}
		}
		
		// effacer les blocs d'erreurs se trouvant sur la page, pour celles qui ne se sont pas produites
		foreach ($this->aErreursPossibles as $sNomErreur)
			$this->tpl->desactiverBloc($sNomErreur);
		
		$tplErreurs->afficher();
		
		// si pas d'erreur(s) fatale(s), on affiche le bloc "pasErreur", sinon on l'efface => pour bien faire, les blocs
		// représentant les erreurs fatales devraient se trouver en dehors du bloc "pasErreur", sinon on ne verra pas 
		// leur texte
		$this->tpl->activerBloc('pasErreur', count($this->aErreursFatales) == 0);
	}
	
	function afficherParties()
	{
		OO::abstraite();
	}
	
	function detecterErreursPossibles()
	{
		preg_match_all('/\[(erreur[^]]+)\+\].*\[\1\-\]/s', $this->tpl->data, $aListeErreurs);
		if (count($aListeErreurs[0]) > 0)
			$this->aErreursPossibles = $aListeErreurs[1];
	}
	
	function declarerErreur($sNomErreur, $bFatale = FALSE, $v_sTexte = '')
	{
		if ($bFatale)
			$this->aErreursFatales[$sNomErreur] = $v_sTexte;
		else
			$this->aErreurs[$sNomErreur] = $v_sTexte;
	}
	
	function erreurDeclaree($sNomErreur)
	{
		return (isset($this->aErreurs[$sNomErreur]) || isset($this->aErreursFatales[$sNomErreur]));
	}
	
	function retNbErreurs()
	{
		return count($this->aErreurs) + count($this->aErreursFatales);
	}
	
	function retNbErreursNonFatales()
	{
		return count($this->aErreurs);
	}
	
	function retNbErreursFatales()
	{
		return count($this->aErreursFatales);
	}
}

OO::defClasseAbstraite();
?>
