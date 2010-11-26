<?php
/*
 * TODO:
 *   - supprimer les echos
 *   - implémenter la sélection des X *premiers* éléments
 */

require_once dirname(__FILE__).'/ValidateurAutomatiqueSousActivite.php';

class ValidateurAutomatiqueFormulaire extends ValidateurAutomatiqueSousActivite
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
        $ids = $this->trouverIdsFormulairesSoumisAPartirDeDate(
            $jour, $mois, $annee
        );
        $this->validerFormulairesParIds($ids);
        
        return count($ids);
    }
    
    /**
     * @param int $combien
     * 
     * @return int
     */
    public function validerXDerniers($combien = 1)
    {
        $ids = $this->trouverIdsXDerniersFormulairesSoumisParPersonne(
            $combien
        );
        $this->validerFormulairesParIds($ids);
        
        return count($ids);
    }
    
    /**
     * @param int $jour
     * @param int $mois
     * @param int $annee
     * 
     * @return array[int]int
     */
    private function trouverIdsFormulairesSoumisAPartirDeDate($jour, $mois, $annee)
    {
        $sqlIntermediaire = "
            SELECT
              FormulaireComplete_SousActiv.IdFCSousActiv AS id,
              FormulaireComplete.TitreFC AS nom
            FROM
              FormulaireComplete_SousActiv
              INNER JOIN FormulaireComplete USING (IdFC)
            WHERE
              FormulaireComplete_SousActiv.IdSousActiv='%d'
              /*AND FormulaireComplete_SousActiv.StatutFormSousActiv='%d'*/
              AND FormulaireComplete.DateFC >= '%4d-%02d-%02d'
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
    private function trouverIdsXDerniersFormulairesSoumisParPersonne($combien)
    {
    	if ($combien != 1) {
    		throw new Exception("Le validateur automatique de formulaire n'accepte que '1' comme nombre de derniers formulaires à valider par personne");
    	}
    	
        $sqlIntermediaire = "
            SELECT
              fcsa2.IdFCSousActiv AS id,
              fc2.TitreFC AS nom
            FROM
              FormulaireComplete_SousActiv AS fcsa2
              INNER JOIN FormulaireComplete AS fc2 USING (IdFC)
              INNER JOIN
              (
                SELECT
                  fc.IdPers as subIdPers,
                  MAX(fc.DateFC) AS subDateFC
                FROM
                  FormulaireComplete_SousActiv AS fcsa
                  INNER JOIN FormulaireComplete AS fc USING (IdFC)
                WHERE
                  fcsa.IdSousActiv = '%1\$d'
                GROUP BY
                  fc.IdPers
              ) AS sub ON (fc2.IdPers=sub.subIdPers AND fc2.DateFC=sub.subDateFC)
            WHERE
              fcsa2.IdSousActiv = '%1\$d'
        ";
        
        $variablesSql = array(
            $this->sousActivite->retId()
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
    private function validerFormulairesParIds($ids)
    {
        $ids = (array)$ids;
        if (count($ids) <= 0) {
            return;
        }
        
        $listeIds = implode(',', $ids);
        
        $sqlIntermediaire = "
            UPDATE
              FormulaireComplete_SousActiv
            SET
              StatutFormSousActiv='%d'
            WHERE
              IdFCSousActiv IN (%s)
        ";
        
        $variablesSql = array(
            STATUT_RES_ACCEPTEE,
            $listeIds
        );
        
        $sqlFinal = vsprintf($sqlIntermediaire, $variablesSql);
        
        $this->sousActivite->oBdd->executerRequete($sqlFinal);
    }
}
