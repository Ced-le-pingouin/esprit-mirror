<?php
require_once dirname(__FILE__).'/FichierInfo.php';

class FichierInfoEtendu extends FichierInfo
{
	/**
	 * @return int
	 * 
	 * @see src/lib/std/FichierInfo::retTaille()
	 */
	public function retTaille()
	{
		if ($this->estDossier())
            return $this->retTailleDossier();

        return @filesize($this->retChemin());
	}
	
	/**
	 * ( contient du code tiré de 
	 *   http://www.go4expert.com/forums/showthread.php?t=290 )
	 * 
	 * @param string $chemin
	 * @return int
	 */
	protected function retTailleDossier($chemin = null)
	{
		if (is_null($chemin)) {
			$chemin = $this->retChemin();
		}
		
		$tailleTotale = 0;
		
		if ($handleRepertoire = opendir($chemin)) {
			while (($fichier = readdir($handleRepertoire))) {
				$cheminFichier = $chemin . '/' . $fichier;
				
				if ($fichier != '.' && $fichier != '..' && !is_link($cheminFichier)) {
				    if (is_dir($cheminFichier)) {
                        $tailleTotale += $this->retTailleDossier($cheminFichier);
                    } else if (is_file($cheminFichier)) {
                    	$tailleTotale += filesize($cheminFichier);
                    }
				}
			}
		}
		closedir($handleRepertoire);
		
		return $tailleTotale;
	}
	
	/**
	 * @param int $tailleEnOctets
	 * @return string
	 */
	public static function tailleEnOctetsVersTailleFormatee($tailleEnOctets)
	{
		static $unites = array('B', 'KB', 'MB', 'GB', 'TB');
		
		foreach ($unites as $cle => $unite) {
			if ($tailleEnOctets < pow(1024, $cle + 1)) {
				$tailleFormatee = $tailleEnOctets / pow(1024, $cle);
				return sprintf('%.2f %s', $tailleFormatee, $unite);
			}
		}
		
		return sprintf('%f ???', $tailleEnOctets);
	}
}