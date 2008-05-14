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
** Fichier ................: zone_menu.php
** Description ............: 
** Date de création .......: 01/01/2004
** Dernière modification ..: 12/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
** 
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once("globals.icones.php");
require_once(dir_admin("awareness","awareness.inc.php",TRUE));

$oProjet = new CProjet();

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("zone_menu.tpl",FALSE,TRUE));

// ---------------------
// Initialiser
// ---------------------
$iIdPers = $oProjet->retIdUtilisateur();

$iIdForm = (is_object($oProjet->oFormationCourante) ? $oProjet->oFormationCourante->retId() : 0);
$iIdMod  = (is_object($oProjet->oModuleCourant) ? $oProjet->oModuleCourant->retId() : 0);

// {{{ Permissions
$bPeutGererTousSujets  = $oProjet->verifPermission("PERM_MOD_SUJETS_FORUMS");
$bPeutGererTousSujets |= ($oProjet->verifPermission("PERM_MOD_SUJETS_FORUM") && $oProjet->verifModifierModule());

$bPeutVoirModFerme = $oProjet->verifPermission("PERM_VOIR_COURS_FERME");

$bPeutVoirRubrFermee = $oProjet->verifPermission("PERM_VOIR_RUBRIQUE_FERMEE");
$bPeutVoirRubrInv    = $oProjet->verifPermission("PERM_VOIR_RUBRIQUE_INV");
// }}}

$bAccederForumParEquipe = ($bPeutGererTousSujets | $oProjet->verifEquipe());

// ---------------------
// Insérer ce bloc dans l'entête de la page html
// ---------------------
$sBlocHtmlEntete = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
<script type="text/javascript" language="javascript" src="javascript://sous_activ.js"></script>
BLOCK_HTML_HEAD;

$oBlock_Plateform_Header = new TPL_Block("BLOCK_HTML_HEAD",$oTpl);
$oBlock_Plateform_Header->ajouter($sBlocHtmlEntete);
$oBlock_Plateform_Header->afficher();

// ---------------------
// ---------------------
$oBlocSansFormations    = new TPL_Block("BLOCK_SANS_FORMATIONS",$oTpl);
$oBlocTitreModule       = new TPL_Block("BLOCK_TITRE_COURS",$oTpl);
$sVarSeparateurIntitule = $oBlocTitreModule->defVariable("VAR_SEPARATEUR_INTITULE_COURS");
$oBlocDescriptionModule = new TPL_Block("BLOCK_DESCRIPTION",$oTpl);

// ---------------------
// Composer la liste des rubriques/unités
// ---------------------
$oBlock_Cours = new TPL_Block("BLOCK_COURS",$oTpl);

// {{{ Forum
$oSet_Forum        = $oTpl->defVariable("SET_FORUM");
$oSet_Forum_Ouvert = $oTpl->defVariable("SET_FORUM_OUVERT");
$oSet_Forum_Ferme  = $oTpl->defVariable("SET_FORUM_FERME");
// }}}

// {{{ Rubrique/Unité
$oSet_Unite = $oTpl->defVariable("SET_UNITE");
// }}}

// {{{ Page html
$oSet_Page_Html        = $oTpl->defVariable("SET_PAGE_HTML");
$oSet_Page_Html_Ouvert = $oTpl->defVariable("SET_PAGE_HTML_OUVERT");
$oSet_Page_Html_Ferme  = $oTpl->defVariable("SET_PAGE_HTML_FERME");
// }}}

// {{{ Site internet
$oSet_Site_Internet        = $oTpl->defVariable("SET_SITE_INTERNET");
$oSet_Site_Internet_Ouvert = $oTpl->defVariable("SET_SITE_INTERNET_OUVERT");
$oSet_Site_Internet_Ferme  = $oTpl->defVariable("SET_SITE_INTERNET_FERME");
// }}}

// {{{ Document à télécharger
$oSet_Document_Telecharger        = $oTpl->defVariable("SET_DOCUMENT_TELECHARGER");
$oSet_Document_Telecharger_Ouvert = $oTpl->defVariable("SET_DOCUMENT_TELECHARGER_OUVERT");
$oSet_Document_Telecharger_Ferme  = $oTpl->defVariable("SET_DOCUMENT_TELECHARGER_FERME");
// }}}

