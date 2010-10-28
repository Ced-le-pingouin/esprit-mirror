<?php
// inclure globals => plate_forme/CProjet => CBdd, CFormation, ...
require_once dirname(__FILE__).'/../../globals.inc.php';
require_once dirname(__FILE__).'/../../include/ElementFormation.php';
require_once dirname(__FILE__).'/../../lib/std/AfficheurPageEtendu.php';
require_once dirname(__FILE__).'/../../lib/std/FichierInfoEtendu.php';
require_once dirname(__FILE__).'/../../include/FichiersElementFormation.php';

class NettoyageFormations extends AfficheurPageEtendu
{
	/** @var CProjet */
    protected $projet;
    /** @var CBdd */
    protected $db;
	
    /** @var string */
    protected $titre = '';
    
	/** @var array[int]string */
	protected $dossiersFormations = array();
	/** @var array[int]int */
	protected $idsFormations = array();
	/** @var array[int]CFormation */
	protected $formations = array();
	
	/** @var CFormation */
	protected $formationCourante;
	/** @var array[string]FichierInfo */
	protected $fichiersInutiles = array();
	
	protected function avantAction()
	{
		$this->projet = new CProjet();
		$this->db = $this->projet->oBdd;
		
		if (!$this->projet->verifAdministrateur()) {
			throw new Exception("Droits insuffisants pour accéder à cette page");
		}
	}
	
    protected function actionIndex()
    {
    	$this->titre = 'Espace occupé par les formations';
    	
    	$this->trouverFormationsAPartirDesDossiers();
    	$this->calculerEspaceOccupeParFormations();
    	$this->trierFormationsParEspaceOccupeDesc();
    	
    	$this->definirVariablesTemplate('formations');
    }
    
    protected function trouverFormationsAPartirDesDossiers()
    {
    	$this->trouverDossiersFormations();
    	$this->trouverIdsFormationsAPartirDesDossiers();
    	$this->trouverFormationsAPartirDesIds();
    }
    
    protected function trouverDossiersFormations()
    {
    	// TODO: PHP5: ceci provoque une erreur stricte pour l'appel statique
    	$motifDossiersFormations = CFormation::retDossierFormations().'f*';
        
    	$this->dossiersFormations = glob(
    	   $motifDossiersFormations, GLOB_ONLYDIR
    	);
    }
    
    protected function trouverIdsFormationsAPartirDesDossiers()
    {
        $this->idsFormations = preg_replace(
           '%.*[/\\\\]f([0-9]+)$%', 
           '\\1', 
           $this->dossiersFormations
        );
        
        // les ids doivent être des entiers
        foreach ($this->idsFormations as &$id) {
            $id = (int)$id;
        }
        unset($id);
    }
    
    protected function trouverFormationsAPartirDesIds()
    {
    	// TODO: pas très efficace, il faudrait une méthode dans CFormation
    	//       pour initialiser un tableau de formations, avec une requête
    	//       SQL unique (pour les autres objets que CFormation aussi, 
    	//       d'ailleurs)
        foreach ($this->idsFormations as $id) {
        	$this->formations[] = new CFormation($this->db, (int)$id);
        }
    }
    
    protected function calculerEspaceOccupeParFormations()
    {
    	foreach ($this->formations as &$formation) {
    		$dossierFormation = new FichierInfoEtendu($formation->retDossier());
    		$formation->espaceOccupe = $dossierFormation->retTaille();
    		$formation->espaceOccupeFormate = FichierInfoEtendu::tailleEnOctetsVersTailleFormatee($formation->espaceOccupe);
    	}
    	unset($formation);
    }
    
    protected function trierFormationsParEspaceOccupeDesc()
    {
    	usort(
    	    $this->formations, 
    	    array($this, 'callbackTriFormationsParEspaceOccupeDesc')
    	);
    }
    
