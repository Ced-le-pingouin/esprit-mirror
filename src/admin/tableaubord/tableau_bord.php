<?php

/*
** Fichier ................: tableau_bord.php
** Description ............:
** Date de création .......: 20/06/2005
** Dernière modification ..: 19/12/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("evenement.tbl.php"));
require_once(dir_locale("globals.lang"));
require_once(dir_chat("archive.class.php",TRUE));

$oProjet = new CProjet();

include_once(dir_include("verification.inc.php"));

$oProjet->initModuleCourant();

$g_iIdUtilisateur       = $oProjet->retIdUtilisateur();
$g_iIdStatutUtilisateur = $oProjet->retStatutUtilisateur();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iTypeNiveau = (empty($HTTP_GET_VARS["typeNiveau"]) ? 0 : $HTTP_GET_VARS["typeNiveau"]);
$url_iIdNiveau   = (empty($HTTP_GET_VARS["idNiveau"]) ? 0 : $HTTP_GET_VARS["idNiveau"]);
$url_iIdModalite = (empty($HTTP_GET_VARS["idModal"]) ? NULL : $HTTP_GET_VARS["idModal"]); // !!! Laisser NULL car 0 = chat public et 1 = chat par équipe
$url_iIdType     = (empty($HTTP_GET_VARS["idType"]) ? 0 : $HTTP_GET_VARS["idType"]);

// ---------------------
// Fonctions locales
// ---------------------
function retTexteModalite ($v_sTexteType,$v_iIdModalite)
{
	return "({$v_sTexteType}"
		.(MODALITE_INDIVIDUEL == $v_iIdModalite || MODALITE_POUR_TOUS == $v_iIdModalite
			? NULL
			: " - ".htmlentities(TXT_MODALITE_PAR_EQUIPE))
		.")";
}

// ---------------------
// Initialiser
// ---------------------
$oIds = new CIds($oProjet->oBdd,$url_iTypeNiveau,$url_iIdNiveau);
$iIdForm = $oIds->retIdForm();
$iIdMod = $oIds->retIdMod();
$iIdRubrique = $oIds->retIdRubrique();
unset($oIds);

$bPeutAfficherCollecticiels = ($url_iIdType == 0 | $url_iIdType == LIEN_COLLECTICIEL);
$bPeutAfficherFormulaires   = ($url_iIdType == 0 | $url_iIdType == LIEN_FORMULAIRE);
$bPeutAfficherForums        = ($url_iIdType == 0 | $url_iIdType == LIEN_FORUM);
$bPeutAfficherChats         = ($url_iIdType == 0 | $url_iIdType == LIEN_CHAT);

$bEstEtudiant = ($g_iIdStatutUtilisateur == STATUT_PERS_ETUDIANT);

$g_bModaliteParEquipe = (MODALITE_PAR_EQUIPE == $url_iIdModalite);

$oFormation = new CFormation($oProjet->oBdd,$iIdForm);
$oModule    = new CModule($oProjet->oBdd,$iIdMod);

$oResSousActiv = new CRessourceSousActiv($oProjet->oBdd);

$abModalites = array();

// {{{ Initialiser les équipes
$oEquipe = new CEquipe($oProjet->oBdd);

$_aiEquipes = array();

if ($oProjet->initEquipes(TRUE,$url_iIdNiveau,$url_iTypeNiveau) > 0)
	foreach ($oProjet->aoEquipes as $oEquipe)
	{
		$iIdEquipe = $oEquipe->retId();
		
		foreach ($oEquipe->aoMembres as $oMembre)
			$_aiEquipes[$oMembre->retId()] = $iIdEquipe;
	}
// }}}

// {{{ Rechercher les étudiants qui se trouvent dans les équipes
if ($g_bModaliteParEquipe)
{
	// inscrits dans des équipes
	$iIdxMembre = 0;
	$aoInscrits = array();
	
	foreach ($oProjet->aoEquipes as $oEquipe)
	{
		$iIdEquipe  = $oEquipe->retId();
		$sNomEquipe = $oEquipe->retNom();
		
		// Le premier membre de l'équipe recevra le nom de l'équipe
		foreach ($oEquipe->aoMembres as $aoInscrits[$iIdxMembre])
		{
			$aoInscrits[$iIdxMembre]->IdEquipe = $iIdEquipe;
			$aoInscrits[$iIdxMembre++]->NomEquipe = $sNomEquipe;
			
			// Le membre suivant de cette équipe n'aura pas le nom de l'équipe
			$iIdEquipe = 0; $sNomEquipe = NULL;
		}
	}
}
else if ($oFormation->retInscrAutoModules())
{
	// inscrits à cette formation
	$oFormation->initInscrits();
	$aoInscrits = &$oFormation->aoInscrits;
}
else
{
	// inscrits à ce module
	$oModule->initInscrits();
	$aoInscrits = &$oModule->aoInscrits;
}
// }}}

if (TYPE_MODULE == $url_iTypeNiveau)
	$oModule->initRubriques(LIEN_UNITE);
else
	$oModule->aoRubriques = array(new CModule_Rubrique($oProjet->oBdd,$url_iIdNiveau));

// ---------------------
// Template
// ---------------------
$oTpl = new Template("tableau_bord.tpl");
$oTpl->ajouterTemplate(dir_theme_commun("globals.inc.tpl",FALSE,TRUE));

// {{{ Traduire les termes
include_once(dir_locale("globals.lang"));
include_once(dir_locale("tableau_bord.lang"));

$asRechTpl = array(
	"[TXT_AUCUNE_EQUIPE_TROUVEE_DANS_CETTE_UNITE]"
	, "[TXT_AUCUN_INSCRIT_DANS_CETTE_UNITE]"
	, "[TXT_ETUDIANTS_INSCRITS_AU_COURS]"
	, "[TLT_CHAT_NOMBRE_MESSAGES_ARCHIVE]"
	, "[TLT_CLIQUER_ICI_POUR_ACCEDER_AU_COLLECTICIEL]"
	, "[TLT_CLIQUER_ICI_POUR_ACCEDER_AU_FORMULAIRE]"
	, "[TLT_FORUM_NOMBRE_MESSAGES_FORUM]"
	// {{{ Termes globaux
	, "[TLT_ENVOYER_COURRIEL]"
	// }}}
);

$asReplTpl = array(
	htmlentities(TXT_AUCUNE_EQUIPE_TROUVEE_DANS_CETTE_UNITE)
	, htmlentities(TXT_AUCUN_INSCRIT_DANS_CETTE_UNITE)
	, htmlentities(TXT_ETUDIANTS_INSCRITS_AU_COURS)
	, htmlentities(TLT_CHAT_NOMBRE_MESSAGES_ARCHIVE)
	, htmlentities(TLT_CLIQUER_ICI_POUR_ACCEDER_AU_COLLECTICIEL)
	, htmlentities(TLT_CLIQUER_ICI_POUR_ACCEDER_AU_FORMULAIRE)
	, htmlentities(TLT_FORUM_NOMBRE_MESSAGES_FORUM)
	// {{{ Termes globaux
	, htmlentities(TLT_ENVOYER_COURRIEL)
	// }}}
);

$oTpl->remplacer($asRechTpl,$asReplTpl);
// }}}

$sSetCollecticiel = $oTpl->defVariable("SET_COLLECTICIEL");
$sSetFormulaire   = $oTpl->defVariable("SET_FORMULAIRE");
$sSetForum        = $oTpl->defVariable("SET_FORUM");
$sSetChat         = $oTpl->defVariable("SET_CHAT");

$asTplGlobalCommun = array(
	"url_archives" => $oTpl->defVariable("SET_URL_CHAT_ARCHIVES")
	, "url_envoi_courriel" => $oTpl->defVariable("SET_URL_ENVOI_COURRIEL")
	, "url_forum" => $oTpl->defVariable("SET_URL_FORUM")
);

// {{{ Barre des outils
$oBlocBarreOutils = new TPL_Block("BLOCK_BARRE_OUTILS",$oTpl);
$sBarreOutils = $asTplGlobalCommun["url_envoi_courriel"];
$oBlocBarreOutils->remplacer(
	array("{barre_outils}","{url.params}")
	, array($sBarreOutils,"?idForm={$iIdForm}&idMod={$iIdMod}&idUnite={$iIdRubrique}&".(MODALITE_PAR_EQUIPE == $url_iIdModalite ? "idEquipes=tous" : "idStatuts=".STATUT_PERS_ETUDIANT)."&typeCourriel=courriel-unite"."&select=1")
);
$oBlocBarreOutils->afficher();
// }}}

$oBlocRubrique = new TPL_Block("BLOCK_RUBRIQUE",$oTpl);
$oBlocRubrique->beginLoop();

foreach ($oModule->aoRubriques as $oRubrique)
{
	$oBlocRubrique->nextLoop();
	$oBlocRubrique->remplacer("{rubrique.nom}",htmlentities($oRubrique->retNomComplet()));
	
	// ---------------------
	// Afficher les entêtes du tableau
	// ---------------------
	$iCol = 1;
	$iIdRubr = $oRubrique->retId();
	
	// {{{ Colonnes des collecticiels
	$aoBlocs = array(
		"nom" => new TPL_Block("BLOCK_COLLECTICIEL_NOM",$oBlocRubrique)
		, "modalite" => new TPL_Block("BLOCK_COLLECTICIEL_MODALITE",$oBlocRubrique)
	);
	
	if ($bPeutAfficherCollecticiels
		&& ($iNbCollecticiels = $oRubrique->initCollecticiels($url_iIdModalite)) > 0)
	{
		$aoBlocs["nom"]->beginLoop();
		$aoBlocs["modalite"]->beginLoop();
		
		foreach ($oRubrique->aoCollecticiels as $oCollecticiel)
		{
			$abModalites[$iCol] = $oCollecticiel->retModalite(TRUE);
			
			$aoBlocs["nom"]->nextLoop();
			$aoBlocs["nom"]->remplacer("{collecticiel.td.id}","u{$iIdRubr}c{$iCol}");
			$aoBlocs["nom"]->remplacer("{collecticiel.nom}",htmlentities($oCollecticiel->retNom()));
			
			$aoBlocs["modalite"]->nextLoop();
			$aoBlocs["modalite"]->remplacer("{collecticiel.modalite}",retTexteModalite(TXT_COLLECTICIEL,$abModalites[$iCol]));
			
			$iCol++;
		}
		
		$aoBlocs["nom"]->afficher();
		$aoBlocs["modalite"]->afficher();
	}
	else
	{
		$aoBlocs["nom"]->effacer();
		$aoBlocs["modalite"]->effacer();
	}
	// }}}
	
	// {{{ Colonnes des formulaires
	$aoBlocs = array(
		"nom" => new TPL_Block("BLOCK_FORMULAIRE_NOM",$oBlocRubrique)
		, "modalite" => new TPL_Block("BLOCK_FORMULAIRE_MODALITE",$oBlocRubrique)
	);
	
	if ($bPeutAfficherFormulaires
		&& ($iNbFormulaires = $oRubrique->initFormulaires($url_iIdModalite)) > 0)
	{
		$aoBlocs["nom"]->beginLoop();
		$aoBlocs["modalite"]->beginLoop();
		
		foreach ($oRubrique->aoFormulaires as $oFormulaire)
		{
			$abModalites[$iCol] = $oFormulaire->retModalite(TRUE);
			
			$aoBlocs["nom"]->nextLoop();
			$aoBlocs["nom"]->remplacer("{formulaire.td.id}","u{$iIdRubr}c{$iCol}");
			$aoBlocs["nom"]->remplacer("{formulaire.nom}",htmlentities($oFormulaire->retNom()));
			
			$aoBlocs["modalite"]->nextLoop();
			$aoBlocs["modalite"]->remplacer("{formulaire.modalite}",retTexteModalite("AEL",$abModalites[$iCol]));
			
			$iCol++;
		}
		
		$aoBlocs["nom"]->afficher();
		$aoBlocs["modalite"]->afficher();
	}
	else
	{
		$aoBlocs["nom"]->effacer();
		$aoBlocs["modalite"]->effacer();
	}
	// }}}
	
	// {{{ Colonnes des forums
	$aoBlocs = array(
		"nom" => new TPL_Block("BLOCK_FORUM_NOM",$oBlocRubrique)
		, "modalite" => new TPL_Block("BLOCK_FORUM_MODALITE",$oBlocRubrique)
	);
	
	$iIdModalite = (isset($url_iIdModalite)
		? ($g_bModaliteParEquipe ? array(MODALITE_PAR_EQUIPE, MODALITE_PAR_EQUIPE_INTERCONNECTEE, MODALITE_PAR_EQUIPE_COLLABORANTE) : MODALITE_POUR_TOUS)
		: NULL);
	
	if ($bPeutAfficherForums
		&& ($iNbForums = $oRubrique->initForums($iIdModalite)) > 0)
	{
		$aoBlocs["nom"]->beginLoop();
		$aoBlocs["modalite"]->beginLoop();
		
		foreach ($oRubrique->aoForums as $oForum)
		{
			$abModalites[$iCol] = $oForum->retModalite();
			
			$aoBlocs["nom"]->nextLoop();
			$aoBlocs["nom"]->remplacer("{forum.td.id}","u{$iIdRubr}c{$iCol}");
			$aoBlocs["nom"]->remplacer("{forum.nom}",htmlentities($oForum->retNom()));
			
			$aoBlocs["modalite"]->nextLoop();
			$aoBlocs["modalite"]->remplacer("{forum.modalite}",retTexteModalite(TXT_FORUM,$abModalites[$iCol]));
			
			$iCol++;
		}
		
		$aoBlocs["nom"]->afficher();
		$aoBlocs["modalite"]->afficher();
	}
	else
	{
		$aoBlocs["nom"]->effacer();
		$aoBlocs["modalite"]->effacer();
	}
	// }}}
	
	// {{{ colonnes des chats
	$iModaliteChat = (isset($url_iIdModalite)
		? ($g_bModaliteParEquipe ? 1 : 0)
		: NULL);
	
	$aoBlocs = array(
		"nom" => new TPL_Block("BLOCK_CHAT_NOM",$oBlocRubrique)
		, "modalite" => new TPL_Block("BLOCK_CHAT_MODALITE",$oBlocRubrique)
	);
	
	if ($bPeutAfficherChats
		&& ($iNbChats = $oRubrique->initChats2($iModaliteChat)) > 0)
	{
		$aoBlocs["nom"]->beginLoop();
		$aoBlocs["modalite"]->beginLoop();
		
		foreach ($oRubrique->aoChats as $oChat)
		{
			$abModalites[$iCol] = $oChat->retModalite(TRUE);
			
			$oChat->initParent();
			
			$aoBlocs["nom"]->nextLoop();
			$aoBlocs["nom"]->remplacer("{chat.td.id}","u{$iIdRubr}c{$iCol}");
			$aoBlocs["nom"]->remplacer("{chat.parent.nom}",htmlentities($oChat->oParent->retNom()));
			$aoBlocs["nom"]->remplacer("{chat.nom}",htmlentities($oChat->retNom()));
			
			$aoBlocs["modalite"]->nextLoop();
			$aoBlocs["modalite"]->remplacer("{chat.modalite}",retTexteModalite(TXT_CHAT,$abModalites[$iCol]));
			
			$iCol++;
		}
		
		$aoBlocs["nom"]->afficher();
		$aoBlocs["modalite"]->afficher();
	}
	else
	{
		$aoBlocs["nom"]->effacer();
		$aoBlocs["modalite"]->effacer();
	}
	// }}}
	
	// {{{ Colonne des connexions
	$oBlocRubrique->remplacer("{connexion.th.id}","u{$iIdRubr}c{$iCol}");
	$iCol++;
	// }}}
	
	$iNbColsEntete = $iCol;
	
	// ---------------------
	// Afficher les informations par étudiant
	// ---------------------
	//$iTotalCols = $iNbCollecticiels + $iNbFormulaires + $iNbForums + $iNbChats;
	$iLigne = 1; $iCol = 1;
	
	// {{{ Afficher les informations pour chaque étudiant
	$oBlocTableauBord = new TPL_Block("BLOCK_TABLEAU_BORD",$oBlocRubrique);
	
	$oBlocTableauBord->beginLoop();
	
	foreach ($aoInscrits as $oInscrit)
	{
		$iIdInscrit     = $oInscrit->retId();
		$sPseudoInscrit = $oInscrit->retPseudo();
		
		$oBlocTableauBord->nextLoop();
		
		// {{{ Afficher ou pas le nom de l'équipe
		$oBlocEquipe = new TPL_Block("BLOCK_EQUIPE",$oBlocTableauBord);
		
		if ($g_bModaliteParEquipe && isset($oInscrit->NomEquipe))
		{
			$sNomEquipe = $oInscrit->NomEquipe;
			$iIdEquipe  = $oInscrit->IdEquipe;
			
			$oBlocEquipe->remplacer(
				array("{equipe.td.colspan}","{equipe.id}","{equipe.nom}")
				, array($iNbColsEntete+1,$iIdEquipe,htmlentities($sNomEquipe))
			);
			
			$oBlocEquipe->afficher();
		}
		else
			$oBlocEquipe->effacer();
		// }}}
		
		// {{{ Colonne de l'étudiant
		$oBlocTableauBord->remplacer(
			array("{personne.td.id}","{personne.index}","{personne.id}","{personne.nom}","{personne.prenom}")
			, array("u{$iIdRubr}l{$iLigne}",$iLigne,$iIdInscrit,strtoupper($oInscrit->retNom()),$oInscrit->retPrenom())
		);
		
		$oBloc = new TPL_Block("BLOCK_PERSONNE_INDICE",$oBlocTableauBord);
		
		if ($iIdInscrit == $g_iIdUtilisateur)
			$oBloc->afficher();
		else
			$oBloc->effacer();
		// }}}
		
		// {{{ Colonnes des collecticiels
		$oBloc = new TPL_Block("BLOCK_COLLECTICIEL",$oBlocTableauBord);
		
		if ($bPeutAfficherCollecticiels && $iNbCollecticiels > 0)
		{
			$oBloc->beginLoop();
			
			foreach ($oRubrique->aoCollecticiels as $oCollecticiel)
			{
				$sParamsUrl = "&idPers={$iIdInscrit}";
				
				$aiIdPers = array();
				
				if ($g_bModaliteParEquipe ||
					MODALITE_PAR_EQUIPE == $oCollecticiel->retModalite(TRUE))
				{
					$sParamsUrl = NULL;
					
					if ($oEquipe->initEquipe($iIdInscrit,$iIdRubr,TYPE_RUBRIQUE,TRUE) > 0)
					{
						$sParamsUrl = "&idEquipe=".$oEquipe->retId();
						
						foreach ($oEquipe->aoMembres as $oMembre)
							$aiIdPers[] = $oMembre->retId();
					}
				}
				else
					$aiIdPers[] = $iIdInscrit;
				
				$aiStatutPlusHautRes = $oCollecticiel->retStatutPlusHautRes($aiIdPers);
				
				// Dans la modalité par équipe, il faut afficher qu'un seul
				// document déposé par équipe
				if ($aiStatutPlusHautRes["StatutResPlusHautIdPers"] != $iIdInscrit)
					$aiStatutPlusHautRes["StatutResPlusHaut"] = 0;
				
				$iStatutPlusHautRes  = $aiStatutPlusHautRes["StatutResPlusHaut"];
				$sStatutPlusHautRes  = $oResSousActiv->retTexteStatut($iStatutPlusHautRes)
					.($iStatutPlusHautRes > STATUT_RES_EN_COURS && $aiStatutPlusHautRes["StatutResPlusHautNb"] > 1
						? "&nbsp;(".$aiStatutPlusHautRes["StatutResPlusHautNb"].")"
						: NULL);
				
				$oBloc->nextLoop();
				
				if (!$bEstEtudiant || $iIdInscrit == $g_iIdUtilisateur)
					$oBloc->remplacer("{collecticiel}",$sSetCollecticiel);
				
				$oBloc->remplacer(array("{collecticiel.td.id}","{collecticiel}"),array("u{$iIdRubr}l{$iLigne}c{$iCol}",$sStatutPlusHautRes));
				$oBloc->remplacer(array("{formation.id}","{module.id}","{rubrique.id}","{activite.id}","{sous_activite.id}","{params.url}"),array($iIdForm,$iIdMod,$iIdRubr,$oCollecticiel->retIdParent(),$oCollecticiel->retId(),$sParamsUrl));
				$oBloc->remplacer("{collecticiel.date}",($aiStatutPlusHautRes["StatutResPlusHautNb"] > 0 && $aiStatutPlusHautRes["StatutResPlusHaut"] != 0 ? "<br><small class=\"date\">".formatterDate($aiStatutPlusHautRes["StatutResDateRecente"])."</small>" : NULL));
				$iCol++;
			}
			
			$oBloc->afficher();
		}
		else
			$oBloc->effacer();
		// }}}
		
		// {{{ Colonnes des formulaires
		$oBloc = new TPL_Block("BLOCK_FORMULAIRE",$oBlocTableauBord);
		
		if ($bPeutAfficherFormulaires && $iNbFormulaires > 0)
		{
			$oBloc->beginLoop();
			
			foreach ($oRubrique->aoFormulaires as $oFormulaire)
			{
				$sParamsUrl = "&idPers={$iIdInscrit}";
				
				$aiStatutPlusHautFormulaire = $oFormulaire->retStatutPlusHautFormulaire($iIdInscrit);
				$iStatutPlusHautFormulaire = $aiStatutPlusHautFormulaire[0];
				$sStatutPlusHautFormulaire  = $oResSousActiv->retTexteStatut($iStatutPlusHautFormulaire)
					/*.($iStatutPlusHautFormulaire > STATUT_RES_EN_COURS && $aiStatutPlusHautFormulaire[1] > 1 ? "&nbsp;(".$aiStatutPlusHautFormulaire[1].")" : NULL)*/;
				$oBloc->nextLoop();
				
				if (!$bEstEtudiant || $iIdInscrit == $g_iIdUtilisateur)
					$oBloc->remplacer("{formulaire}",$sSetFormulaire);
				
				$oBloc->remplacer(array("{formulaire.td.id}","{formulaire}"),array("u{$iIdRubr}l{$iLigne}c{$iCol}",$sStatutPlusHautFormulaire));
				$oBloc->remplacer(array("{formation.id}","{module.id}","{rubrique.id}","{activite.id}","{sous_activite.id}","{params.url}"),array($iIdForm,$iIdMod,$iIdRubr,$oFormulaire->retIdParent(),$oFormulaire->retId(),$sParamsUrl));
				$oBloc->remplacer("{formulaire.date}",($aiStatutPlusHautFormulaire[0] > 0 ? "<br><small class=\"date\">".formatterDate($aiStatutPlusHautFormulaire[2])."</small>" : NULL));
				$iCol++;
			}
			
			$oBloc->afficher();
		}
		else
			$oBloc->effacer();
		// }}}
		
		// {{{ Colonnes des forums
		$oBloc = new TPL_Block("BLOCK_FORUM",$oBlocTableauBord);
		
		if ($bPeutAfficherForums && $iNbForums > 0)
		{
			$oBloc->beginLoop();
			
			foreach ($oRubrique->aoForums as $oForum)
			{
				$iIdForum    = $oForum->retId();
				$iIdNiveau   = $oForum->retIdNiveau();
				$iTypeNiveau = $oForum->retTypeNiveau();
				$amNbMessages = $oForum->retNbMessages($iIdInscrit);
				
				$oBloc->nextLoop();
				$oBloc->remplacer("{forum.td.id}","u{$iIdRubr}l{$iLigne}c{$iCol}");
				
				if (!$bEstEtudiant || $iIdInscrit == $g_iIdUtilisateur)
					$oBloc->remplacer("{forum}",$asTplGlobalCommun["url_forum"]);
				else
					$oBloc->remplacer("{forum}",$sSetForum);
				
				$oBloc->remplacer(array("{forum.params}","{forum.params.fenetre_nom}"),array("?idForum={$iIdForum}&idNiveau={$iIdNiveau}&typeNiveau={$iTypeNiveau}".($abModalites[$iCol] ? "&idEquipe={$_aiEquipes[$iIdInscrit]}" : NULL),"winForum{$iIdForum}"));
				$oBloc->remplacer(array("{forum.nom}","{forum.messages.nombre}"),($amNbMessages["NbMessagesForum"] > 0 ? $amNbMessages["NbMessagesForum"] : array("-",0)));
				$oBloc->remplacer("{forum.date}",($amNbMessages["NbMessagesForum"] > 0 ? "<br><small class=\"date\">".formatterDate($amNbMessages["DateDernierMessage"])."</small>" : NULL));
				$iCol++;
			}
			
			$oBloc->afficher();
		}
		else
			$oBloc->effacer();
		// }}}
		
		// {{{ Colonnes des chats
		$oBloc = new TPL_Block("BLOCK_CHAT",$oBlocTableauBord);
		
		if ($bPeutAfficherChats && $iNbChats > 0)
		{
			$oBloc->beginLoop();
			
			foreach ($oRubrique->aoChats as $oChat)
			{
				$iIdChat = $oChat->retId();
				
				$oIds = new CIds($oProjet->oBdd,$oChat->retTypeNiveau(),$oChat->retIdNiveau());
				
				switch ($oChat->retTypeNiveau())
				{
					case TYPE_SOUS_ACTIVITE:
						$sRepChatLog = dir_chat_log($oIds->retIdActiv(),$oIds->retIdForm(),NULL,TRUE);
						break;
				}
				
				unset($oIds);
				
				if (CHAT_PAR_EQUIPE == ($iModaliteChat = $oChat->retModalite()))
				{
					if ($oProjet->initEquipe(FALSE,$iIdInscrit))
						$sNomEquipe = $oProjet->oEquipe->retNom();
					else
						$sNomEquipe = "/@##@/";
				}
				else
					$sNomEquipe = NULL;
				
				// {{{ Compter le nombre de messages
				$iNbMessages = 0;
				$oArchives = new CArchives($sRepChatLog);
				$oArchives->defFiltre((MODALITE_PAR_EQUIPE == $url_iIdModalite ? ":".urlencode($sNomEquipe).":" : NULL)."delta_chat_{$iIdChat}");
				$iNbArchives = 0;
				$oArchives->initArchives();
				
				foreach ($oArchives->aoArchives as $oArchive)
					if (($i = $oArchive->initMessages($sPseudoInscrit)) > 0)
					{
						$iNbArchives++;
						$iNbMessages += $i;
					}
				// }}}
				
				$oBloc->nextLoop();
				$oBloc->remplacer("{chat.td.id}","u{$iIdRubr}l{$iLigne}c{$iCol}");
				
				if (!$bEstEtudiant || $iIdInscrit == $g_iIdUtilisateur)
					$oBloc->remplacer("{chat}",$asTplGlobalCommun["url_archives"]);
				else
					$oBloc->remplacer("{chat}",$sSetChat);
				
				$oBloc->remplacer("{params.idNiveau}",$oChat->retIdNiveau());
				$oBloc->remplacer("{params.typeNiveau}",$oChat->retTypeNiveau());
				$oBloc->remplacer("{params.idChat}",$iIdChat);
				$oBloc->remplacer("{params.idEquipe}",$iIdEquipe);
				$oBloc->remplacer("{params.idPers}",$iIdInscrit);
				$oBloc->remplacer("{chat_archives.nom}",($iNbArchives > 0 ? "{chat.messages.nombre} ({chat.archives.nombre})" : "-"));
				$oBloc->remplacer(array("{chat.messages.nombre}","{chat.archives.nombre}"),array($iNbMessages,$iNbArchives));
				$iCol++;
			}
			
			$oBloc->afficher();
		}
		else
			$oBloc->effacer();
		// }}}
		
		// {{{ Colonne des connexions
		$oEven = new CEvenement($oProjet->oBdd);
		$iNbConnexions = $oEven->initEvenementsPersonne($iIdInscrit,$iIdForm);
		if ($iNbConnexions > 0)
			$sDate = formatterDate($oEven->aoEvenements[0]->retMomentEven())." ({$iNbConnexions})";
		else
			$sDate = "-";
		
		$oBlocTableauBord->remplacer(array("{connexion.td.id}","{connexion}"),array("u{$iIdRubr}l{$iLigne}c{$iCol}",$sDate));
		$iCol++;
		// }}}
		
		$iLigne++; $iCol = 1;
	}
	
	$oBlocTableauBord->afficher();
	// }}}
}

// {{{ Dans le cas ou il n'y aurait pas d'équipe dans cette unité
$oBloc = new TPL_Block("BLOCK_MESSAGE",$oBlocRubrique);

if ($iLigne > 1)
	$oBloc->effacer();
else
{
	$sSetAucunInscrit = $oBloc->defVariable("SET_AUCUN_INSCRIT");
	$sSetAucuneEquipe = $oBloc->defVariable("SET_AUCUNE_EQUIPE");
	
	$oBloc->remplacer("{message.td.colspan}",$iNbColsEntete);
	$oBloc->remplacer("{message.texte}",($g_bModaliteParEquipe ? $sSetAucuneEquipe : $sSetAucunInscrit));
	$oBloc->afficher();
}
// }}}

$oBlocRubrique->afficher();

$oTpl->remplacer("{module.nom}",$oModule->retNomComplet());

$oTpl->afficher();

$oProjet->terminer();

?>

