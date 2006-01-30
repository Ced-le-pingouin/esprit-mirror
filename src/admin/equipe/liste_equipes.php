<?php

/*
** Fichier ................: liste_equipes.php
** Description ............:
** Date de cr�ation .......: 08/12/2002
** Derni�re modification ..: 28/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

$g_iIdUtilisateur = $oProjet->retIdUtilisateur();

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_aiIdStatuts       = (empty($HTTP_GET_VARS["idStatuts"]) ? NULL : explode("x",$HTTP_GET_VARS["idStatuts"]));
$url_aiIdEquipes       = (empty($HTTP_GET_VARS["idEquipes"]) ? NULL : explode("x",$HTTP_GET_VARS["idEquipes"]));
$url_bAfficherBlocVide = (empty($HTTP_GET_VARS["affBlocVide"]) ? TRUE : ($HTTP_GET_VARS["affBlocVide"] == "oui"));

// ---------------------
// Initialiser
// ---------------------
$iNbPers = 0;

$sNomActiv = NULL; //(is_object($oProjet->oActivCourante) ? $oProjet->oActivCourante->retNom() : NULL);

// {{{ Rechercher les tuteurs
if (is_array($url_aiIdStatuts) && in_array(STATUT_PERS_TUTEUR,$url_aiIdStatuts))
	$iNbTuteurs = $oProjet->oModuleCourant->initTuteurs();
else
	$iNbTuteurs = 0;
// }}}

// {{{ Rechercher les �quipes
$iNbEquipes = 0;

if (isset($url_aiIdEquipes))
{
	if ($url_aiIdEquipes[0] == "tous")
	{
		$iNbEquipes = $oProjet->initEquipes(TRUE);
		$aoEquipes = &$oProjet->aoEquipes;
	}
	else
	{
		$aoEquipes = array();
		
		foreach ($url_aiIdEquipes as $iIdEquipe)
			$aoEquipes[] = new CEquipe($oProjet->oBdd,$iIdEquipe,TRUE);
		
		$iNbEquipes = count($aoEquipes);
	}
}
// }}}

// ---------------------
// D�clarer les fonctions locales
// ---------------------
function retFicheCompletee ($v_oPersonne)
{
	global $g_iIdUtilisateur, $iNbPers, $asTplRech, $asSetsPersonne;
	
	$iIdPers   = $v_oPersonne->retId();
	$sCourriel = $v_oPersonne->retEmail();
	
	$asTplRepl = array(
		$iNbPers++
		, $iIdPers
		, $v_oPersonne->retPseudo()
		, $v_oPersonne->retNom()
		, $v_oPersonne->retPrenom()
		, ($v_oPersonne->retSexe() == "F" ? $asSetsPersonne["sexe_feminin"] : $asSetsPersonne["sexe_masculin"])
		, ($iIdPers == $g_iIdUtilisateur ? $asSetsPersonne["indice"] : NULL)
		, (emailValide($sCourriel) || $g_iIdUtilisateur < 1 ? $asSetsPersonne["courriel"] : $asSetsPersonne["sans_courriel"])
		, $sCourriel);
	
	return str_replace($asTplRech,$asTplRepl,$asSetsPersonne["fiche_personne"]);
}

// ---------------------
// Template globale
// ---------------------
$oTpl = new Template(dir_theme("globals.inc.tpl",FALSE,TRUE));
$asSetTplGlobale = array(
		"envoi_courriel" => $oTpl->defVariable("SET_ENVOI_COURRIEL")
		, "envoi_courriel_icone" => $oTpl->defVariable("SET_ENVOI_COURRIEL_MULTIPLE_ICONE")
	);

// ---------------------
// Onglet
// ---------------------
$oTplOnglet = new Template(dir_theme("onglet/onglet_tab.tpl",FALSE,TRUE));
$sSetOnglet = $oTplOnglet->defVariable("SET_ONGLET");

// ---------------------
// Template
// ---------------------
$oTpl = new Template("liste_equipes.tpl");

// {{{ Barre d'outils
$oBlocBarreOutils = new TPL_Block("BLOCK_BARRE_OUTILS",$oTpl);
$oBlocBarreOutils->ajouter($asSetTplGlobale["envoi_courriel"]);
$oBlocBarreOutils->remplacer("{envoi_courriel.params}","?idStatuts=".STATUT_PERS_TUTEUR."&idEquipes=tous&select=1");
$oBlocBarreOutils->remplacer("{envoi_courriel.icone}",$asSetTplGlobale["envoi_courriel_icone"]);
$oBlocBarreOutils->remplacer("{envoi_courriel.texte}",NULL);
$oBlocBarreOutils->afficher();
// }}}

$asSetsPersonne = array(
	"fiche_personne" => $oTpl->defVariable("SET_FICHE_PERSONNE")
	, "sexe_masculin" => $oTpl->defVariable("SET_SEXE_MASCULIN")
	, "sexe_feminin" => $oTpl->defVariable("SET_SEXE_FEMININ")
	, "courriel" => $oTpl->defVariable("SET_COURRIEL")
	, "sans_courriel" => $oTpl->defVariable("SET_SANS_COURRIEL")
	, "indice" => $oTpl->defVariable("SET_INDICE"));

$sSetOnglet .= $oTpl->defVariable("SET_SEPARATEUR_BLOC");

$asTplRech = array(
	"{personne.index}"
	,"{personne.id}"
	, "{personne.pseudo}"
	, "{personne.nom}"
	, "{personne.prenom}"
	, "{personne.sexe}"
	, "{personne.indice}"
	, "{icones}"
	, "{personne.courriel}");

// {{{ Liste des tuteurs
$oBlocTuteurs = new TPL_Block("BLOCK_TUTEURS",$oTpl);

$sVarMembreNonTrouve = $oBlocTuteurs->defVariable("VAR_MEMBRE_NON_TROUVE");

$sParamsFrameMenu = NULL;

if ($iNbTuteurs > 0)
{
	$sListeTuteurs = NULL;
	
	foreach ($oProjet->oModuleCourant->aoTuteurs as $oTuteur)
		$sListeTuteurs .= retFicheCompletee($oTuteur);
}
else
	$sListeTuteurs = $sVarMembreNonTrouve;

if ($url_bAfficherBlocVide)
{
	$oBlocTuteurs->ajouter($sSetOnglet);
	$oBlocTuteurs->remplacer("{onglet->titre}",$oBlocTuteurs->defVariable("VAR_TITRE"));
	$oBlocTuteurs->remplacer("{onglet->texte}",$oBlocTuteurs->defVariable("VAR_MEMBRES"));
	$oBlocTuteurs->remplacer("{liste_membres}",$sListeTuteurs);
	$oBlocTuteurs->afficher();
}
else
	$oBlocTuteurs->effacer();
// }}}

// {{{ Liste des �quipes
$oBlocEquipes = new TPL_Block("BLOCK_EQUIPES",$oTpl);

$asVarTitres = $oBlocEquipes->defVariable("VAR_TITRE",TRUE);

if ($iNbEquipes > 0)
{
	$sParamsFrameMenu = "&idEquipes=tous";
	
	$oBlocEquipes->beginLoop();
	
	foreach ($aoEquipes as $oEquipe)
	{
		$oBlocEquipes->nextLoop();
		
		// Composer la liste des membres de cette �quipe
		$sListeMembres = NULL;
		
		foreach ($oEquipe->aoMembres as $oMembre)
			$sListeMembres .= retFicheCompletee($oMembre);
		
		$oBlocEquipes->ajouter($sSetOnglet);
		$oBlocEquipes->remplacer("{onglet->titre}",$asVarTitres[1]);
		$oBlocEquipes->remplacer("{onglet->texte}",$oBlocEquipes->defVariable("VAR_MEMBRES"));
		$oBlocEquipes->remplacer("{equipe.nom}",htmlentities($oEquipe->retNom()));
		$oBlocEquipes->remplacer("{liste_membres}",$sListeMembres);
		
		$oBlocEquipes->effacerVariable("VAR_MEMBRE_NON_TROUVE");
	}
	
	$oBlocEquipes->afficher();
}
else if ($url_bAfficherBlocVide)
{
	$oBlocEquipes->ajouter($sSetOnglet);
	$oBlocEquipes->remplacer("{onglet->titre}",$asVarTitres[0]);
	$oBlocEquipes->remplacer("{onglet->texte}",$oBlocEquipes->defVariable("VAR_MEMBRE_NON_TROUVE"));
	
	$oBlocEquipes->effacerVariable("VAR_MEMBRES");
	
	$oBlocEquipes->afficher();
}
else
	$oBlocEquipes->effacer();
// }}}

// Afficher dans la zone de titre le nom de l'activit�
$oTpl->remplacer("{activite.nom}",rawurlencode($sNomActiv));
$oTpl->remplacer("{frame.menu.src}","liste_equipes-menu.php");

$oTpl->afficher();

$oProjet->terminer();

?>

