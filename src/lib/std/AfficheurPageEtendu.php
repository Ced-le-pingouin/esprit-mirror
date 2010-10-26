<?php
require_once dirname(__FILE__).'/AfficheurPage.php';
require_once dirname(__FILE__).'/TemplateEtendu.php';
require_once dirname(__FILE__).'/TemplateBlocEtendu.php';

/**
 * Ajoute des fonctions d'aide à la gestion de actions, par rapport à 
 * la classe de base AfficheurPage
 */
class AfficheurPageEtendu extends AfficheurPage
{
	protected $action = 'index';
	protected $variablesTemplate = array();
	
	/**
	 * @param string $fichierTpl
	 * 
	 * @see src/lib/std/AfficheurPage::demarrer()
	 */
	public function demarrer($fichierTpl = null)
	{
        if (isset($_GET['action'])) {
        	$this->action = $_GET['action'];
        }
		
		parent::demarrer($fichierTpl);
	}
	
	/**
	 * Définition vide qui empêche de provoquer une erreur si on en a pas besoin
	 * 
	 * @see src/lib/std/AfficheurPage::recupererDonnees()
	 */
    public function recupererDonnees()
    {
    }
    
    /**
     * Définition vide qui empêche de provoquer une erreur si on en a pas besoin
     * 
     * @see src/lib/std/AfficheurPage::validerDonnees()
     */
    public function validerDonnees()
    {
    }
    
    /**
     * Implémentation qui appelera automatiquement une méthode de la classe 
     * nommée comme l'action demandée
     * 
     * @see src/lib/std/AfficheurPage::gererActions()
     */
    public function gererActions()
    {
    	$nomMethodeAction = 'action'.ucfirst($this->action);
    	
    	$this->avantAction();
        $this->{$nomMethodeAction}();
        $this->apresAction();
    }
    
    protected function avantAction()
    {
    }
    
    protected function apresAction()
    {
    }
    
    /**
     * @param string $fichierTpl
     * 
     * @see src/lib/std/AfficheurPage::defTpl()
     */
    public function defTpl($fichierTpl)
    {
    	parent::defTpl($fichierTpl);
    	
    	unset($this->tpl);
    	$this->tpl = new TemplateEtendu($this->fichierTpl);
    }
    
    public function afficher()
    {
    	$this->remplacerVariablesTemplateAutomatiquement();
    	
    	parent::afficher();
    }
    
    protected function remplacerVariablesTemplateAutomatiquement()
    {
    	foreach ($this->variablesTemplate as $nom => $valeur) {
    		$estBoucle = $this->afficherBoucleTemplateSiExistante($this->tpl, $nom);
    		
    		if (!$estBoucle) {
    			$this->remplacerVariableTemplate($this->tpl, $nom);
    		}
    	}
    }
    
    /**
     * @param TemplateEtendu $template
     * @param string $nomBoucle
     * @return boolean
     */
    protected function afficherBoucleTemplateSiExistante($template, $nomBoucle)
    {
    	if (!is_array($this->variablesTemplate[$nomBoucle])) {
    		return false;
    	}
    	
    	if (strpos($template->retDonnees(), "[{$nomBoucle}+]") === false) {
    		return false;
    	}
    	
    	$nomVariableBoucle = $this->retVariableDeBoucleAPartirDuNomDeBoucle($nomBoucle);
    	if ($this->variableTemplateExiste($nomVariableBoucle)) {
    		throw new Exception("La boucle '{$nomBoucle}' ne peut être utilisée, car elle aurait une variable de boucle du nom de '{$nomVariableBoucle}', qui est déjà utilisé");
    	}
    	
    	$blocTemplate = new TemplateBlocEtendu($nomBoucle, $template);
    	$blocTemplate->beginLoop();
    	
    	foreach ($this->variablesTemplate[$nomBoucle] as $elementBoucle) {
    		$blocTemplate->nextLoop();
    		$this->definirVariableTemplate($nomVariableBoucle, $elementBoucle);
    		$this->remplacerVariableTemplate($blocTemplate, $nomVariableBoucle);
    	}
    	$blocTemplate->afficher();
    	
    	$this->effacerVariableTemplate($nomVariableBoucle);
    	
    	return true;
    }
    
    /**
     * @param TemplateEtendu $template
     * @param string $nomVariable
     */
    protected function remplacerVariableTemplate($template, $nomVariable)
    {
    	$variable = $this->variablesTemplate[$nomVariable];
    	
    	if (is_scalar($variable)) {
    	    $template->remplacer('{'.$nomVariable.'}', $variable);
    	} else if (is_object($variable)) {
    	    $template->remplacerParMotifEtCallback(
    	        '/{('.$nomVariable.')\\.(\\w+)}/',
    	        array($this, 'remplacerVariablesComplexesTemplate')
    	    );
    	}
    }
    
    /**
     * @param array[int]string $correspondances
     */
    public function remplacerVariablesComplexesTemplate($correspondances)
    {
    	// on doit utiliser l'opérateur @ car on peut obtenir une notice 
    	// indiquant que les parenthèses sont absentes (groupe facultatif dans
    	// le motif preg)
    	@list($tout, $nomVariable, $attribut) = $correspondances;
    	
    	$variable = $this->variablesTemplate[$nomVariable];
    	
    	if (isset($variable)) {
    		if (is_callable(array($variable, $attribut))) {
    			$remplacement = call_user_func(array($variable, $attribut));
                return $remplacement;
    		} else if (isset($variable->{$attribut})
    		        && is_scalar($variable->{$attribut})) {
    			return (string)$variable->{$attribut};
    		}
    	}
    	
    	return $tout;
    }
    
    /**
     * Définition vide qui empêche de provoquer une erreur si on en a pas besoin
     * 
     * @see src/lib/std/AfficheurPage::afficherParties()
     */
    public function afficherParties()
    {
    }
    
    protected function definirVariablesTemplate()
    {
    	$variables = func_get_args();
    	
    	foreach ($variables as $variable) {
    		$this->definirVariableTemplate($variable);
    	}
    }
    
    /**
     * @param string $nomVariable
     * @param mixed $valeurVariable
     */
    protected function definirVariableTemplate($nomVariable, $valeurVariable = null)
    {
    	if (is_null($valeurVariable)) {
    	    if (!isset($this->{$nomVariable})) {
                throw new Exception("Tentative de définir la variable de template '{$nomVariable}', sans lui donner de valeur");
            }
            
            $valeurVariable = $this->{$nomVariable};
    	}
    	
    	$this->variablesTemplate[$nomVariable] = $valeurVariable;
    }
    
    /**
     * @param string $nomVariable
     */
    protected function effacerVariableTemplate($nomVariable)
    {
    	unset($this->variablesTemplate[$nomVariable]);
    }
    
    /**
     * @param string $nomVariable
     */
    protected function variableTemplateExiste($nomVariable)
    {
    	return isset($this->variablesTemplate[$nomVariable]);
    }
    
    /**
     *  Ceci est une méthode un peu à l'arrache pour obtenir le nom d'une
     *  "variable de boucle" au "singulier" par rapport au nom de la boucle
     *  (ex: si boucle = [formations+], variable de boucle = {formation.xxx})
     * 
     * @param string $nomBoucle
     */
    protected function retVariableDeBoucleAPartirDuNomDeBoucle($nomBoucle)
    {
    	return substr($nomBoucle, 0, -1);
    }
}