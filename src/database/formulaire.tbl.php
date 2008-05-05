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

/**
 * @file	formulaire.tbl.php
 * 
 * Contient la classe de gestion des formulaires, en rapport avec la DB
 * 
 * @date	2004/05/05
 * 
 * @author	Ludovic FLAMME
 */

/**
* Gestion des formulaires, et encapsulation de la table Formulaire de la DB
*/
class CFormulaire 
{
	var $oBdd;			///< Objet représentant la connexion à la DB
	var $oEnregBdd;		///< Quand l'objet a été rempli à partir de la DB, les champs de l'enregistrement sont disponibles ici

	var $iId;			///< Utilisé dans le constructeur, pour indiquer l'id de la formation à récupérer dans la DB

	var $aoFormulaire;	///< Tableau rempli par #retListeFormulairesVisibles(), contenant des formulaires
	var $aoObjets;		///< Tableau rempli par #initObjets(), contenant des objets de formulaire
	var $aoAxes;		///< Tableau rempli par #initAxes(), contenant les axes du formulaire courant
	
	var $oExportation;	///< objet initialisé par #determinerDonneesAExporter(), contenant les objet à exporter du formulaire courant

	/**
	 * Constructeur.	Voir CPersonne#CPersonne()
	 * 
	 */
	function CFormulaire(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;
			
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}

