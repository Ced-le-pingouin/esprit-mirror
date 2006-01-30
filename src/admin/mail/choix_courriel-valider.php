<?php

/*
** Fichier ................: choix_courriel-valider.php
** Description ............:
** Date de cr�ation .......: 19/01/2005
** Derni�re modification ..: 23/02/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iBoiteEnvoi      = (empty($HTTP_POST_VARS["boiteCourrielle"]) ? BOITE_COURRIELLE_PLATEFORME : $HTTP_POST_VARS["boiteCourrielle"]);
$url_sSujetCourriel   = (empty($HTTP_POST_VARS["sujetCourriel"]) ? NULL : $HTTP_POST_VARS["sujetCourriel"]);
$url_sMessageCourriel = (empty($HTTP_POST_VARS["messageCourriel"]) ? NULL : $HTTP_POST_VARS["messageCourriel"]);
$url_iIdStatuts       = (empty($HTTP_POST_VARS["idStatuts"]) ? NULL : $HTTP_POST_VARS["idStatuts"]);
$url_iIdEquipes       = (empty($HTTP_POST_VARS["idEquipes"]) ? NULL : $HTTP_POST_VARS["idEquipes"]);
$url_iIdPers          = (empty($HTTP_POST_VARS["idPers"]) ? NULL : $HTTP_POST_VARS["idPers"]);
$url_sTypeCourriel    = (empty($HTTP_POST_VARS["typeCourriel"]) ? NULL : $HTTP_POST_VARS["typeCourriel"]);

// ---------------------
// Initialiser
// ---------------------
$sCourrielParams  = (isset($url_iIdStatuts) ? "?idStatuts={$url_iIdStatuts}" : NULL);
$sCourrielParams .= (isset($url_iIdEquipes) ? (empty($sCourrielParams) ? "?" : "&")."idEquipes={$url_iIdEquipes}" : NULL);
$sCourrielParams .= (isset($url_iIdPers) ? (empty($sCourrielParams) ? "?" : "&")."idPers={$url_iIdPers}" : NULL);
$sCourrielParams .= (isset($url_sTypeCourriel) ? (empty($sCourrielParams) ? "?" : "&")."typeCourriel={$url_sTypeCourriel}" : NULL);

// Rechercher les adresses courriel
$asAdressesCourrielles = array();

if (BOITE_COURRIELLE_OS == $url_iBoiteEnvoi)
{
	include_once(dir_database("personnes.class.php"));
	
	$oPersonnes = new CPersonnes($oProjet);
	
	if (isset($url_iIdStatuts))
		$oPersonnes->initGraceIdStatuts(explode("x",$url_iIdStatuts));
	
	if (isset($url_iIdEquipes))
		$oPersonnes->initGraceIdEquipes(explode("x",$url_iIdEquipes));
	
	if (isset($url_iIdPers))
		$oPersonnes->initGraceIdPers(explode("x",$url_iIdPers));
}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("choix_courriel-valider.tpl");

$oBlocJavascriptFunctionInit = new TPL_Block("BLOCK_JAVASCRIPT_FUNCTION_INIT",$oTpl);
$oBlocBoiteEnvoiOs           = new TPL_Block("BLOCK_BOITE_ENVOI_OS",$oTpl);

$asSetJavascriptFunctionInit = array(
	"plateforme" => $oBlocJavascriptFunctionInit->defVariable("SET_BOITE_COURRIELLE_PLATEFORME")
	, "os" => $oBlocJavascriptFunctionInit->defVariable("SET_BOITE_COURRIELLE_OS")
);

if (BOITE_COURRIELLE_OS == $url_iBoiteEnvoi)
{
	// Pour r�cup�rer la constante COURRIEL_MAX_UTILISATEURS
	include_once(dir_code_lib("mail.class.php"));
	
	$oMail = new CMail();
	
	$oBlocBoiteEnvoiDirecte   = new TPL_Block("BLOCK_BOITE_ENVOI_DIRECTE",$oBlocBoiteEnvoiOs);
	$oBlocBoiteEnvoiIndirecte = new TPL_Block("BLOCK_BOITE_ENVOI_INDIRECTE",$oBlocBoiteEnvoiOs);
	
	if (($iNbPersonnes = count($oPersonnes->aoPersonnes)) < COURRIEL_MAX_UTILISATEURS)
	{
		// Dans le cas o�, le nombre de personnes est inf�rieur au nombre maximum
		// de personnes autoris�es, on pourra lancer directement la boite d'envoi
		// du syst�me d'exploitation
		$oBlocBoiteEnvoiIndirecte->effacer();
		
		$sListeAdressesCourrielles = NULL;
		
		foreach ($oPersonnes->aoPersonnes as $oPersonne)
		{
			$sAdresseCourrielle = $oPersonne->retEmail();
			
			if (strlen($sAdresseCourrielle) > 0)
				$sListeAdressesCourrielles .= (isset($sListeAdressesCourrielles) ? ", " : NULL)
					.$oMail->retFormatterAdresse($sAdresseCourrielle,$oPersonne->retNomComplet());
		}
		
		$oBlocJavascriptFunctionInit->ajouter($asSetJavascriptFunctionInit["os"]);
		
		$oBlocBoiteEnvoiDirecte->remplacer("{liste_adresses_courrielles}",$sListeAdressesCourrielles);
		$oBlocBoiteEnvoiDirecte->afficher();
	}
	else
	{
		$oBlocBoiteEnvoiDirecte->effacer();
		
		$oBlocListeDestinataires = new TPL_Block("BLOCK_LISTE_DESTINATAIRES",$oBlocBoiteEnvoiIndirecte);
		
		$oBlocListeDestinataires->beginLoop();
		
		$iIdxPers = 0;
		$iIdxPersCourant = 0;
		$sListeAdressesCourrielles = NULL;
		
		foreach ($oPersonnes->aoPersonnes as $oPersonne)
		{
			$sAdresseCourrielle = $oPersonne->retEmail();
			
			if (strlen($sAdresseCourrielle) < 1)
				continue;
			
			$sListeAdressesCourrielles .= (isset($sListeAdressesCourrielles) ? ", " : NULL)
				.$oMail->retFormatterAdresse($sAdresseCourrielle,$oPersonne->retNomComplet());
			
			if (++$iIdxPersCourant == $iNbPersonnes ||
				++$iIdxPers == COURRIEL_MAX_UTILISATEURS)
			{
				$oBlocListeDestinataires->nextLoop();
				$oBlocListeDestinataires->remplacer("{liste_adresses_courrielles}",$sListeAdressesCourrielles);
				$oBlocListeDestinataires->remplacer("{liste_adresses_courrielles:htmlentities}",htmlentities($sListeAdressesCourrielles));
				$iIdxPers = 0;
				$sListeAdressesCourrielles = NULL;
			}
		}
		
		$oBlocListeDestinataires->afficher();
		
		$oBlocBoiteEnvoiIndirecte->afficher();
	}
	
	$oBlocBoiteEnvoiOs->afficher();
}
else
{
	// Par d�faut ou dans le cas d'erreur dans l'envoi de param�tres, c'est
	// la bo�te courrielle de la plate-forme qui sera ouvert
	$oBlocJavascriptFunctionInit->ajouter($asSetJavascriptFunctionInit["plateforme"]);
	$oBlocBoiteEnvoiOs->effacer();
}

$oBlocJavascriptFunctionInit->afficher();

$oTpl->remplacer("{courriel_params}",$sCourrielParams);

$oTpl->afficher();

$oProjet->terminer();

?>

