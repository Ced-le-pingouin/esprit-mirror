<?php
/*
** Fichier ................: reponse.tbl.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CReponse 
{
	var $oBdd;
	var $iId;
	var $oEnregBdd;
	
	var $aiValeurAxe;
	
	function CReponse(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;  
							//si 0 crée un objet presque vide sinon 
							//rempli l'objet avec les données de la table Reponse
							//de l'elément ayant l'Id passé en argument 
							//(ou avec l'objet passé en argument mais sans passer par le constructeur)
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	//INIT est une fonction que l'on peut utiliser sans passer par le constructeur. 
	//On lui passe alors un objet obtenu par exemple en faisant une requête sur une autre page.
	//Ceci permet alors d'utiliser toutes les fonctions disponibles sur cet objet
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
				." FROM Reponse"
				." WHERE IdReponse='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		$this->iId = $this->oEnregBdd->IdReponse;
	}
	
	function initValeursParAxe($v_sListeAxesAutorises = NULL)
	{
		if (isset($this->aiValeurAxe))
			return;
		
		if (isset($v_sListeAxesAutorises))
			$sSqlAxes = "  AND IdAxe IN ($v_sListeAxesAutorises)";
		else
			$sSqlAxes = "";
		
		$sRequeteSql =
			"  SELECT * FROM Reponse_Axe"
			." WHERE IdReponse = '{$this->iId}'"
			.$sSqlAxes
			." ORDER BY IdAxe"
			;
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			$this->aiValeurAxe[$oEnreg->IdAxe] = $oEnreg->Poids;
		
		$this->oBdd->libererResult($hResult);
	}
	
	function ajouter () 	//Cette fonction ajoute une réponse avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO QReponse SET IdReponse=NULL;";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	function enregistrer ($v_bModifOrdre = TRUE)
	{
		$sTexteReponse = validerTexte($this->oEnregBdd->TexteReponse);

		//Quand on envoie false cela veut dire que l'on ne modifie ni l'ordre, ni l'Id de l'objet auquel
		// se rapporte la réponse
		if ($v_bModifOrdre)
		{
			$sModifOrdre = ", OrdreReponse='{$this->oEnregBdd->OrdreReponse}'";
			$sModifIdObjForm =	", IdObjForm='{$this->oEnregBdd->IdObjForm}'";
		}
		
		$sRequeteSql =
			($this->retId() > 0 ? "UPDATE Reponse SET":"INSERT INTO Reponse SET")
			." IdReponse='{$this->oEnregBdd->IdReponse}'"
			." , TexteReponse='{$sTexteReponse}'"
			.$sModifOrdre
			.$sModifIdObjForm
			.($this->oEnregBdd->IdReponse > 0 ? " WHERE IdReponse='{$this->oEnregBdd->IdReponse}'" : NULL);
		
		//echo "Req enregistrer réponse : ".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);

		return TRUE;
	}

	function copier ($v_iIdObjForm)
	{
		if ($v_iIdObjForm < 1)
			return;
		
		$sTexteReponse = validerTexte($this->oEnregBdd->TexteReponse);
		
		$sRequeteSql =
			"  INSERT INTO Reponse SET"
			//." IdReponse='{$this->oEnregBdd->IdReponse}'"
			." TexteReponse='{$sTexteReponse}'"
			." , OrdreReponse='{$this->oEnregBdd->OrdreReponse}'"
			." , IdObjForm='{$v_iIdObjForm}'";
									
		$this->oBdd->executerRequete($sRequeteSql);
		//echo "<br>Copier".$sRequeteSql;
		$iIdReponse = $this->oBdd->retDernierId();
		
		return $iIdReponse;
	}

	function effacer ()
	{
		$sRequeteSql = "DELETE FROM Reponse WHERE IdReponse ='{$this->oEnregBdd->IdReponse}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSql = "DELETE FROM Reponse_Axe WHERE IdReponse ='{$this->oEnregBdd->IdReponse}'";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}

	/*
	** Fonction 		: effacerRepObj
	** Description		: supprime TOUTES les réponses qui se rapportent à un objet formulaire 
	** Entrée			:
					$v_iIdObjForm : Id de l'objet formulaire à traiter
	** Sortie			: 
	*/
	function effacerRepObj ($v_iIdObjForm)
	{
		$sRequeteSql = "DELETE FROM Reponse WHERE IdObjForm ='$v_iIdObjForm'";
		//echo "effacerRepObj()".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	/*
	** Fonction 		: effacerRepPoidsObj
	** Description		: supprime TOUTES les réponses et leurs poids qui se rapportent à un objet formulaire 
	** Entrée			:
					$v_iIdObjForm : Id de l'objet formulaire à traiter
	** Sortie			: 
	*/
	function effacerRepPoidsObj ($v_iIdObjForm)
	{	
		/*
		$sRequeteSqlSelect = "SELECT DISTINCT" 
	  						." ra.IdReponse"
							." FROM" 
							." Reponse_Axe as ra"
							." ,Reponse as r"
							." WHERE"
							." r.IdObjForm = '{$v_iIdObjForm}' AND ra.IdReponse = r.IdReponse";
		*/
		$sRequeteSql = "LOCK TABLES Reponse WRITE, Reponse_Axe WRITE";
		$this->oBdd->executerRequete($sRequeteSql);
		
		$sRequeteSqlSelect = "SELECT" 
	  						." IdReponse"
							." FROM" 
							." Reponse"
							." WHERE"
							." IdObjForm = '{$v_iIdObjForm}'";
							
		$hResult = $this->oBdd->executerRequete($sRequeteSqlSelect);
		$iNbEnreg = $this->oBdd->retNbEnregsDsResult();
		
		if ($iNbEnreg > 0)
		{
			$sListeIdReponse="";
			
			while ($oEnregSelect = $this->oBdd->retEnregSuiv($hResult))
			{
				$sListeIdReponse.="{$oEnregSelect->IdReponse}".",";
			}
			
			$this->oBdd->libererResult($hResult);
			
			//Ci-dessous : suppression de la virgule de trop a la fin de la chaîne de caractères
			$sListeIdReponse = substr($sListeIdReponse,0,strlen($sListeIdReponse)-1);
			
			//Suppression des poids
			$sRequeteSql = "DELETE FROM Reponse_Axe WHERE IdReponse IN ($sListeIdReponse)";
			//echo "<br>effacerRepPoidsObj()".$sRequeteSql;
			$this->oBdd->executerRequete($sRequeteSql);
			
			//Suppression des réponses
			$sRequeteSql = "DELETE FROM Reponse WHERE IdObjForm ='$v_iIdObjForm'";
			//echo "<br>effacerRepPoidsObj()".$sRequeteSql;
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$sRequeteSql = "UNLOCK TABLES";
		$this->oBdd->executerRequete($sRequeteSql);
		return TRUE;
	}
	
	//Fonctions de définition
	function defId ($v_iIdReponse) { $this->oEnregBdd->IdReponse = $v_iIdReponse; } //Ne pas confondre IdObfForm[Multi] et IdReponse[Unique] - Fonction pas utile car auto_increment ?
	function defTexteReponse ($v_sTexteReponse) { $this->oEnregBdd->TexteReponse = $v_sTexteReponse; }
	function defOrdreReponse ($v_iOrdreReponse) { $this->oEnregBdd->OrdreReponse = $v_iOrdreReponse; }
	function defIdObjForm ($v_iIdObjForm) { $this->oEnregBdd->IdObjForm = $v_iIdObjForm; } //Ne pas confondre IdObfForm[Multi] et IdReponse[Unique] 
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdReponse; }
	function retTexteReponse () { return $this->oEnregBdd->TexteReponse; }
	function retTexte() { return $this->retTexteReponse(); }
	function retOrdreReponse () { return $this->oEnregBdd->OrdreReponse; }
	function retOrdre() { return $this->retOrdreReponse(); }
	function retIdObjForm () { return $this->oEnregBdd->IdObjForm; }
}
?>
