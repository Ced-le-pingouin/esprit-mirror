<?php

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
		$url_iOrdreRubrique  = $HTTP_POST_VARS["ordre_rubrique"];
		$url_iTypeRubrique   = $HTTP_POST_VARS["type_rubrique"];
		$url_sNomRubrique    = $HTTP_POST_VARS["nom_rubrique"];
		$url_iStatutRubrique = $HTTP_POST_VARS["statut_rubrique"];
		$url_sNomIntitule    = $HTTP_POST_VARS["intitule_rubrique"];
		$url_iNumDepart      = $HTTP_POST_VARS["numdepart_rubrique"];
		// }}}
		
		if ($url_bModifierStatut)
			$oRubrique->defStatut($url_iStatutRubrique);
		
		if (!$url_bModifier)
			return;
		
		$html_rubrique = $html_rubrique_name = "none";
		
		// ---------------------
		if (isset($HTTP_POST_FILES["fichier_rubrique"]))
		{
			for ($i=0; $i<count($HTTP_POST_FILES["fichier_rubrique"]); $i++)
			{
				if (isset($HTTP_POST_FILES["fichier_rubrique"]["tmp_name"][$i]))
					$html_rubrique = $HTTP_POST_FILES["fichier_rubrique"]["tmp_name"][$i];
				
				if (!empty($html_rubrique) && $html_rubrique != "none")
				{
					$html_rubrique_name = stripslashes($HTTP_POST_FILES["fichier_rubrique"]["name"][$i]);
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
			list($sNomFichierEffacer) = explode(":",$oRubrique->retDonnee());
			
			@unlink($repDeposer.$sNomFichierEffacer);
			
			// Charger le fichier
			chargerFichier($html_rubrique,$repDeposer.$html_rubrique_name);
			
			// Mettre à jour la base de données
			if (file_exists($repDeposer.$html_rubrique_name))
				$oRubrique->defDonnee($html_rubrique_name);
			else
				$oRubrique->defDonnee("");
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
			$sDonnees = (empty($HTTP_POST_VARS["LIEN_SITE_INTERNET"]) ? "" : $HTTP_POST_VARS["LIEN_SITE_INTERNET"]);
			// Pourquoi un rawurlencode ?
			// C'est à cause de ces deux points: "disc.vjf.inserm.fr(:)2010/basisrapports/psycho/psycho_ch10.pdf"
			$oRubrique->defDonnee(rawurlencode($sDonnees));
			$sDonnees = NULL;
		}
		else if (LIEN_FORUM == $url_iTypeRubrique)
		{
			$url_iModaliteForum = (empty($HTTP_POST_VARS["modalite_forum"]) ? MODALITE_POUR_TOUS : $HTTP_POST_VARS["modalite_forum"]);
			$url_bAccessibleVisiteursForum = ($HTTP_POST_VARS["accessible_visiteurs_forum"] == "on" ? "1" : "0");
			
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
			$url_sTexteFormatte = (empty($HTTP_POST_VARS["DESCRIPTION"][LIEN_TEXTE_FORMATTE]) ? NULL : $HTTP_POST_VARS["DESCRIPTION"][LIEN_TEXTE_FORMATTE]);
			$oRubrique->defDescr($url_sTexteFormatte);
		}
		else if (LIEN_CHAT == $url_iTypeRubrique)
		{
			// Rechercher tous les chats de cette rubrique
			if ($oRubrique->initChats() == 0)
				$oRubrique->ajouterChat();
		}
		
		break;
}

?>
