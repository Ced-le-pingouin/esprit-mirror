<?php

/*
** Fichier ................: exporter-personnes.php
** Description ............:
** Date de cr�ation .......: 
** Derni�re modification ..: 27/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->verifPeutUtiliserOutils("PERM_OUTIL_EXPORT_TABLE_PERSONNE");

require_once(dir_include("personnes.class.php"));
$oPersonnes = new CPersonnes($oProjet->oBdd);

$url_iIdForm   = (isset($HTTP_GET_VARS["ID_FORM"]) && $HTTP_GET_VARS["ID_FORM"] > 0 ? $HTTP_GET_VARS["ID_FORM"] : NULL);
$url_iIdStatut = (isset($HTTP_GET_VARS["ID_STATUT"]) && $HTTP_GET_VARS["ID_STATUT"] > 0 ? $HTTP_GET_VARS["ID_STATUT"] : NULL);
$url_sTri      = (isset($HTTP_GET_VARS["TRI"]) ? $HTTP_GET_VARS["TRI"] : "nom");
$url_sOrdreTri = (isset($HTTP_GET_VARS["ORDRE_TRI"]) ? $HTTP_GET_VARS["ORDRE_TRI"] : $oPersonnes->ORDRE_TRI_CROISSANT);

if ($url_sTri == "prenom")
	$oPersonnes->defTrierSur($oPersonnes->TRIER_PRENOM);
elseif ($url_sTri == "pseudo")
	$oPersonnes->defTrierSur($oPersonnes->TRIER_PSEUDO);
else
	$oPersonnes->defTrierSur($oPersonnes->TRIER_NOM);

$oPersonnes->defOrdreTri($url_sOrdreTri);

switch ($url_iIdStatut)
{
	case STATUT_PERS_RESPONSABLE:
		$aoPersonnes = $oPersonnes->retListeResponsables($url_iIdForm);
		break;
		
	case STATUT_PERS_CONCEPTEUR:
		$aoPersonnes = $oPersonnes->retListeConcepteurs($url_iIdForm);
		break;
		
	case STATUT_PERS_TUTEUR:
		$aoPersonnes = $oPersonnes->retListeTuteurs($url_iIdForm);
		break;
		
	case STATUT_PERS_ETUDIANT:
		$aoPersonnes = $oPersonnes->retListeEtudiants($url_iIdForm);
		break;
		
	default:
		$aoPersonnes = $oPersonnes->retListePersonnes($url_iIdForm);
}

$oTpl = new Template("exporter-personnes.tpl");

$oTpl->remplacer("{form->id_form}",$url_iIdForm);
$oTpl->remplacer("{form->id_statut}",$url_iIdStatut);

// R�cup�rer les ic�nes de tri croissant/d�croissant
$oSet_icone_asc  = $oTpl->defVariable("SET_IMAGE_TRI_ASC");
$oSet_icone_desc = $oTpl->defVariable("SET_IMAGE_TRI_DESC");

// Placer l'ic�ne de tri
$asTrier = array("nom","prenom","pseudo");

foreach ($asTrier as $sTrier)
{
	$sOrdreTri = $oPersonnes->ORDRE_TRI_CROISSANT;
	$sImageTri = NULL;
	
	if ($url_sTri == $sTrier)
	{
		if ($url_sOrdreTri == $oPersonnes->ORDRE_TRI_CROISSANT)
		{
			$sOrdreTri = $oPersonnes->ORDRE_TRI_DESCROISSANT;
			$sImageTri = $oSet_icone_asc;
		}
		else
		{
			$sImageTri = $oSet_icone_desc;
		}
	}
	
	$oTpl->remplacer("{{$sTrier}->tri->ordre}",$sOrdreTri);
	$oTpl->remplacer("{{$sTrier}->image->tri}",$sImageTri);
}

// Liste des personnes
$oBloc_personne = new TPL_Block("BLOCK_PERSONNE",$oTpl);

if (is_array($aoPersonnes))
{
	$oBloc_personne->beginLoop();
	
	$sCelluleCss = NULL;
	
	$lettre = 96;
	$iPositionPersonne = 1;
	
	foreach ($aoPersonnes as $oPersonne)
	{
		$sCelluleCss            = ($sCelluleCss == "cellule_clair" ? "cellule_fonce" : "cellule_clair");
		$sCelluleCssTrierNom    = ($oPersonnes->sTrier == $oPersonnes->TRIER_NOM ? "cellule_clair_fonce" : $sCelluleCss);
		$sCelluleCssTrierPrenom = ($oPersonnes->sTrier == $oPersonnes->TRIER_PRENOM ? "cellule_clair_fonce" : $sCelluleCss);
		$sCelluleCssTrierPseudo = ($oPersonnes->sTrier == $oPersonnes->TRIER_PSEUDO ? "cellule_clair_fonce" : $sCelluleCss);
		
		$lien = NULL;
		$sPremiereLettre = strtolower(substr($oPersonne->retNom(),0,1));
		
		if ($lettre < ord($sPremiereLettre))
		{
			$lettre = $sPremiereLettre;
			$lien = "<a name=\"{$lettre}\"></a>";
		}
		
		$oBloc_personne->nextLoop();
		
		$oBloc_personne->remplacer("{id->lettre}",$lettre.$iPositionPersonne);
		$oBloc_personne->remplacer("{personne->position}",$iPositionPersonne++);
		
		$oBloc_personne->remplacer("{td->personne->class}",$sCelluleCss);
		$oBloc_personne->remplacer("{personne->id}",$oPersonne->retId());
		
		$oBloc_personne->remplacer("{td->nom->class}",$sCelluleCssTrierNom);
		$oBloc_personne->remplacer("{personne->nom}",$oPersonne->retNom().$lien);
		
		$oBloc_personne->remplacer("{td->prenom->class}",$sCelluleCssTrierPrenom);
		$oBloc_personne->remplacer("{personne->prenom}",$oPersonne->retPrenom());
		
		$oBloc_personne->remplacer("{td->pseudo->class}",$sCelluleCssTrierPseudo);
		$oBloc_personne->remplacer("{personne->pseudo}",$oPersonne->retPseudo());
	}
	
	$oBloc_personne->afficher();
}
else
	$oBloc_personne->effacer();

$oTpl->afficher();

$oProjet->terminer();

?>