	/**
	 * Initialise l'objet avec un enregistrement de la DB ou un objet PHP existant représentant un tel enregistrement
	 * Voir CPersonne#init()
	 */
	function init ($v_oEnregExistant=NULL)  
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM Formulaire"
					." WHERE IdFormul='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdFormul;
	}
	
	/**
	 * Initialise un tableau contenant les axes du formulaire courant
	 */
	function initAxes()
	{
		if (isset($this->aoAxes))
			return;
		
		$sRequeteSql =	"  SELECT a.* FROM Axe AS a, Formulaire_Axe AS fa"
				." WHERE fa.IdFormul='".$this->retId()."' AND fa.IdAxe=a.IdAxe"
				." ORDER BY a.IdAxe";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$iIndexAxe = $oEnreg->IdAxe;
			$this->aoAxes[$iIndexAxe] = new CAxe($this->oBdd);
			$this->aoAxes[$iIndexAxe]->init($oEnreg);
		}
		$this->oBdd->libererResult($hResult);
	}
	
	/**
	 * Initialise un tableau contenant les objet du formulaire courant
	 * 
	 * @param	v_bInitDetail	si \c true(defaut) initialise les objets du formulaire, la fonction initAxes() doit être
	 * 							appelée avant celle-ci
	 * @param	v_bInitValeursParAxe si \c true(\c false par défaut), initialise les reponses de l'objet par axe
	 */
	function initObjets($v_bInitDetail = TRUE, $v_bInitValeursParAxe = FALSE)
	{
		if (isset($this->aoObjets))
			return;
		
		$sListeAxes = NULL;
		if (isset($this->aoAxes))
		{
			foreach ($this->aoAxes as $oAxe)
				$aiAxes[] = $oAxe->retId();
			
			if (count($aiAxes))
				$sListeAxes = implode(',', $aiAxes);
		}
		
		$sRequeteSql =	"SELECT * FROM ObjetFormulaire"
				." WHERE IdFormul=".$this->retId()
				." ORDER BY OrdreObjFormul";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$iIndexObjet = $oEnreg->IdObjFormul;
			$this->aoObjets[$iIndexObjet] = new CObjetFormulaire($this->oBdd);
			$this->aoObjets[$iIndexObjet]->init($oEnreg);
			if ($v_bInitDetail)
				$this->aoObjets[$iIndexObjet]->initDetail($v_bInitValeursParAxe, $sListeAxes);
		}
		$this->oBdd->libererResult($hResult);
	}
	
	/**
	 * Initialise oExportation qui contiendra les données à exporter.
	 * Exporte seulement les listes, radios, et cases à cocher.
	 * Les axes, objets, réponses possibles, et valeurs par axe doivent tous être initialisés pour que ça fonctionne
	 * 
	 * @param	v_iValeurAxeNeutreMin	nombre représentant le début de l'intervalle pour lequel les réponses ne seront pas exportées.
	 * 									Cet intervalle correspond aux valeurs de l'axe qui ne sont pas prisent en compte
	 * 									lors de l'exportation
	 * @param	v_iValeurAxeNeutreMax	nombre représentant le début de l'intervalle pour lequel les réponses ne seront pas exportées
	 */
	function determinerDonneesAExporter($v_iValeurAxeNeutreMin = 0, $v_iValeurAxeNeutreMax = 0)
	{
		// effacement d'éventuelles données d'exportation existantes
		unset($this->oExportation->abExporterAxeObjet);
		unset($this->oExportation->abExporterObjetSansAxe);
		
		// certaines valeurs peuvent être considérées comme neutres, dans ce cas elles ne seront pas exportées
		// (on calcule aussi la moyenne de la valeur neutre, pour éventuellement l'utiliser plus tard (assigner une valeur par défaut ?))
		$this->oExportation->iValeurAxeNeutreMin = $v_iValeurAxeNeutreMin;
		$this->oExportation->iValeurAxeNeutreMax = $v_iValeurAxeNeutreMax;
		$this->oExportation->iValeurAxeNeutre = $v_iValeurAxeNeutreMin + ( ($v_iValeurAxeNeutreMax - $v_iValeurAxeNeutreMin) / 2 );
		
		// on "marque" pour l'exportation les éléments
		foreach ($this->aoObjets as $oObjet)
		{
			// dans le cas des questions liste, radio, et cases à cocher, on doit déterminer si on exporte en fonction des axes ou pas (alors, on exporte le texte)
			if ($oObjet->retIdType() == OBJFORM_QLISTEDEROUL || $oObjet->retIdType() == OBJFORM_QRADIO || $oObjet->retIdType() == OBJFORM_QCOCHER)
			{
				$bAucuneReponseAxe = TRUE;
				// s'il y a bien des axes dans le formulaire, on va exporter la valeur/axe des réponses (mode 1)
				if (count($this->aoAxes))
				{
					foreach ($oObjet->aoReponsesPossibles as $oReponsePossible)
					{
						foreach ($this->aoAxes as $oAxe)
						{
							$iAxe = $oAxe->retId();
							if ( isset($oReponsePossible->aiValeurAxe[$iAxe]) 
							  && ($oReponsePossible->aiValeurAxe[$iAxe] < $v_iValeurAxeNeutreMin || $oReponsePossible->aiValeurAxe[$iAxe] > $v_iValeurAxeNeutreMax) 
							  && !$this->oExportation->abExporterAxeObjet[$iAxe][$oObjet->retId()] )
							{
								$this->oExportation->abExporterAxeObjet[$iAxe][$oObjet->retId()] = TRUE;
								$bAucuneReponseAxe = FALSE;
							}
						}
					}
				}
				
				// s'il n'y a pas d'axes au formulaire, ou que toutes les réponses sont neutres pour tous les axes, on exportera
				// le contenu de la réponse au lieu de la valeur/axe ( = mode 2)
				if ($bAucuneReponseAxe)
				{
					$this->oExportation->abExporterObjetSansAxe[$oObjet->retId()] = TRUE;
				}
			}
			// dans le cas des questions ouvertes (texte à taper), on exporte en mode 2, càd le texte (donc pas de valeur/axe)
			else if ($oObjet->retIdType() == OBJFORM_QTEXTELONG || $oObjet->retIdType() == OBJFORM_QTEXTECOURT || $oObjet->retIdType() == OBJFORM_QNOMBRE)
			{
				$this->oExportation->abExporterObjetSansAxe[$oObjet->retId()] = TRUE;
			}
		}
	}
	
	/**
	 * Ajoute un nouveau formulaire dans la DB
	 * 
	 * @param	iIdPers	l'id de la personne
	 * 
	 * @return	l'id du nouveau formulaire
	 */
	function ajouter($iIdPers)
	{
		$sRequeteSql = "INSERT INTO Formulaire SET IdFormul=NULL, Titre='Nouvelle activité en ligne', Encadrer=1, TypeLarg='N', Largeur=5, InterElem=10, InterEnonRep=5, IdPers='$iIdPers';";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	/**
	 * Enregistre les données du formulaire courant dans la DB
	 */
	function enregistrer()
	{
		// Les variables contenant du "texte" doivent être formatées, cela permet 
		//de les stocker dans la BD sans erreur 
		$sTitre = validerTexte($this->oEnregBdd->Titre);
		
		$sRequeteSql = ($this->retId() > 0 ? "UPDATE Formulaire SET" : "INSERT INTO Formulaire SET")
					." Titre='{$sTitre}'"
					.", Encadrer='{$this->oEnregBdd->Encadrer}'"
					.", Largeur='{$this->oEnregBdd->Largeur}'"
					.", TypeLarg='{$this->oEnregBdd->TypeLarg}'"
					.", InterElem='{$this->oEnregBdd->InterElem}'"
					.", InterEnonRep='{$this->oEnregBdd->InterEnonRep}'"
					.", RemplirTout='".$this->retRemplirTout()."'"
					.", Statut='{$this->oEnregBdd->Statut}'"
					.", Type='{$this->oEnregBdd->Type}'"
					.", AutoCorrection='{$this->oEnregBdd->AutoCorrection}'"
					.", MethodeCorrection='{$this->oEnregBdd->MethodeCorrection}'"
					.", IdPers='{$this->oEnregBdd->IdPers}'"
					.($this->oEnregBdd->IdFormul > 0 ? " WHERE IdFormul='{$this->oEnregBdd->IdFormul}'" : NULL);
		
		$this->oBdd->executerRequete($sRequeteSql);
	}
	  
	  /**
	   * Copie le formulaire courant dans un nouveau formulaire appartenant à la nouvelle personne
	   * 
	   * @param	v_iIdPers	l'id de la personne
	   * 
	   * @return l'id du nouveau formulaire
	   */
	  function copier($v_iIdPers)
	  {
		// Les variables contenant du "texte" doivent être formatées, cela permet 
		// de les stocker dans la BD sans erreur 
		$sTitre = "Copie de ".validerTexte($this->oEnregBdd->Titre);
		
		$sRequeteSql = "INSERT INTO Formulaire SET"
					." Titre='{$sTitre}'"
					.", Encadrer='{$this->oEnregBdd->Encadrer}'"
					.", Largeur='{$this->oEnregBdd->Largeur}'"
					.", TypeLarg='{$this->oEnregBdd->TypeLarg}'"
					.", InterElem='{$this->oEnregBdd->InterElem}'"
					.", InterEnonRep='{$this->oEnregBdd->InterEnonRep}'"
					.", Statut='{$this->oEnregBdd->Statut}'"
					.", Type='{$this->oEnregBdd->Type}'"
					.", AutoCorrection='{$this->oEnregBdd->AutoCorrection}'"
					.", MethodeCorrection='{$this->oEnregBdd->MethodeCorrection}'"
					.", IdPers='{$v_iIdPers}'";
			
		$this->oBdd->executerRequete($sRequeteSql);
		
		$iIdFormul = $this->oBdd->retDernierId();
		
		return $iIdFormul;
	  }

	/**
	 * Efface le formulaire courant
	 */
	function effacer()
	{
		$sRequeteSql = "DELETE FROM Formulaire WHERE IdFormul ='{$this->oEnregBdd->IdFormul}'";
		$this->oBdd->executerRequete($sRequeteSql);
	}
	
	/**
	 * Retourne la liste des formulaires visibles
	 * 
	 * @param	v_iIdAuteur		l'id de l'auteur des formulaires
	 * @param	v_sType			le type de formulaire
	 * @param	v_iStatut		le statut du formulaire
	 * @param	v_bToutMontrer	si \c true(\c false par défaut), la liste complète des formulaires sera retournée
	 * 
	 * @return	la liste des formulaires visibles
	 */
	function retListeFormulairesVisibles($v_iIdAuteur = NULL , $v_sType = NULL, $v_iStatut = NULL, $v_bToutMontrer = FALSE) // Statut 1 = terminé
	{
		$sRequeteSql = "SELECT * FROM Formulaire";
		if (!$v_bToutMontrer)
		{
			if (isset($v_sType))
			{
				$sRequeteSql .= " WHERE (Type='$v_sType'";
				if (isset($v_iIdAuteur))
					$sRequeteSql .= " OR IdPers='$v_iIdAuteur')";
				else
					$sRequeteSql .= " )";
			}
			else
			{
				if (isset($v_iIdAuteur))
					$sRequeteSql .= " WHERE IdPers='$v_iIdAuteur'";
			}
			if (isset($v_iStatut))
				$sRequeteSql .= " AND Statut='$v_iStatut'";
		}
		$sRequeteSql .= " ORDER BY Titre";
		
		$hResultForms = $this->oBdd->executerRequete($sRequeteSql);
		$iIndexFormulaire = 0;
		$r_aoFormulaires = array();
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResultForms))
		{
			$r_aoFormulaires[$iIndexFormulaire] = new CFormulaire($this->oBdd);
			$r_aoFormulaires[$iIndexFormulaire]->init($oEnreg);
			$iIndexFormulaire++;
		}
		$this->oBdd->libererResult($hResultForms);
		return $r_aoFormulaires;
	}

	/**
	 * Retourne le nombre d'utilisation du formulaire
	 * 
	 * @return	le nombre d'utilisation du formulaire
	 */
	function retNbUtilisationsDsSessions()
	{
		$sRequeteSql = "SELECT COUNT(*) FROM SousActiv"
					." WHERE IdTypeSousActiv='".LIEN_FORMULAIRE."'"
					."  AND LEFT(DonneesSousActiv, ".(strlen($this->retId()) + 1).") = '".$this->retId().";'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNb = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		
		return $iNb;
	}
	
	/**
	 * Retourne le nombre de formulaire complété
	 * 
	 * @return	le nombre de formulaire complété
	 */
	function retNbRemplisDsSessions()
	{
		$sRequeteSql = "SELECT COUNT(*) FROM FormulaireComplete WHERE IdFormul='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNb = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		
		return $iNb;
	}
	
	/**
	 * Retourne la liste des objets de formulaire
	 * 
	 * @return	la liste des objets de formulaire
	 */
	function retListeObjetFormulaire()
	{
		$iIdxObjForm = 0;
		$aoObjetFormulaire = array();
		$sRequeteSql = "SELECT * FROM ObjetFormulaire WHERE IdFormul ='".$this->retId()."' ORDER by OrdreObjFormul";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		while ($oEnregBdd = $this->oBdd->retEnregSuiv($hResult))
		{
				$aoObjetFormulaire[$iIdxObjForm] = new CObjetFormulaire($this->oBdd);
				$aoObjetFormulaire[$iIdxObjForm]->init($oEnregBdd);
				$iIdxObjForm++;
		}
		$this->oBdd->libererResult($hResult);
		return $aoObjetFormulaire;
	}
	
	/**
	 * Retourne le nombre d'éléments de cette activité en ligne
	 * 
	 * @return	le nombre d'éléments de cette activité en ligne
	 */
	function retNbreObjetFormulaire()
	{
		$sRequeteSql = "SELECT COUNT(*) FROM ObjetFormulaire"
					." WHERE IdFormul='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNb = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		return $iNb;
	}
	
	/**
	 * Retourne le nombre d'éléments qui ne sont pas auto-corrigés
	 * 
	 * @return	le nombre d'éléments qui ne sont pas auto-corrigés
	 */
	function retNbreObjetFormulaireNonAutoCorrige()
	{
		$sRequeteSql = "SELECT COUNT(*) FROM ObjetFormulaire"
					." WHERE IdFormul='".$this->retId()."' AND IdTypeObj IN (".OBJFORM_QTEXTELONG.",".OBJFORM_QTEXTECOURT.",".OBJFORM_QNOMBRE.")";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNb = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		return $iNb;
	}
	
	/** @name Fonctions de définition des champs pour ce formulaire */
	//@{
	function defTitre ($v_sTitre) { $this->oEnregBdd->Titre = trim($v_sTitre); }
	function defEncadrer ($v_iEncadrer) { $this->oEnregBdd->Encadrer = $v_iEncadrer; }
	function defLargeur ($v_iLargeur) { $this->oEnregBdd->Largeur = $v_iLargeur; }
	function defTypeLarg ($v_sTypeLarg) { $this->oEnregBdd->TypeLarg = trim($v_sTypeLarg); }
	function defInterElem ($v_iInterElem) { $this->oEnregBdd->InterElem = $v_iInterElem; }
	function defInterEnonRep ($v_iInterEnonRep) { $this->oEnregBdd->InterEnonRep = trim($v_iInterEnonRep); }
	function defRemplirTout ($v_bRemplirTout) { $this->oEnregBdd->RemplirTout = ( $v_bRemplirTout?1:0 ); }
	function defStatut ($v_iStatut) { $this->oEnregBdd->Statut = $v_iStatut; }
	function defType ($v_sType) { $this->oEnregBdd->Type = trim($v_sType); }
	function defAutoCorrection ($v_iAutoCorrect) { $this->oEnregBdd->AutoCorrection = $v_iAutoCorrect; }
	function defMethodeCorrection ($v_iMethode) { $this->oEnregBdd->MethodeCorrection = $v_iMethode; }
	function defIdPers ($v_iIdPers) { $this->oEnregBdd->IdPers = $v_iIdPers; }
	//@}
	
	/** @name Fonctions de lecture des champs pour ce formulaire */
	//@{
	function retId () { return $this->oEnregBdd->IdFormul; }
	function retTitre () { return $this->oEnregBdd->Titre; }
	function retEncadrer () { return $this->oEnregBdd->Encadrer; }
	function retLargeur () { return $this->oEnregBdd->Largeur; }
	function retTypeLarg () { return $this->oEnregBdd->TypeLarg; }
	function retInterElem () { return $this->oEnregBdd->InterElem; }
	function retInterEnonRep () { return $this->oEnregBdd->InterEnonRep; }
	function retRemplirTout () { return $this->oEnregBdd->RemplirTout; }
	function retStatut () { return $this->oEnregBdd->Statut; }
	function retType () { return $this->oEnregBdd->Type; }
	function retAutoCorrection () { return $this->oEnregBdd->AutoCorrection; }
	function retMethodeCorrection () { return $this->oEnregBdd->MethodeCorrection; }
	function retIdPers () { return $this->oEnregBdd->IdPers; }
	//@}
}
?>
