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
** Fichier ................: gestion_rubr.php
** Description ............: 
** Date de création .......: 01/02/2001
** Dernière modification ..: 23/09/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

if ($g_iRubrique < 1)
	return;

switch ($act)
{
	case "ajouter": ajouter_rubrique(); break;
	case "supprimer": effacer_rubrique(); break;
	case "modifier":
	//   ----------
		$oRubrique = new CModule_Rubrique($oProjet->oBdd,$g_iRubrique);
		
		// {{{ Récupérer les variables de l'url
		$url_iOrdreRubrique  = $_POST["ordre_rubrique"];
		$url_iTypeRubrique   = $_POST["type_rubrique"];
		$url_sNomRubrique    = $_POST["nom_rubrique"];
		$url_iStatutRubrique = $_POST["statut_rubrique"];
		$url_sNomIntitule    = $_POST["intitule_rubrique"];
		$url_iNumDepart      = $_POST["numdepart_rubrique"];
		// }}}
		
		if ($url_bModifierStatut)
			$oRubrique->defStatut($url_iStatutRubrique);
		
		if (!$url_bModifier)
			return;
		
		$html_rubrique = $html_rubrique_name = "none";
		
		// ---------------------
		if (isset($_FILES["fichier_rubrique"]))
		{
			for ($i=0; $i<count($_FILES["fichier_rubrique"]); $i++)
			{
				if (isset($_FILES["fichier_rubrique"]["tmp_name"][$i]))
					$html_rubrique = $_FILES["fichier_rubrique"]["tmp_name"][$i];
				
				if (!empty($html_rubrique) && $html_rubrique != "none")
				{
					$html_rubrique_name = stripslashes($_FILES["fichier_rubrique"]["name"][$i]);
					break;
				}
				
				$html_rubrique = "none";
			}
		}
		
		// {{{ Charger le fichier vers le serveur
		if ($oRubrique->retType() != LIEN_FORUM
			&& $html_rubrique != "none")
		{
			$repDeposer = dir_rubriques($g_iFormation,NULL,TRUE);
			
			if (!is_dir($repDeposer))
			{
				$sRep = "";
				
				foreach (explode("/",$repDeposer) as $k)
				{
					$sRep .= $k."/";
					
					if (!file_exists($sRep))
						mkdir($sRep,0744);
				}
			}
			
			// Effacer l'ancien fichier qui se trouve dans le répertoire du serveur
			list($sNomFichierEffacer) = explode(":",$oRubrique->retDonnees());
			
			@unlink($repDeposer.$sNomFichierEffacer);
			
			// Charger le fichier
			chargerFichier($html_rubrique,$repDeposer.$html_rubrique_name);
			
			// Mettre à jour la base de données
			if (file_exists($repDeposer.$html_rubrique_name))
				$oRubrique->defDonnees($html_rubrique_name);
			else
				$oRubrique->defDonnees("");
		}
		// }}}
		
		// ---------------------
		// Retourner l'identifiant unique de l'intitulé
		// ---------------------
		$oIntitule = new CIntitule($oProjet->oBdd);
		$oIntitule->initParNom($url_sNomIntitule,TYPE_RUBRIQUE);
		$iIdIntitule = $oIntitule->retId();
		$oIntitule = NULL;
		
		if (empty($url_sNomRubrique) || $url_sNomRubrique == INTITULE_RUBRIQUE." sans nom")
		{
			switch ($url_iTypeRubrique)
			{
				case LIEN_FORUM: $url_sNomRubrique = "Forum"; break;
				case LIEN_CHAT: $url_sNomRubrique = CHAT_NOM_DEFAUT; break;
				case LIEN_TEXTE_FORMATTE: $url_sNomRubrique = "Texte formaté"; break;
			}
		}
		
		// ---------------------
		// Sauvegarder les modifications
		// ---------------------
		$oRubrique->defNom($url_sNomRubrique);
		$oRubrique->defType($url_iTypeRubrique);
		$oRubrique->redistNumsOrdre($url_iOrdreRubrique);
		$oRubrique->defNumDepart($url_iNumDepart);
		$oRubrique->defIdIntitule($iIdIntitule);
		
		if (LIEN_SITE_INTERNET == $url_iTypeRubrique)
		{
			$sDonnees = (empty($_POST["LIEN_SITE_INTERNET"]) ? "" : $_POST["LIEN_SITE_INTERNET"]);
			// Pourquoi un rawurlencode ?
			// C'est à cause de ces deux points: "disc.vjf.inserm.fr(:)2010/basisrapports/psycho/psycho_ch10.pdf"
			$oRubrique->defDonnees(rawurlencode($sDonnees));
			$sDonnees = NULL;
		}
		else if (LIEN_FORUM == $url_iTypeRubrique)
		{
			$url_iModaliteForum = (empty($_POST["modalite_forum"]) ? MODALITE_POUR_TOUS : $_POST["modalite_forum"]);
			$url_bAccessibleVisiteursForum = ($_POST["accessible_visiteurs_forum"] == "on" ? "1" : "0");
			
			$oForum = new CForum($oProjet->oBdd);
			$oForum->initForumParType(TYPE_RUBRIQUE,$g_iRubrique);
			
			if ($oForum->retId() > 0)
			{
				// Le forum existe, il suffit de changer son nom
				$oForum->defNom($url_sNomRubrique);			// Le forum doit avoir le même nom que la rubrique
				$oForum->defStatut($url_iStatutRubrique);	// Le forum doit avoir le même statut que la rubrique
				$oForum->defModalite($url_iModaliteForum);
				$oForum->defAccessibleVisiteurs($url_bAccessibleVisiteursForum);
				$oForum->enregistrer();
			}
			else
			{
				// Si le forum n'existe pas, il nous faudra, donc, en créer un nouveau
				$oForum->Ajouter($url_sNomRubrique
					,$url_iModaliteForum
					,$url_iStatutRubrique
					,$url_bAccessibleVisiteursForum
					,0
					,$g_iRubrique
					,0
					,0
					,$g_iIdUtilisateur);
			}
		}
		else if (LIEN_TEXTE_FORMATTE == $url_iTypeRubrique)
		{
			$url_sTexteFormatte = (empty($_POST["DESCRIPTION"][LIEN_TEXTE_FORMATTE]) ? NULL : $_POST["DESCRIPTION"][LIEN_TEXTE_FORMATTE]);
			$oRubrique->defDescr($url_sTexteFormatte);
		}
		else if (LIEN_CHAT == $url_iTypeRubrique)
		{
			// Rechercher tous les chats de cette rubrique
			if ($oRubrique->initChats() == 0)
				$oRubrique->ajouterChat();
		}
		else if (LIEN_NON_ACTIVABLE == $url_iTypeRubrique)
		{
			if (isset($_POST["ligne"]))
			{
				$sStyleNonActivable = "<hr />";
			}
			else if (isset($_POST["vide"]))
			{
				$sStyleNonActivable = "&nbsp;";
			}
			else if (isset($_POST["check"]))
			{
				$url_sStyle = $_POST["check"];
				for ($i=0; $i<count($url_sStyle);$i++)
				{
					$sStyleDebut .= "<".$url_sStyle[$i].">";
					$sStyleFin .= "</".$url_sStyle[count($url_sStyle)-($i+1)].">";
				}
				$sStyleNonActivable = $sStyleDebut.$url_sNomRubrique.$sStyleFin;
			}
			else $sStyleNonActivable = $url_sNomRubrique;
			$oRubrique->defDescr($sStyleNonActivable);
			$oRubrique->defStatut("2"); // on force le statut à ouvert!
		}
		
		break;
}

?>
