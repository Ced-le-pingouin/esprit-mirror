<?php
require_once dirname(__FILE__).'/../globals.inc.php';
require_once dirname(__FILE__).'/ValidateurAutomatiqueCollecticiel.php';
require_once dirname(__FILE__).'/ValidateurAutomatiqueFormulaire.php';

abstract class ValidateurAutomatiqueSousActivite
{
	const PREMIERS = 1;
	const DERNIERS = 2;
	
	/** @var CSousActiv */
	protected $sousActivite;
	
	/**
	 * @param CSousActiv $sousActivite
	 */
	final protected function __construct(CSousActiv $sousActivite)
	{
		$this->sousActivite = $sousActivite;
	} 
	
	/**
	 * @param CSousActiv $sousActivite
	 * @return ValidateurAutomatiqueSousActivite
	 * @throws Exception
	 */
    public static function creer(CSousActiv $sousActivite)
    {
    	$typeSousActivite = $sousActivite->retType();
    	
    	switch($typeSousActivite) {
    		case LIEN_COLLECTICIEL:
    			return new ValidateurAutomatiqueCollecticiel($sousActivite);
    			break;
    		case LIEN_FORMULAIRE:
    			return new ValidateurAutomatiqueFormulaire($sousActivite);
    			break;
    		default:
    			throw new Exception("La sous-activité à valider doit être de type collecticiel ou formulaire");
    	}
    }
    
    /**
     * @param int $jour
     * @param int $mois
     * @param int $annee
     * 
     * @return int
     */
    abstract public function validerAPartirDeDate($jour, $mois, $annee);

    /**
     * @param int $combien
     * 
     * @return int
     */
    abstract public function validerXPremiers($combien = 1);
    
    /**
     * @param int $combien
     * 
     * @return int
     */
    abstract public function validerXDerniers($combien = 1);
}