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
// Copyright (C) 2001-2006  Silecs. 

/**
 * @file	accueil.tbl.php
 * 
 * Contient la classe de gestion de la page d'accueil, en rapport avec la DB (table Accueil)
 * 
 * @date	2006/12/01
 * 
 * @author	François GANNAZ
 */

/**
 * Gestion de la page d'accueil en utilisant la table Accueil de la DB
 */
class CAccueil
{
	var $oBdd;					///< Objet représentant la connexion à la DB

	// variables prévues pour servir de cache
	// mais en fait non utilisées pour cela
	// (il faudrait les filtrer avec "array_filter($aoBreves,'isVisible')")
	var $sAvert;
	var $sTexte;
	var $aoBreves;
	var $aoLiens;

	/**
	 * Constructeur
	 * 
	 */
	function CAccueil (&$v_oBdd, $init=FALSE)
	{
		$this->oBdd = &$v_oBdd;
		if ($init) $this->init();
	}

	/**
	 * Lit l'intégralité de la table et met à jour l'objet
	 */
	function init()
	{
		$hResult = $this->oBdd->executerRequete("SELECT * FROM Accueil WHERE TypeContenu='texte'"
												." ORDER BY OrdreLIMIT 1");
		while($oEnreg = $this->oBdd->retEnregSuiv($hResult)) {
			switch($oEnreg->TypeContenu) {
				case 'avert' :
					$this->sAvert = $oEnreg->Texte;
					break;
				case 'texte' :
					$this->sTexte = $oEnreg->Texte;
					break;
				case 'breve' :
					$this->aoBreves[] = $oEnreg;
					break;
				case 'lien' :
					$this->aoLiens[] = $oEnreg;
					break;
			}
		}		
	}

		 // fonctions GET...

	/**
	 * Renvoie le texte de l'avertissement à afficher sur la page d'accueil
	 */
	function getAvert ($Visible=0)
	{
		$this->sAvert = NULL;
		if ($Visible==0)
			$hResult = $this->oBdd->executerRequete("SELECT * FROM Accueil WHERE TypeContenu='avert' LIMIT 1");
		else
			$hResult = $this->oBdd->executerRequete("SELECT Texte FROM Accueil WHERE TypeContenu='avert' AND Visible=1 LIMIT 1");
		$oEnreg = $this->oBdd->retEnregSuiv($hResult);
		$this->sAvert = $oEnreg->Texte;
		return $this->sAvert;
	}

	/**
	 * Renvoie le texte d'accueil
	 */
	function getTexte ($Visible=0)
	{
		$this->sTexte = NULL;
		if ($Visible==0)
			$hResult = $this->oBdd->executerRequete("SELECT * FROM Accueil WHERE TypeContenu='texte' LIMIT 1");
		else
			$hResult = $this->oBdd->executerRequete("SELECT * FROM Accueil WHERE TypeContenu='texte' AND Visible=1 LIMIT 1");
		$oEnreg = $this->oBdd->retEnregSuiv($hResult);
		$this->sTexte = $oEnreg->Texte;
		return $this->sTexte;
	}

