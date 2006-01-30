<?php

/*
** Fichier ................: objetformulaire.tbl.php
** Description ............: 
** Date de cr�ation .......: 
** Derni�re modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CObjetFormulaire 
{
	var $oBdd;
	var $iId;
	var $oEnregBdd;
	
	var $oDetail;
	var $aoReponsesPossibles;
	var $sReponse;
	
	function CObjetFormulaire(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;  
								  //si 0 cr�e un objet presque vide sinon 
								  //rempli l'objet avec les donn�es de la table Formulaire
								  //de l'el�ment ayant l'Id pass� en argument
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	function init ($v_oEnregExistant = NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql =
				"  SELECT *"
				." FROM ObjetFormulaire"
				." WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjForm;
	}
	
	function initDetail($v_bInitValeursParAxe = FALSE, $v_sListeAxesAutorises = NULL)
	{
		if (!$this->retIdTypeObj() || !$this->retId())
			return;
		
		switch($this->retIdTypeObj())
		{
			case OBJFORM_QTEXTELONG:
				$this->oDetail = new CQTexteLong($this->oBdd, $this->retId());
				break;
			
			case OBJFORM_QTEXTECOURT:
				$this->oDetail = new CQTexteCourt($this->oBdd, $this->retId());
				break;
			
			case OBJFORM_QNOMBRE:
				$this->oDetail = new CQNombre($this->oBdd, $this->retId());
				break;
				
			case OBJFORM_QLISTEDEROUL:
				$this->oDetail = new CQListeDeroul($this->oBdd, $this->retId());
				$this->initReponsesPossibles($v_bInitValeursParAxe, $v_sListeAxesAutorises);
				break;
				
			case OBJFORM_QRADIO:
				$this->oDetail = new CQRadio($this->oBdd, $this->retId());
				$this->initReponsesPossibles($v_bInitValeursParAxe, $v_sListeAxesAutorises);
				break;
				
			case OBJFORM_QCOCHER:
				$this->oDetail = new CQCocher($this->oBdd, $this->retId());
				$this->initReponsesPossibles($v_bInitValeursParAxe, $v_sListeAxesAutorises);
				break;
				
			case OBJFORM_MPTEXTE:
				$this->oDetail = new CMPTexte($this->oBdd, $this->retId());
				break;
				
			case OBJFORM_MPSEPARATEUR:
				$this->oDetail = new CMPSeparateur($this->oBdd, $this->retId());
				break;
				
			default:
				//echo "Erreur: num�ro d'objet de formulaire incorrect !<br>";
				break;
		}
	}
	
	function initReponsesPossibles($v_bInitValeursParAxe = FALSE, $v_sListeAxesAutorises = NULL)
	{
		if (isset($this->aoReponsesPossibles))
			return;
		
		$sRequeteSql =
			"  SELECT * FROM Reponse"
			." WHERE IdObjForm = '{$this->iId}'"
			." ORDER BY OrdreReponse"
			;
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$iIndexReponse = $oEnreg->IdReponse;
			$this->aoReponsesPossibles[$iIndexReponse] = new CReponse($this->oBdd);
			$this->aoReponsesPossibles[$iIndexReponse]->init($oEnreg);
			if ($v_bInitValeursParAxe)
				$this->aoReponsesPossibles[$iIndexReponse]->initValeursParAxe($v_sListeAxesAutorises);
		}
		
		/*$iIndexReponse = 0;
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoReponsesPossibles[$iIndexReponse] = new CReponse($this->oBdd);
			$this->aoReponsesPossibles[$iIndexReponse]->init($oEnreg);
			if ($v_bInitValeursParAxe)
				$this->aoReponsesPossibles[$iIndexReponse]->initValeursParAxe($v_sListeAxesAutorises);
			
			$iIndexReponse++;
		}*/
		
		$this->oBdd->libererResult($hResult);
	}
	
	
	function ajouter()  //Cette fonction ajoute un Objet Formulaire, avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO ObjetFormulaire SET IdObjForm=NULL;";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}

	function DeplacerObjet($v_iNouvPos, $v_bVerrouillerTables = TRUE)
	{
		if (empty($v_iNouvPos) || $v_iNouvPos == $this->oEnregBdd->OrdreObjForm)
			return false;

		// Verrouillage de la table ObjetFormulaire
		if ($v_bVerrouillerTables)
			$this->oBdd->executerRequete("LOCK TABLES ObjetFormulaire WRITE");
		
		if ($v_iNouvPos > $this->oEnregBdd->OrdreObjForm)
		{
			$sRequeteSql =
				"  UPDATE ObjetFormulaire SET"
			  	." OrdreObjForm = OrdreObjForm - 1"
				." WHERE OrdreObjForm > '{$this->oEnregBdd->OrdreObjForm}'"
				." AND OrdreObjForm <= '$v_iNouvPos'"
				." AND IdForm = '{$this->oEnregBdd->IdForm}'";
				  
			//echo "<br>deplacer vers le bas : ".$sRequeteSql;
			$this->oBdd->executerRequete($sRequeteSql);
		} 
		else if ($v_iNouvPos < $this->oEnregBdd->OrdreObjForm)
		{
			$sRequeteSql =
				"  UPDATE ObjetFormulaire SET"
				." OrdreObjForm = OrdreObjForm + 1"
				." WHERE OrdreObjForm >= '$v_iNouvPos'"
				." AND OrdreObjForm < '{$this->oEnregBdd->OrdreObjForm}'"
				." AND IdForm = '{$this->oEnregBdd->IdForm}'";
			
			//echo "<br>deplacer vers le haut : ".$sRequeteSql;
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$sRequeteSql =
			"  UPDATE ObjetFormulaire SET"
			." OrdreObjForm = '$v_iNouvPos'"
			." WHERE IdObjForm = '{$this->oEnregBdd->IdObjForm}'";
		
		//echo "<br>replacement du nouveau : ".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		// d�verrouillage de la table ObjetFormulaire
		if ($v_bVerrouillerTables)
			$this->oBdd->executerRequete("UNLOCK TABLES");
		
		return true;
	}
	
	
	/*
	** Fonction 		: NbObjForm
	** Description		: renvoie le nombre total d'objet que comprend un formulaire
	** Entr�e			: 
	**					$v_iNumForm	: num�ro du formulaire � traiter
	** Sortie			:
	**					nombre total d'objets pour ce formulaire
	*/
	function NbObjForm($v_iNumForm) 	//$v_iNumForm = {$this->oEnregBdd->IdForm} ne fonctionne pas car la classe n'existe pas ? Mais aurait pu etre pratique
	{
		$sRequeteSql =
			"  SELECT * FROM ObjetFormulaire"
			." WHERE IdForm ='{$v_iNumForm}'";
		//echo "requete : ".$sRequeteSql;
		$hResult2=$this->oBdd->executerRequete($sRequeteSql);
		$i_NbObjForm = $this->oBdd->retNbEnregsDsResult($hResult2);
		$this->oBdd->libererResult($hResult2);
		//echo "nb objet : ".$i_NbObjForm;
		return $i_NbObjForm;
	}

	/*
	** Fonction 		: OrdreMaxObjForm
	** Description		: renvoie le plus grand num�ro d'ordre (objet) que comprend un formulaire
	** Entr�e			: 
	**					$v_iNumForm	: num�ro du formulaire � traiter
	** Sortie			:
	**					le plus grand num�ro d'ordre pour ce formulaire
	*/
	function OrdreMaxObjForm($v_iNumForm) 	//$v_iNumForm = {$this->oEnregBdd->IdForm} ne fonctionne pas car la classe n'existe pas ? Mais aurait pu etre pratique
	{
		$sRequeteSql =
			"  SELECT MAX(OrdreObjForm) as OrdreMax FROM ObjetFormulaire"
			." WHERE IdForm ='{$v_iNumForm}'";
		
		//echo "requete : ".$sRequeteSql;
		$hResult2=$this->oBdd->executerRequete($sRequeteSql);
		$oEnreg = $this->oBdd->retEnregSuiv($hResult2);
		$iMaxOrdreObjForm = $oEnreg->OrdreMax;
		$this->oBdd->libererResult($hResult2);
		
		//echo "<br>nb objet : ".$iMaxOrdreObjForm;
		return $iMaxOrdreObjForm;
	}

	function cHtmlNumeroOrdre()
	{
		$sCodeHtml = "";
		
		$sCodeHtml .= "<div style=\"padding: 0px 0px 8px 0px;\">\n";
		$sCodeHtml .= "<span class=\"titreModif\">Num�ro d'ordre&nbsp;:&nbsp;</span>";
		$iNbObjets = $this->OrdreMaxObjForm($this->retIdForm());
		$sCodeHtml .= "<select name=\"selOrdreObjet\">\n";
		for ($iIndexOrdreObjet = 1; $iIndexOrdreObjet <= $iNbObjets; $iIndexOrdreObjet++)
		{
			if ($this->retOrdre() != $iIndexOrdreObjet)
				$sSelect = "";
			else
				$sSelect = "SELECTED";
			$sCodeHtml .= "\t<option value=\"{$iIndexOrdreObjet}\" $sSelect>$iIndexOrdreObjet</option>\n";
		}
		$sCodeHtml .= "</select>\n";
		$sCodeHtml .= "</div>\n";
		
		return $sCodeHtml;
	}

	function enregistrer()
	{
		$sRequeteSql =
			($this->retId() > 0 ? "UPDATE ObjetFormulaire SET" : "INSERT INTO ObjetFormulaire SET")
			." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
			.", OrdreObjForm='{$this->oEnregBdd->OrdreObjForm}'"
			.", IdTypeObj='{$this->oEnregBdd->IdTypeObj}'"
			.", IdForm='{$this->oEnregBdd->IdForm}'"
			.($this->oEnregBdd->IdObjForm > 0 ? " WHERE IdForm='{$this->oEnregBdd->IdObjForm}'" : NULL);
		//echo "<br>enregistrer : ".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		$this->defIdObjForm($this->oBdd->retDernierId()); //On place dans l'objet cr�� son Id
		
		return TRUE;
	}


	function copier ($v_iIdFormParent, $iIdObjParent, $v_iOrdreObjet = NULL)
	{
		if ($iIdObjParent < 1)
			return;
		
		$this->oBdd->executerRequete("LOCK TABLES ObjetFormulaire WRITE");
	
		if (empty($v_iOrdreObjet))
		{
			$sSqlOrdreObjet = " OrdreObjForm='{$this->oEnregBdd->OrdreObjForm}'";
		}
		else if ($v_iOrdreObjet == "max")
		{
			$iOrdre = $this->OrdreMaxObjForm($v_iIdFormParent) + 1;
			$sSqlOrdreObjet = " OrdreObjForm='{$iOrdre}'";
		}
		else
		{
			$sSqlOrdreObjet = " OrdreObjForm='{$v_iOrdreObjet}'";
		}
	
		$sRequeteSql =
			"INSERT INTO ObjetFormulaire SET"
			//." IdObjForm='{$this->oEnregBdd->IdObjForm}', "
			.$sSqlOrdreObjet
			.", IdTypeObj='{$this->oEnregBdd->IdTypeObj}'"
			.", IdForm='{$v_iIdFormParent}'";
					
		$this->oBdd->executerRequete($sRequeteSql);
		
		$iIdObjForm = $this->oBdd->retDernierId();
		$this->oBdd->executerRequete("UNLOCK TABLES");
		
		return $iIdObjForm;
	}
	
	function effacer()
	{
		$sRequeteSql =
			"  DELETE FROM ObjetFormulaire"
			." WHERE IdObjForm ='{$this->oEnregBdd->IdObjForm}'";
		//echo "<br>effacer ObjetFormulaire() : ".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		$this->reorganiser();
		return TRUE;
	}


	/*
	** Fonction 		: reorganiser
	** Description		: modifie l'ordre des objets d'un formulaire apr�s suppression d'un de ces objets.
								  Cette fonction s'appelle uniquement apr�s un effacement
	** Entr�e			:
	** Sortie			:
	*/	
	function reorganiser()
	{
		$sRequeteSql =
			"  UPDATE ObjetFormulaire"
			." SET OrdreObjForm = OrdreObjForm-1"
			." WHERE IdForm='{$this->oEnregBdd->IdForm}'"
			." AND OrdreObjForm>'{$this->oEnregBdd->OrdreObjForm}'";
		
		//echo "<br>reorganiser ObjetFormulaire() : ".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	function verrouillerTablesQuestion()
	{
		$this->oBdd->executerRequete
		(
			"LOCK TABLES"
			." ObjetFormulaire WRITE"
			." , Reponse WRITE"
			." , Reponse_Axe WRITE"
			." , QCocher WRITE"
			." , QRadio WRITE"
			." , QListeDeroul WRITE"
			." , QTexteCourt WRITE"
			." , QTexteLong WRITE"
			." , QNombre WRITE"
			." , MPSeparateur WRITE"
			." , MPTexte WRITE"
		);
	}
	
	function deverrouillerTablesQuestion()
	{
		$this->oBdd->executerRequete("UNLOCK TABLES");
	}
	
	// Fonctions de d�finitions
	function defIdObjForm($v_iIdObjForm) { $this->oEnregBdd->IdObjForm = $v_iIdObjForm; }
	function defOrdreObjForm($v_iOrdre) { $this->oEnregBdd->OrdreObjForm = $v_iOrdre; }
	function defIdTypeObj($v_iTypeObj) { $this->oEnregBdd->IdTypeObj = $v_iTypeObj; }
	function defIdForm($v_iIdForm) { $this->oEnregBdd->IdForm = $v_iIdForm; }
	/*function defObjFormulaire($v_iOrdre,$v_iIdForm,$v_iIdForm)
	{
	  $this->oEnregBdd->OrdreObjForm = $v_iOrdre;
	  $this->oEnregBdd->idTypeObj = $v_iTypeObj;
	  $this->oEnregBdd->IdForm = $v_iIdForm;
	}*/
	
	// Fonctions de retour
	function retId() { return $this->oEnregBdd->IdObjForm; }
	function retOrdreObjForm() { return $this->oEnregBdd->OrdreObjForm; }
	function retOrdre() { return $this->retOrdreObjForm(); }
	function retIdTypeObj() { return $this->oEnregBdd->IdTypeObj; }
	function retIdType() { return $this->retIdTypeObj(); }
	function retIdForm() { return $this->oEnregBdd->IdForm; }
}

?>
