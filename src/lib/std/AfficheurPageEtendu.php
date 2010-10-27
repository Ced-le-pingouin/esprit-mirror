<?php
require_once dirname(__FILE__).'/AfficheurPage.php';
require_once dirname(__FILE__).'/TemplateEtendu.php';
require_once dirname(__FILE__).'/TemplateBlocEtendu.php';

/**
 * Ajoute des fonctions d'aide à la gestion de actions, par rapport à 
 * la classe de base AfficheurPage
 * 
 * @todo Cette classe agit beaucoup trop sur les templates/blocs, par exemple 
 *       pour les remplacement de variables ou déroulements de boucles 
 *       automatiques. Il faudrait déplacer ces fonctionnalités dans les classes
 *       des templates/blocs. Cela pourrait nécessiter que les données 
 *       nécessaires aux déroulements/remplacements soit passées aux templates/
 *       blocs 
 */
class AfficheurPageEtendu extends AfficheurPage
{
	const ACTION_PAR_DEFAUT = 'index';
	
	const TPL_MODE_UN_SEUL = 1;
	const TPL_MODE_UN_PAR_ACTION = 2;
	
	/**	@var string */
	protected $action = self::ACTION_PAR_DEFAUT;
	/** @var int */
	protected $gestionTemplates = self::TPL_MODE_UN_SEUL;
	/** @var stdClass */
	protected $variablesTemplate;
    /** @var array[int]string */
	protected $prefixesRechercheVariables = array();
	
    public function __construct()
    {
    	$this->variablesTemplate = new stdClass();
    }
    
    /**
	 * @param int $mode
	 * @return AfficheurPageEtendu
	 */
	public function defGestionTemplates($mode) {
		$this->gestionTemplates = $mode;
		
		return $this;
	}
	
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
     * @param string $nomVariable
     * @return string
     */
    protected function get($nomVariable)
    {
    	return $this->aDonneesUrl[$nomVariable];
    }
    
    /**
     * @param string $nomVariable
     * @return string
     */
    protected function post($nomVariable)
    {
        return $this->aDonneesForm[$nomVariable];
    }
    
    /**
     * @param string $nomVariable
     * @return string
     */
    protected function session($nomVariable)
    {
    	return $this->aDonneesPersist[$nomVariable];
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
        
    	if (empty($fichierTpl)
    	 && $this->gestionTemplates == self::TPL_MODE_UN_PAR_ACTION) {
    		$this->reconstruireNomFichierTemplateSurBaseActionCourante();
    	}
    	
    	unset($this->tpl);
    	$this->tpl = new TemplateEtendu($this->fichierTpl, false);
    	
    	$this->inclureSousTemplatesParAction();
    }
    
    protected function reconstruireNomFichierTemplateSurBaseActionCourante()
    {
    	$this->fichierTpl = preg_replace(
    	    '/(.*)(\\.\\w+)$/',
    	    '\\1.'.$this->action.'\\2',
    	    $this->fichierTpl
    	);
    }
    
    protected function inclureSousTemplatesParAction()
    {
    	$nomSousTemplateAction = get_class($this).'.'.$this->action;
    	
    	$this->tpl->remplacer('[sub:action]', "[sub:{$nomSousTemplateAction}]");
    	$this->tpl->inclureSousTemplates();
    }
    
    public function afficher()
    {
    	$this->traiterBouclesEtVariablesTemplateAutomatiquement($this->tpl);
    	
    	parent::afficher();
    }
    
    /**
     * @param TemplateEtendu $template
     */
    protected function traiterBouclesEtVariablesTemplateAutomatiquement($template)
    {
        $this->remplacerVariablesTemplate($template);
        $this->deroulerBouclesTemplate($template);
    }
    
    /**
     * @param TemplateEtendu $template
     */
    protected function remplacerVariablesTemplate($template)
    {
    	$template->remplacerParMotifEtCallback(
            '/{(\\w+)(?:\\.(\\w+))?}/',
            array($this, 'callbackRemplacerVariablesTemplate')
        );
    }
    
    /**
     * @param array[int]string $correspondances
     */
    public function callbackRemplacerVariablesTemplate($correspondances)
    {
    	// on doit utiliser l'opérateur @ car on peut obtenir une notice 
    	// indiquant que les parenthèses sont absentes (groupe facultatif dans
    	// le motif preg)
    	@list($tout, $nomVariable, $attribut) = $correspondances;
    	
    	$variable = $this->retVariableTemplate($nomVariable);
    	
    	if (!is_null($variable)) {
    		if (!isset($attribut)) {
    			if (is_scalar($variable)) {
    			    return (string)$variable;
    			}
    		} else if (is_callable(array($variable, $attribut))) {
    			$remplacement = call_user_func(array($variable, $attribut));
                return $remplacement;
    		} else if (isset($variable->{$attribut})
    		        && is_scalar($variable->{$attribut})) {
    			return (string)$variable->{$attribut};
    		}
    	}
    	
    	return $tout;
    }
    
