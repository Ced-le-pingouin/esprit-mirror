<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

/*
** Fichier ................: avertissement.php
** Description ............:
** Date de création .......: 08/07/2005
** Dernière modification ..: 12/07/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
include_once(dir_database("accueil.tbl.php"));

// ---------------------
// Données
// ---------------------
$oProjet = new CProjet();
$oAccueil = new CAccueil($oProjet->oBdd);

// ---------------------
// Appliquer les changements
// ---------------------
if (!empty($_POST['modifier']))
{
	switch ($_POST['modifier']) {
		case "avertissement" : 
			$oAccueil->setAvert($_POST['avertissementEditeur']);
			break;
		case "texteAccueil" :
			$oAccueil->setTexte($_POST['texteAccueilEditeur']);
			break;
		case "liens" :
			$oAccueil->setLien($_POST['texte'],$_POST['lien'],$_POST['typeLien'],1,'NULL',$_POST['id']);
			break;
	}
	/*
	exit("<html>\n"
		 ."<head>\n"
		 ."<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n"
		 ."<script type=\"text/javascript\" language=\"javascript\">\n"
		 ."<!--\n"
		 ."function init() { top.close(); }\n"
		 ."//-->\n"
		 ."</script>\n"
		 ."</head>\n"
		 ."<body onload=\"init()\"></body>\n"
		 ."</html>\n"
		 );
	*/
}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("avertissement.tpl");

if (empty($_REQUEST['onglet'])) {
	$oTpl->remplacer("{onglet}",'avertissement');
} else {
	$oTpl->remplacer("{onglet}",$_REQUEST['onglet']);
}

insertEditor($oTpl,"avertissement",$oAccueil->getAvert());

insertEditor($oTpl,"texteAccueil",$oAccueil->getTexte());

$oBlock = new TPL_Block("BLOCK_LOOP_LIENS",$oTpl);
$oBlock->beginLoop();
foreach ($oAccueil->getLiens() as $lien) {
	$oBlock->nextLoop();
	$oBlock->remplacer("{lien_id}",emb_htmlentities($lien->Id));
	$oBlock->remplacer("{lien_text}",emb_htmlentities($lien->Texte));
	$oBlock->remplacer("{lien_lien}",emb_htmlentities($lien->Lien));
	$oBlock->remplacer("{sel_".$lien->TypeLien."}",' selected="1"');
	$oBlock->remplacer("{sel_frame}",'');
	$oBlock->remplacer("{sel_page}",'');
	$oBlock->remplacer("{sel_popup}",'');
	$oBlock->remplacer("{sel_inactif}",'');
}
$oBlock->afficher();

$oTpl->remplacer("icones://",dir_icones());
$oTpl->remplacer("editeur://",dir_admin("commun"));

$oTpl->afficher();

$oProjet->terminer();



function insertEditor( &$template, $id, $content="" ) {
	$oTplEditeur = new Template(dir_admin("commun","editeur.inc.tpl",TRUE));
	$oBlocTableauDeBord = new TPL_Block("BLOCK_TABLEAU_DE_BORD",$oTplEditeur);
	$oBlocTableauDeBord->effacer();
	$oTplEditeur->remplacer("{editeur->nom}",$id."Editeur");
	$oTplEditeur->remplacer("26","10"); // hauteur
	$oTplEditeur->remplacer("80","70"); // largeur
	$oTplEditeur->remplacer('class="editeur_texte"></textarea>',
							'class="editeur_texte" onchange="changed('."'$id')".'" onkeypress="blur();focus();">'.$content.'</textarea>');
	$sSetEditeur = $oTplEditeur->defVariable("SET_EDITEUR");
	$template->remplacer('{'.$id.'}',
						 '<form action="'.$_SERVER['PHP_SELF'].'" name="'.$id.'Form" method="post">'.
						 '<input type="hidden" name="modifier" value="'.$id.'" />'.
						 '<input type="hidden" name="onglet" value="'.$id.'" />'.
						 $sSetEditeur.
						 "</form>");

	/*
	$oTplVisualiseur = new Template(dir_admin("commun","editeur.tpl",TRUE));
	$sSetVisualiseur = $oTplVisualiseur->defVariable("SET_VISUALISEUR");

	// {{{ Editeur
	$oBlocEditeur = new TPL_Block("BLOCK_EDITEUR",$oTplVisualiseur);
	$oBlocEditeur->ajouter($sSetEditeur);
	$oBlocEditeur->afficher();
	// }}}

	// {{{ Visualiseur
	$oBlocVisualiseur = new TPL_Block("BLOCK_VISUALISATEUR",$oTplVisualiseur);
	$oBlocVisualiseur->effacer();
	// }}}
	*/

}
?>

