<?php

/*
** Fichier ................: copie_courriel.php
** Description ............:
** Date de cr�ation .......: 29/11/2004
** Derni�re modification ..: 21/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

$bPeutGererTousMessages  = $oProjet->verifPermission("PERM_MOD_MESSAGES_FORUMS");
$bPeutGererTousMessages |= ($oProjet->verifPermission("PERM_MOD_MESSAGES_FORUM") && $oProjet->verifModifierModule());

$iMonIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);
$sMonEmail  = ($iMonIdPers > 0 ? $oProjet->oUtilisateur->retEmail() : NULL);

// ---------------------
// Mise � jour
// ---------------------
if (is_array($HTTP_POST_VARS) && count($HTTP_POST_VARS) > 0)
{
	$url_iIdForum  = (empty($HTTP_POST_VARS["idForum"]) ? 0 : $HTTP_POST_VARS["idForum"]);
	
	if ($url_iIdForum > 0 && $iMonIdPers > 0)
	{
		$url_aiIdsEquipes   = (empty($HTTP_POST_VARS["idEquipes"]) ? NULL : $HTTP_POST_VARS["idEquipes"]);
		$iNbrEquipes        = count($url_aiIdsEquipes);
		$url_iCopieCourriel = (empty($HTTP_POST_VARS["copieCourriel"]) ? ($iNbrEquipes > 0 ? "1" : "0") : ("on" == $HTTP_POST_VARS["copieCourriel"]));
		
		$oForumPrefs = new CForumPrefs($oProjet->oBdd);
		
		if ($oForumPrefs->initForumPrefs($url_iIdForum,$iMonIdPers))
		{
			// Mise � jour de la table "ForumPrefs"
			$oForumPrefs->defCopieCourriel($url_iCopieCourriel);
			$oForumPrefs->enregistrer();
		}
		else
		{
			// Ajouter un enregistrement dans la table "ForumPrefs"
			$oForumPrefs->ajouter($url_iIdForum,$iMonIdPers,$url_iCopieCourriel);
		}
		
		if ($oForumPrefs->estForumParEquipe() && empty($url_aiIdsEquipes) && !$bPeutGererTousMessages)
		{
			$url_aiIdsEquipes = array();
			
			if ($url_iCopieCourriel)
			{
				if (MODALITE_PAR_EQUIPE == $oForumPrefs->retModalite())
				{
					if ($oProjet->initEquipe())
						$url_aiIdsEquipes[] = $oProjet->oEquipe->retId();
				}
				else
				{
					// Dans le cas o�, la modalit� du forum est un forum par �quipe
					// autre que "Equipe isol�e", d�s lors la personne doit �tre au
					// courant (par mail) des messages d�pos�s par n'importe quelles
					// �quipes.
					$oProjet->initEquipes();
					
					foreach ($oProjet->aoEquipes as $oEquipe)
						$url_aiIdsEquipes[] = $oEquipe->retId();
				}
			}
		}
		
		$oForumPrefs->defEquipes($url_aiIdsEquipes);
		
		fermerBoiteDialogue("top.opener.location=top.opener.location;");
		
		exit();
	}
}
else
{
	// R�cup�rer les variables de l'url
	$url_iIdForum  = (empty($HTTP_GET_VARS["idForum"]) ? 0 : $HTTP_GET_VARS["idForum"]);
}

// ---------------------
// Initialiser
// ---------------------
$bPeutUtiliserCopieCourriel = $oProjet->verifPermission("PERM_COPIE_COURRIEL_FORUM");

$oForumPrefs = new CForumPrefs($oProjet->oBdd);
$oForumPrefs->initForumPrefs($url_iIdForum,$iMonIdPers);

$iModaliteForum  = $oForumPrefs->retModalite();
$bForumParEquipe = (MODALITE_POUR_TOUS != $iModaliteForum);

$sFrameMenuSrc = "copie_courriel-menu.php";

// ---------------------
// Template
// ---------------------
$oTpl = new Template("copie_courriel.tpl");

$oBlocJavascriptFunctionValider = new TPL_Block("BLOCK_JAVASCRIPT_FUNCTION_VALIDER",$oTpl);

$oBlocCopieCourriel = new TPL_Block("BLOCK_COPIE_COURRIEL",$oTpl);

// Variables du template
$sVarSansEmail            = $oTpl->defVariable("SET_SANS_EMAIL");
$sVarEmailErrone          = $oTpl->defVariable("SET_EMAIL_ERRONE");
$sVarMessageCommun        = $oTpl->defVariable("SET_MESSAGE_COMMUN");
$sVarCopieCourriel        = $oTpl->defVariable("SET_COPIE_COURRIEL");
$sVarCopieCourrielEquipes = $oTpl->defVariable("SET_COPIE_COURRIEL_EQUIPES");

$sFormulaire = "<form>";
$sJavascriptFunctionValider = NULL;

if (empty($sMonEmail))
{
	// L'utilisateur n'a pas d'adresse �lectronique
	$sFrameMenuSrc .= "?menu=profil";
	$oBlocJavascriptFunctionValider->effacer();
	$oBlocCopieCourriel->ajouter($sVarSansEmail);
}
else if (!emailValide($sMonEmail))
{
	// L'adresse �lectronique de l'utilisateur n'est pas valide
	$sFrameMenuSrc .= "?menu=profil";
	$oBlocJavascriptFunctionValider->effacer();
	$oBlocCopieCourriel->ajouter($sVarEmailErrone);
}
else if ($iMonIdPers > 0 && $bPeutUtiliserCopieCourriel)
{
	$sFrameMenuSrc .= "?menu=valider";
	$asVarCopieCourrielValider = $oBlocJavascriptFunctionValider->defVariable("VAR_COPIE_COURRIEL_VALIDER",TRUE);
	
	if ($bForumParEquipe && ($bPeutGererTousMessages || MODALITE_PAR_EQUIPE != $iModaliteForum))
	{
		$oBlocJavascriptFunctionValider->remplacer("{valider}",$asVarCopieCourrielValider[1]);
		$oBlocCopieCourriel->ajouter($sVarCopieCourrielEquipes);
	}
	else
	{
		$oBlocJavascriptFunctionValider->remplacer("{valider}",$asVarCopieCourrielValider[0]);
		$sFormulaire = "<form action=\"copie_courriel.php\" target=\"_self\" method=\"post\">";
		$oBlocCopieCourriel->ajouter($sVarCopieCourriel);
	}
	
	$oBlocCopieCourriel->remplacer("{message_commun}",$sVarMessageCommun);
	
	$oBlocCopieCourriel->remplacer("{personne->email}",$sMonEmail);
	//$oBlocCopieCourriel->remplacer("{personne->email}","<a href=\"mailto:{$sMonEmail}\" target=\"_self\" onfocus=\"blur()\">".htmlentities($sMonEmail)."</a>");
	
	$oBlocCopieCourriel->remplacer("{iframe->src}","copie_courriel-equipes.php?idForum={forum->id}");
	
	$oBlocJavascriptFunctionValider->afficher();
}

$oBlocCopieCourriel->remplacer("{copieCourriel->selectionne}",($oForumPrefs->retCopieCourriel() ? " checked=\"checked\"" : NULL));

// Afficher la liste des �quipes

$oBlocCopieCourriel->afficher();

$oTpl->remplacer("{javascript_function_valider}",$sJavascriptFunctionValider);

$oTpl->remplacer("{frames['menu']->url}",$sFrameMenuSrc);

// Formulaire
$oTpl->remplacer("{html_form}",$sFormulaire);
$oTpl->remplacer("{forum->id}",$url_iIdForum);
$oTpl->remplacer("{/html_form}","</form>");

$oTpl->afficher();

$oProjet->terminer();

?>

