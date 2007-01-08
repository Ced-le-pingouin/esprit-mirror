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

class CAccueil
{
	var $oBdd;					///< Objet représentant la connexion à la DB

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
		error_log('init');
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

	function getAvert ($Visible=0)
	{
		if (empty($this->sAvert)) {
			if($Visible==0) $hResult = $this->oBdd->executerRequete("SELECT * FROM Accueil WHERE TypeContenu='avert' LIMIT 1");
                        else $hResult = $this->oBdd->executerRequete("SELECT Texte FROM Accueil WHERE TypeContenu='avert' AND Visible=1 LIMIT 1");
			$oEnreg = $this->oBdd->retEnregSuiv($hResult);
			$this->sAvert = $oEnreg->Texte;
		}
		return $this->sAvert;
	}

	function getTexte ($Visible=0)
	{
		if (empty($this->sTexte)) {
			if($Visible==0) $hResult = $this->oBdd->executerRequete("SELECT * FROM Accueil WHERE TypeContenu='texte' LIMIT 1");
                        else $hResult = $this->oBdd->executerRequete("SELECT * FROM Accueil WHERE TypeContenu='texte' AND Visible=1 LIMIT 1");
			$oEnreg = $this->oBdd->retEnregSuiv($hResult);
			$this->sTexte = $oEnreg->Texte;
		}
		return $this->sTexte;
	}

	function getBreves ($Visible=0, $Date=0)
	{
		if (empty($this->aoBreves)) { 
                        $params="";                  
                        if($Visible!=0) $params=" AND Visible=1 ";
			if($Date!=0) {
                           $ladate = date("Y-m-d");
                           $params.="AND  DateDeb <= '$ladate'AND DateFin >= '$ladate'";
                        }
                        $requete = "SELECT * FROM Accueil WHERE TypeContenu='breve'".$params." ORDER BY Ordre";
                        $hResult = $this->oBdd->executerRequete($requete);
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult)) {
				$this->aoBreves[] = $oEnreg;
			}
		}
		return $this->aoBreves;
	}

	function getLiens ($Visible=0)
	{
		if (empty($this->aoLiens)) {

	                if($Visible==0) $hResult = $this->oBdd->executerRequete("SELECT * FROM Accueil WHERE TypeContenu='lien' ORDER BY Ordre");
                        else $hResult = $this->oBdd->executerRequete("SELECT * FROM Accueil WHERE TypeContenu='lien' AND Visible=1 ORDER BY Ordre");

			
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResult)) {
				$this->aoLiens[] = $oEnreg;
			}
		}
		return $this->aoLiens;
	}

	function getItem ( $id )
	{
		$hResult = $this->oBdd->executerRequete("SELECT * FROM Accueil WHERE Id=$id");
		return $this->oBdd->retEnregSuiv($hResult);
	}

		 // fonctions SET...

	function setAvert ( $txt )
	{
		if ($this->getAvert()===FALSE) {
			$this->oBdd->executerRequete("INSERT INTO Accueil (TypeContenu,Texte) VALUES ('avert','"
										 .MySQLEscapeString($txt)."')");
		} else {
			$this->oBdd->executerRequete("UPDATE Accueil SET Texte='".MySQLEscapeString($txt)
										 ."' WHERE TypeContenu='avert'");
		}
	}

	function setTexte ( $txt )
	{
		if ($this->getTexte()===FALSE) {
			$this->oBdd->executerRequete("INSERT INTO Accueil (TypeContenu,Texte) VALUES ('texte','"
										 .MySQLEscapeString($txt)."')");
		} else {
			$this->oBdd->executerRequete("UPDATE Accueil SET Texte='".MySQLEscapeString($txt)
										 ."' WHERE TypeContenu='texte'");
		}
	}

	function setBreve ( $txt, $visible=1, $ordre='NULL', $dateDeb='NULL', $dateFin='NULL', $id=FALSE )
	{
		if ($dateDeb!=='NULL') $dateDeb = "'".MySQLEscapeString($dateDeb)."'";
		if ($dateFin!=='NULL') $dateFin = "'".MySQLEscapeString($dateFin)."'";
		$txt = "'".MySQLEscapeString($txt)."'";
		if (!$id) { // création
			$this->oBdd->executerRequete("INSERT INTO Accueil "
										 ." (TypeContenu,Texte,DateDeb,DateFin,Visible,Ordre,DateCreation) "
										 ." VALUES ('breve',$txt,$dateDeb,$dateFin,$visible,$ordre,CURDATE())");
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

	 function toggleVisible ( $id )
	{
		$hResult = $this->oBdd->executerRequete("UPDATE Accueil SET Visible=1-Visible WHERE Id=$id");
	}


}
?>