// {{{ Chat
$oSet_Chat        = $oTpl->defVariable("SET_CHAT");
$oSet_Chat_Ouvert = $oTpl->defVariable("SET_CHAT_OUVERT");
$oSet_Chat_Ferme  = $oTpl->defVariable("SET_CHAT_FERME");
// }}}

// {{{ Texte formatté
$oSet_TexteFormatte        = $oTpl->defVariable("SET_TEXTE_FORMATTE");
$oSet_TexteFormatte_Ouvert = $oTpl->defVariable("SET_TEXTE_FORMATTE_OUVERT");
$oSet_TexteFormatte_Ferme  = $oTpl->defVariable("SET_TEXTE_FORMATTE_FERME");
// }}}

// {{{ Autres
$oSet_Unite_Espacer = $oTpl->defVariable("SET_UNITE_ESPACE");
// }}}

if ($iIdMod > 0)
{
	$oBlocSansFormations->effacer();
	
	// {{{ Titre du module
	$oBlocTitreModule->afficher();
	
	$sTexteIntitule = $oProjet->oModuleCourant->retTexteIntitule();
	$oTpl->remplacer("{cours.intitule}",$sTexteIntitule
		.(strlen($sTexteIntitule) > 0 ? $sVarSeparateurIntitule : NULL));
	
	$oTpl->remplacer("{cours.titre}",emb_htmlentities($oProjet->oModuleCourant->retNom()));
	// }}}
	
	// {{{ Description du module
	$sDescr = convertBaliseMetaVersHtml($oProjet->oModuleCourant->retDescr());
	
	if (strlen($sDescr))
	{
		$oBlocDescriptionModule->afficher();
		$oTpl->remplacer(
			array("{description_cours}","{tableaudebord.niveau.id}","{tableaudebord.niveau.type}"),
			array($sDescr,$oProjet->oModuleCourant->retId(),TYPE_MODULE));
	}
	else
		$oBlocDescriptionModule->effacer();
	
	unset($sDescr);
	// }}}
	
	// Rechercher toutes les rubriques de ce cours
	$iNbrRubriques = $oProjet->oModuleCourant->initRubriques();
	$aoRubriques   = &$oProjet->oModuleCourant->aoRubriques;
	
	// Rubriques
	for ($r=0; $r<$iNbrRubriques; $r++)
	{
		if (($iIdStatut = $aoRubriques[$r]->retStatut()) == STATUT_INVISIBLE &&
			!$bPeutVoirRubrInv)
			continue;
		else if (($iIdStatut == STATUT_FERME && !$bPeutVoirModFerme) ||
			($iIdStatut == STATUT_FERME && !$bPeutVoirRubrFermee))
			$bStatutOuvert = FALSE;
		else
			$bStatutOuvert = TRUE;
		
		$iIdRub = $aoRubriques[$r]->retId();
		
		// {{{ Mettre un espace entre les autres liens et les unités
		if ($r > 0 &&
			(($aoRubriques[$r-1]->retType() == LIEN_UNITE && $aoRubriques[$r]->retType() != LIEN_UNITE) ||
			($aoRubriques[$r-1]->retType() != LIEN_UNITE && $aoRubriques[$r]->retType() == LIEN_UNITE)))
		{
			$oBlock_Cours->ajouter($oSet_Unite_Espacer);
		}
		// }}}
		
		switch ($aoRubriques[$r]->retType())
		{
			case LIEN_FORUM:
			//   ----------
				$oBlock_Cours->ajouter($oSet_Forum);
				
				$oForum = new CForum($oProjet->oBdd);
				$oForum->initForumParType(TYPE_RUBRIQUE,$iIdRub);
				
				$iIdForum            = $oForum->retId();
				$bAccessibleVisiteur = $oForum->retAccessibleVisiteurs();
				
				// {{{ Vérifier que cet utilisateur a le droit d'entrer dans un
				//     forum par équipe
				if (MODALITE_POUR_TOUS != $oForum->retModalite() &&
					!$bAccederForumParEquipe &&
					!$bAccessibleVisiteur)
					$iIdForum = 0;
				// }}}
				
				if ($iIdForum < 1 || !$bStatutOuvert ||
					($iIdPers < 1 && !$bAccessibleVisiteur))
				{
					// - Le forum n'a pas été créé
					// - Le forum est fermé
					// - Les visiteurs n'ont pas le droit de consulter ce forum
					//   si ce forum n'est pas accessible
					$oBlock_Cours->remplacer("{lien_forum}",$oSet_Forum_Ferme);
				}
				else
				{
					$sUrl = "?idForum={$iIdForum}"
						."&idNiveau={$iIdRub}"
						."&typeNiveau=".TYPE_RUBRIQUE;
					$oBlock_Cours->remplacer("{lien_forum}",$oSet_Forum_Ouvert);
					$oBlock_Cours->remplacer("{rubrique.url}",$sUrl);
				}
				
				unset($bAccessibleVisiteur);
				
				break;
				
			case LIEN_UNITE:
			//   ----------
				$oBlock_Cours->ajouter($oSet_Unite);
				
				// {{{ Récupérer les variables de l'unité
				$asVarUnites     = $oBlock_Cours->defVariable("VAR_UNITE",TRUE);
				$sVarUniteOuvert = $oBlock_Cours->defVariable("VAR_UNITE_OUVERT");
				$sVarUniteFerme  = $oBlock_Cours->defVariable("VAR_UNITE_FERME");
				// }}}
				
				// Récupérer l'intitulé de l'unité
				$sTexteIntitule = $aoRubriques[$r]->retTexteIntitule();
				$bIntitule = (strlen($sTexteIntitule) > 0);
				
				$oBlock_Cours->remplacer("{lien_unite}",$asVarUnites[$bIntitule]);
				
				if ($bStatutOuvert)
				{
						$sUrl = "{unite.url}"
							."?idForm={$iIdForm}"
							."&idMod={$iIdMod}"
							."&idUnite={$iIdRub}";
						$oBlock_Cours->remplacer("{lien_unite}",$sVarUniteOuvert);
						$oBlock_Cours->remplacer("{rubrique.url}",$sUrl);
				}
				else
				{
					$oBlock_Cours->remplacer("{lien_unite}",$sVarUniteFerme);
				}
				
				if ($bIntitule)
				{
					// Afficher l'intitulé de l'unité
					$oBlock_Cours->remplacer("{rubrique.intitule}",$sTexteIntitule);
				}
				
				break;
				
				case LIEN_PAGE_HTML:
				//   --------------
					$oBlock_Cours->ajouter($oSet_Page_Html);
					list($sUrl) = explode(":",$aoRubriques[$r]->retDonnees());
					
					if ($bStatutOuvert && strlen($sUrl) > 0)
					{
						
						$sUrl = $oProjet->retRepRubriques(rawurlencode($sUrl));
						$oBlock_Cours->remplacer("{lien_page_html}",$oSet_Page_Html_Ouvert);
						$oBlock_Cours->remplacer("{rubrique.url}",$sUrl);
					}
					else
					{
						$oBlock_Cours->remplacer("{lien_page_html}",$oSet_Page_Html_Ferme);
					}
					
					break;
					
				case LIEN_SITE_INTERNET:
				//   ------------------
					$oBlock_Cours->ajouter($oSet_Site_Internet);
					list($sUrl) = explode(":",$aoRubriques[$r]->retDonnees());
					
					if ($bStatutOuvert && strlen($sUrl) > 0)
					{
						$oBlock_Cours->remplacer("{lien_site_internet}",$oSet_Site_Internet_Ouvert);
						$oBlock_Cours->remplacer("{rubrique.url}",(eregi("^http://",$sUrl) ? NULL : "http://").rawurldecode($sUrl));
					}
					else
					{
						$oBlock_Cours->remplacer("{lien_site_internet}",$oSet_Site_Internet_Ferme);
					}
					
					break;
					
				case LIEN_DOCUMENT_TELECHARGER:
				//   -------------------------
					$oBlock_Cours->ajouter($oSet_Document_Telecharger);
					
					list($sUrl) = explode(":",$aoRubriques[$r]->retDonnees());
					
					if ($bStatutOuvert && strlen($sUrl) > 0)
					{
						$sUrl = "{telechargement.url}?f=".$oProjet->retRepRubriques(rawurlencode($sUrl));
						$oBlock_Cours->remplacer("{lien_document_telecharger}",$oSet_Document_Telecharger_Ouvert);
						$oBlock_Cours->remplacer("{rubrique.url}",$sUrl);
					}
					else
					{
						$oBlock_Cours->remplacer("{lien_document_telecharger}",$oSet_Document_Telecharger_Ferme);
					}
					
					break;
					
				case LIEN_CHAT:
				//   ---------
					$oBlock_Cours->ajouter($oSet_Chat);
					$oBlock_Cours->remplacer("{lien_chat}",($bStatutOuvert ? $oSet_Chat_Ouvert : $oSet_Chat_Ferme));
					
					break;
					
				case LIEN_TEXTE_FORMATTE:
				//   -------------------
					$oBlock_Cours->ajouter($oSet_TexteFormatte);
					$oBlock_Cours->remplacer("{lien_texte_formatte}",($bStatutOuvert ? $oSet_TexteFormatte_Ouvert : $oSet_TexteFormatte_Ferme));
					
					break;

				case LIEN_HOTPOTATOES:
				//   --------------
					$oBlock_Cours->ajouter($oSet_Page_Html);
					list($sUrl) = explode(":",$aoRubriques[$r]->retDonnees());
					
					if ($bStatutOuvert && strlen($sUrl) > 0)
					{
						
						$sUrl = $oProjet->retRepRubriques(rawurlencode($sUrl));
						$oBlock_Cours->remplacer("{lien_hotpotatoes}",$oSet_Page_Html_Ouvert);
						$oBlock_Cours->remplacer("{rubrique.url}",$sUrl);
					}
					else
					{
						$oBlock_Cours->remplacer("{lien_hotpotatoes}",$oSet_Page_Html_Ferme);
					}
					
					break;
					
		}
		
		// Nom de l'unité
		$sNomUnite = emb_htmlentities($aoRubriques[$r]->retNom());
		$oBlock_Cours->remplacer("{rubrique.id}",$iIdRub);
		$oBlock_Cours->remplacer("{rubrique.nom}",ereg_replace("[[:space:]]+\?","&nbsp;?",$sNomUnite));
	}
	
	$oTpl->remplacer("{module.id}",$iIdMod);
}
else
{
	$oBlocSansFormations->afficher();
	$oBlocTitreModule->effacer();
	$oBlocDescriptionModule->effacer();
}