    /*
     * @param TemplateEtendu $template
     */
    protected function deroulerBouclesTemplate($template)
    {
    	$positionRecherche = 0;
    	
    	do {
    		$boucleTrouvee = (boolean)preg_match(
    		    '/\\[(\\w+)\\+\\]/',
    		    $template->retDonnees(),
    		    $correspondances,
    		    PREG_OFFSET_CAPTURE,
    		    $positionRecherche
    		);
    		
    		if ($boucleTrouvee) {
    	        list($tout, $boucle) = $correspondances;
                list($nomBoucle, $positionBoucle) = $boucle;
                // permet de continuer + loin si la boucle n'est finalement pas
                // déroulée. Sans ça, on retrouverait la même boucle 
                // indéfiniment
                $positionRecherche = $positionBoucle + 1;
    			
                $elementsBoucle = $this->retVariableTemplateEnUtilisantPrefixes($nomBoucle);
                
                // si aucun tableau dispo au nom de la boucle dans les 
                // variables du template, on ne déroule pas la boucle
    			if (!is_array($elementsBoucle)) {
    			 	continue;
    			}
    			
                $nomVariableBoucle = $this->retVariableDeBoucleAPartirDuNomDeBoucle($nomBoucle);
                if (!is_null($this->retVariableTemplate($nomVariableBoucle))) {
                    throw new Exception("La boucle '{$nomBoucle}' ne peut être utilisée, car elle aurait une variable de boucle du nom de '{$nomVariableBoucle}', qui est déjà utilisé");
                }
    			
                $boucle = new TemplateBlocEtendu($nomBoucle, $template);
    			$boucle->beginLoop();
    			
    			$this->ajouterPrefixeRechercheVariables($nomVariableBoucle);
    			
    			foreach ($elementsBoucle as $elementBoucle) {
                    $boucle->nextLoop();
                    $this->definirVariableTemplate($nomVariableBoucle, $elementBoucle);
                    $this->traiterBouclesEtVariablesTemplateAutomatiquement($boucle);
    			}
                $boucle->afficher();
                
                $this->supprimerPrefixeRechercheVariables();                
                $this->effacerVariableTemplate($nomVariableBoucle);
    		}
    	} while ($boucleTrouvee);
    }
    
    /**
     * Définition vide qui empêche de provoquer une erreur si on en a pas besoin
     * 
     * @see src/lib/std/AfficheurPage::afficherParties()
     */
    public function afficherParties()
    {
    }
    
    /**
     * @param string $prefixe
     */
    protected function ajouterPrefixeRechercheVariables($prefixe)
    {
    	array_unshift($this->prefixesRechercheVariables, $prefixe);
    }
    
    protected function supprimerPrefixeRechercheVariables()
    {
    	array_shift($this->prefixesRechercheVariables);
    }
    
    protected function definirVariablesTemplate()
    {
    	$variables = func_get_args();
    	
    	foreach ($variables as $variable) {
    		@list($nomVariable, $valeurVariable) = (array)$variable;
    		
    		$this->definirVariableTemplate($nomVariable, $valeurVariable);
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
    	
    	$this->variablesTemplate->{$nomVariable} = $valeurVariable;
    }
    
    /**
     * @param string $nomVariable
     */
    protected function effacerVariableTemplate($nomVariable)
    {
    	unset($this->variablesTemplate->{$nomVariable});
    }
    
    /**
     * @param string $nomVariable
     * @return mixed
     */
    protected function retVariableTemplate($nomVariable)
    {
        if (isset($this->variablesTemplate->{$nomVariable})) {
            return $this->variablesTemplate->{$nomVariable};
        }
        
        return null;
    }
    
    /**
     * @param string $nomVariable
     * @return mixed
     */
    protected function retVariableTemplateEnUtilisantPrefixes($nomVariable)
    {
    	foreach ($this->prefixesRechercheVariables as $prefixe) {
    	    if (isset($this->variablesTemplate->{$prefixe})
    		 && isset($this->variablesTemplate->{$prefixe}->{$nomVariable})) {
    		    return $this->variablesTemplate->{$prefixe}->{$nomVariable};
    	    }
    	}
    	
    	return $this->retVariableTemplate($nomVariable);
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