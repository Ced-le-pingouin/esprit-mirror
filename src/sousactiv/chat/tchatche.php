<?php

/*
** Fichier ................: tchatche.php
** Description ............: 
** Date de création .......: 01/03/2001
** Dernière modification ..: 03/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("ids.class.php"));
require_once(dir_database("chat.tbl.php"));
require_once(dir_include("equipes.class.php"));
require_once("connecte.class.php");
require_once("archive.class.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdNiveau   = (empty($HTTP_GET_VARS["idNiveau"]) ? 0 : $HTTP_GET_VARS["idNiveau"]);
$url_iTypeNiveau = (empty($HTTP_GET_VARS["typeNiveau"]) ? 0 : $HTTP_GET_VARS["typeNiveau"]);

// ---------------------
// Initialiser
// ---------------------
$oIds = new CIds($oProjet->oBdd,$url_iTypeNiveau,$url_iIdNiveau);

$amParams = array("idForm" => $oIds->retIdForm()
	, "idActiv" => $oIds->retIdActiv());

switch ($url_iTypeNiveau)
{
	case TYPE_SOUS_ACTIVITE:
	//   ------------------
		$oParent = new CSousActiv($oProjet->oBdd,$url_iIdNiveau);
		break;
		
	case TYPE_RUBRIQUE:
	//   -------------
		$oParent = new CModule_Rubrique($oProjet->oBdd,$url_iIdNiveau);
		break;
}

// Répertoire ou sont déposés les archives des chats
$sRepArchives = dir_chat_archives($url_iTypeNiveau,$amParams,NULL,TRUE);

// Une personne inscrite ou visiteur ?
$iIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

// Les visiteurs n'ont pas le droit de participer au chat
$bPeutAccederChat = ($iIdPers > 0);

// ---------------------
// Equipes
// ---------------------
$oEquipes = new CEquipes($oProjet->oBdd,$url_iIdNiveau,$url_iTypeNiveau);

if (retHautStatut($oProjet->retStatutUtilisateur()))
{
	// Rechercher toutes les équipes
	$oEquipes->initEquipes(FALSE);
	$aoEquipes = &$oEquipes->aoEquipes;
}
else
{
	// Rechercher l'équipe de cet utilisateur
	$aoEquipes = array($oEquipes->initEquipeGraceIdPers($iIdPers));
}

$iTotalEquipes = count($aoEquipes);

// ---------------------
// Composer la liste des salons
// ---------------------
$oParent->initChats();

// Rechercher les archives des chats
$oArchives = new CArchives($sRepArchives,NULL,TRUE);

$sSalons = NULL;

$aaChats = array();

foreach ($oParent->aoChats as $oChat)
{
	$iId                = $oChat->retId();
	$iIdNiveau          = $oChat->retIdNiveau();
	$iTypeNiveau        = $oChat->retTypeNiveau();
	$sNom               = $oChat->retNom();
	$iModalite          = $oChat->retModalite();
	$bArchive           = ($oChat->retEnregConversation() ? TRUE : FALSE);
	$bConversationPrive = $oChat->retSalonPrive();
	$iNbrArchives       = 0;
	
	if ($iModalite == CHAT_PAR_EQUIPE)
	{
		// ---------------------
		// Chat par équipe
		// ---------------------
		for ($iIdxEquipe=0; $iIdxEquipe<$iTotalEquipes; $iIdxEquipe++)
		{
			if (!is_object($aoEquipes[$iIdxEquipe]))
				continue;
			
			$iIdEquipe  = $aoEquipes[$iIdxEquipe]->retId();
			$sNomEquipe = $aoEquipes[$iIdxEquipe]->retNom();
			
			if ($bArchive)
			{
				// Compter le nombre d'archives
				$oArchives->defFiltre(retIdUniqueChat($iId,urlencode($sNomEquipe)));
				$iNbrArchives = count($oArchives->retArchives());
			}
			
			$aaChats[] = array(
				"idChat" => $iId
				,"idNiveau" => $iIdNiveau
				,"typeNiveau" => $iTypeNiveau
				,"nomChat" => $sNom
				,"modaliteChat" => $sNomEquipe
				,"archivesChat" => $bArchive
				,"idEquipe" => $iIdEquipe
				,"utiliserSalonPrive" => $bConversationPrive
				,"nbArchives" => $iNbrArchives);
			}
	}
	else
	{
		// ---------------------
		// Chat public
		// ---------------------
		if ($bArchive)
		{
			// Compter le nombre d'archives
			$oArchives->defFiltre(retIdUniqueChat($iId));
			$iNbrArchives = count($oArchives->retArchives());
		}
		
		$aaChats[] = array(
			"idChat" => $iId
			,"idNiveau" => $iIdNiveau
			,"typeNiveau" => $iTypeNiveau
			,"nomChat" => $sNom
			,"modaliteChat" => "Public"
			,"archivesChat" => $bArchive
			,"idEquipe" => 0
			,"utiliserSalonPrive" => $bConversationPrive
			,"nbArchives" => $iNbrArchives);
	}
}

// ---------------------
// ---------------------
$oTpl = new Template(dir_theme_commun("globals.inc.tpl",FALSE,TRUE));

$asTplGlobalCommun = array(
	"url_chat_archives" => $oTpl->defVariable("SET_URL_CHAT_ARCHIVES")
);

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("tchatche.tpl",FALSE,TRUE));

$oBlocListeChats = new TPL_Block("BLOCK_LISTE_CHATS",$oTpl);

$sSetChatActif      = $oTpl->defVariable("SET_CHAT_ACTIF");
$sSetChatPassif     = $oTpl->defVariable("SET_CHAT_PASSIF");
$sSetArchive        = $oTpl->defVariable("SET_ARCHIVE");
$sSetChatSeparateur = $oTpl->defVariable("SET_SEPARATEUR_CHATS");
$sSetPasChatTrouve  = $oTpl->defVariable("SET_PAS_CHAT_TROUVE");

$sSalon = NULL;
$sListeChats = NULL;

foreach ($aaChats as $aChat)
{
	// Ajouter un chat
	$oBlocListeChats->ajouter(($bPeutAccederChat ? $sSetChatActif : $sSetChatPassif));
	
	if ($aChat["archivesChat"])
	{
		$oBlocListeChats->remplacer("{archive}",$asTplGlobalCommun["url_chat_archives"]);
		$oBlocListeChats->remplacer("{chat_archives.nom}",$sSetArchive);
		$oBlocListeChats->remplacer("{archives->total}",$aChat["nbArchives"]);
	}
	else
	{
		$oBlocListeChats->remplacer("{archive}",NULL);
	}
	
	$oBlocListeChats->remplacer("{chat->id}",$aChat["idChat"]);
	$oBlocListeChats->remplacer("{params.idNiveau}",$aChat["idNiveau"]);
	$oBlocListeChats->remplacer("{params.typeNiveau}",$aChat["typeNiveau"]);
	$oBlocListeChats->remplacer("{params.idChat}",$aChat["idChat"]);
	$oBlocListeChats->remplacer("{params.idEquipe}",$aChat["idEquipe"]);
	$oBlocListeChats->remplacer("{params.idPers}",0);
	$oBlocListeChats->remplacer("{chat->nom}",$aChat["nomChat"]);
	$oBlocListeChats->remplacer("{chat->modalite}",$aChat["modaliteChat"]);
	$oBlocListeChats->remplacer("{chat->salon_prive}",$aChat["utiliserSalonPrive"]);
	$oBlocListeChats->remplacer("{equipe->id}",$aChat["idEquipe"]);
	
	// Ajouter une séparation entre les chats
	$oBlocListeChats->ajouter($sSetChatSeparateur);
	// new Array("g_idListeConnectes36","idListeConnectes36_10","Equipe%20C1")
	// new Array("chat.id","equipe.id","equipe.nom")
	$sListeChats .= (isset($sListeChats) ? ", " : NULL)
		."new Array("
			."\"".$aChat["idChat"]."\""
			.",\"".$aChat["idEquipe"]."\""
			.",\"".($aChat["idEquipe"] == 0 ? NULL : urlencode($aChat["modaliteChat"]))."\""
		.")\n";
}

if (count($aaChats) == 0)
	$oBlocListeChats->ajouter($sSetPasChatTrouve);

$oBlocListeChats->afficher();

$oTpl->remplacer("{niveau->id}",$url_iIdNiveau);
$oTpl->remplacer("{niveau->type}",$url_iTypeNiveau);

$oTpl->remplacer("{chats->liste}",$sListeChats);

$oTpl->remplacer("{chat->url}","deltachat.php");
$oTpl->remplacer("{archives->url}","archives-index.php");
$oTpl->remplacer("{liste_connectes->url}","tchatche-connectes.php");

$oTpl->afficher();

$oProjet->terminer();

?>