    /**
     * @param CFormation $f1
     * @param CFormation $f2
     * @return int
     */
    protected function callbackTriFormationsParEspaceOccupeDesc($f1, $f2)
    {
    	// forcer à retourner -1 ou 1 car le $diff (retourné, dans la version
    	// initiale de la fonction) peut parfois être un float, et dans ce cas
    	// le tri déconne
    	$diff = $f2->espaceOccupe - $f1->espaceOccupe;
    	return $diff > 0 ? 1 : -1;
    }
    
    protected function actionVoir()
    {
        $this->trouverFichiersInutilesDansActivites();
        
        $this->titre = "Détail pour la formation {$this->formationCourante->retId()}";
        
        $this->definirVariablesTemplate(
            array('formation', $this->formationCourante),
            array('modules', $this->formationCourante->modules)
        );
    }
    
    protected function actionConfirmerEffacement()
    {
        $this->trouverFichiersInutilesDansActivites();
        
        $this->definirVariablesTemplate(
            array('fichiers', $this->fichiersInutiles)
        );
    }
    
    protected function initFormationCouranteEtTousSesDescendants()
    {
        $formation = ElementFormation::retElementFormation(
            $this->db, TYPE_FORMATION, $this->get('id')
        );
        
        $formation->modules = $formation->retElementsEnfants();
        foreach ($formation->modules as $module) {
            $module->rubriques = $module->retElementsEnfants();
            foreach ($module->rubriques as $rubrique) {
                $rubrique->activites = $rubrique->retElementsEnfants();
                foreach ($rubrique->activites as $activite) {
                    $activite->sousActivites = $activite->retElementsEnfants();
                }
            }
        }
        
        $this->formationCourante = $formation;
    }
    
    protected function trouverFichiersInutilesDansActivites()
    {
    	if (!isset($this->formationCourante)) {
    		$this->initFormationCouranteEtTousSesDescendants();
    	}
    	
    	$nbTotalFichiersInutiles = 0;
    	$tailleTotaleFichiersInutiles = 0;
    	
    	foreach ($this->formationCourante->modules as $module) {
    		foreach ($module->rubriques as $rubrique) {
    			foreach ($rubrique->activites as $activite) {
    				$fichiersActivite = new FichiersElementFormation($activite);
    				$fichiersInutiles = $fichiersActivite->trouverFichiersForumsInutiles();
    				
    				$nbFichiersInutiles = count($fichiersInutiles);
    				$tailleFichiersInutiles = $this->retTailleTotaleFichiers(
    				    $fichiersInutiles
    				);
    				
    				$activite->fichiersInutiles = $fichiersInutiles;
    				$activite->nbFichiersInutiles = $nbFichiersInutiles;
    				$activite->tailleFichiersInutiles = $tailleFichiersInutiles;
    				$activite->tailleFichiersInutilesFormatee = 
    				    FichierInfoEtendu::tailleEnOctetsVersTailleFormatee(
    				        $tailleFichiersInutiles
    				    );
    				    
    				$tailleTotaleFichiersInutiles += $tailleFichiersInutiles;
    				$nbTotalFichiersInutiles += $nbFichiersInutiles;
    				
    				$this->fichiersInutiles += $fichiersInutiles;
    			}
    		}
    	}
    	
    	$this->formationCourante->nbTotalFichiersInutiles = $nbTotalFichiersInutiles;
    	$this->formationCourante->tailleTotaleFichiersInutiles = $tailleTotaleFichiersInutiles;
    	$this->formationCourante->tailleTotaleFichiersInutilesFormatee = 
    	    FichierInfoEtendu::tailleEnOctetsVersTailleFormatee(
    	        $tailleTotaleFichiersInutiles
    	    );
    }
    
    /**
     * @param array[]FichierInfo $fichiers
     * @return int
     */
    protected function retTailleTotaleFichiers($fichiers)
    {
    	$tailleTotale = 0;
    	
    	foreach ($fichiers as $fichier) {
    		$tailleTotale += $fichier->retTaille();
    	}
    	
    	return $tailleTotale;
    }
    
    protected function apresAction()
    {
        $this->definirVariablesTemplate('titre');
    }
}

$page = new NettoyageFormations();
$page->demarrer();