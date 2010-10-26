<?php
// inclure globals => plate_forme/CProjet => CBdd, CFormation, ...
require_once dirname(__FILE__).'/../../globals.inc.php';
require_once dirname(__FILE__).'/../../lib/std/AfficheurPageEtendu.php';
require_once dirname(__FILE__).'/../../lib/std/FichierInfoEtendu.php';

class NettoyageFormations extends AfficheurPageEtendu
{
	/** @var CProjet */
    protected $projet;
    /** @var CBdd */
    protected $db;
	
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
    	$this->trouverFormationsAPartirDesDossiers();
    	$this->calculerEspaceOccupeParFormations();
    	$this->trierFormationsParEspaceOccupeDesc();
    	
    	$this->definirVariablesTemplate('formations');
    }
    
    protected function actionVoir()
    {
    	
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
}

$page = new NettoyageFormations();
$page->demarrer();