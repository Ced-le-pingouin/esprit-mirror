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
** Fichier ................: zone_cours-menu.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 14/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Cédric FLOQUET <cedric.floquet.umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdPers   = (empty($HTTP_GET_VARS["idPers"]) ? 0 : $HTTP_GET_VARS["idPers"]);
$url_iIdEquipe = (empty($HTTP_GET_VARS["idEquipe"]) ? 0 : $HTTP_GET_VARS["idEquipe"]);

// ---------------------
// Définitions
// ---------------------
define("PREMIERE_PAGE_FRAME_PRINCIPALE",0);
define("PREMIERE_PAGE_NOUVELLE_FENETRE",1);
define("PREMIERE_PAGE_FORUM",2);
define("PREMIERE_PAGE_CHAT",3);
define("PREMIERE_PAGE_TEXTE_FORMATTE",4);
define("PREMIERE_PAGE_TABLEAU_DE_BORD",5);

// --------------------------
// Initialiser
// --------------------------
$iIdPers                = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);
$g_iIdSousActiv         = (is_object($oProjet->oSousActivCourante) ? $oProjet->oSousActivCourante->retId() : 0);
$g_iIdStatutUtilisateur = $oProjet->retStatutUtilisateur();
$iNbrActivs             = $oProjet->oRubriqueCourante->initActivs();

// --------------------------
// Permissions
// --------------------------
$bPermVoirBlocInv       = $oProjet->verifPermission("PERM_VOIR_BLOC_INV");
$bPermVoirBlocFerme     = $oProjet->verifPermission("PERM_VOIR_BLOC_FERME");
$bVerifierAccessibilite = (STATUT_PERS_TUTEUR < $g_iIdStatutUtilisateur);

// --------------------------
// Template principal
// --------------------------
$oTpl = new Template(dir_theme("zone_cours-menu.tpl",FALSE,TRUE));

// --------------------------
// Afficher les fichiers (css ou js) nécessaires à la plate-forme
// --------------------------
$oBlock_Plateform_Header = new TPL_Block("BLOCK_HTML_HEAD",$oTpl);
$oBlock_Plateform_Header->ajouter(<<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://sous_activ.js"></script>
BLOCK_HTML_HEAD
);
$oBlock_Plateform_Header->afficher();

// Première page
$oBlock_Premiere_Page = new TPL_Block("BLOCK_FONCTION_INIT",$oTpl);

$sTableauTitresActivs = ($iNbrActivs > 0 ? NULL : "&nbsp;");

// Bloc des activités
$oBlock_Activ = new TPL_Block("BLOCK_BLOC",$oTpl);

$oSet_Bloque_Vide          = $oTpl->defVariable("SET_SANS_SOUS_ACTIVITE");

$sSetLienFramePrincipale   = $oTpl->defVariable("SET_LIEN_FRAME_PRINCIPALE");
$oSet_Lien_Desactiver      = $oTpl->defVariable("SET_LIEN_DESACTIVER");
$oSet_Equipe               = $oTpl->defVariable("SET_EQUIPE");
$oSet_Page_Html1           = $oTpl->defVariable("SET_PAGE_HTML_FRAME_CENTRALE");
$oSet_Page_Html2           = $oTpl->defVariable("SET_PAGE_HTML_NOUVELLE_FENETRE");
$oSet_Collecticiel         = $oTpl->defVariable("SET_COLLECTICIEL");
$oSet_Galerie              = $oTpl->defVariable("SET_GALERIE");
$oSet_Chat                 = $oTpl->defVariable("SET_CHAT");
$oSet_Forum                = $oTpl->defVariable("SET_FORUM");
$oSet_Document_Telecharger = $oTpl->defVariable("SET_DOCUMENT_TELECHARGER");
$oSet_Site_Internet        = $oTpl->defVariable("SET_SITE_INTERNET");
$aoSet_Texte_Formatte      = array(
	FRAME_CENTRALE_DIRECT => $oTpl->defVariable("SET_TEXTE_FORMATTE_FRAME_PRINCIPALE")
	, NOUVELLE_FENETRE_DIRECT => $oTpl->defVariable("SET_TEXTE_FORMATTE_NOUVELLE_FENETRE"));
$oSet_Glossaire            = $oTpl->defVariable("SET_GLOSSAIRE");
$sSetFormulaire            = $oTpl->defVariable("SET_FORMULAIRE");
$sSetTableauDeBord         = $oTpl->defVariable("SET_TABLEAU_DE_BORD");

// Premières pages
$iTypePremierePage = -1; // Pas de première page à lancer

