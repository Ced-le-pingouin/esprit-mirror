<?php

/*
** Fichier ................: formulaire_axe.tbl.php
** Description ............: 
** Date de cr�ation .......: 
** Derni�re modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CFormulaire_Axe
{
	var $oBdd;
	///var $iId;
	var $oEnregBdd;
	var $aoObjetFormulaire;

	function CFormulaire_Axe(&$v_oBdd,$v_iIdFormulaire=0,$v_iIdAxe=0) 
	{
		$this->oBdd = &$v_oBdd;  
						  //si 0 cr�e un objet presque vide sinon 
						  //rempli l'objet avec les donn�es de la table Axe
						  //de l'el�ment ayant l'Id pass� en argument
		
		$this->iIdForm = $v_iIdFormulaire;
		$this->iIdAxe = $v_iIdAxe;
	}
	
	function init ($v_oEnregExistant=NULL)  
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql =
				"  SELECT *"
				." FROM Formulaire_Axe"
				." WHERE IdForm='{$this->iIdForm}'"
				." AND IdAxe='{$this->iIdAxe}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		$this->iId = $this->oEnregBdd->IdForm;  //Attention cl� multiple -> incompl�te
	}

	function ajouter()  		//Cette fonction ajoute un Objet Formulaire_Axe en fin de table
								//Les arguments ont �t� pass� lors de la cr�ation de l'objet
	{
		$sRequeteSql = "INSERT INTO Formulaire_Axe SET IdForm='{$this->iIdForm}', IdAxe='{$this->iIdAxe}';";
		//echo "<br>ajouter() : ".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		return true;
	}

	/*
	** Fonction 		: AxesDsFormulaire
	** Description		: renvoie les  IdAxe des axes pr�sent dans le formulaire dont on lui a envoy� l'Id
	** Entr�e			: 
	**					$v_iIdForm	: num�ro du formulaire � traiter
	** Sortie			:
	**					$TabAxesForm[] :un tableau contenant les Id des axes utilis�s pour ce formulaire
	*/
	
	function AxesDsFormulaire($v_iIdFormulaire)
	{
		$sRequeteSql = "SELECT IdAxe FROM Formulaire_Axe WHERE IdForm = '$v_iIdFormulaire'";
		//echo "requete : ".$sRequeteSql;
		$hResultInt=$this->oBdd->executerRequete($sRequeteSql);
		$i=0;
		$TabAxesForm=array();
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResultInt))
		{
			$TabAxesForm[$i] = $oEnreg->IdAxe;
			//echo "<br>L'axe ".$TabAxesForm[$i]." == ".$oEnreg->IdAxe." est pr�sent dans le formulaire ".$v_iIdFormulaire;
			$i++;
		}
		
		//echo "<br>Nombre d'axes pour ce formulaire : ".$i." == ".count($TabAxesForm);
		
		$this->oBdd->libererResult($hResultInt);
		return $TabAxesForm;
	}

	/*
	** Fonction 		: NomsFormulaires
	** Description		: renvoie le/les nom(s) de(s) formulaire(s) dans le ou lesquels l'axe, dont on a recu l'Id, est pr�sent 
	** Entr�e			: 
	**					$v_iIdAxe	: num�ro de l'axe � traiter
	** Sortie			:
	**					$TabNomsForm[] :un tableau contenant les noms des formulaires qui utilisent cet axe
	*/
	function NomsFormulaires($v_iIdAxe)
	{
		$sRequeteSql =
			"  SELECT f.Titre, fa.IdAxe"
			." FROM Formulaire as f, Formulaire_Axe as fa"
			." WHERE fa.IdAxe = '$v_iIdAxe' AND f.IdForm = fa.IdForm";
		
		$hResultInt=$this->oBdd->executerRequete($sRequeteSql);
		$i=0;
		$TabNomsForm[]=array();
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResultInt))
		{
			$TabNomsForm[$i] = $oEnreg->Titre;
			$i++;
		}
		
		$this->oBdd->libererResult($hResultInt);
		return $TabNomsForm;
	}


	/*
	** Fonction 		: effacerAxesForm
	** Description		: efface tous les axes d'un formulaire donn�
	** Entr�e			: 
	**					$v_iIdFormulaire	: num�ro du formulaire � traiter
	** Sortie			:
	*/
	function effacerAxesForm($v_iIdFormulaire)
	{
		$sRequeteSql = "DELETE FROM Formulaire_Axe"
		." WHERE IdForm ='{$v_iIdFormulaire}'";
		//echo "<br>effacerAxesForm() : ".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}

	function copier($v_iIdNvForm)
	{
		if ($v_iIdNvForm < 1)
			return;
		
		$sRequeteSql = "INSERT INTO Formulaire_Axe SET"
			." IdForm='{$v_iIdNvForm}'"
			.", IdAxe='{$this->oEnregBdd->IdAxe}'";
			
		$this->oBdd->executerRequete($sRequeteSql);
		
		$v_iIdNvForm = $this->oBdd->retDernierId();
		
		return $v_iIdNvForm;
	}
	
	//Fonctions de d�finitions
	function defIdForm($v_iIdFormulaire) { $this->oEnregBdd->IdForm = $v_iIdFormulaire; }
	function defIdAxe($v_iIdAxe) { $this->oEnregBdd->IdAxe = $v_iIdAxe; }
	
	// Fonctions de retour
	function retIdForm() { return $this->oEnregBdd->IdForm; }
	function retIdAxe() { return $this->oEnregBdd->IdAxe; }
}
?>
