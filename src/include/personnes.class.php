<?php

/**
 * @class CPersonnes
 * \created 14/04/2004
 * \modified 16/04/2004
 *
 */

class CPersonnes
{
	var $TRIER_NOM = "Personne.Nom";
	var $TRIER_PRENOM = "Personne.Prenom";
	var $TRIER_PSEUDO = "Personne.Pseudo";
	
	var $ORDRE_TRI_CROISSANT = "ASC";
	var $ORDRE_TRI_DESCROISSANT = "DESC";
	
	var $oBdd;
	
	var $sTrier;
	var $sOrdreTri;
	
	function CPersonnes (&$v_oBdd)
	{
		$this->oBdd = &$v_oBdd;
		
		$this->sTrier = $this->TRIER_NOM;
		$this->sOrdreTri = $this->ORDRE_TRI_CROISSANT;
	}
	
	function rechPersonnes ($v_sRequeteSql)
	{
		$aoPersonnes = array();
		$iIndex = 0;
		
		$hResult = $this->oBdd->executerRequete($v_sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$aoPersonnes[$iIndex] = new CPersonne($this->oBdd);
			$aoPersonnes[$iIndex]->init($oEnreg);
			$iIndex++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $aoPersonnes;
	}
	
	function defTrierSur ($v_sTrierSur)
	{
		$this->sTrier = $v_sTrierSur;
	}
	
	function retTrierSur ()
	{
		return " GROUP BY Personne.IdPers"
			." ORDER BY {$this->sTrier} {$this->sOrdreTri}";
	}
	
	function defOrdreTri ($v_sOrdreTri)
	{
		$this->sOrdreTri = $v_sOrdreTri;
	}
	
	function retOrdreTri ()
	{
		return $this->sOrdreTri;
	}
	
	function retListePersonnes ($v_iIdForm=NULL)
	{
		$sRequeteSql = "SELECT Personne.* FROM Personne";
		
		if ($v_iIdForm > 0)
			$sRequeteSql .= " LEFT JOIN Formation_Inscrit"
				."   ON Personne.IdPers = Formation_Inscrit.IdPers"
				."   AND Formation_Inscrit.IdForm = '{$v_iIdForm}'"
				." LEFT JOIN Formation_Tuteur"
				."   ON Personne.IdPers = Formation_Tuteur.IdPers"
				."   AND Formation_Tuteur.IdForm = '{$v_iIdForm}'"
				." LEFT JOIN Formation_Concepteur"
				."   ON Personne.IdPers = Formation_Concepteur.IdPers"
				."   AND Formation_Concepteur.IdForm = '{$v_iIdForm}'"
				." LEFT JOIN Formation_Resp"
				."   ON Personne.IdPers = Formation_Resp.IdPers"
				."   AND Formation_Resp.IdForm = '{$v_iIdForm}'"
				." LEFT JOIN Projet_Concepteur"
				."   ON Personne.IdPers = Projet_Concepteur.IdPers"
				." LEFT JOIN Projet_Resp"
				."   ON Personne.IdPers = Projet_Resp.IdPers"
				." LEFT JOIN Projet_Admin"
				."   ON Personne.IdPers = Projet_Admin.IdPers"
				." WHERE (Formation_Inscrit.IdForm IS NOT NULL"
				."   OR Formation_Tuteur.IdForm IS NOT NULL"
				."   OR Formation_Concepteur.IdForm IS NOT NULL"
				."   OR Formation_Resp.IdForm IS NOT NULL"
				."   OR Projet_Concepteur.IdPers IS NOT NULL"
				."   OR Projet_Resp.IdPers IS NOT NULL"
				."   OR Projet_Admin.IdPers IS NOT NULL)";
		
		$sRequeteSql .= $this->retTrierSur();
		
		return $this->rechPersonnes($sRequeteSql);
	}
	
	function retListePersonnesGraceIds ($v_aiIdPers)
	{
		$sValeursRequete = NULL;
		$iNbValeurs = 0;
		
		foreach ($v_aiIdPers as $iIdPers)
			if ($iIdPers > 0)
			{
				$sValeursRequete .= (isset($sValeursRequete) ? "," : NULL)
					."'{$iIdPers}'";
				$iNbValeurs++;
			}
		
		if ($iNbValeurs == 0)
			return array();
		
		$sRequeteSql = "SELECT Personne.* FROM Personne"
			." WHERE Personne.IdPers IN ({$sValeursRequete})"
			.$this->retTrierSur()
			." LIMIT {$iNbValeurs}";
		
		return $this->rechPersonnes($sRequeteSql);
	}
	
	function retListeResponsables ($v_iIdForm=NULL,$v_bPotentiel=FALSE)
	{
		$sRequeteSql = "SELECT Personne.* FROM Personne";
		
		if ($v_bPotentiel)
			// Rechercher les responsables potentiel
			$sRequeteSql .= " LEFT JOIN Projet_Resp USING (IdPers)";
		else
			$sRequeteSql .= " LEFT JOIN Formation_Resp USING (IdPers)"
				." WHERE Formation_Resp.IdForm".($v_iIdForm > 0 ? "='{$v_iIdForm}'" : " IS NOT NULL");
		
		$sRequeteSql .= $this->retTrierSur();
		
		return $this->rechPersonnes($sRequeteSql);
	}
	
	function retListeConcepteurs ($v_iIdForm=NULL,$v_bPotentiel=FALSE)
	{
		$sRequeteSql = "SELECT Personne.* FROM Personne";
		
		if ($v_bPotentiel)
			// Rechercher les responsables potentiel
			$sRequeteSql .= " LEFT JOIN Projet_Concepteur USING (IdPers)";
		else
			$sRequeteSql .= " LEFT JOIN Formation_Concepteur USING (IdPers)"
				." WHERE Formation_Concepteur.IdForm".($v_iIdForm > 0 ? "='{$v_iIdForm}'" : " IS NOT NULL");
		
		$sRequeteSql .= $this->retTrierSur();
		
		return $this->rechPersonnes($sRequeteSql);
	}
	
	function retListeTuteurs ($v_iIdForm=NULL)
	{
		$sRequeteSql = "SELECT Personne.* FROM Personne"
			." LEFT JOIN Formation_Tuteur USING (IdPers)"
			." WHERE Formation_Tuteur.IdForm".($v_iIdForm > 0 ? "='{$v_iIdForm}'" : " IS NOT NULL")
			.$this->retTrierSur();
		
		return $this->rechPersonnes($sRequeteSql);
	}
	
	function retListeEtudiants ($v_iIdForm=NULL)
	{
		$sRequeteSql = "SELECT Personne.* FROM Personne"
			." LEFT JOIN Formation_Inscrit USING (IdPers)"
			." WHERE Formation_Inscrit.IdForm".($v_iIdForm > 0 ? "='{$v_iIdForm}'" : " IS NOT NULL")
			.$this->retTrierSur();
		
		return $this->rechPersonnes($sRequeteSql);
	}
}

?>