// Attention à l'ordre
$oSet_Premiere_Page = array();
$oSet_Premiere_Page[] = $oTpl->defVariable("SET_PREMIERE_PAGE_FRAME_PRINCIPALE");
$oSet_Premiere_Page[] = $oTpl->defVariable("SET_PREMIERE_PAGE_NOUVELLE_FENETRE");
$oSet_Premiere_Page[] = $oTpl->defVariable("SET_PREMIERE_PAGE_FORUM");
$oSet_Premiere_Page[] = $oTpl->defVariable("SET_PREMIERE_PAGE_CHAT");
$oSet_Premiere_Page[] = $oTpl->defVariable("SET_PREMIERE_PAGE_TEXTE_FORMATTE");
$oSet_Premiere_Page[] = $oTpl->defVariable("SET_PREMIERE_TABLEAU_DE_BORD");

// --------------------------
// Remplacer des éléments globaux
// --------------------------
$oTpl->remplacer("{texte_formatte.url}",dir_sousactiv(LIEN_PAGE_HTML,"description-index.php",FALSE));

// --------------------------
// Composer la liste des menus
// --------------------------
$sTableauHistoriques = NULL;
$iOrdreHistorique = 0;

$oBlock_Activ->beginLoop();

foreach ($oProjet->oRubriqueCourante->aoActivs as $oActiv)
{
	if (STATUT_INVISIBLE == ($iStatutActiv = $oActiv->retStatut()))
	{
		if ($bPermVoirBlocInv)
			$iStatutActiv = STATUT_OUVERT;
		else
			continue;
	}
	else if (STATUT_FERME == $iStatutActiv && $bPermVoirBlocFerme)
			$iStatutActiv = STATUT_OUVERT;
	
	$oBlock_Activ->nextLoop();
	
	// Activité
	$iIdActiv = $oActiv->retId();
	
	$oBlock_Activ->remplacer("{nom_bloc}",$oActiv->retNom());
	
	$oBlockSousActiv = new TPL_Block("BLOCK_SOUS_ACTIVITE",$oBlock_Activ);
	
	if (MODALITE_PAR_EQUIPE == $oActiv->retModalite())
	{
		if (STATUT_PERS_VISITEUR != $g_iIdStatutUtilisateur)
		{
			// Afficher la liste des tuteurs ainsi que la liste des équipes
			// dans la cas où le statut de l'utilisateur est différent
			// du visiteur
			$oBlockSousActiv->ajouter($oSet_Equipe);
			$oBlockSousActiv->remplacer("{id_bloc}",$iIdActiv);
		}
	}
	
	// Sous-activité
	$iNbrSousActivs = $oActiv->initSousActivs(($bVerifierAccessibilite ? $iIdPers : NULL));
	$iIdSousActivPremierePage = ($g_iIdSousActiv > 0 ? $g_iIdSousActiv : $oActiv->retIdPremierePage());
	
	if ($iNbrSousActivs == 0)
		$oBlockSousActiv->ajouter($oSet_Bloque_Vide);
	else
	{
		foreach ($oActiv->aoSousActivs as $oSousActiv)
		{
			$iIdSousActiv = $oSousActiv->retId();
			
			if (STATUT_INVISIBLE == ($iStatutSousActiv = $oSousActiv->retStatut()))
			{
				if ($bPermVoirBlocInv)
					$iStatutSousActiv = STATUT_OUVERT;
				else
					continue;
			}
			else if (STATUT_FERME == $iStatutSousActiv)
				if ($bPermVoirBlocFerme)
					$iStatutSousActiv = STATUT_OUVERT;
			
			if (STATUT_FERME == $iStatutActiv || STATUT_FERME == $iStatutSousActiv)
			{
				$oBlockSousActiv->ajouter($oSet_Lien_Desactiver);
				$oBlockSousActiv->remplacer("{sousactiv.nom}",$oSousActiv->retNom());
				
				continue;
			}
			
			$sHref = $iTypePremierePageTmp = NULL;
			$sSignet = "signet-{$iIdSousActiv}"; // Afficher le signet en face de la sous-activité
			$sHrefTitle = $oSousActiv->retInfoBulle(TRUE);
			
			$iTypePremierePageTmp = PREMIERE_PAGE_FRAME_PRINCIPALE;
			
			$sDonnees = $oSousActiv->retDonnees();
			
			switch ($oSousActiv->retType())
			{
				case LIEN_PAGE_HTML:
				//   --------------
					$sRepCours = $oActiv->retRepCours("html.php",TRUE);
					
					// Vérifier si le fichier existe
					if (!is_file($sRepCours))
						@copy(dir_formation(NULL,"html.inc.php",TRUE),$sRepCours);
					
					list($sFichier,$iType) = explode(";",$sDonnees);
					
					switch ($iType)
					{
						case FRAME_CENTRALE_DIRECT:
						case NOUVELLE_FENETRE_DIRECT:
						//   -----------------------
							if (is_file($sRepCours))
								$sHref = $oActiv->retRepCours("html.php",FALSE)
									."?idActiv={$iIdActiv}"
									."&idSousActiv={$iIdSousActiv}"
									."&fi=".urlencode($sFichier);
							else
								$sHref = dir_theme("blank.htm",FALSE);
							
							if ($iType == FRAME_CENTRALE_DIRECT)
								$oBlockSousActiv->ajouter($oSet_Page_Html1);
							else
							{
								$sSignet = NULL;
								$oBlockSousActiv->ajouter($oSet_Page_Html2);
								$iTypePremierePageTmp = PREMIERE_PAGE_NOUVELLE_FENETRE;
							}
							
							$bDeplacerSignet = TRUE;
							break;
							
						case FRAME_CENTRALE_INDIRECT:
						case NOUVELLE_FENETRE_INDIRECT:
						//   -------------------------
							$sHref = "{lien.url}?idActiv={$iIdActiv}&idSousActiv={$iIdSousActiv}";
							$oBlockSousActiv->ajouter($oSet_Page_Html1);
							break;
					}
					
					break;
					
				case LIEN_DOCUMENT_TELECHARGER:
				//   -------------------------
					// Il y a un ";" à la fin, dans le cas où il y aurait qu'une seule
					// données
					list($sFichier,$iType) = explode(";",$sDonnees);
					
					if ($iType == FRAME_CENTRALE_INDIRECT)
					{
						$oBlockSousActiv->ajouter($oSet_Document_Telecharger);
						$sHref = "{lien.url}?idActiv={$iIdActiv}&idSousActiv={$iIdSousActiv}";
						$oBlockSousActiv->remplacer("{sousactiv.lien.cible}","Principal");
					}
					else
					{
						$sSignet = NULL;
						
						$oBlockSousActiv->ajouter($oSet_Page_Html1);
						
						$sHref = dir_lib("download.php",FALSE)
							."?f=".$oActiv->retRepCours($sFichier,FALSE)
							."&fn=1";
						
						$oBlockSousActiv->remplacer("{sousactiv.lien.cible}","_blank");
					}
					
					break;
					
				case LIEN_SITE_INTERNET:
				//   ------------------
					list($sUrl,$iType) = explode(";",$sDonnees);
					
					$oBlockSousActiv->ajouter($oSet_Site_Internet);
					
					switch ($iType)
					{
						case FRAME_CENTRALE_DIRECT:
						//   ---------------------
							$sHref = "http://{$sUrl}";
							$oBlockSousActiv->remplacer("{sousactiv.lien.cible}","Principal");
							break;
							
						case FRAME_CENTRALE_INDIRECT:
						case NOUVELLE_FENETRE_INDIRECT:
						//   -------------------------
							$sHref = "{lien.url}?idActiv={$iIdActiv}&idSousActiv={$iIdSousActiv}";
							$oBlockSousActiv->remplacer("{sousactiv.lien.cible}","Principal");
							break;
							
						case NOUVELLE_FENETRE_DIRECT:
						//   -----------------------
							$sHref = "http://{$sUrl}"; $sSignet = NULL;
							$oBlockSousActiv->remplacer("{sousactiv.lien.cible}","_blank");
							$iTypePremierePageTmp = PREMIERE_PAGE_NOUVELLE_FENETRE;
							break;
					}
					
					break;
					
				case LIEN_CHAT:
				//   ---------
					$sSignet = NULL;
					$iTypePremierePageTmp = PREMIERE_PAGE_CHAT;
					$sHref = "";
					$oBlockSousActiv->ajouter($oSet_Chat);
					$oBlockSousActiv->remplacer("{chat.lien}",$oSet_Premiere_Page[PREMIERE_PAGE_CHAT]);
					break;
					
				case LIEN_FORUM:
				//   ----------
					$sSignet = NULL;
					$iTypePremierePageTmp = PREMIERE_PAGE_FORUM;
					
					// Vérifier que ce lien contient bien un forum.
					// Dans le cas contraire ajouter un forum de type
					// sous-activité.
					$oForum = new CForum($oProjet->oBdd);
					$oForum->initForumParType(TYPE_SOUS_ACTIVITE,$iIdSousActiv);
					$iIdForum = $oForum->retId();
					
					$sHref = "";
					
					if ($iIdForum < 1 || ($iIdPers < 1 && !$oForum->retAccessibleVisiteurs()))
						$oBlockSousActiv->ajouter($oSet_Lien_Desactiver);
					else
					{
						$sHref = "?idForum={$iIdForum}"
							."&idNiveau={$iIdActiv}"
							."&typeNiveau=".TYPE_ACTIVITE;
						$oBlockSousActiv->ajouter($oSet_Forum);
						$oBlockSousActiv->remplacer("{forum.url}",$oSet_Premiere_Page[PREMIERE_PAGE_FORUM]);
						$oBlockSousActiv->remplacer("{forum.url}",$sHref);
					}
					
					break;
					
				case LIEN_COLLECTICIEL:
				//   -----------------
					$oBlockSousActiv->ajouter($oSet_Collecticiel);
					$sHref = "{collecticiel.url}?idActiv={$iIdActiv}&idSousActiv={$iIdSousActiv}";
					break;
					
				case LIEN_GALERIE:
				//   ------------
					$oBlockSousActiv->ajouter($oSet_Galerie);
					$sHref = "{galerie.url}?idActiv={$iIdActiv}&idSousActiv={$iIdSousActiv}";
					break;
					
				case LIEN_TEXTE_FORMATTE:
				//   -------------------
					list(,$iType) = explode(";",$sDonnees);
					
					if (empty($iType))
						$iType = FRAME_CENTRALE_DIRECT;
					
					$oBlockSousActiv->ajouter($aoSet_Texte_Formatte[$iType]);
					
					if (NOUVELLE_FENETRE_DIRECT == $iType)
					{
						$sHref = "javascript: texte_formatte('{$iIdSousActiv}','".TYPE_SOUS_ACTIVITE."'); void(0);";
						$iTypePremierePageTmp = PREMIERE_PAGE_TEXTE_FORMATTE;
					}
					else
						$sHref = "{texte_formatte.url}?idNiveau={$iIdSousActiv}&typeNiveau=".TYPE_SOUS_ACTIVITE;
					break;
					
				case LIEN_FORMULAIRE:
				//   ---------------
					$oBlockSousActiv->ajouter($sSetFormulaire);
					$sHref = "{formulaire.url}?idActiv={$iIdActiv}&idSousActiv={$iIdSousActiv}";
					break;
				
				case LIEN_GLOSSAIRE:
				//   --------------
					$oBlockSousActiv->ajouter($oSet_Glossaire);
					$sHref = "{glossaire.url}?idActiv={$iIdActiv}&idSousActiv={$iIdSousActiv}";
					break;
				
				case LIEN_TABLEAU_DE_BORD:
				//   --------------------
					$iModalite = $oSousActiv->retModalite(TRUE);
					list(,$iModeAffichage) = explode(";",$sDonnees);
					
					$oBlockSousActiv->ajouter($sSetTableauDeBord);
					
					if (NOUVELLE_FENETRE_DIRECT == $iModeAffichage)
					{
						$iTypePremierePageTmp = PREMIERE_PAGE_TABLEAU_DE_BORD;
						
						$sHref = convertLien(MODALITE_PAR_EQUIPE == $iModalite
								? "[tableaudebord /e]"
								: "[tableaudebord /i]"
							, "id_tableau_de_bord_{$iIdSousActiv}");
						
						$sHref = str_replace(
							array("{tableaudebord.niveau.id}","{tableaudebord.niveau.type}")
							, array($oProjet->oRubriqueCourante->retId(),TYPE_RUBRIQUE)
							, $sHref);
					}
					else
					{
						$sHref = "{tableau_de_bord.url}?idActiv={$iIdActiv}&idSousActiv={$iIdSousActiv}";
						$oBlockSousActiv->remplacer("{sousactiv.lien}",$sSetLienFramePrincipale);
					}
					
					break;
			}
			
			$sNomSousActiv = $oSousActiv->retNom();
			
			if (isset($sHref))
			{
				// {{{ Afficher la première page
				$sHrefPremierPage = $sHref
					.(empty($url_iIdEquipe)
						? (empty($url_iIdPers)
							? NULL
							: "&idPers={$url_iIdPers}")
						: "&idEquipe={$url_iIdEquipe}");
				
				if ($iIdSousActivPremierePage == $iIdSousActiv)
				{
					$iTypePremierePage = $iTypePremierePageTmp;
					
					$oBlock_Premiere_Page->ajouter($oSet_Premiere_Page[$iTypePremierePage]);
					
					switch ($iTypePremierePage)
					{
						case PREMIERE_PAGE_FRAME_PRINCIPALE:
							$oBlock_Premiere_Page->remplacer("{premiere_page.lien}",$sHrefPremierPage);
							$oBlock_Premiere_Page->remplacer("{premiere_page.signet}",$sSignet);
							$oBlock_Premiere_Page->remplacer("{premiere_page.ordre_historique}",$iOrdreHistorique);
							break;
							
						case PREMIERE_PAGE_NOUVELLE_FENETRE:
							$oBlock_Premiere_Page->remplacer("{premiere_page.lien}",rawurlencode($sHref));
							break;
							
						case PREMIERE_PAGE_FORUM:
							$oBlock_Premiere_Page->remplacer("{forum.url}",$sHref);
							$oBlock_Premiere_Page->remplacer("{forum.id}",$oForum->retId());
							break;
							
						case PREMIERE_PAGE_TABLEAU_DE_BORD:
							break;
					}
					
					$oBlock_Premiere_Page->remplacer("{activ.id}",$iIdActiv);
					$oBlock_Premiere_Page->remplacer("{sousactiv.id}",$iIdSousActiv);
				}
				// }}}
				
				// Bloc
				$oBlockSousActiv->remplacer("{activ.id}",$iIdActiv);
				
				// Elément actif
				$oBlockSousActiv->remplacer("{sousactiv.ordre}",(isset($sSignet) ? $iOrdreHistorique : "-1"));
				
				$oBlockSousActiv->remplacer("{sousactiv.id}",$iIdSousActiv);
				$oBlockSousActiv->remplacer("{sousactiv.type}",TYPE_SOUS_ACTIVITE);
				
				$oBlockSousActiv->remplacer("{sousactiv.nom}",htmlentities($sNomSousActiv));
				$oBlockSousActiv->remplacer("{sousactiv.nom_encoder}",htmlentities($sNomSousActiv));
				
				$oBlockSousActiv->remplacer("{sousactiv.lien}",$sHref);
				$oBlockSousActiv->remplacer("{sousactiv.signet}",$sSignet);
				
				$oBlockSousActiv->remplacer("{sousactiv.infobulle}",$sHrefTitle);
				
				$sTableauHistoriques .= (isset($sTableauHistoriques) ? ", " : NULL)
					."\"".rawurlencode(str_replace(" ","&nbsp;",$sNomSousActiv))."\"\n";
				
				$iOrdreHistorique++;
			}
		}
	}
	
	$oBlockSousActiv->afficher();
}

