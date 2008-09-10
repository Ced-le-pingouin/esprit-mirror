<?php
require_once dir_lib('std/FichierInfo.php', true);

class FichierEsprit extends FichierInfo
{
	/**
	 * @return bool true si le fichier ou dossier représenté par l'objet courant
	 *              est spécial ("système") dans Esprit, comme par exemple un  
	 *              dossier activ_<numero> ou un fichier tableaudebord.csv
	 *
	 */
	function estSpecial()
	{
		return $this->estFichierSpecial() || $this->estDossierSpecial();
	}

	/**
	 * @return bool
	 */
	function estFichierSpecial()
	{
		static $regexFichier = '/^(?:html\.php|tableaudebord\.csv)$/i';
		
		return $this->estFichier()
		       && preg_match($regexFichier, $this->retNom()) > 0; 
	}
	
	/**
	 * @return bool
	 */
	function estDossierSpecial()
	{
		static $regexDossier = '/^(?:activ_[0-9]+|chatlog|forum|ressources|rubriques)$/i';
		
		return $this->estDossier()
		       && preg_match($regexDossier, $this->retNom()) > 0;
	}
}
?>