$oBlock_Cours->afficher();

// Awareness
$oAwareness = new TPL_Block("BLOCK_APPLET_AWARENESS",$oTpl);
$oAwareness->remplacer("{applet_awareness}",retAwarenessSpy($oProjet->oFormationCourante->retNom(),TRUE));
$oAwareness->afficher();

$oTpl->remplacer("{rubrique.niveau.id}",TYPE_RUBRIQUE);

$oTpl->remplacer("{personne.statut:urlencode}",rawurlencode($oProjet->retTexteStatutUtilisateur()));

$oTpl->remplacer("{unite.url}","zone_cours-index.php");
$oTpl->remplacer("{telechargement.url}",dir_lib("download.php"));
$oTpl->remplacer("{chat.url}",dir_chat("tchatche-index.php"));
$oTpl->remplacer("{texte_formatte.url}",dir_sousactiv(LIEN_PAGE_HTML,"description-index.php"));

// {{{ Outils du cours
if (!empty($iNbrRubriques) && $oProjet->verifPermission("PERM_OUTIL_TABLEAU_DE_BORD"))
	$oTpl->remplacer(
		"{outils.tableau_de_bord}",
		"<a"
			." href=\"admin://tableaubord/tableau_bord-index.php"
				."?idNiveau={$iIdMod}"
				."&typeNiveau=".TYPE_MODULE
				."&idType=0"
				."&idModal=0\""
			." onclick=\"return tableau_de_bord(this)\""
			." target=\"_blank\""
			." title=\"Tableau de bord\">"
			."<img"
				." src=\"commun://icones/24x24/tableaubord.gif\""
				." width=\"24\""
				." height=\"24\""
				." border=\"0\">"
		."</a>");
else
	$oTpl->remplacer("{outils.tableau_de_bord}",NULL);

$oTpl->remplacer("{outils.choix_courriel}",retLienEnvoiCourriel("?idStatuts=".STATUT_PERS_TUTEUR."x".STATUT_PERS_ETUDIANT."x".STATUT_PERS_RESPONSABLE."&typeCourriel=courriel-cours@cours"));
$oTpl->remplacer("{outils.liste_inscrits}",retLienListeInscrits());
// }}}

// Afficher la page html
$oTpl->afficher();

$oProjet->terminer();

?>

