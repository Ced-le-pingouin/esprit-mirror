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
** Fichier ................: gestion_sousactiv.php
** Description ............:
** Date de création .......: 01/03/2002
** Dernière modification ..: 14/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Cédric FLOQUET <cedric.floquet@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

if ($g_iSousActiv < 1)
	return;

switch ($act)
{
	case "ajouter":
		ajouter_sous_activite();
		break;
		
	case "supprimer":
		effacer_sous_activite();
		break;
		
	case "modifier":
		$oSousActiv = new CSousActiv($oProjet->oBdd,$g_iSousActiv);
		
		if ($url_bModifierStatut)
			$oSousActiv->defStatut($_POST["STATUT"]);
		
		if (!$url_bModifier)
			return;
		
		// Récupération des variables de l'url
		$url_iNumOrdreSousActiv     = $_POST["ORDRE"];
		$url_sNomSousActiv          = $_POST["NOM"];
		$url_iTypeSousActiv         = $_POST["TYPE"];
		$url_iStatutSousActiv       = $_POST["STATUT"];
		$url_iModaliteAffichage     = (empty($_POST["MODALITE_AFFICHAGE"][$url_iTypeSousActiv]) ? 0 : $_POST["MODALITE_AFFICHAGE"][$url_iTypeSousActiv]);
		$url_bPremierePageSousActiv = (empty($_POST["PREMIERE_PAGE"]) ? FALSE : $_POST["PREMIERE_PAGE"] == "on");
		$url_sIntitule              = (empty($_POST["INTITULE"][$url_iTypeSousActiv]) ? NULL : $_POST["INTITULE"][$url_iTypeSousActiv]);
		$url_sDescriptionSousActiv  = (empty($_POST["DESCRIPTION"][$url_iTypeSousActiv]) ? NULL : $_POST["DESCRIPTION"][$url_iTypeSousActiv]);
		$fichier_telecharger        = NULL;
		$fichier_telecharger_tmp    = NULL;
		
		if ($url_iModaliteAffichage == FRAME_CENTRALE_INDIRECT ||
			$url_iModaliteAffichage == NOUVELLE_FENETRE_INDIRECT)
		{
			$url_sIntitule = $_POST["INTITULE"][0];
			$url_sDescriptionSousActiv = $_POST["DESCRIPTION"][0];
		}
		
		// Sauvegarder les modifications globales
		$oSousActiv->defNom($url_sNomSousActiv);
		$oSousActiv->redistNumsOrdre($url_iNumOrdreSousActiv);
		$oSousActiv->defType($url_iTypeSousActiv);
		$oSousActiv->defDescr($url_sDescriptionSousActiv);
		$oSousActiv->defPremierePage($url_bPremierePageSousActiv,$g_iRubrique);
		
		// ---------------------
		// Sauvegarder les modifications par type
		// ---------------------
		switch ($url_iTypeSousActiv)
		{
			case LIEN_PAGE_HTML:
			//   --------------
				$url_sDonneesSousActiv = $_POST["DONNEES"][LIEN_PAGE_HTML];
				
				// Nous allons essayer de récupérer automatiquement le titre
				// de la page html qui se trouve entre les balises "<title>"
				if (($url_sNomSousActiv == NULL ||
					$url_sNomSousActiv == INTITULE_SOUS_ACTIV." sans nom") &&
					ereg(".htm?\$",$url_sDonneesSousActiv))
					$oSousActiv->defNom(retTitrePageHtml(dir_cours($g_iActiv,$g_iFormation,$url_sDonneesSousActiv)));
				
				$oSousActiv->defDonnees("{$url_sDonneesSousActiv};{$url_iModaliteAffichage};{$url_sIntitule}");
				
				break;
				
			case LIEN_TEXTE_FORMATTE:
			//   -------------------
				$oSousActiv->defDonnees(";{$url_iModaliteAffichage};");
				
				break;
				
			case LIEN_DOCUMENT_TELECHARGER:
			//   -------------------------
				$url_sDonneesSousActiv = $_POST["DONNEES"][LIEN_DOCUMENT_TELECHARGER];
				
				if ($url_iModaliteAffichage == FRAME_CENTRALE_INDIRECT )
					$oSousActiv->defDonnees($url_sDonneesSousActiv.";".FRAME_CENTRALE_INDIRECT.";".$url_sIntitule);
				else
					$oSousActiv->defDonnees($url_sDonneesSousActiv);
				
				break;
				
			case LIEN_SITE_INTERNET:
			//   ------------------
				$url_sDonneesSousActiv = $_POST["DONNEES"][LIEN_SITE_INTERNET];
				$oSousActiv->defDonnees($url_sDonneesSousActiv.";".$url_iModaliteAffichage.";".$url_sIntitule);
				break;
				
			case LIEN_COLLECTICIEL:
			//   -----------------
				$url_sDonneesSousActiv = $_POST["DONNEES"][LIEN_COLLECTICIEL];
				$url_sIntitule         = $_POST["INTITULE"][LIEN_COLLECTICIEL];
				
				$iModalite = $_POST["MODALITE"][LIEN_COLLECTICIEL];
				
				$oSousActiv->defDonnees($url_sDonneesSousActiv.";0;".$url_sIntitule);
				$oSousActiv->defModalite($iModalite);
				break;
				
			case LIEN_FORUM:
			//   ----------
				if (($url_sNomSousActiv == NULL || $url_sNomSousActiv == INTITULE_SOUS_ACTIV." sans nom"))
					$url_sNomSousActiv = "Forum";
				
				$url_iModaliteForum = $_POST["MODALITE"][LIEN_FORUM];
				$url_bAccessibleVisiteurForum = ($_POST["ACCESSIBLE_VISITEURS"][LIEN_FORUM] == "on" ? "1" : "0");
				
				$oSousActiv->defNom($url_sNomSousActiv);
				
				$oForum = new CForum($oProjet->oBdd);
				$oForum->initForumParType(TYPE_SOUS_ACTIVITE,$g_iSousActiv);
				
				if ($oForum->retId() > 0)
				{
					// Sauvegarder les modifications
					$oForum->defNom($url_sNomSousActiv);
					$oForum->defModalite($url_iModaliteForum);
					$oForum->defStatut($url_iStatutSousActiv);
					$oForum->defAccessibleVisiteurs($url_bAccessibleVisiteurForum);
					$oForum->enregistrer();
				}
				else
				{
					// Ajouter un nouveau forum
					$oForum->ajouter($url_sNomSousActiv,$url_iModaliteForum,$url_iStatutSousActiv,$url_bAccessibleVisiteurForum,0,0,$g_iSousActiv,0,$g_iIdUtilisateur);
				}
				
				$oForum = NULL;
				break;
				
			case LIEN_GALERIE:
			//   ------------
				if (($url_sNomSousActiv == NULL ||
					$url_sNomSousActiv == INTITULE_SOUS_ACTIV." sans nom"))
					$oSousActiv->defNom("Galerie");
				
				if (isset($_POST["COLLECTICIEL"]))
				{
					$oGalerie = new CGalerie($oProjet->oBdd,$g_iSousActiv);
					
					// Vider la table contenant la liste des collecticiels
					// associés
					$oGalerie->effacerCollecticiels();
					
					// Réinsérer la nouvelle liste des collecticiels
					$oGalerie->ajouterCollecticiels($_POST["COLLECTICIEL"]);
					
					$oGalerie = NULL;
				}
				
				break;
				
			case LIEN_CHAT:
			//   ---------
				if (($url_sNomSousActiv == NULL ||
					$url_sNomSousActiv == INTITULE_SOUS_ACTIV." sans nom"))
					$oSousActiv->defNom(CHAT_NOM_DEFAUT);
				
				// Rechercher tous les chats de cette sous-activité
				if ($oSousActiv->initChats() == 0)
					$oSousActiv->ajouterChat();
				
				break;
				
			case LIEN_FORMULAIRE:
			//   ---------------
				$url_sDonnees     = $_POST["DONNEES"][LIEN_FORMULAIRE];
				$url_iDeroulement = (empty($_POST["DEROULEMENT"][LIEN_FORMULAIRE]) ? SOUMISSION_MANUELLE : $_POST["DEROULEMENT"][LIEN_FORMULAIRE]);
				$url_sIntitule    = $_POST["INTITULE"][LIEN_FORMULAIRE];
				$url_iModalite    = $_POST["MODALITE"][LIEN_FORMULAIRE];
				
				// "{Formulaire.IdForm};{soumission automatique/manuelle};{Intitulé du lien}"
				$oSousActiv->defDonnees("{$url_sDonnees};{$url_iDeroulement};{$url_sIntitule}");
				$oSousActiv->defModalite($url_iModalite);
				
				break;
				
			case LIEN_GLOSSAIRE:
			//   --------------
				$iIdGlossaire = $_POST["ID_GLOSSAIRE"];
				
				if ($iIdGlossaire > 0)
					$oSousActiv->associerGlossaire($iIdGlossaire);
				else
					$oSousActiv->effacerGlossaire();
				
				break;
			
			case LIEN_TABLEAU_DE_BORD:
			//   --------------------
				$url_iModalite = $_POST["MODALITE"][LIEN_TABLEAU_DE_BORD];
				
				$oSousActiv->defDonnees(";{$url_iModaliteAffichage};");
				$oSousActiv->defModalite($url_iModalite);
				
				break;

			case LIEN_HOTPOTATOES:
			//   --------------
				$url_sDonneesSousActiv = $_POST["DONNEES"][LIEN_HOTPOTATOES];

				// Nous allons essayer de récupérer automatiquement le titre
				// de la page html qui se trouve entre les balises "<title>"
				if (($url_sNomSousActiv == NULL ||
					$url_sNomSousActiv == INTITULE_SOUS_ACTIV." sans nom") &&
					ereg(".htm?\$",$url_sDonneesSousActiv))
					$oSousActiv->defNom(retTitrePageHtml(dir_cours($g_iActiv,$g_iFormation,$url_sDonneesSousActiv)));

				$oHotpotatoes = new CHotpotatoes($oProjet->oBdd);
				if ($oSousActiv->initHotpotatoes())
					$oHotpotatoes = $oSousActiv->oHotpotatoes; // reprise
				else
					$oHotpotatoes->ajouter($g_iIdUtilisateur); // nouveau
				$oHotpotatoes->defTitre( $oSousActiv->retNom() );
				$oHotpotatoes->defFichier( $url_sDonneesSousActiv );
				$oHotpotatoes->defIdPers( $oProjet->oUtilisateur->retId() );
				$oHotpotatoes->enregistrer();

				$oSousActiv->defDonnees("{$url_sDonneesSousActiv};{$url_iModaliteAffichage};{$url_sIntitule};".$oHotpotatoes->retId());
				break;

		}

		break;
}

?>

