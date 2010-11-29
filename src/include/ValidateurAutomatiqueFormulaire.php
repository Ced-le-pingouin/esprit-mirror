<?php
/*
 * TODO:
 *   - accepter un X > 1 pour la validation des X premiers ou derniers éléments
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
    public function validerXPremiers($combien = 1)
    {
        $ids = $this->trouverIdsXPremiersOuDerniersFormulairesSoumisParPersonne(
            $combien, self::PREMIERS
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
        $ids = $this->trouverIdsXPremiersOuDerniersFormulairesSoumisParPersonne(
            $combien, self::DERNIERS
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
              FormulaireComplete_SousActiv.IdFCSousActiv AS id
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
     * @param int $premiersOuDerniers
     * 
     * @return array[int]int 
     */
    private function trouverIdsXPremiersOuDerniersFormulairesSoumisParPersonne($combien, $premiersOuDerniers = self::DERNIERS)
    {
    	if ($combien != 1) {
    		throw new Exception("Le validateur automatique de formulaire n'accepte que '1' comme nombre de premiers/derniers formulaires à valider par personne");
    	}
    	
    	$minOuMax = ( $premiersOuDerniers == self::PREMIERS ? 'MIN' : 'MAX' );
    	
        $sqlIntermediaire = "
            SELECT
              fcsa2.IdFCSousActiv AS id
            FROM
              FormulaireComplete_SousActiv AS fcsa2
              INNER JOIN FormulaireComplete AS fc2 USING (IdFC)
              INNER JOIN
              (
                SELECT
                  fc.IdPers as subIdPers,
                  /* ici, on aura MIN ou MAX selon qu'on veut le 1er ou le dernier */
                  %1\$s(fc.DateFC) AS subDateFC
                FROM
                  FormulaireComplete_SousActiv AS fcsa
                  INNER JOIN FormulaireComplete AS fc USING (IdFC)
                WHERE
                  fcsa.IdSousActiv = '%2\$d'
                GROUP BY
                  fc.IdPers
              ) AS sub ON (fc2.IdPers=sub.subIdPers AND fc2.DateFC=sub.subDateFC)
            WHERE
              fcsa2.IdSousActiv = '%2\$d'
        ";
        
        $variablesSql = array(
            $minOuMax,
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