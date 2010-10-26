<?php
// inclure globals => plate_forme/CProjet => CBdd, CFormation, ...
require_once dirname(__FILE__).'/../../globals.inc.php';
require_once dirname(__FILE__).'/../../include/ElementFormation.php';
require_once dirname(__FILE__).'/../../lib/std/AfficheurPageEtendu.php';
require_once dirname(__FILE__).'/../../lib/std/FichierInfoEtendu.php';

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
	
	protected function avantAction()
	{
		$this->projet = new CProjet();
		$this->db = $this->projet->oBdd;
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
        $formation = ElementFormation::retElementFormation(
            $this->db, TYPE_FORMATION, $this->get('id')
        );
        
        // TODO: modifier le système de remplacement automatique des boucles et
        // variables dans AfficheurEtendu, de manière à ne pas devoir créer 
        // soi-même le code ci-dessous quand on veut des boucles dans des 
        // boucles, avec des ensemble de variables différents pour chaque 
        // itération
        $modules = $formation->retElementsEnfants();
        $blocModule = new TemplateBlocEtendu('modules', $this->tpl);
        $blocModule->beginLoop();
        foreach ($modules as $module) {
        	$blocModule->nextLoop();
        	$blocModule->remplacer('{module.retNom}', $module->retNom());        	
        	
        	$rubriques = $module->retElementsEnfants();
        	$blocRubrique = new TemplateBlocEtendu('rubriques', $blocModule);
            $blocRubrique->beginLoop();
        	foreach ($rubriques as $rubrique) {
        		$blocRubrique->nextLoop();
        		$blocRubrique->remplacer('{rubrique.retNom}', $rubrique->retNom());
        		
        		$activites = $rubrique->retElementsEnfants();
        		$blocActivite = new TemplateBlocEtendu('activites', $blocRubrique);
                $blocActivite->beginLoop();
        		foreach ($activites as $activite) {
        			$blocActivite->nextLoop();
        			$blocActivite->remplacer('{activite.retNom}', $activite->retNom());
        			
        			$sousActivites = $activite->retElementsEnfants();
        			$blocSousActivite = new TemplateBlocEtendu('sousActivites', $blocActivite);
                    $blocSousActivite->beginLoop();
        			foreach ($sousActivites as $sousActivite) {
        				$blocSousActivite->nextLoop();
        				$blocSousActivite->remplacer('{sousActivite.retNom}', $sousActivite->retNom());
        			}
        			$blocSousActivite->afficher();
        		}
        		$blocActivite->afficher();
        	}
        	$blocRubrique->afficher();
        }
        $blocModule->afficher();
        
        $this->titre = "Détail pour la formation {$formation->retId()}";
        
        $this->definirVariableTemplate('formation', $formation);
    }
    
    protected function apresAction()
    {
        $this->definirVariablesTemplate('titre');
    }
}

$page = new NettoyageFormations();
$page->demarrer();