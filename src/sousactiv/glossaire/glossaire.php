<?php

/*
** Fichier ................: glossaire.php
** Description ............:
** Date de création .......: 25/07/2004
** Dernière modification ..: 27/07/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
**
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Initialiser
// ---------------------
$oProjet->initSousActivCourante();

// Initialiser le glossaire
// ainsi que les éléments attachés à ce glossaire
$oProjet->oSousActivCourante->initGlossaire(TRUE);

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("glossaire/glossaire.tpl",FALSE,TRUE));
$oBloc_GlossaireElems = new TPL_Block("BLOCK_GLOSSAIRE_ELEMENTS",$oTpl);

if (is_object($oProjet->oSousActivCourante->oGlossaire))
	$sTitreGlossaire = htmlentities($oProjet->oSousActivCourante->oGlossaire->retTitre());
else
	$sTitreGlossaire = "&nbsp;";

// ---------------------
// Glossaire
// ---------------------
$oTpl->remplacer("{glossaire->titre}",$sTitreGlossaire);

// ---------------------
// Eléments du glossaire
// ---------------------
$oSet_GlossaireElem = $oTpl->defVariable("SET_GLOSSAIRE_ELEMENT");
$oSet_GlossaireSansElems = $oTpl->defVariable("SET_GLOSSAIRE_SANS_ELEMENTS");

if (is_object($oProjet->oSousActivCourante->oGlossaire))
{
	foreach ($oProjet->oSousActivCourante->oGlossaire->aoElements as $oGlossaireElem)
	{
		$oBloc_GlossaireElems->ajouter($oSet_GlossaireElem);
		
		$oBloc_GlossaireElems->remplacer("{glossaire->element->titre}",$oGlossaireElem->retTitre());
		$oBloc_GlossaireElems->remplacer("{glossaire->element->texte}",$oGlossaireElem->retTexte());
	}
}
else
{
	$oBloc_GlossaireElems->ajouter($oSet_GlossaireSansElems);
}

$oBloc_GlossaireElems->afficher();

$oTpl->afficher();

$oProjet->terminer();

?>