$oBlock_Activ->afficher();

if (isset($sTableauHistoriques))
	$oTpl->remplacer("{tableau_historiques}",$sTableauHistoriques);
else
	$oTpl->remplacer("{tableau_historiques}","");

// --------------------------
// Première page
// --------------------------
if ($iTypePremierePage > -1)
	$oBlock_Premiere_Page->afficher();
else
	$oBlock_Premiere_Page->effacer();

$oTpl->remplacer("{sousactiv.niveau.id}",TYPE_SOUS_ACTIVITE);
$oTpl->remplacer("{activ.niveau.id}",TYPE_ACTIVITE);

// {{{ Urls
$oTpl->remplacer("{lien.url}",dir_sousactiv(LIEN_PAGE_HTML,"lien.php",FALSE));
$oTpl->remplacer("{collecticiel.url}",dir_sousactiv(LIEN_COLLECTICIEL,"collecticiel-index.php",FALSE));
$oTpl->remplacer("{galerie.url}",dir_sousactiv(LIEN_GALERIE,"galerie.php",FALSE));
$oTpl->remplacer("{formulaire.url}",dir_sousactiv(LIEN_FORMULAIRE,"formulaire.php"));
$oTpl->remplacer("{chat.url}",dir_chat("tchatche-index.php"));
$oTpl->remplacer("{texte_formatte.url}",dir_sousactiv(LIEN_PAGE_HTML,"description.php",FALSE));
$oTpl->remplacer("{glossaire.url}",dir_sousactiv(LIEN_GLOSSAIRE,"glossaire.php",FALSE));
$oTpl->remplacer("{equipes.url}",dir_admin("equipe","liste_equipes-index.php"));
$oTpl->remplacer("{tableau_de_bord.url}",dir_sousactiv(LIEN_PAGE_HTML,"tableaudebord.php",FALSE));
// }}}

// {{{ Types de statut
$oTpl->remplacer(array("{statut.tuteur.id}","{statut.etudiant.id}"),array(STATUT_PERS_TUTEUR,STATUT_PERS_ETUDIANT));
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

