<?php

/*
** Fichier .................: evenement.tbl.php
** Description .............:
** Date de création ........: 01/03/2002
** Dernière modification ...: 19/12/2005
** Auteurs .................: Filippo PORCO
** Emails ..................: ute@umh.ac.be
**
*/

require_once(dir_database("evenement_detail.tbl.php"));

define("ERREUR_CONNEXION",1);
define("PERSONNE_CONNECTE",2);
define("PERSONNE_DECONNECTE",3);

class CEvenement
{
	var $oBdd;
	var $iId;
	
	var $aoEvenements;
	var $oConnecte;
	var $oEnregBdd;
	
	var $bParFormations = FALSE;
	
	var $iModeTri, $bTriAscendant;
	
	var $iIdPers;
	
	// Pseudo constant
	var $TRI_NOM = 0;
	var $TRI_PRENOM = 1;
	var $TRI_PSEUDO = 2;
	var $TRI_NBR_CONNEXIONS = 3;
	var $TRI_DERNIERE_CONNEXION = 4;
	var $TRI_DERNIERE_DECONNEXION = 5;
	var $TRI_CONNEXION = 6;
	var $TRI_DECONNEXION = 7;
	var $TRI_TEMPS_CONNEXIONS = 8;
	var $TRI_FORMATION = 9;
	
	var $TRI_ASCENDANT = TRUE;
	var $TRI_DESCENDANT = FALSE;
	
