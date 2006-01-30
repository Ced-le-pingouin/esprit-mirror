<?php

/*
** Fichier ................: typeobjetform.tbl.php
** Description ............: 
** Date de cr�ation .......: 
** Derni�re modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CTypeObjetForm 
{
 var $oBdd;
 var $iId;
 var $oEnregBdd;
 var $aoFormulaire;

 function CTypeObjetForm(&$v_oBdd,$v_iId=0) 
 {
   			$this->oBdd = &$v_oBdd;  
								  //si 0 cr�e un objet presque vide sinon 
								  //rempli l'objet avec les donn�es de la table TypeObjetForm
								  //de l'el�ment ayant l'Id pass� en argument 
								  //(ou avec l'objet pass� en argument mais sans passer par le constructeur)
		if (($this->iId = $v_iId) > 0)
			$this->init();
 }
	
	//INIT est une fonction que l'on peut utiliser sans passer par le constructeur. 
	//On lui passe alors un objet obtenu par exemple en faisant une requ�te sur une autre page.
	//Ceci permet alors d'utiliser toutes les fonctions disponibles sur cet objet
 function init ($v_oEnregExistant=NULL)  
 {
	    if (isset($v_oEnregExistant))
	    {
				 $this->oEnregBdd = $v_oEnregExistant;
	    }
	    else
	    {
			$sRequeteSql = "SELECT *"
				." FROM TypeObjetForm"
				." WHERE IdTypeObj='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
	    }

		$this->iId = $this->oEnregBdd->IdTypeObj;
 }

 function ajouter () //Cette fonction ajoute une question de type texte court,
				 // avec tous ses champs vide, en fin de table
 {
   $sRequeteSql = "INSERT INTO TypeObjetForm SET IdTypeObj=NULL;";
   $this->oBdd->executerRequete($sRequeteSql);
   return ($this->iId = $this->oBdd->retDernierId());
 }


//Fonctions de d�finition

 function defIdTypeObj ($v_iIdTypeObj)
{
  $this->oEnregBdd->IdTypeObj = $v_iIdTypeObj;
}


 function defNomTypeObj ($v_sNomTypeObj)
{
  $this->oEnregBdd->NomTypeObj = $v_sNomTypeObj;
}

function defDescTypeObj ($v_sDescTypeObj)
{
  $this->oEnregBdd->DescTypeObj = $v_sDescTypeObj;
}

//Fonctions de retour

function retId () { return $this->oEnregBdd->IdTypeObj; }
function retNomTypeObj () { return $this->oEnregBdd->NomTypeObj; }
function retDescTypeObj () { return $this->oEnregBdd->DescTypeObj; }

/*
function enregistrer ()
	{
	if ($this->oEnregBdd->IdObjForm !=NULL)
	   {
		
		// Les variables contenant du "texte" doivent �tre format�es, cela permet 
		//de les stocker dans la BD sans erreur 
		$sEnonQTC = validerTexte($this->oEnregBdd->EnonQTC);
		$sTxtAvQTC = validerTexte($this->oEnregBdd->TxtAvQTC);
		$sTxtApQTC = validerTexte($this->oEnregBdd->TxtApQTC);
		
		//Valeur par d�faut de MaxCar c'est la valeur de LargeurQTC
		if (strlen($this->oEnregBdd->MaxCarQTC) < 1) 
				{$this->oEnregBdd->MaxCarQTC = $this->oEnregBdd->LargeurQTC;}
		
		
		$sRequeteSql = "REPLACE QTexteCourt SET"									  
			." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
			.", EnonQTC='{$sEnonQTC}'"
			.", AlignEnonQTC='{$this->oEnregBdd->AlignEnonQTC}'"
			.", AlignRepQTC='{$this->oEnregBdd->AlignRepQTC}'"
			.", TxtAvQTC='{$sTxtAvQTC}'"
			.", TxtApQTC='{$sTxtApQTC}'"
			.", LargeurQTC='{$this->oEnregBdd->LargeurQTC}'"
			.", MaxCarQTC='{$this->oEnregBdd->MaxCarQTC}'"; 		
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	   }
	else
	   {
	   Echo "Identifiant NULL enregistrement impossible";
	   }
	
	
	}
*/
}

?>
