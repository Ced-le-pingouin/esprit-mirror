<?php

/*
** Fichier ................: copie_courriel-mail.inc.php
** Description ............: 
** Date de création .......: 06/12/2004
** Dernière modification ..: 03/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

if (!is_object($oSujet))
	$oSujet = new CSujetForum($oProjet->oBdd,$url_iIdSujet);

$iIdForumCopieCourriel = $oSujet->retIdParent();

$oForumPrefs = new CForumPrefs($oProjet->oBdd);

if ($iIdForumCopieCourriel > 0)
	$oForumPrefs->initForum($iIdForumCopieCourriel);

// Il faut envoyer le nouveau message par courriel à toutes les personnes qui
// se sont inscrites et une copie courriel est envoyé automatiquement aux
// administrateurs de la plate-forme
if ($oForumPrefs->peutEnvoyerCopieCourrielForum() ||
	(defined("GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN") &&
			strlen(GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN)))
{
	$oProjet->initSousActivCourante();
	
	$asTplRechercher   = array();
	$asTplRechercher[] = "{plateforme.nom}";
	$asTplRechercher[] = "{plateforme.url}";
	$asTplRechercher[] = "{formation.nom}";
	$asTplRechercher[] = "{module.nom}";
	$asTplRechercher[] = "{personne.nom}";
	$asTplRechercher[] = "{personne.prenom}";
	$asTplRechercher[] = "{personne.pseudo}";
	$asTplRechercher[] = "{forum.nom}"; 
	
	$asTplRemplacer   = array();
	$asTplRemplacer[] = $oProjet->retNom();
	$asTplRemplacer[] = dir_http_plateform();
	$asTplRemplacer[] = $oProjet->oFormationCourante->retNom();
	$asTplRemplacer[] = $oProjet->oModuleCourant->retNom();
	$asTplRemplacer[] = $oProjet->oUtilisateur->retNom();
	$asTplRemplacer[] = $oProjet->oUtilisateur->retPrenom();
	$asTplRemplacer[] = $oProjet->oUtilisateur->retPseudo();
	$asTplRemplacer[] = $oForumPrefs->retNom();
	
	// Template contenant les modéles de courriel
	$oTpl = new Template(dir_admin("mail","sujet_msg_courriel.inc.tpl",TRUE));
	
	$oBlocCopieCourriel = new TPL_Block("BLOCK_COPIE_MESSAGE_FORUM",$oTpl);
	$oBlocCopieCourriel->remplacer($asTplRechercher,$asTplRemplacer);
	
	$sSujetCopieCourriel   = $oBlocCopieCourriel->defVariable("VAR_SUJET_COURRIEL");
	$sMessageCopieCourriel = $url_sMessage
		."\r\n\r\n"
		.$oBlocCopieCourriel->defVariable("VAR_MESSAGE_COURRIEL");
	
	$oForumPrefs->envoyerCopieCourriel($sSujetCopieCourriel,$sMessageCopieCourriel,$oProjet->retEmail(),$oProjet->retNom(),$url_iIdEquipe);
}

unset($iIdForumCopieCourriel);

?>

