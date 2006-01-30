<?php

/*
** Fichier ................: gestion_sousactiv.php
** Description ............:
** Date de cr�ation .......: 01/03/2002
** Derni�re modification ..: 14/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           C�dric FLOQUET <cedric.floquet@umh.ac.be>
**
** Unit� de Technologie de l'Education
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
			$oSousActiv->defStatut($HTTP_POST_VARS["STATUT"]);
		
		if (!$url_bModifier)
			return;
		
		// R�cup�ration des variables de l'url
		$url_iNumOrdreSousActiv     = $HTTP_POST_VARS["ORDRE"];
		$url_sNomSousActiv          = $HTTP_POST_VARS["NOM"];
		$url_iTypeSousActiv         = $HTTP_POST_VARS["TYPE"];
		$url_iStatutSousActiv       = $HTTP_POST_VARS["STATUT"];
		$url_iModaliteAffichage     = (empty($HTTP_POST_VARS["MODALITE_AFFICHAGE"][$url_iTypeSousActiv]) ? 0 : $HTTP_POST_VARS["MODALITE_AFFICHAGE"][$url_iTypeSousActiv]);
		$url_bPremierePageSousActiv = (empty($HTTP_POST_VARS["PREMIERE_PAGE"]) ? FALSE : $HTTP_POST_VARS["PREMIERE_PAGE"] == "on");
		$url_sIntitule              = (empty($HTTP_POST_VARS["INTITULE"][$url_iTypeSousActiv]) ? NULL : $HTTP_POST_VARS["INTITULE"][$url_iTypeSousActiv]);
		$url_sDescriptionSousActiv  = (empty($HTTP_POST_VARS["DESCRIPTION"][$url_iTypeSousActiv]) ? NULL : $HTTP_POST_VARS["DESCRIPTION"][$url_iTypeSousActiv]);
		$fichier_telecharger        = NULL;
		$fichier_telecharger_tmp    = NULL;
		
		if ($url_iModaliteAffichage == FRAME_CENTRALE_INDIRECT ||
			$url_iModaliteAffichage == NOUVELLE_FENETRE_INDIRECT)
		{
			$url_sIntitule = $HTTP_POST_VARS["INTITULE"][0];
			$url_sDescriptionSousActiv = $HTTP_POST_VARS["DESCRIPTION"][0];
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
				$url_sDonneesSousActiv = $HTTP_POST_VARS["DONNEES"][LIEN_PAGE_HTML];
				
				// Nous allons essayer de r�cup�rer automatiquement le titre
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
				$url_sDonneesSousActiv = $HTTP_POST_VARS["DONNEES"][LIEN_DOCUMENT_TELECHARGER];
				
				if ($url_iModaliteAffichage == FRAME_CENTRALE_INDIRECT )
					$oSousActiv->defDonnees($url_sDonneesSousActiv.";".FRAME_CENTRALE_INDIRECT.";".$url_sIntitule);
				else
					$oSousActiv->defDonnees($url_sDonneesSousActiv);
				
				break;
				
			case LIEN_SITE_INTERNET:
			//   ------------------
				$url_sDonneesSousActiv = $HTTP_POST_VARS["DONNEES"][LIEN_SITE_INTERNET];
				$oSousActiv->defDonnees($url_sDonneesSousActiv.";".$url_iModaliteAffichage.";".$url_sIntitule);
				break;
				
			case LIEN_COLLECTICIEL:
			//   -----------------
				$url_sDonneesSousActiv = $HTTP_POST_VARS["DONNEES"][LIEN_COLLECTICIEL];
				$url_sIntitule         = $HTTP_POST_VARS["INTITULE"][LIEN_COLLECTICIEL];
				
				$iModalite = $HTTP_POST_VARS["MODALITE"][LIEN_COLLECTICIEL];
				
				$oSousActiv->defDonnees($url_sDonneesSousActiv.";0;".$url_sIntitule);
				$oSousActiv->defModalite($iModalite);
				break;
				
			case LIEN_FORUM:
			//   ----------
				if (($url_sNomSousActiv == NULL || $url_sNomSousActiv == INTITULE_SOUS_ACTIV." sans nom"))
					$url_sNomSousActiv = "Forum";
				
				$url_iModaliteForum = $HTTP_POST_VARS["MODALITE"][LIEN_FORUM];
				$url_bAccessibleVisiteurForum = ($HTTP_POST_VARS["ACCESSIBLE_VISITEURS"][LIEN_FORUM] == "on" ? "1" : "0");
				
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
				
				if (isset($HTTP_POST_VARS["COLLECTICIEL"]))
				{
					$oGalerie = new CGalerie($oProjet->oBdd,$g_iSousActiv);
					
					// Vider la table contenant la liste des collecticiels
					// associ�s
					$oGalerie->effacerCollecticiels();
					
					// R�ins�rer la nouvelle liste des collecticiels
					$oGalerie->ajouterCollecticiels($HTTP_POST_VARS["COLLECTICIEL"]);
					
					$oGalerie = NULL;
				}
				
				break;
				
			case LIEN_CHAT:
			//   ---------
				if (($url_sNomSousActiv == NULL ||
					$url_sNomSousActiv == INTITULE_SOUS_ACTIV." sans nom"))
					$oSousActiv->defNom(CHAT_NOM_DEFAUT);
				
				// Rechercher tous les chats de cette sous-activit�
				if ($oSousActiv->initChats() == 0)
					$oSousActiv->ajouterChat();
				
				break;
				
			case LIEN_FORMULAIRE:
			//   ---------------
				$url_sDonnees     = $HTTP_POST_VARS["DONNEES"][LIEN_FORMULAIRE];
				$url_iDeroulement = (empty($HTTP_POST_VARS["DEROULEMENT"][LIEN_FORMULAIRE]) ? SOUMISSION_MANUELLE : $HTTP_POST_VARS["DEROULEMENT"][LIEN_FORMULAIRE]);
				$url_sIntitule    = $HTTP_POST_VARS["INTITULE"][LIEN_FORMULAIRE];
				$url_iModalite    = $HTTP_POST_VARS["MODALITE"][LIEN_FORMULAIRE];
				
				// "{Formulaire.IdForm};{soumission automatique/manuelle};{Intitul� du lien}"
				$oSousActiv->defDonnees("{$url_sDonnees};{$url_iDeroulement};{$url_sIntitule}");
				$oSousActiv->defModalite($url_iModalite);
				
				break;
				
			case LIEN_GLOSSAIRE:
			//   --------------
				$iIdGlossaire = $HTTP_POST_VARS["ID_GLOSSAIRE"];
				
				if ($iIdGlossaire > 0)
					$oSousActiv->associerGlossaire($iIdGlossaire);
				else
					$oSousActiv->effacerGlossaire();
				
				break;
			
			case LIEN_TABLEAU_DE_BORD:
			//   --------------------
				$url_iModalite = $HTTP_POST_VARS["MODALITE"][LIEN_TABLEAU_DE_BORD];
				
				$oSousActiv->defDonnees(";{$url_iModaliteAffichage};");
				$oSousActiv->defModalite($url_iModalite);
				
				break;
		}
		
		break;
}

?>

