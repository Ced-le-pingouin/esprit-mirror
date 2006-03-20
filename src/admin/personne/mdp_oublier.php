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
** Fichier ................: mdp_oublier.php
** Description ............:
** Date de création .......: 21/12/2004
** Dernière modification ..: 24/02/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Définition des constantes locales
// ---------------------
define("ERREUR_OK",0);
define("ERREUR_NOM_PRENOM_INCORRECT",1);
define("ERREUR_AUCUNE_ADRESSE_COURRIELLE",2);
define("ERREUR_ENVOI_COURRIEL",3);

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sNomPers    = (empty($HTTP_GET_VARS["nomPers"]) ? NULL : stripslashes(trim($HTTP_GET_VARS["nomPers"])));
$url_sPrenomPers = (empty($HTTP_GET_VARS["prenomPers"]) ? NULL : stripslashes(trim($HTTP_GET_VARS["prenomPers"])));
$url_sEmailPers  = (empty($HTTP_GET_VARS["emailPers"]) ? NULL : stripslashes(trim($HTTP_GET_VARS["emailPers"])));

// ---------------------
// Initialisation
// ---------------------
$iErreur = -1;

if (isset($url_sNomPers) && isset($url_sPrenomPers))
{
	include_once(dir_code_lib("mail.class.php"));
	
	$bEnvoyer = FALSE;
	$oPersonne = new CPersonne($oProjet->oBdd);
	
	$asInfosPers = array(
		"Nom" => $url_sNomPers
		, "Prenom" => $url_sPrenomPers
		, "Email" => (isset($url_sEmailPers) ? $url_sEmailPers : NULL));
	
	$iNbPersTrouvees = $oPersonne->initPersonne($asInfosPers);
	
	if ($iNbPersTrouvees < 1)
	{
		$iErreur = ERREUR_NOM_PRENOM_INCORRECT;
	}
	else if ($iNbPersTrouvees == 1)
	{
		$sEmail = $oPersonne->retEmail();
		
		if (emailValide($sEmail))
		{
			// {{{ Récupérer les mots de passe des utilisateurs
			$asLignesFichierMdp = array();
			$sFichierMdp = dir_tmp("mdpncpte",TRUE);
			
			if (is_file($sFichierMdp))
			{
				chmod($sFichierMdp,0600);
				$asLignesFichierMdp = file($sFichierMdp);
				chmod($sFichierMdp,0200);
			}
			// }}}
			
			$sPeudo = $oPersonne->retPseudo();
			
			for ($i=count($asLignesFichierMdp)-1; $i >= 0; $i--)
				if (strstr($asLignesFichierMdp[$i],":{$sPeudo}:"))
					break;
			
			if ($i >= 0)
			{
				$sMdp = trim(substr(strrchr($asLignesFichierMdp[$i],":"),1));
				
				if (strlen($sMdp) > 0)
				{
					$sSujetCourriel = $oProjet->retNom()." : pseudo et mot de passe";
					$sMessageCourriel = "Pseudo : {$sPeudo}\r\n"
						."Mot de passe : {$sMdp}";
					
					$oMail = new CMail($sSujetCourriel,$sMessageCourriel,$sEmail,$oPersonne->retNomComplet());
					
					if ($oMail->envoyer()) $iErreur = ERREUR_OK; else $iErreur = ERREUR_ENVOI_COURRIEL;
				}
			}
		}
		else
		{
			$iErreur = ERREUR_AUCUNE_ADRESSE_COURRIELLE;
		}
	}
}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("mdp_oublier.tpl");

$oBlocEntrerInformations = new TPL_Block("BLOCK_ENTRER_INFORMATIONS",$oTpl);
$oBlocCourrielEnvoyer    = new TPL_Block("BLOCK_COURRIEL_ENVOYER",$oTpl);

if ($iErreur != -1)
{
	$oBlocCourrielEnvoyer->remplacer("{personne->email}",$sEmail);
	$oBlocCourrielEnvoyer->remplacer("{plateforme.nom}",htmlentities($oProjet->retNom(),ENT_COMPAT,"UTF-8"));
	
	$sVarOk                  = $oBlocCourrielEnvoyer->defVariable("VAR_OK");
	$sVarNomPrenomIncorrect  = $oBlocCourrielEnvoyer->defVariable("VAR_NOM_PRENOM_INCORRECT");
	$sVarAucuneAdresse       = $oBlocCourrielEnvoyer->defVariable("VAR_AUCUNE_ADRESSE");
	$sVarErreurEnvoiCourriel = $oBlocCourrielEnvoyer->defVariable("VAR_ERREUR_ENVOI_COURRIEL");
	
	switch ($iErreur)
	{
		case ERREUR_OK:
			$oBlocCourrielEnvoyer->remplacer("{erreur}",$sVarOk);
			break;
			
		case ERREUR_NOM_PRENOM_INCORRECT:
			$oBlocCourrielEnvoyer->remplacer("{erreur}",$sVarNomPrenomIncorrect);
			break;
			
		case ERREUR_AUCUNE_ADRESSE_COURRIELLE:
			$oBlocCourrielEnvoyer->remplacer("{erreur}",$sVarAucuneAdresse);
			break;
			
		case ERREUR_ENVOI_COURRIEL:
			$oBlocCourrielEnvoyer->remplacer("{erreur}",$sVarErreurEnvoiCourriel);
			break;
	}
	
	$oBlocCourrielEnvoyer->afficher();
	$oBlocEntrerInformations->effacer();
}
else
{
	$oBlocAdresseCourriel = new TPL_Block("BLOCK_ADRESSE_COURRIEL",$oBlocEntrerInformations);
	
	$oBlocEntrerInformations->remplacer("{personne->nom}",$url_sNomPers);
	$oBlocEntrerInformations->remplacer("{personne->prenom}",$url_sPrenomPers);
	
	// Dans le cas où deux personnes ont le même nom et prénom, la plate-forme
	// demandera à l'utilisateur d'entrer son adresse courriel
	if (isset($iNbPersTrouvees) && $iNbPersTrouvees > 1)
		$oBlocAdresseCourriel->afficher();
	else
		$oBlocAdresseCourriel->effacer();
	
	$oBlocEntrerInformations->afficher();
	
	$oBlocCourrielEnvoyer->effacer();
}

$oTpl->afficher();

$oProjet->terminer();

?>

