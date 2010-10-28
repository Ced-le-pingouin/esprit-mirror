<?php
require_once dirname(__FILE__).'/../globals.inc.php';

require_once dirname(__FILE__).'/../lib/std/FichierInfo.php';
require_once dirname(__FILE__).'/../lib/std/IterateurDossier.php';

class FichiersElementFormation
{
	// TODO: ces 2 constantes sont des énièmes redéfinitions de choses existant
	// ailleurs, mais par exemple pour utiliser dir_forum_ressources qui se 
	// trouve dans src/sousactiv/forum2/globals.inc.php, ilfaut en + passer par
	// la classe "bizarre" CIds, qui fait de requêtes DB supplémentaires :( :(
	// Un jour il faudra centraliser tout ça!!!
	const DIR_FORUM = 'forum';
	const DIR_RESSOURCES = 'ressources';
	
	/** @var mixed */
	protected $element;
	/** @var CBdd */
	protected $db;
	
	/** array[string]FichierInfo */
	protected $fichiersForumsTous;
	/** array[string]FichierInfo */
    protected $fichiersForumsNecessaires;
    /** array[string]FichierInfo */
    protected $fichiersForumsInutiles;
	
	/**
	 * @param mixed $element
	 */
	public function __construct($element)
	{
		if ($element->retTypeNiveau() != TYPE_ACTIVITE) {
			throw new Exception("Seul le type TYPE_ACTIVITE est supporté par FichiersElementFormation pour l'instant");
		}
		
		$this->element = $element;
		$this->db = $element->oBdd;
	}

    public function trouverFichiersForumsTous()
    {
    	if (is_null($this->fichiersForumsTous)) {
    		$dossierForum = $this->element->retDossier().'/'.self::DIR_FORUM;
    		$itr = new IterateurDossier($dossierForum);
    		
    		for ($fichiers = array(); $itr->estValide(); $itr->suiv()) {
    			$cheminFichier = $itr->courant()->retCheminReel();
    			$index = md5($cheminFichier);
    			
    			$fichiers[$index] = $itr->courant();
    		}
    		
    		$this->fichiersForumsTous = $fichiers;
    	}
    	
        return $this->fichiersForumsTous;
    }
	
	public function trouverFichiersForumsNecessaires()
	{
		if (is_null($this->fichiersForumsNecessaires)) {
			// TODO: tout est encodé en dur dans la requête (type forum, 
			// type niveau)
    		$sql = "
    		    SELECT
                  Ressource.UrlRes 
                FROM
                  SousActiv
                  INNER JOIN Forum USING (IdSousActiv)
                  INNER JOIN SujetForum USING (IdForum)
                  INNER JOIN MessageForum USING (IdSujetForum)
                  INNER JOIN MessageForum_Ressource USING (IdMessageForum)
                  INNER JOIN Ressource USING (IdRes)
                WHERE
                  SousActiv.IdActiv = {$this->element->retId()}
                  AND SousActiv.IdTypeSousActiv = 5 /* = forum */
    		";
    		
    		$this->db->executerRequete($sql);
    		
    		$dossierForum = $this->element->retDossier().'/'.self::DIR_FORUM;
    		while($enreg = $this->db->retEnregSuiv()) {
    			$cheminFichier = $dossierForum.'/'.$enreg->UrlRes;
    			$index = md5($cheminFichier);
    			
    			$fichiers[$index] = new FichierInfo($cheminFichier);
    		}
    		
    		$this->fichiersForumsNecessaires = $fichiers;
		}
		
		return $this->fichiersForumsNecessaires;
	}
    
	public function trouverFichiersForumsInutiles()
	{
		if (is_null($this->fichiersForumsInutiles))
		{
    		$this->trouverFichiersForumsTous();
    		$this->trouverFichiersForumsNecessaires();
    		
    		$this->fichiersForumsInutiles = array_diff_key(
    		    $this->fichiersForumsTous,
    		    $this->fichiersForumsNecessaires
    		);
		}
		
		return $this->fichiersForumsInutiles;
	}
	
	public function retFichiersCollecticielsNecessaires()
	{
		
	}
	
	public function retFichiersCollecticielsTous()
	{
		
	}
}