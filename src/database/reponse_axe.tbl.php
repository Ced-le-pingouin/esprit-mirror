<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

/*
** Fichier ................: reponse_axe.tbl.php
** Description ............: 
** Date de cr�ation .......: 
** Derni�re modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CReponse_Axe
{
	var $oBdd;
	///var $iId;
	var $oEnregBdd;
	var $aoObjetFormulaire;

	function CReponse_Axe(&$v_oBdd, $v_iIdReponse = 0, $v_iIdAxe = 0) 
	{
		$this->oBdd = &$v_oBdd;
					//si 0 cr�e un objet presque vide sinon 
					//rempli l'objet avec les donn�es de la table Axe
					//de l'el�ment ayant l'Id pass� en argument
		
		$this->iIdReponse = $v_iIdReponse;
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
				." FROM Reponse_Axe"
				." WHERE IdForm='{$this->iIdReponse}'"
				." AND IdAxe='{$this->iIdAxe}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdReponse; //Attention cl� multiple -> incompl�te
	}

	function ajouter()  //Cette fonction ajoute un Objet Axe, avec le champ Poids vide
	{
		$sRequeteSql = "INSERT INTO Formulaire_Axe SET IdForm='{$this->iIdReponse}', IdAxe='{$this->iIdAxe}';";
		//echo "<br>ajouter Reponse_Axe() : ".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		return true;
	}

	/*
	** Fonction 		: VerifierValidite
	** Description		: supprime le(s) poids des r�ponses d'un formulaire pour lesquels 1(ou plusieurs) axe a �t� supprim�
	** Entr�e			:
							$v_iIdFormulaire : Id du formulaire � traiter
							$v_sListeAxes : liste des Id des Axes pr�sent dans le formulaire( cette liste se pr�sente comme ceci ex : 1,5,8)
	** Sortie			: 
	*/
	function VerifierValidite($v_iIdFormulaire,$v_sListeAxes)
	{
		$sRequeteSql = "LOCK TABLES ObjetFormulaire READ, Reponse READ, Reponse_Axe WRITE";
		$this->oBdd->executerRequete($sRequeteSql);
		
		//Cette requ�te donne les enregistrements de la table r�ponse_axe qui ne sont plus valables 
		//car le/les axes ont �t� supprim� du formulaire
		$sRequeteSql ="SELECT Reponse_Axe.*"
		   ." FROM ObjetFormulaire, Reponse, Reponse_Axe"
		   ." WHERE ObjetFormulaire.IdForm = '$v_iIdFormulaire' AND Reponse.IdObjForm = ObjetFormulaire.IdObjForm"
		   ." AND Reponse_Axe.IdReponse = Reponse.IdReponse"
		   ." AND Reponse_Axe.IdAxe NOT IN($v_sListeAxes)";
		
		/* Cette requ�te a �t� abandonn�e car il �tait obligatoire de locker �galement les alias
		$sRequeteSql ="SELECT ra.*"
		   ." FROM ObjetFormulaire as of, Reponse as r, Reponse_Axe as ra"
		   ." WHERE of.IdForm = '$v_iIdFormulaire' AND r.IdObjForm = of.IdObjForm"
		   ." AND ra.IdReponse = r.IdReponse"
		   ." AND ra.IdAxe NOT IN($v_sListeAxes)";
		*/
		//echo "<br>VerifierValidite : ".$sRequeteSql;

		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		$iNbEnreg = $this->oBdd->retNbEnregsDsResult();
		
		if ($iNbEnreg > 0)
		{
			$sListeEffacer="";
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
			{
				$sListeEffacer.="(IdReponse='$oEnreg->IdReponse' AND IdAxe='$oEnreg->IdAxe') OR "; 
			}
			
			$sListeEffacer = subStr($sListeEffacer,0,strlen($sListeEffacer)-4);
			
			$sRequeteSql = 
				"  DELETE FROM Reponse_Axe"
				." WHERE $sListeEffacer ";
			
			//echo "<br>".$sRequeteSql;
			$this->oBdd->executerRequete($sRequeteSql);
		}
		
		$sRequeteSql = "UNLOCK TABLES";
		$this->oBdd->executerRequete($sRequeteSql);
		return TRUE;
	}

	function enregistrer()
	{
		$sRequeteSql =
			"  REPLACE Reponse_Axe SET" 
			." IdReponse='{$this->oEnregBdd->IdReponse}'"
			." , IdAxe='{$this->oEnregBdd->IdAxe}'"
			." , Poids='{$this->oEnregBdd->Poids}'";
			
		//echo "<br>enregistrer : ".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}


	function copier($v_iIdNvReponse)
	{
		if ($v_iIdNvReponse < 1)
			return;
		
		$sRequeteSql =
			"  INSERT INTO Reponse_Axe SET"
			." IdReponse='{$v_iIdNvReponse}'"
			." , IdAxe='{$this->oEnregBdd->IdAxe}'"
			." , Poids='{$this->oEnregBdd->Poids}'";
									
		$this->oBdd->executerRequete($sRequeteSql);
		//echo "<br>Copier".$sRequeteSql;
		$iIdNvReponse = $this->oBdd->retDernierId();
		
		return $iIdNvReponse;
	}
	
	//Fonctions de d�finitions
	function defIdReponse($v_iIdReponse) { $this->oEnregBdd->IdReponse = $v_iIdReponse; }
	function defIdAxe($v_iIdAxe) { $this->oEnregBdd->IdAxe = $v_iIdAxe; }
	function defPoids($v_iPoids) { $this->oEnregBdd->Poids = $v_iPoids; }
	
	// Fonctions de retour
	function retIdReponse() { return $this->oEnregBdd->IdReponse; }
	function retIdAxe() { return $this->oEnregBdd->IdAxe; }
	function retPoids() { return $this->oEnregBdd->Poids; }
}
?>
