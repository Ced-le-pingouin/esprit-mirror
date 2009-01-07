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
** Fichier ................: mail.class.php
** Description ............:
** Date de création .......: 07/12/2004
** Dernière modification ..: 01/07/2005
** Auteurs ................: filippo.porco@umh.ac.be
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

define("COURRIEL_MAX_UTILISATEURS",20);

class CMail
{
	var $sSujet;
	var $sMessage;
	var $aasDestinataires;
	var $asEntetes;
	
	function CMail ($v_sSujet=NULL,$v_sMessage=NULL,$v_sDestinataire=NULL,$v_sNomComplet=NULL,$v_sTypeEntete=NULL)
	{
		$this->asEntetes = array();
		$this->aasDestinataires = array();
		
		$this->defSujet($v_sSujet);
		$this->defMessage($v_sMessage);
		
		if ($v_sTypeEntete!=NULL) {// envoie d'un mail au format texte et HTML
			$this->ajouterEntete('MIME-Version','1.0');
			$this->ajouterEntete('Content-Type','multipart/alternative; boundary="'.$v_sTypeEntete.'"');
		}
		else $this->ajouterEntete('Content-Type','text/plain; charset=utf-8'); // charset par défaut

		if (isset($v_sDestinataire))
			$this->ajouterDestinataire($v_sDestinataire,$v_sNomComplet);
	}
	
	function defSujet ($v_sSujet) { $this->sSujet = trim(stripslashes($v_sSujet)); }
	function defMessage ($v_sMessage) { $this->sMessage = trim(stripslashes($v_sMessage)); }
	
	function retFormatterAdresse ($v_sAdresseCourrielle,$v_sNomComplet=NULL)
	{
		return (empty($v_sNomComplet) ? NULL : "'{$v_sNomComplet}'")
			.(empty($v_sNomComplet) ? $v_sAdresseCourrielle : " <{$v_sAdresseCourrielle}>");
	}
	
	function defExpediteur ($v_sAdresseCourrielle,$v_sNomComplet=NULL) { $this->ajouterEntete("From",$this->retFormatterAdresse($v_sAdresseCourrielle,$v_sNomComplet)); }
	function defCopieCarbone ($v_sAdresseCourrielle,$v_sNomComplet=NULL) { $this->ajouterEntete("Cc",$this->retFormatterAdresse($v_sAdresseCourrielle,$v_sNomComplet)); }
	function defCopieCarboneInvisible ($v_sAdresseCourrielle,$v_sNomComplet=NULL) { $this->ajouterEntete("Bcc",$this->retFormatterAdresse($v_sAdresseCourrielle,$v_sNomComplet)); }
	
	// definit le mail pour un �ventuel retour "mailer daemon" (adresse mail 'introuvable')
	function defRetourMailInvalide ($v_sAdresseCourrielle) { $this->ajouterEntete("Return-Path",$this->retFormatterAdresse($v_sAdresseCourrielle)); }
	
	function ajouterEntete ($v_sCle,$v_sValeur)
	{
		$this->asEntetes[$v_sCle] = (isset($this->asEntetes[$v_sCle])
				? $this->asEntetes[$v_sCle].", "
				: NULL)
			.$v_sValeur;
	}
	
	function ajouterDestinataire ($v_sAdresseCourrielle,$v_sNomComplet=NULL)
	{
		$this->aasDestinataires[] = array(
			"email" => trim($v_sAdresseCourrielle)
			, "nom_complet" => trim($v_sNomComplet));
	}
	
	function retListeEntetes ()
	{
		$sListeEntetes = NULL;
		
		foreach ($this->asEntetes as $sCle => $sValeur)
			$sListeEntetes .= "{$sCle}: {$sValeur}\n";
		
		return $sListeEntetes;
	}
	
	function retListeDestinataires ()
	{
		$iIdxDestinataire = 0;
		$sListeDestinataires = NULL;
		$asListeDestinataires = array();
		
		foreach ($this->aasDestinataires as $asDestinataire)
		{
			if (strstr($asDestinataire["email"],"undisclosed-recipients:;"))
				return array("");
			
			$sListeDestinataires .= (isset($sListeDestinataires) ? ", " : NULL)
				.$this->retFormatterAdresse($asDestinataire["email"],$asDestinataire["nom_complet"]);
			
			$iIdxDestinataire++;
			
			if ($iIdxDestinataire >= COURRIEL_MAX_UTILISATEURS)
			{
				$asListeDestinataires[] = $sListeDestinataires;
				
				$iIdxDestinataire = 0;
				$sListeDestinataires = NULL;
			}
		}
		
		if (isset($sListeDestinataires))
			$asListeDestinataires[] = $sListeDestinataires;
		
		return $asListeDestinataires;
	}
	
	function envoyer ()
	{
		$sListeEntetes        = $this->retListeEntetes();
		$asListeDestinataires = $this->retListeDestinataires();
		$bCourrierEnvoye      = (count($asListeDestinataires) > 0);
		
		foreach ($asListeDestinataires as $sListeDestinataires)
			$bCourrierEnvoye &= mail($sListeDestinataires,$this->sSujet,$this->sMessage,$sListeEntetes);
		
		return $bCourrierEnvoye;
	}
}

?>
