<?php
require_once dirname(__FILE__).'/ValidateurAutomatiqueSousActivite.php';

class ValidateurAutomatiqueCollecticiel extends ValidateurAutomatiqueSousActivite
{
	/**
     * @param int $jour
     * @param int $mois
     * @param int $annee
     * 
     * @return int
     */
	public function validerAPartirDeDate($jour, $mois, $annee)
	{
		$ids = $this->trouverIdsRessourcesSoumisesAPartirDeDate(
		    $jour, $mois, $annee
		);
		$this->validerRessourcesParIds($ids);
		
		return count($ids);
	}
	
	/**
	 * @param int $combien
	 * 
	 * @return int
	 */
	public function validerXDerniers($combien = 1)
	{
        $ids = $this->trouverIdsXDernieresRessourcesSoumisesParPersonne(
            $combien
        );
        $this->validerRessourcesParIds($ids);
        
        return count($ids);
	}
	
    /**
     * @param int $jour
     * @param int $mois
     * @param int $annee
     * 
     * @return array[int]int
     */
    private function trouverIdsRessourcesSoumisesAPartirDeDate($jour, $mois, $annee)
    {
        $sqlIntermediaire = "
            SELECT
              Ressource_SousActiv.IdResSousActiv AS id
            FROM
              Ressource_SousActiv
              INNER JOIN Ressource USING (IdRes)
            WHERE
              Ressource_SousActiv.IdSousActiv='%d'
              AND Ressource_SousActiv.StatutResSousActiv='%d'
              AND Ressource.DateRes >= '%4d-%02d-%02d'
        ";
        
        $variablesSql = array(
            $this->sousActivite->retId(),
            STATUT_RES_SOUMISE,
            (int)$annee, (int)$mois, (int)$jour
        );
        
        $sqlFinal = vsprintf($sqlIntermediaire, $variablesSql);
        
        return $this->retTableauIdsAPartirRequete($sqlFinal);
    }
	
	/**
	 * @param int $combien
	 * 
	 * @return array[int]int 
	 */
	private function trouverIdsXDernieresRessourcesSoumisesParPersonne($combien)
	{
		$sqlIntermediaire = "
            SELECT
              rsa.IdResSousActiv AS id
            FROM
              Ressource_SousActiv AS rsa
              INNER JOIN Ressource AS r USING (IdRes)
            WHERE
              rsa.IdSousActiv='%1\$d'
              AND rsa.StatutResSousActiv='%2\$d'
              AND 
              (
                /* 
                 on compte le nombre de documents déposés par la même personne 
                 dans cette activité, A PARTIR DE celui traité dans la requête 
                 externe... 
                */ 
                SELECT
                  COUNT(*)
                FROM
                  Ressource_SousActiv AS rsa2
                  INNER JOIN Ressource AS r2 USING (IdRes)
                WHERE
                  rsa2.IdSousActiv='%1\$d'
                  AND rsa2.StatutResSousActiv='%2\$d'
                  AND r.IdPers=r2.IdPers
                  /* NOTE: pour prendre les X _premiers_, remplacer >= par <= */
                  AND r2.DateRes >= r.DateRes
              /*
               ...donc, si le document est bien dans les X derniers, il est 
               gardé dans les résultats 
              */
              ) <= '%3\$d'
        ";
        
        $variablesSql = array(
            $this->sousActivite->retId(),
            STATUT_RES_SOUMISE,
            $combien
        );
        
        $sqlFinal = vsprintf($sqlIntermediaire, $variablesSql);
        
        return $this->retTableauIdsAPartirRequete($sqlFinal);
	}
	
	/**
	 * @param string $requete
	 * 
	 * @return array[int]int
	 */
	private function retTableauIdsAPartirRequete($requete)
	{
		$db = $this->sousActivite->oBdd;
        $resultat = $db->executerRequete($requete);
        
        $ids = array();
        while (false !== ($ligne = $db->retEnregSuiv($resultat))) {
            $ids[] = (int)$ligne->id;
        }
        $db->libererResult($resultat);

        return $ids;
	}
	
	/**
	 * @param int|array[]int $ids
	 */
	private function validerRessourcesParIds($ids)
	{
		$ids = (array)$ids;
		if (count($ids) <= 0) {
			return;
		}
		
		$listeIds = implode(',', $ids);
		
		$sqlIntermediaire = "
            UPDATE
              Ressource_SousActiv
            SET
              StatutResSousActiv='%d'
            WHERE
              IdResSousActiv IN (%s)
		";
		
		$variablesSql = array(
		    STATUT_RES_ACCEPTEE,
		    $listeIds
		);
		
		$sqlFinal = vsprintf($sqlIntermediaire, $variablesSql);
		
		$this->sousActivite->oBdd->executerRequete($sqlFinal);
	}
}