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
** Date de crÃ©ation .......: 07/12/2004
** DerniÃ¨re modification ..: 01/07/2005
** Auteurs ................: filippo.porco@umh.ac.be
**
** UnitÃ© de Technologie de l'Education
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
		else $this->ajouterEntete('Content-Type','text/plain; charset=utf-8'); // charset par dÃ©faut
		
		if (isset($v_sDestinataire))
			$this->ajouterDestinataire($v_sDestinataire,$v_sNomComplet);
	}
	
	function defSujet ($v_sSujet) { $this->sSujet = trim(stripslashes($v_sSujet)); }
	function defMessage ($v_sMessage) { $this->sMessage = trim(stripslashes($v_sMessage)); }
	function defMessageMixte ($v_sNomForm,$v_sPseudo,$v_sMdp,$v_sPrenomNomExpediteur)
	{
		$sMessageCourrielTexte = "Bonjour,\r\n\r\nCe mail vous informe que vous avez bien été inscrit(e) à la formation\r\n"
			."'$v_sNomForm'\r\naccessible sur Esprit (".$_SERVER['PHP_SELF'].").\r\n\r\n"
			."Pour accéder à l'espace réservé à votre formation sur Esprit,\r\nintroduisez le pseudo et le mot de passe (mdp) (en respectant scrupuleusement,\r\n"
			."les majuscules, minuscules, caractères accentués et espaces éventuels) et\r\ncliquez sur Ok.\r\n\r\n"
			."Votre pseudo est : $v_sPseudo\r\nVotre mot de passe est : $v_sMdp\r\n\r\n"
			."Astuces :\r\n\r\n"
			."		* Après connexion, vous pouvez modifier votre pseudo et mot de passe dans le\r\n"
			."		profil (cliquer sur le lien \"Profil\" en bas de l'écran)\r\n\r\n"
    		."		* Si, un jour, vous oubliez votre pseudo et/ou votre mot de passe,\r\n"
    		."		cliquez sur le lien \"Oublié ?\". Ce lien se trouve juste au-dessus de la zone\r\n"
    		."		\"Pseudo\", au niveau de la page d'accueil d'Esprit\r\n"
    		."		(http://flodi.grenet.fr/esprit).\r\n"
    		."		Ceci vous permettra de récupérer ces informations par courriel.\r\n\r\n"
    		."Bonne formation,\r\n\r\nPour l'équipe Esprit,\r\n\r\n$v_sPrenomNomExpediteur";

		$sMessageCourrielHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head><title>Inscription sur Esprit</title></head><body>'
			."Bonjour,<br /><br />Ce mail vous informe que vous avez bien été inscrit(e) à la formation '<strong>$v_sNomForm</strong>' accessible sur <a href =\"http://flodi.grenet.fr/esprit\">Esprit</a>.<br /><br />"
			."Pour accéder à l'espace réservé à votre formation sur Esprit, introduisez le pseudo et le mot de passe (<ins>en respectant scrupuleusement les majuscules, minuscules, caractères accentués et espaces éventuels</ins>) et cliquez sur Ok.<br /><br />"
			."Votre pseudo est : <strong>$v_sPseudo</strong><br />Votre mot de passe est : <strong>$v_sMdp</strong><br /><br />"
			."Astuces :<br /><br />* Après connexion, vous pouvez modifier votre pseudo et mot de passe dans le	profil (cliquer sur le lien \"Profil\" en bas de l'écran)<br />"
			."* Si, un jour, vous oubliez votre pseudo et/ou votre mot de passe, <ins>cliquez sur le lien \"Oublié ?\"</ins>. Ce lien se trouve juste au-dessus de la zone	\"Pseudo\", au niveau de la page d'accueil d'<a href =\"".$_SERVER['PHP_SELF']."\". Ceci vous permettra de récupérer ces informations par courriel.>Esprit</a>.<br />Ceci vous permettra de récupérer ces informations par courriel.<br /><br />"
    		."Bonne formation,<br /><br />Pour l'équipe Esprit,<br /><br />$v_sPrenomNomExpediteur</body></html>";
    		
    	$sFrontiereEntreTexteHTML = '-----'.md5(uniqid(mt_rand()));

		//on insere d'abord le message au format texte
		$sMessageFinal	= 'This is a multi-part message in MIME format.'."\r\n";
 		$sMessageFinal .= '--'.$sFrontiereEntreTexteHTML."\r\n";
     	$sMessageFinal .= 'Content-Type: text/plain; charset=iso-8859-1'."\r\n";
     	$sMessageFinal .= 'Content-Transfer-Encoding: 8bit'."\r\n\r\n";
     	$sMessageFinal .= $sMessageCourrielTexte."\r\n\r\n";
		//on ajoute le texte HTML
		$sMessageFinal .= '--'.$sFrontiereEntreTexteHTML."\r\n";
     	$sMessageFinal .= 'Content-Type: text/html; charset=iso-8859-1'."\r\n";
     	$sMessageFinal .= 'Content-Transfer-Encoding: 8bit'."\r\n\r\n";
     	$sMessageFinal .= $sMessageCourrielHtml."\r\n\r\n";
     	//on ferme le message
     	$sMessageFinal .= '--'.$sFrontiereEntreTexteHTML.'--'."\r\n";
     	
     	$this->sMessageMixte = trim(stripslashes($sMessageFinal));
	}
	
	function retFormatterAdresse ($v_sAdresseCourrielle,$v_sNomComplet=NULL)
	{
		return (empty($v_sNomComplet) ? NULL : "'{$v_sNomComplet}'")
			.(empty($v_sNomComplet) ? $v_sAdresseCourrielle : " <{$v_sAdresseCourrielle}>");
	}
	
	function defExpediteur ($v_sAdresseCourrielle,$v_sNomComplet=NULL) { $this->ajouterEntete("From",$this->retFormatterAdresse($v_sAdresseCourrielle,$v_sNomComplet)); }
	function defCopieCarbone ($v_sAdresseCourrielle,$v_sNomComplet=NULL) { $this->ajouterEntete("Cc",$this->retFormatterAdresse($v_sAdresseCourrielle,$v_sNomComplet)); }
	function defCopieCarboneInvisible ($v_sAdresseCourrielle,$v_sNomComplet=NULL) { $this->ajouterEntete("Bcc",$this->retFormatterAdresse($v_sAdresseCourrielle,$v_sNomComplet)); }
	
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
	
	function envoyer ($bMessageEstMixte=FALSE)
	{
		$sListeEntetes        = $this->retListeEntetes();
		$asListeDestinataires = $this->retListeDestinataires();
		$bCourrierEnvoye      = (count($asListeDestinataires) > 0);
		
		foreach ($asListeDestinataires as $sListeDestinataires)
			$bCourrierEnvoye &= mail($sListeDestinataires,$this->sSujet,($bMessageEstMixte? $this->sMessage : $this->sMessageMixte),$sListeEntetes);
		
		return $bCourrierEnvoye;
	}
}

?>
