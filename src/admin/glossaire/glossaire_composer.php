<?php

/*
** Fichier ................: glossaire_composer.php
** Description ............:
** Date de création .......: 28/07/2004
** Dernière modification ..: 29/07/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdGlossaire = (empty($HTTP_GET_VARS["idGlossaire"]) ? 0 : $HTTP_GET_VARS["idGlossaire"]);

if (!empty($HTTP_POST_VARS["idGlossaire"]))
{
	$url_iIdGlossaire = $HTTP_POST_VARS["idGlossaire"];
	
	$oProjet->oFormationCourante->effacerElementsGlossaire($url_iIdGlossaire);
	
	if (is_array($HTTP_POST_VARS["idElementsGlossaire"]))
		$oProjet->oFormationCourante->ajouterElementsGlossaire($url_iIdGlossaire,$HTTP_POST_VARS["idElementsGlossaire"]);
}
// ---------------------
// Initialiser
// ---------------------
$oGlossaire = new CGlossaire($oProjet->oBdd,$url_iIdGlossaire);
$oProjet->oFormationCourante->initElementsGlossaire($url_iIdGlossaire);

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("glossaire/glossaire_composer.tpl",FALSE,TRUE));
$oTpl->remplacer("{glossaire->titre}",htmlentities($oGlossaire->retTitre()));

$oBloc_ElementsGlossaire = new TPL_Block("BLOCK_ELEMENTS_GLOSSAIRE",$oTpl);

$oSet_ElementGlossaire = $oTpl->defVariable("SET_ELEMENT_GLOSSAIRE");

if ($url_iIdGlossaire > 0)
		foreach ($oProjet->oFormationCourante->aoElementsGlossaire as $oElementGlossaire)
		{
			$oBloc_ElementsGlossaire->ajouter($oSet_ElementGlossaire);
			
			$oBloc_ElementsGlossaire->remplacer("{glossaire->element->selectionne}",($oElementGlossaire->estSelectionne() ? " checked" : NULL));
			$oBloc_ElementsGlossaire->remplacer("{glossaire->element->id}",$oElementGlossaire->retId());
			$oBloc_ElementsGlossaire->remplacer("{glossaire->element->titre}",$oElementGlossaire->retTitre());
			$oBloc_ElementsGlossaire->remplacer("{glossaire->element->texte}",$oElementGlossaire->retTexte());
		}

$oBloc_ElementsGlossaire->afficher();

$oTpl->remplacer("{glossaire->id}",$url_iIdGlossaire);

$oTpl->afficher();

$oProjet->terminer();

?>