	/**
	 * Renvoie le tableau des brèves à afficher sur la page d'accueil
	 * @param Visible Si non nul, uniquement les éléments visibles.
	 * @param Date    Si vrai, tient compte de la date courante
	 */
	function getBreves ($Visible=0, $Date=FALSE)
	{
		$this->aoBreves = NULL;
		$params="";                  
		if ($Visible!=0) $params=" AND Visible=1 ";
		if ($Date) {
			$ladate = date("Y-m-d");
			$params .= " AND (DateDeb <= '$ladate' OR DateDeb IS NULL) AND (DateFin >= '$ladate' OR DateFin IS NULL)";
		}
		$requete = "SELECT * FROM Accueil WHERE TypeContenu='breve'".$params." ORDER BY -Ordre DESC, DateEdition DESC, DateCreation DESC";
		$hResult = $this->oBdd->executerRequete($requete);
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult)) {
			$this->aoBreves[] = $oEnreg;
		}
		return $this->aoBreves;
	}

	/**
	 * Renvoie le tableau des liens à afficher sur la page d'accueil
	 * @param Visible Si non nul, uniquement les éléments visibles.
	 */
	function getLiens ($Visible=0)
	{
		$this->aoLiens = NULL;
		$ordre = " ORDER BY  -Ordre DESC, DateEdition DESC, DateCreation DESC";
		if ($Visible==0)
			$hResult = $this->oBdd->executerRequete("SELECT * FROM Accueil WHERE TypeContenu='lien' $ordre");
		else
			$hResult = $this->oBdd->executerRequete("SELECT * FROM Accueil WHERE TypeContenu='lien' AND Visible=1 $ordre");
		while ($oEnreg = $this->oBdd->retEnregSuiv($hResult)) {
			$this->aoLiens[] = $oEnreg;
		}
		return $this->aoLiens;
	}

	/**
	 * Renvoie l'item (avertissement, texte, breve, lien) de la page d'accueil
	 * @param id L'ID SQL de l'item.
	 */
	function getItem ( $id )
	{
		$hResult = $this->oBdd->executerRequete("SELECT * FROM Accueil WHERE Id=$id");
		return $this->oBdd->retEnregSuiv($hResult);
	}

	/**
	 * Renvoie les titres des sections (breves, liens) de la page d'accueil
	 */
	function getTitres ( )
	{
		$titres = array();
		$hResult = $this->oBdd->executerRequete("SELECT TypeContenu,Texte FROM Accueil WHERE TypeContenu IN ('titre_breves','titre_liens')");
		while ($e = $this->oBdd->retEnregSuiv($hResult)) {
			$titres[substr($e->TypeContenu,6)] = $e->Texte;
		}
		if (empty($titres['breves'])) $titres['breves'] = 'Les brèves';
		if (empty($titres['liens']))  $titres['liens']  = 'Les liens';
		return $titres;
	}

	/**
	 * Renvoie le nombre d'item (avertissement, texte, breve, lien) du type donné
	 * @param type Le type des items.
	 */
	function getNumByType ( $type )
	{
		$hResult = $this->oBdd->executerRequete("SELECT count(Id) as num FROM Accueil WHERE TypeContenu='$type'");
		$oEnreg = $this->oBdd->retEnregSuiv($hResult);
		return $oEnreg->num;
	}

		 // fonctions SET...

	function setAvert ( $txt )
	{
		if ($this->getAvert()===NULL) {
			$this->oBdd->executerRequete("INSERT INTO Accueil (TypeContenu,Texte) VALUES ('avert','"
										 .MySQLEscapeString($txt)."')");
		} else {
			$this->oBdd->executerRequete("UPDATE Accueil SET Texte='".MySQLEscapeString($txt)
										 ."' WHERE TypeContenu='avert'");
		}
	}

	function setTexte ( $txt )
	{
		if ($this->getTexte()===NULL) {
			$this->oBdd->executerRequete("INSERT INTO Accueil (TypeContenu,Texte) VALUES ('texte','"
										 .MySQLEscapeString($txt)."')");
		} else {
			$this->oBdd->executerRequete("UPDATE Accueil SET Texte='".MySQLEscapeString($txt)
										 ."' WHERE TypeContenu='texte'");
		}
	}

	function setBreve ( $txt, $dateDeb='NULL', $dateFin='NULL', $visible=1, $ordre='NULL', $id=FALSE )
	{
		if (!$dateDeb) $dateDeb='NULL';
		if (!$dateFin) $dateFin='NULL';
		if ($dateDeb!=='NULL')
			$dateDeb = "'".MySQLEscapeString($dateDeb)."'";
		if ($dateFin!=='NULL')
			$dateFin = "'".MySQLEscapeString($dateFin)."'";
		$txt = "'".MySQLEscapeString($txt)."'";
		if (!$id) { // création
			$this->oBdd->executerRequete("INSERT INTO Accueil "
										 ." (TypeContenu,Texte,DateDeb,DateFin,Visible,Ordre,DateCreation) "
										 ." VALUES ('breve',$txt,$dateDeb,$dateFin,$visible,$ordre,CURRENT_DATE())");
		} else { // mise à jour
			$this->oBdd->executerRequete("UPDATE Accueil "
										 ." SET TypeContenu='breve', Texte=$txt, DateDeb=$dateDeb, DateFin=$dateFin,"
										 ." Visible=$visible, Ordre=$ordre, DateEdition=CURRENT_DATE()"
										 ." WHERE Id=$id");
		}
	}

	function setLien ( $txt, $lien, $typeLien='popup', $visible=1, $ordre='NULL', $id=FALSE )
	{
		$lien = "'".MySQLEscapeString($lien)."'";
		$typeLien = "'".MySQLEscapeString($typeLien)."'";
		$txt = "'".MySQLEscapeString($txt)."'";
		if (!$id) { // création
			$this->oBdd->executerRequete("INSERT INTO Accueil "
										 ." (TypeContenu,Texte,Lien,TypeLien,Visible,Ordre,DateCreation) "
										 ." VALUES ('lien',$txt,$lien,$typeLien,$visible,$ordre,CURRENT_DATE())");
		} else { // mise à jour
			$this->oBdd->executerRequete("UPDATE Accueil "
										 ." SET TypeContenu='lien', Texte=$txt, Lien=$lien, TypeLien=$typeLien,"
										 ." Visible=$visible, Ordre=$ordre, DateEdition=CURRENT_DATE()"
										 ." WHERE Id=$id");
		}
	}

	/**
	 * Affecte le titre d'une rubrique (breves, liens) de la page d'accueil
	 * @param rubrique Le nom de la rubrique (breves, liens).
	 * @param texte Le nouveau titre.
	 */
	function setTitre ($rubrique, $texte )
	{
		$hResult = $this->oBdd->executerRequete("SELECT count(Id) as num FROM Accueil WHERE TypeContenu='titre_$rubrique'");
		$e = $this->oBdd->retEnregSuiv($hResult);
		if ($e->num > 0) {
			// màj
			error_log('màj');
			$this->oBdd->executerRequete("UPDATE Accueil "
										 ." SET Texte='".MySQLEscapeString($texte)
										 ."' WHERE TypeContenu='titre_$rubrique'");
		} else {
			$this->oBdd->executerRequete("INSERT INTO Accueil (TypeContenu,Texte) "
										 ." VALUES('titre_$rubrique','".MySQLEscapeString($texte)."')");
		}
	}

	function deleteItem ( $id )
	{
		$hResult = $this->oBdd->executerRequete("DELETE FROM Accueil WHERE Id=$id LIMIT 1");
	}

	function toggleVisible ( $id )
	{
		$hResult = $this->oBdd->executerRequete("UPDATE Accueil SET Visible=1-Visible WHERE Id=$id");
	}

	function isVisible($ele)
	{
		return $ele->Visible;
	}

}
?>
