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
** Fichier ................: accueil.php
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
			if (empty($_POST['delete'])) {
				$oAccueil->setLien($_POST['texte'],$_POST['lien'],$_POST['typeLien'],1,$_POST['ordre'],$_POST['id']);
			} else {
				$oAccueil->deleteItem($_POST['id']);
			}
			break;
		case "breves" :
			if (isset($_POST['hideBreve'])) {
				$oAccueil->toggleVisible($_POST['hideBreve']);
			} else if (isset($_POST['deleteBreve'])) {
				$oAccueil->deleteItem($_POST['deleteBreve']);
			} else if (isset($_POST['editBreve']) && empty($_POST['retour'])) {
				$oAccueil->setBreve($_POST['brevesEditeur'],$_POST['dateDeb'],$_POST['dateFin'],1,$_POST['ordre'],$_POST['editBreve']);
			}
			break;
		case "titre" :
			$oAccueil->setTitre($_POST['titre'],$_POST['texte']);
			break;
	}
}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("accueil.tpl");

if (empty($_REQUEST['onglet'])) {
	$oTpl->remplacer("{onglet}",'avertissement');
	$_REQUEST['onglet'] = 'avertissement';
} else {
	$oTpl->remplacer("{onglet}",$_REQUEST['onglet']);
}

// Titres (brèves & liens)
$titres = $oAccueil->getTitres();
$oTpl->remplacer('{breves->titre}',$titres['breves']);
$oTpl->remplacer('{liens->titre}',$titres['liens']);

// Avertissement
$oBlock = new TPL_Block("BLOCK_AVERTISSEMENT",$oTpl);
if ($_REQUEST['onglet']==='avertissement') {
	insertEditor($oBlock,"avertissement",$oAccueil->getAvert());
} else {
	$oBlock->effacer();
}
$oBlock->afficher();

// Texte d'accueil
$oBlock = new TPL_Block("BLOCK_TEXTEACCUEIL",$oTpl);
if ($_REQUEST['onglet']==='texteAccueil') {
	insertEditor($oBlock,"texteAccueil",$oAccueil->getTexte(),78,15);
} else {
	$oBlock->effacer();
}
$oBlock->afficher();

// Liens
$oBlock = new TPL_Block("BLOCK_LIENS",$oTpl);
if ($_REQUEST['onglet']==='liens') {
	if (($aoLiens = $oAccueil->getLiens()) == NULL)
		 $aoLiens = array();
	$oBlock2 = new TPL_Block("BLOCK_LOOP_LIENS",$oBlock);
	$oBlock2->beginLoop();
	foreach ($aoLiens as $lien) {
		$oBlock2->nextLoop();
		$oBlock2->remplacer("{lien_id}",emb_htmlentities($lien->Id));
		$oBlock2->remplacer("{lien_text}",emb_htmlentities($lien->Texte));
		$oBlock2->remplacer("{lien_lien}",emb_htmlentities($lien->Lien));
		$oBlock2->remplacer("{sel_".$lien->TypeLien."}",' selected="1"');
		$oBlock2->remplacer("{sel_actuelle}",'');
		$oBlock2->remplacer("{sel_nouvelle}",'');
		$oBlock2->remplacer("{sel_popup}",'');
		$oBlock2->remplacer("{sel_inactif}",'');
		$iNumLiens = $oAccueil->getNumByType('lien');
		$ordre = '<select name="ordre"><option value="NULL">Défaut</option>';
		for ($i=1; $i<=$iNumLiens; $i++) {
			$ordre .= "<option".($lien->Ordre==$i?' selected':'').">$i</option>";
		}
		$ordre .= '</select>';
		$oBlock2->remplacer("{lien_position}",$ordre);
		$oBlock2->remplacer("{lien_positionTotal}",$iNumLiens);
	}
	$oBlock2->afficher();
	$ordre = '<select name="ordre">';
	for ($i=1; $i<=$iNumLiens; $i++) {
		$ordre .= "<option>$i</option>";
	}
	$ordre .= '<option selected="1">'.($iNumLiens+1).'</option></select>';
	$oBlock->remplacer("{lien_position}",$ordre);
} else {
	$oBlock->effacer();
}
$oBlock->afficher();

// Brèves
$oBlock = new TPL_Block("BLOCK_BREVES",$oTpl);
if ($_REQUEST['onglet']==='breves') {
	$oBlock1 = new TPL_Block("BLOCK_LOOP_BREVES",$oBlock);
	$oBlock2 = new TPL_Block("BLOCK_EDIT_BREVE",$oBlock);
	$oBlock_titre = new TPL_Block("BLOCK_BREVES_TITRE",$oBlock);
	if (empty($_REQUEST['selectBreve'])) {
		$oBlock2->effacer();
		$oBlock_titre->afficher();
		$oBlock1->beginLoop();
		foreach ($oAccueil->getBreves() as $breve) {
			$oBlock1->nextLoop();
			$oBlock1->remplacer("{breve_id}",emb_htmlentities($breve->Id));
			$oBlock1->remplacer("{texteDebut}",
								emb_htmlentities(mb_substr($breve->Texte,0,50))
								.(mb_strlen($breve->Texte)>50?'...':''));
			if (!$breve->Visible) {
				$oBlock1->remplacer('Masquer</button>','Montrer</button>');
			}
		}
		$oBlock1->ajouter('<li><span><em>Nouvelle entrée</em></span>
  <button name="selectBreve" value="-1" type="submit">Editer</button></li>');
	} else {
		$oBlock1->effacer();
		$oBlock_titre->effacer();
		if ($_REQUEST['selectBreve']=="-1") {
			$breve->DateDeb = date("Y-m-d");
			$breve->DateFin = date("Y-m-d", mktime(0,0,0,date("m")+1,date("d"),date("Y")) );
			$breve->Texte="";
		}
		$breve = $oAccueil->getItem($_REQUEST['selectBreve']);
		$oBlock2->remplacer("{breve_id}",$breve->Id);
		$oBlock2->remplacer("{breve_dateDeb}",emb_htmlentities($breve->DateDeb));
		$oBlock2->remplacer("{breve_dateFin}",emb_htmlentities($breve->DateFin));
		$iNumBreves = $oAccueil->getNumByType('breve');
		$ordre = '<select name="ordre"><option value="NULL">Défaut</option>';
		for ($i=1; $i<=$iNumBreves+1; $i++) {
			$ordre .= "<option".($breve->Ordre==$i?' selected':'').">$i</option>";
		}
		$ordre .= '</select>';
		$oBlock2->remplacer("{breve_position}",$ordre);
		$oBlock2->remplacer("{breve_positionTotal}",$iNumBreves);
		insertEditor($oBlock2,"breves",$breve->Texte,80,10);
	}
	$oBlock1->afficher();
	$oBlock2->afficher();
} else {
	$oBlock->effacer();
}
$oBlock->afficher();


// Compléments
$oTpl->remplacer("icones://",dir_icones());
$oTpl->remplacer("editeur://",dir_admin("commun"));
$oTpl->remplacer("{self}",$_SERVER['PHP_SELF']);

$oTpl->afficher();

$oProjet->terminer();


function insertEditor( &$template, $theme, $content="", $largeur=80, $hauteur=13 ) {
	$sSetEditeur = '<textarea name="'.$theme.'Editeur" class="editeur_texte" onchange="changed('."'$theme')".'" onkeypress="blur();focus();" '."cols=\"$largeur\" rows=\"$hauteur\" >".$content.'</textarea>';
	$template->remplacer('{'.$theme.'Editeur}',$sSetEditeur);
}
?>

