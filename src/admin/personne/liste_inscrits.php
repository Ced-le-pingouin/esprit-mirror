<?php

/*
** Fichier ................: liste_inscrits.php
** Description ............:
** Date de cr�ation .......: 02/09/2004
** Derni�re modification ..: 31/05/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initModuleCourant();

if (!is_object($oProjet->oModuleCourant))
	exit();

// ---------------------
// Initialiser
// ---------------------
// Seules les personnes inscrites ont le droit d'envoyer un email
$bPeutEnvoyerEmail = (isset($oProjet->oUtilisateur) && is_object($oProjet->oUtilisateur));
$iIdPers           = ($bPeutEnvoyerEmail ? $oProjet->oUtilisateur->retId() : 0);

$iIdForm           = (is_object($oProjet->oFormationCourante) ? $oProjet->oFormationCourante->retId() : 0);
$bInscrAutoModules = ($iIdForm > 0 ? $oProjet->oFormationCourante->retInscrAutoModules() : FALSE);

// Rechercher que les �tudiants inscrits � ce module, dans le cas o�, ils ne
// sont pas inscrits automatiquement � tous les modules
$iIdMod = (is_object($oProjet->oModuleCourant) ? $oProjet->oModuleCourant->retId() : 0);

// En-t�te de la page html
$sBlocHtmlHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
BLOCK_HTML_HEAD;

// ---------------------
// Template
// ---------------------
$oTpl = new Template("liste_inscrits.tpl");

$oBlocHtmlHead = new TPL_Block("BLOCK_HTML_HEAD",$oTpl);
$oBlocHtmlHead->ajouter($sBlocHtmlHead);
$oBlocHtmlHead->afficher();

$oBlocListes = new TPL_Block("BLOCK_LISTES",$oTpl);

$sSetListePersonnes = $oTpl->defVariable("SET_LISTE_PERSONNES");

$sSetFichePersonne = $oTpl->defVariable("SET_FICHE_PERSONNE");
$sSetSexeMasculin  = $oTpl->defVariable("SET_SEXE_MASCULIN");
$sSetSexeFeminin   = $oTpl->defVariable("SET_SEXE_FEMININ");

$sSetTraceConnexion = $oTpl->defVariable("SET_TRACE_CONNEXION_INDIVIDUEL");

$sSetEmail    = $oTpl->defVariable("SET_EMAIL");
$sSetNonEmail = $oTpl->defVariable("SET_NON_EMAIL");

$sSetAucunInscrit = $oTpl->defVariable("SET_AUNCUN_INSCRIT");

$sSetIndice = $oTpl->defVariable("SET_INDICE");

$sSetEnvoiCourrielInscrits = $oTpl->defVariable("SET_ENVOI_COURRIEL_INSCRITS");

// Onglet
$oTplOnglet = new Template(dir_theme("onglet/onglet_tab.tpl",FALSE,TRUE));
$sSetOnglet = $oTplOnglet->defVariable("SET_ONGLET");

// Afficher le nom du cours dans la page des titres
$oTpl->remplacer("{module->nom}",rawurlencode($oProjet->oModuleCourant->retNom()));

// Composer la liste des diff�rents inscrits
$aoComposerListes = array(
	array(STATUT_PERS_TUTEUR,"Liste des tuteurs",$sSetListePersonnes,"{personnes->liste}")
	, array(STATUT_PERS_ETUDIANT,"Liste des �tudiants",$sSetListePersonnes /*.$sSetEnvoiCourrielInscrits*/,"{personnes->liste}")
	, array(STATUT_PERS_RESPONSABLE,"Liste des responsables",$sSetListePersonnes,"{personnes->liste}")
);

$asCles = array(
	  "{personne->index}"
	, "{personne->alias}"
	, "{personne->mail}"
	, "{personne->mail}"
	, "{personne->trace}"
	, "{personne->prenom}"
	, "{personne->nom}"
	, "{personne->pseudo}"
	, "{personne->indice}");  // Montrer l'utilisateur actuel

$iPersonneIndex = 0;

foreach ($aoComposerListes as $aListeStatut)
{
	// Rechercher les inscrits par statut
	if ($oProjet->initPersonnes($aListeStatut[0],$iIdForm,(STATUT_PERS_ETUDIANT == $aListeStatut[0] && $bInscrAutoModules ? 0 : $iIdMod)) == 0)
		continue;
	
	// Ajouter un onglet
	$oBlocListes->ajouter($sSetOnglet);
	$oBlocListes->remplacer("{onglet->titre}",$aListeStatut[1]);
	$oBlocListes->remplacer("{onglet->texte}",$aListeStatut[2]);
	
	// Ajouter un espace apr�s ce onglet
	$oBlocListes->ajouter("<img src=\"commun://espacer.gif\" width=\"100%\" height=\"15\" border=\"0\">");
	
	// Composer la liste des inscrits
	$sListeInscrits = NULL;
	
	foreach ($oProjet->aoPersonnes as $oPersonne)
	{
		// Remplir la fiche de l'inscrit
		$iTmpIdPers = $oPersonne->retId();
		$sEmail     = ($bPeutEnvoyerEmail ? $oPersonne->retEmail() : NULL);
		
		$asValeurs = array(
			  ++$iPersonneIndex
			, ($oPersonne->retSexe() == "F" ? $sSetSexeFeminin : $sSetSexeMasculin)
			, $sSetTraceConnexion."&nbsp;".(strlen($sEmail) > 0 ? $sSetEmail : $sSetNonEmail)
			, $sEmail
			, "connexion('{$iTmpIdPers}')"
			, $oPersonne->retPrenom()
			, $oPersonne->retNom()
			, $oPersonne->retPseudo()
			, ($iTmpIdPers != $iIdPers ? NULL : $sSetIndice));
		
		$sListeInscrits .= $sSetFichePersonne;
		$sListeInscrits = str_replace($asCles,$asValeurs,$sListeInscrits);
		
		unset($asValeurs);
	}
	
	// Ajouter cette liste des inscrits dans l'onglet du statut actuel
	$oBlocListes->remplacer($aListeStatut[3],$sListeInscrits);
	$oBlocListes->remplacer("{a['envoi_courriel'].href}","email('?idStatuts=".$aListeStatut[0]."'); return false;");
}

// Dans le cas o�, il n'y aurait pas d'inscrits afficher un message
if ($oBlocListes->caracteres() == 0)
	$oBlocListes->ajouter($sSetAucunInscrit);

$oBlocListes->afficher();

$oTpl->afficher();

$oProjet->terminer();

?>