	function CEvenement ($v_oBdd,$v_iId=NULL)
	{
		$this->oBdd = &$v_oBdd;
		$this->iModeTri = $this->TRI_NOM;
		$this->bTriAscendant = $this->TRI_ASCENDANT;
		$this->iIdPers = NULL;
		
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	function init ($v_oEnregBddExistant=NULL)
	{
		if (isset($v_oEnregBddExistant))
		{
			$this->oEnregBdd = $v_oEnregBddExistant;
			
			if (isset($v_oEnregBddExistant->IdPers))
			{
				$this->oConnecte = new CPersonne($this->oBdd);
				$this->oConnecte->init($v_oEnregBddExistant);
			}
		}
		else
		{
			$sRequeteSql = "SELECT Evenement.*"
				." FROM Evenement"
				." WHERE Evenement.IdEven='{$this->iId}'";
			
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
	}
	
	function initEvenementsPersonne ($v_iIdPers,$v_iIdForm)
	{
		$this->iIdPers = $v_iIdPers;
		$this->iIdForm = $v_iIdForm;
		$this->iModeTri = $this->TRI_CONNEXION;
		$this->bTriAscendant = $this->TRI_DESCENDANT;
		
		return $this->initEvenements();
	}
	
	function effacer ()
	{
		if (isset($this->iIdForm) && $this->iIdForm > 0)
		{
			$oEvenDetail = new CEvenement_Detail($this->oBdd,NULL,$this->iIdForm);
			$aiIdsEvens = $oEvenDetail->retIdsEvensParFormation();
			$oEvenDetail->effacerParFormation();
			
			$sValeursSql = NULL;
			
			foreach ($aiIdsEvens as $iId)
				$sValeursSql .= (isset($sValeursSql) ? "," : NULL)
					."'{$iId}'";
			
			if (isset($sValeursSql))
			{
				$sRequeteSql = "DELETE FROM Evenement"
					." WHERE IdEven IN ({$sValeursSql})"
					." AND MomentEven IS NOT NULL"
					." AND SortiMomentEven IS NOT NULL";
				
				$this->oBdd->executerRequete($sRequeteSql);
			}
		}
	}
	
	function defIdFormation ($v_iIdForm) { $this->iIdForm = $v_iIdForm; }
	function defParFormations ($v_bParFormations=FALSE) { $this->bParFormations = $v_bParFormations; }
	
	function retListeConnectes ()
	{
		$sRequeteSql = "select CURDATE()";
		
		$this->oBdd->executerRequete ($sRequeteSql);
		
		$date = $this->oBdd->retEnregPrecis();
		
		$sRequeteSql = "SELECT * FROM Evenement"
			." WHERE IdPers>'0'"
			." AND IdTypeEven=".PERSONNE_CONNECTE
			." AND MomentEven LIKE \"$date%\" AND SortiMomentEven IS NULL";
		
		$asNomConnectes = array();
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($ligne=$this->oBdd->retEnregSuiv($hResult))
		{
			$oPers = new CPersonne($this->oBdd,$ligne->IdPers);
			$asNomConnectes[] = $oPers->retNomComplet();
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $asNomConnectes;
	}
	
	function defPersonne ($v_iIdPers=NULL) { $this->iIdPers = $v_iIdPers; }
	function retPersonne () { return $this->iIdPers; }
	function defModeTri ($v_iModeTri) { $this->iModeTri = $v_iModeTri; }
	
	function retModeTri ()
	{
		$asModeTri = array("Personne.Nom"
			,"Personne.Prenom"
			,"Personne.Pseudo"
			,"NbrConnexion"
			,"MaxMomentEven"
			,"MaxSortiMomentEven"
			,($this->bParFormations ? "Evenement_Detail" : "Evenement").".MomentEven"
			,($this->bParFormations ? "Evenement_Detail" : "Evenement").".SortiMomentEven"
			,"TempsEven"
			,"Formation.OrdreForm");
		
		return ($asModeTri[$this->iModeTri]." ".($this->bTriAscendant ? "ASC" : "DESC"));
	}
	
	function defTriAscendant ($v_bTriAscendant=TRUE) { $this->bTriAscendant = $v_bTriAscendant; }
	
	function requeteParFormations ()
	{
		return "SELECT Evenement_Detail.*"
				.", Evenement_Detail.MomentEven AS MomentEven"
				.", Evenement_Detail.SortiMomentEven AS SortiMomentEven"
				.", SEC_TO_TIME("
					."UNIX_TIMESTAMP(Evenement_Detail.SortiMomentEven) - UNIX_TIMESTAMP(Evenement_Detail.MomentEven)"
				.") AS TempsEven"
				.", Formation.NomForm AS NomForm"
			." FROM Evenement_Detail"
			." LEFT JOIN Evenement USING (IdEven)"
			." LEFT JOIN Formation ON Evenement_Detail.IdForm=Formation.IdForm"
			." LEFT JOIN Personne ON Evenement.IdPers=Personne.IdPers"
			." WHERE Evenement.IdPers='{$this->iIdPers}'"
			." ORDER BY ".$this->retModeTri();
	}
	
	function requeteParPersonne ()
	{
		$this->bParFormations = TRUE;
		
		return "SELECT Evenement.*"
				.", Evenement_Detail.MomentEven"
				.", Evenement_Detail.SortiMomentEven"
				.", SEC_TO_TIME(UNIX_TIMESTAMP(Evenement_Detail.SortiMomentEven) - UNIX_TIMESTAMP(Evenement_Detail.MomentEven)) AS TempsEven"
			." FROM Evenement_Detail"
			." LEFT JOIN Evenement USING (IdEven)"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Evenement_Detail.IdForm='{$this->iIdForm}'"
				." AND Evenement.IdPers='{$this->iIdPers}'"
			." ORDER BY ".$this->retModeTri();
	}
	
	function requeteParProjet ()
	{
		return "SELECT *, UPPER(Nom) AS Nom"
				.", COUNT(Evenement.IdPers) AS NbrConnexion"
				.", MAX(Evenement.MomentEven) AS MomentEven"
				.", MAX(Evenement.SortiMomentEven) AS SortiMomentEven"
				.", SEC_TO_TIME(UNIX_TIMESTAMP(MAX(SortiMomentEven)) - UNIX_TIMESTAMP(MAX(MomentEven))) AS TempsEven"
				.", SEC_TO_TIME(SUM(UNIX_TIMESTAMP(SortiMomentEven) - UNIX_TIMESTAMP(MomentEven))) AS DureeTotaleConnexions"
			." FROM Evenement"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Evenement.IdPers IS NOT NULL"
			." GROUP BY Evenement.IdPers"
			." ORDER BY ".$this->retModeTri();
	}
	
	function requeteParEvenForm ()
	{
		return "SELECT *, UPPER(Nom) AS Nom"
				.", COUNT(Evenement.IdPers) AS NbrConnexion"
				.", MAX(Evenement_Detail.MomentEven) AS MaxMomentEven"
				.", MAX(Evenement_Detail.SortiMomentEven) AS MaxSortiMomentEven"
				.", SEC_TO_TIME(UNIX_TIMESTAMP(MAX(Evenement_Detail.SortiMomentEven)) - UNIX_TIMESTAMP(MAX(Evenement_Detail.MomentEven))) AS TempsEven"
				.", SEC_TO_TIME(SUM(UNIX_TIMESTAMP(Evenement_Detail.SortiMomentEven) - UNIX_TIMESTAMP(Evenement_Detail.MomentEven))) AS DureeTotaleConnexions"
			." FROM Evenement_Detail"
			." LEFT JOIN Evenement USING (IdEven)"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Evenement_Detail.IdForm='{$this->iIdForm}'"
			." GROUP BY Personne.IdPers"
			." ORDER BY ".$this->retModeTri();
	}
	
	function initEvenements ()
	{
		if ($this->bParFormations)
			$sRequeteSql = $this->requeteParFormations();
		else if ($this->iIdPers > 0)
			$sRequeteSql = $this->requeteParPersonne();
		else
			$sRequeteSql =  $this->requeteParEvenForm();
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$iIdxEven = 0;
		$this->aoEvenements = array();
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoEvenements[$iIdxEven] = new CEvenement($this->oBdd);
			$this->aoEvenements[$iIdxEven]->init($oEnreg);
			$iIdxEven++;
		}
		
		$this->oBdd->libererResult($hResult);
		
		return $iIdxEven;
	}
	
	function retMomentEven () { return $this->oEnregBdd->MomentEven; }
	function retNbConnexions () { return $this->oEnregBdd->NbrConnexion; }
	function retConnexion ($v_bFormatDateCours=TRUE,$v_bFormatHeureCours=FALSE) { return retDateLocale($this->oEnregBdd->MomentEven,$v_bFormatDateCours,$v_bFormatHeureCours); }
	
	function retDeconnexion ($v_bFormatDateCours=TRUE,$v_bFormatHeureCours=FALSE)
	{
		$sDateDeconnexion = $this->oEnregBdd->SortiMomentEven;
		
		if ($sDateDeconnexion < $this->oEnregBdd->MomentEven)
			$sDateDeconnexion = NULL;
			
		return retDateLocale($sDateDeconnexion,$v_bFormatDateCours,$v_bFormatHeureCours);
	}
	
	function retTempsConnexion ($bFormatCours=FALSE)
	{
		$sDureeConnexion = "&#8211;";
		
		if (!empty($this->oEnregBdd->TempsEven))
		{
			$sDureeConnexion = $this->oEnregBdd->TempsEven;
			
			if ($bFormatCours)
				$sDureeConnexion = ereg_replace("([0-9]{2}):([0-9]{2}):([0-9]{2})","\\1:\\2",$sDureeConnexion);
			
			if (ereg("^-",$sDureeConnexion)) // "-00:00:15" => "-"
				$sDureeConnexion = "&#8211;";
			else if (ereg("^00:00\$",$sDureeConnexion)) // "00:00:20" => "< 1min"
				$sDureeConnexion = "&#8249;&nbsp;1min";
		}
		
		return $sDureeConnexion;
	}
	
	function retNavigateur () { return $this->oEnregBdd->DonneesEven; }
	
	function retDateDerniereConnexion ($v_bFormatDateCours=TRUE,$v_bFormatHeureCours=TRUE)
	{
		return retDateLocale($this->oEnregBdd->MaxMomentEven,$v_bFormatDateCours,$v_bFormatHeureCours);
	}
	
	function retDateDerniereDeconnexion ($v_bFormatDateCours=TRUE,$v_bFormatHeureCours=TRUE)
	{
		$sDateDeconnexion = $this->oEnregBdd->MaxSortiMomentEven;
		
		if ($sDateDeconnexion < $this->oEnregBdd->MaxMomentEven)
			$sDateDeconnexion = NULL;
		
		return retDateLocale($sDateDeconnexion,$v_bFormatDateCours,$v_bFormatHeureCours);
	}
	
	function retDureeTotaleConnexions ($v_iIdForm=NULL)
	{
		if (!isset($v_iIdForm))
			$v_iIdForm = $this->iIdForm;
		
		$sDureeTotaleConnexions = "&#8211;";
		
		if ($this->iIdPers > 0)
		{
			if ($this->bParFormations)
				$sRequeteSql = "SELECT SEC_TO_TIME(SUM(UNIX_TIMESTAMP(Evenement_Detail.SortiMomentEven) - UNIX_TIMESTAMP(Evenement_Detail.MomentEven)))"
					." AS DureeTotaleConnexions"
					." FROM Evenement_Detail"
					." LEFT JOIN Evenement USING (IdEven)"
					." WHERE Evenement.IdPers='{$this->iIdPers}'"
					." AND Evenement_Detail.IdForm='{$v_iIdForm}'";
			else
				$sRequeteSql = "SELECT SEC_TO_TIME(SUM(UNIX_TIMESTAMP(SortiMomentEven) - UNIX_TIMESTAMP(MomentEven)))"
					." AS DureeTotaleConnexions"
					." FROM Evenement"
					." WHERE IdPers='{$this->iIdPers}'";
			
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			
			$this->oBdd->libererResult($hResult);
		}
		
		return (empty($this->oEnregBdd->DureeTotaleConnexions) ? "&#8211;" : $this->oEnregBdd->DureeTotaleConnexions);
	}
	
	/*function retIdFormation () { return $this->oEnregBdd->IdForm; }*/
	/*function retNomFormation () { return $this->oEnregBdd->NomForm; }*/
	
	function initFichierExporter ($v_sNomFichierCSV=NULL)
	{
		if (isset($v_sNomFichierCSV))
			$sFichier = $v_sNomFichierCSV;
		else
		{
			$aDate = getDate();
			
			$sFichier = "even-"
				.$aDate["mday"].$aDate["mon"].$aDate["year"]
				."_"
				.$aDate["hours"].$aDate["minutes"].$aDate["seconds"]
				.".csv";
		}
		
		$sFichierTmp = dir_tmp($sFichier,TRUE);
		
		$sRequeteSql = "SELECT *"
			.", DATE_FORMAT(MomentEven,\"%d/%m/%y\") AS DateConnexion"
			.", DATE_FORMAT(MomentEven,\"%H:%i:%s\") AS HeureConnexion"
			.", DATE_FORMAT(SortiMomentEven,\"%H:%i:%s\") AS HeureDeconnexion"
			.", SEC_TO_TIME(UNIX_TIMESTAMP(SortiMomentEven) - UNIX_TIMESTAMP(MomentEven)) AS DureeConnexion"
			." FROM Evenement"
			." LEFT JOIN Personne USING (IdPers)"
			." WHERE Evenement.IdPers IS NOT NULL"
			." ORDER BY Personne.Nom, Evenement.MomentEven DESC";
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$fp = fopen($sFichierTmp,"w");
		
		$sTmp = "\"Nom\""
			.";\"Prenom\""
			.";\"Pseudo\""
			.";\"Sexe\""
			.";\"Connexion (date)\""
			.";\"Connexion (heure)\""
			.";\"Déconnexion (heure)\""
			.";\"Durée\""
			.";\"Navigateur\""
			."\r\n";
		
		fputs($fp,$sTmp);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$sTmp = "\"{$oEnreg->Nom}\""
				.";\"{$oEnreg->Prenom}\""
				.";\"{$oEnreg->Pseudo}\""
				.";\"{$oEnreg->Sexe}\""
				.";\"{$oEnreg->DateConnexion}\""
				.";\"{$oEnreg->HeureConnexion}\""
				.";\"{$oEnreg->HeureDeconnexion}\""
				.";\"{$oEnreg->DureeConnexion}\""
				.";\"{$oEnreg->DonneesEven}\""
				."\r\n";
			fputs($fp,$sTmp);
		}
		
		fclose($fp);
		
		$this->oBdd->libererResult($hResult);
		
		return $sFichierTmp;
	}
}

function retDateLocale ($v_sDate,$v_bFormatDateCours=FALSE,$v_bFormatHeureCours=FALSE)
{
	if (empty($v_sDate))
		$sDateLocale = "&#8211;";
	else
	{
		ereg("([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})",$v_sDate,$a);
		
		$sDateLocale = "{$a[3]}/{$a[2]}/"
			.($v_bFormatDateCours ? substr("{$a[1]}",-2) : "{$a[1]}")
			." {$a[4]}:{$a[5]}"
			.($v_bFormatHeureCours ? NULL :":{$a[6]}");
		
		unset($a);
	}
	
	return $sDateLocale;
}

?>
