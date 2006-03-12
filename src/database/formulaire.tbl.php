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
** Fichier ................: formulaire.tbl.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CFormulaire 
{
	var $oBdd;
	var $iId;
	var $oEnregBdd;
	var $aoFormulaire;
	
	var $aoObjets;
	var $aoAxes;
	
	var $oExportation;

	/*
	** Fonction 		: CFormulaire
	** Description	: constructeur
	** Entrée			: 
	**	 			&$v_oBdd : référence de l'objet Bdd appartenant a l'objet Projet
	**				$v_iId : identifiant d'un objet formulaire
	** Sortie			: 
	*/
	function CFormulaire(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;
			
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}

	/*
	** Fonction 		: init
	** Description	: permet d'initialiser l'objet formulaire soit en lui passant un enregistrement
	**					  provenant de la BD, soit en effectuant directement une requête dans la BD avec 
	**                l'id passé via la constructeur
	** Entrée			:
	**				$v_oEnregExistant=NULL : enregistrement représentant un formulaire
	** Sortie			: 
	*/
	function init ($v_oEnregExistant=NULL)  
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT *"
				." FROM Formulaire"
				." WHERE IdForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		$this->iId = $this->oEnregBdd->IdForm;
	}
	
	function initAxes()
	{
		if (isset($this->aoAxes))
			return;
		
		$sRequeteSql =
			"  SELECT a.*"
			." FROM Axe AS a, Formulaire_Axe AS fa"
			." WHERE fa.IdForm=".$this->retId()
			."  AND fa.IdAxe=a.IdAxe"
			." ORDER BY a.IdAxe"
			;
			
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$iIndexAxe = $oEnreg->IdAxe;
			$this->aoAxes[$iIndexAxe] = new CAxe($this->oBdd);
			$this->aoAxes[$iIndexAxe]->init($oEnreg);
		}
		
		/*$iIndexAxe = 0;
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoAxes[$iIndexAxe] = new CAxe($this->oBdd);
			$this->aoAxes[$iIndexAxe]->init($oEnreg);
			$iIndexAxe++;
		}*/
		
		$this->oBdd->libererResult($hResult);
	}
	
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
		
		$sRequeteSql =
			"  SELECT *"
			." FROM ObjetFormulaire"
			." WHERE IdForm=".$this->retId()
			." ORDER BY OrdreObjForm"
			;
		
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$iIndexObjet = $oEnreg->IdObjForm;
			$this->aoObjets[$iIndexObjet] = new CObjetFormulaire($this->oBdd);
			$this->aoObjets[$iIndexObjet]->init($oEnreg);
			if ($v_bInitDetail)
				$this->aoObjets[$iIndexObjet]->initDetail($v_bInitValeursParAxe, $sListeAxes);
		}
		
		/*$iIndexObjet = 0;
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
		{
			$this->aoObjets[$iIndexObjet] = new CObjetFormulaire($this->oBdd);
			$this->aoObjets[$iIndexObjet]->init($oEnreg);
			if ($v_bInitDetail)
				$this->aoObjets[$iIndexObjet]->initDetail($v_bInitValeursParAxe, $sListeAxes);
			$iIndexObjet++;
		}*/
		
		$this->oBdd->libererResult($hResult);
	}
	
	// pour l'instant, prépare seulement l'exportation des listes, radios, et cases à cocher.
	// les axes, objets, réponses possibles, et valeurs par axe doivent tous être initialisés pour que ça fonctionne
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
	
	/*
	** Fonction 		: ajouter
	** Description	: créer un enregistrement dans la table Formulaire en initialisant certaines valeurs et
	**				     surtout le propriétaire du formulaire créé
	** Entrée			:
	**				$iIdPers : identifiant du propriétaire du nouveau formulaire
	** Sortie			: Id renvoyé par la BD
	*/
	function ajouter ($iIdPers)
	{
		$sRequeteSql = "INSERT INTO Formulaire SET IdForm=NULL, Titre='Nouveau Formulaire', Encadrer=1, IdPers='$iIdPers';";
		$this->oBdd->executerRequete($sRequeteSql);
		
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	/*
	** Fonction 		: cHtmlFormulaireModif
	** Description	: renvoie le code html du formulaire qui permet de modifier les caractéristiques d'un formulaire,
	**				     vérifie les données transmises par l'utilisateur afin de permettre un enregistrement ultérieur dans la BD
	** Entrée			:
	**				$v_iIdObjForm
	**				$v_iIdFormulaire
	** Sortie			:
	*/
	function cHtmlFormulaireModif($v_iIdObjForm,$v_iIdFormulaire)
	{
		global $HTTP_POST_VARS, $HTTP_GET_VARS;
	
		//initialisation des messages d'erreurs à 'vide' et de la variable servant a détecter
		//si une erreur dans le remplissage du formulaire a eu lieu (ce qui engendre le non enregistrement
		//de celui-ci dans la base de données + affiche d'une astérisque à l'endroit de l'erreur)
		
		$sMessageErreur1 = $sMessageErreur2 = $sMessageErreur3 = $sMessageErreur4 ="";
		$iFlagErreur=0;
		
		if (isset($HTTP_POST_VARS['envoyer'])) 
		{
			//Récupération des variables transmises par le formulaire
			$this->oEnregBdd->Titre = stripslashes($HTTP_POST_VARS['Titre']);
			$this->oEnregBdd->Encadrer = $HTTP_POST_VARS['Encadrer'];
			$this->oEnregBdd->Largeur = $HTTP_POST_VARS['Largeur'];
			$this->oEnregBdd->TypeLarg = $HTTP_POST_VARS['TypeLarg'];
			$this->oEnregBdd->InterElem = $HTTP_POST_VARS['InterElem'];
			$this->oEnregBdd->InterEnonRep = $HTTP_POST_VARS['InterEnonRep'];
			$this->defRemplirTout($HTTP_POST_VARS['RemplirTout']);
			$this->oEnregBdd->Statut = 1; //$HTTP_POST_VARS['Statut'];
			$this->oEnregBdd->Type = $HTTP_POST_VARS['Type'];
				
				
			//Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
			if (strlen($HTTP_POST_VARS['Titre']) < 1)
				{ $sMessageErreur1="<font color =\"red\">*</font>"; $iFlagErreur=1; }
			
			if ((int)$HTTP_POST_VARS['Largeur'] || strlen($HTTP_POST_VARS['Largeur']) < 1 || $HTTP_POST_VARS['Largeur'] == "0") 
				{;}
			else
				{ $sMessageErreur2="<font color =\"red\">*</font>"; $iFlagErreur=1; }
			
			if ((int)$HTTP_POST_VARS['InterElem'] || strlen($HTTP_POST_VARS['InterElem']) < 1 || $HTTP_POST_VARS['InterElem'] == "0") 
				{;}
			else
				{ $sMessageErreur3="<font color =\"red\">*</font>"; $iFlagErreur=1; }
			if ((int)$HTTP_POST_VARS['InterEnonRep'] || strlen($HTTP_POST_VARS['InterEnonRep']) < 1 || $HTTP_POST_VARS['InterEnonRep'] == "0") 
				{;}
			else
				{ $sMessageErreur4="<font color =\"red\">*</font>"; $iFlagErreur=1; }
			   
			if ($iFlagErreur == 0) //si pas d'erreur, enregistrement physique
			{
				$this->enregistrer();
				echo "<script type=\"text/javascript\">\n";
				echo "rechargerliste($v_iIdObjForm,$v_iIdFormulaire)\n";
				echo "rechargermenugauche()";
				echo "</script>\n";
			} 
		}
	
		//Les instructions suivantes permettent de cocher les cases en fonction des données de l'objet en cours
		$sEncadr1 = $sEncadr2 = "";	  
		if ($this->oEnregBdd->Encadrer==1)
			$sEncadr1="CHECKED";
		else
			$sEncadr2="CHECKED";
	   
		$sTypeLargeur1 = $sTypeLargeur2= "";
		if ($this->oEnregBdd->TypeLarg=="P")
			$sTypeLargeur1="CHECKED";
		else
			$sTypeLargeur2="CHECKED";
		
		/*$sStatut1 = $sStatut2 = "";
		if ($this->oEnregBdd->Statut == "0")
			$sStatut1="CHECKED";
		else
			$sStatut2="CHECKED";*/
		
		$sType1 = $sType1 = "";
		if ($this->oEnregBdd->Type == "prive")
			$sType1="CHECKED";
		else
			$sType2="CHECKED";
		
		if ($this->retRemplirTout())
			$sRemplirToutSel = "CHECKED";
		else
			$sRemplirToutSel = "";
		
		$sParam="?idobj=".$v_iIdObjForm."&idformulaire=".$v_iIdFormulaire;
		
		$sCodeHtml = "<form action=\"{$_SERVER['PHP_SELF']}$sParam\" name=\"formmodif\" method=\"POST\" enctype=\"text/html\">\n"
			."<fieldset><legend><b>Titre du formulaire</b></legend>\n"
			."<TABLE>\n"
			."<TR>\n"
			."<TD>$sMessageErreur1 Titre :</TD>\n"
			."<TD><input type=\"text\" size=\"70\" maxlength=\"100\" name=\"Titre\" Value=\"{$this->oEnregBdd->Titre}\"></TR>\n"
			."</TR>\n"
			."<TR>\n"
			."<TD>Encadrer :</TD>\n"
			."<TD><INPUT TYPE=\"radio\" NAME=\"Encadrer\" VALUE=\"1\" $sEncadr1>Oui\n"
			."<INPUT TYPE=\"radio\" NAME=\"Encadrer\" VALUE=\"0\" $sEncadr2>Non\n"
			."</TD>\n"
			."</TR>\n"
			."</TABLE>\n"
			."</fieldset>\n"
			
			."<fieldset><legend><b>Mise en page</b></legend>\n"
			."<TABLE>\n"
			."<TR>\n"
			."<TD>$sMessageErreur2 Largeur des marges :</TD>\n"
			."<TD><input type=\"text\" size=\"3\" maxlength=\"3\" name=\"Largeur\" Value=\"{$this->oEnregBdd->Largeur}\"></TD>\n"
			."<TD><INPUT TYPE=\"radio\" NAME=\"TypeLarg\" VALUE=\"P\" $sTypeLargeur1>pourcents\n"
			."<INPUT TYPE=\"radio\" NAME=\"TypeLarg\" VALUE=\"N\" $sTypeLargeur2>pixels\n"
			."</TD>\n"
			."</TR>\n"
			."<TR>\n"
			."<TD>$sMessageErreur3 Interligne éléments :</TD>\n"
			."<TD><input type=\"text\" size=\"3\" maxlength=\"3\" name=\"InterElem\" Value=\"{$this->oEnregBdd->InterElem}\"></TD>\n"
			."</TR><TR>\n"
			."<TD>$sMessageErreur4 Interligne énoncé-réponse :</TD>\n"
			."<TD><input type=\"text\" size=\"3\" maxlength=\"3\" name=\"InterEnonRep\" Value=\"{$this->oEnregBdd->InterEnonRep}\"></TD>\n"
			."</TR>\n"
			."</TABLE>\n"
			."</fieldset>\n"			
			
			."<fieldset><legend><b>Options supplémentaires</b></legend>\n"
			."<TABLE>\n"
			/*."<TR>\n"
			."<TD>Statut : </TD>\n"
			."<TD><INPUT TYPE=\"radio\" NAME=\"Statut\" VALUE=\"0\" $sStatut1>En cours</TD>\n"
			."<TD><INPUT TYPE=\"radio\" NAME=\"Statut\" VALUE=\"1\" $sStatut2>Terminé</TD>\n"
			."</TR>\n"*/			
			."<TR>\n"
			."<TD>Type : </TD>\n"
			."<TD><INPUT TYPE=\"radio\" NAME=\"Type\" VALUE=\"prive\" $sType1>Privé</TD>\n"
			."<TD><INPUT TYPE=\"radio\" NAME=\"Type\" VALUE=\"public\" $sType2>Public</TD>\n"
			."</TR>\n"
			."<TR>\n"
			."<TD>Tous les champs doivent être remplis : </TD>\n"
			."<TD COLSPAN=\"2\"><INPUT TYPE=\"checkbox\" NAME=\"RemplirTout\" VALUE=\"1\" $sRemplirToutSel></TD>\n"
			."</TR>\n"
			."</TABLE>\n"
			."</fieldset>\n"

			//Le champ caché ci-dessous permet de "simuler" le fait d'appuyer 
			//sur le bouton submit et ainsi permettre l'enregistrement dans la BD
			."<input type=\"hidden\" name=\"envoyer\" value=\"1\">\n"   
			."</form>\n";
		
		return $sCodeHtml;
	}
	
	
	  /*
	  ** Fonction 		: enregistrer
	  ** Description	: enregistre les données de l'objet courant dans la BD
	  ** Entrée			:
	  ** Sortie			:
	  */
	 
	function enregistrer ()
	{
		// Les variables contenant du "texte" doivent être formatées, cela permet 
		//de les stocker dans la BD sans erreur 
		$sTitre = validerTexte($this->oEnregBdd->Titre);
		
		$sRequeteSql = ($this->retId() > 0 ? "UPDATE Formulaire SET" : 
									  "INSERT INTO Formulaire SET")
			." Titre='{$sTitre}'"
			.", Encadrer='{$this->oEnregBdd->Encadrer}'"
			.", Largeur='{$this->oEnregBdd->Largeur}'"
			.", TypeLarg='{$this->oEnregBdd->TypeLarg}'"
			.", InterElem='{$this->oEnregBdd->InterElem}'"
			.", InterEnonRep='{$this->oEnregBdd->InterEnonRep}'"
			.", RemplirTout='".$this->retRemplirTout()."'"
			.", Statut='{$this->oEnregBdd->Statut}'"
			.", Type='{$this->oEnregBdd->Type}'"
			.", IdPers='{$this->oEnregBdd->IdPers}'"
			.($this->oEnregBdd->IdForm > 0 ? " WHERE IdForm='{$this->oEnregBdd->IdForm}'" : NULL);
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}


	  /*
	  ** Fonction 		: copier
	  ** Description	: permet de faire une copie de l'objet courant au sein de la BD tout en lui
	  **					  affectant un nouveau propriétaire
	  ** Entrée			:
	  					$v_iIdParent : identifiant du formulaire parent (uniquement présent à titre de contrôle)
						$v_iIdPers : identifiant de la personne qui sera propriétaire de la copie
	  ** Sortie			:
	  **				$iIdForm : identifiant de la copie, renvoyé par la BD
	  */

	  function copier ($v_iIdParent,$v_iIdPers)
	  {
		if ($v_iIdParent < 1)
			return;
		
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
			.", IdPers='{$v_iIdPers}'";
			
		$this->oBdd->executerRequete($sRequeteSql);
		
		$iIdForm = $this->oBdd->retDernierId();
		
		return $iIdForm;
	  }


     /*
	  ** Fonction 		: effacer
	  ** Description	: efface de la BD l'enregistrement concernant l'objet courant
	  ** Entrée			:
	  ** Sortie			:
	  */

	function effacer()
	{
		$sRequeteSql = "DELETE FROM Formulaire"
				." WHERE IdForm ='{$this->oEnregBdd->IdForm}'";
		//echo "SupprimerFormulaire".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	
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
		
		//print $sRequeteSql;
		$hResultForms = $this->oBdd->executerRequete($sRequeteSql);
		$iIndexFormulaire = 0;
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResultForms))
		{
			$r_aoFormulaires[$iIndexFormulaire] = new CFormulaire($this->oBdd);
			$r_aoFormulaires[$iIndexFormulaire]->init($oEnreg);
			$iIndexFormulaire++;
		}
		
		$this->oBdd->libererResult($hResultForms);
		
		return $r_aoFormulaires;
	}

	function retNbUtilisationsDsSessions()
	{
		$sRequeteSql =
			"  SELECT COUNT(*) FROM SousActiv"
			." WHERE IdTypeSousActiv='".LIEN_FORMULAIRE."'"
			."  AND LEFT(DonneesSousActiv, ".(strlen($this->retId()) + 1).") = '".$this->retId().";'"
			;
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNb = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		
		return $iNb;
	}
	
	function retNbRemplisDsSessions()
	{
		$sRequeteSql = "SELECT COUNT(*) FROM FormulaireComplete WHERE IdForm='".$this->retId()."'";
		$hResult = $this->oBdd->executerRequete($sRequeteSql);
		$iNb = $this->oBdd->retEnregPrecis($hResult);
		$this->oBdd->libererResult($hResult);
		
		return $iNb;
	}

	//Fonctions de définition
	function defTitre ($v_sTitre) { $this->oEnregBdd->Titre = trim($v_sTitre); }
	function defEncadrer ($v_iEncadrer) { $this->oEnregBdd->Encadrer = $v_iEncadrer; }
	function defLargeur ($v_iLargeur) { $this->oEnregBdd->Largeur = $v_iLargeur; }
	function defTypeLarg ($v_sTypeLarg) { $this->oEnregBdd->TypeLarg = trim($v_sTypeLarg); }
	function defInterElem ($v_iInterElem) { $this->oEnregBdd->InterElem = $v_iInterElem; }
	function defInterEnonRep ($v_iInterEnonRep) { $this->oEnregBdd->InterEnonRep = trim($v_iInterEnonRep); }
	function defRemplirTout ($v_bRemplirTout) { $this->oEnregBdd->RemplirTout = ( $v_bRemplirTout?1:0 ); }
	function defStatut ($v_iStatut) { $this->oEnregBdd->Statut = $v_iStatut; }
	function defType ($v_sType) { $this->oEnregBdd->Type = trim($v_sType); }
	function defIdPers ($v_iIdPers) { $this->oEnregBdd->IdPers = $v_iIdPers; }
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdForm; }
	function retTitre () { return $this->oEnregBdd->Titre; }
	function retEncadrer () { return $this->oEnregBdd->Encadrer; }
	function retLargeur () { return $this->oEnregBdd->Largeur; }
	function retTypeLarg () { return $this->oEnregBdd->TypeLarg; }
	function retInterElem () { return $this->oEnregBdd->InterElem; }
	function retInterEnonRep () { return $this->oEnregBdd->InterEnonRep; }
	function retRemplirTout () { return $this->oEnregBdd->RemplirTout; }
	function retStatut () { return $this->oEnregBdd->Statut; }
	function retType () { return $this->oEnregBdd->Type; }
	function retIdPers () { return $this->oEnregBdd->IdPers; }
}
?>
