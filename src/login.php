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

/**
 * @file	copie_activ.php
 * 
 * Copie une activité d'une rubrique vers une autre
 * 
 * @date	2005/07/12
 * 
 * @author	Filippo PORCO <filippo.porco@umh.ac.be>
 * @author	Jérôme TOUZE
 */

require_once("globals.inc.php");
include_once(dir_database("accueil.tbl.php"));

define('MAX_BREVES',4);

$oProjet = new CProjet();
$oAccueil = new CAccueil($oProjet->oBdd);
// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iCodeEtat = (empty($_GET["codeEtat"]) ? 0 : $_GET["codeEtat"]);

// ---------------------
// Template
// ---------------------
if (isset($_REQUEST['breves'])) { // popup des brèves
	$oTpl = new Template(dir_theme("login/breves.tpl",FALSE,TRUE));
	$titres = $oAccueil->getTitres();
	$oTpl->remplacer('{breves->titre}',$titres['breves']);
	$oBlocBreve = new TPL_Block("BLOCK_BREVE",$oTpl);
	$oBlocBreve->beginLoop();
	$breves = $oAccueil->getBreves(TRUE,FALSE); // breves(visibles,date)
	if ($breves) {
		foreach ($breves as $breve) {
			$oBlocBreve->nextLoop();
			$oBlocBreve->remplacer("{breve->info}",convertBaliseMetaVersHtml($breve->Texte));
		}
		$oBlocBreve->afficher(); 
	} else {
		$oBlocBreve->effacer();
	}
	$oTpl->afficher();
	exit(0);
}

$oTpl = new Template(dir_theme("login/login.tpl",FALSE,TRUE));
$oBlocErreurLogin = new TPL_Block("BLOCK_ERREUR_LOGIN",$oTpl);
$oBlocAvertissementLogin = new TPL_Block("BLOCK_AVERTISSEMENT_LOGIN",$oTpl);
$oBlocInfosPlateforme = new TPL_Block("BLOCK_INFOS_PLATEFORME",$oTpl);
$oBlocListeFormations = new TPL_Block("BLOCK_LISTE_FORMATIONS",$oBlocInfosPlateforme);
$oBlocFormation       = new TPL_Block("BLOCK_FORMATION",$oBlocListeFormations);

//  {{{ Afficher un message d'erreur lorsque le pseudo ou le mot de passe de
//      l'utilisateur est incorrect
if ($url_iCodeEtat > 0)
	$oBlocErreurLogin->afficher();
else
	$oBlocErreurLogin->effacer();
// }}}

// {{{ Le logo local (s'il existe)
$logos = glob(dir_theme('login/images/logo_local.*',FALSE,TRUE));
foreach ($logos as $tmplogo) {
	error_log($tmplogo);
	if (preg_match('/\.(png|gif|jpg)$/i',$tmplogo,$ext) && is_readable($tmplogo)) {
		$ext = $ext[1];
		break;
	} else {
		unset($ext);
	}
}
if (isset($ext)) {
	$oTpl->remplacer("{logo->local}",
	                 '<img id="logo-local" src="'.dir_theme('login/images/logo_local.',TRUE).$ext.'" border="0" alt="Logo local" />');
} else {
	$oTpl->remplacer("{logo->local}",'');
}
// }}}

// {{{ Afficher un message d'avertissement
$oBlocAvertissementLogin->beginLoop();
$avert = $oAccueil->getAvert($Visible=1);
if ($avert) {
	$oBlocAvertissementLogin->nextLoop();
	$oBlocAvertissementLogin->remplacer("{login.avertissement}",convertBaliseMetaVersHtml($avert));
	$oBlocAvertissementLogin->afficher();
}
else $oBlocAvertissementLogin->effacer();
// }}}

// {{{ Formulaire
$oTpl->remplacer(array("{form}","{/form}"),
                 array("<form name=\"formulId\" action=\"index2.php\" method=\"post\" target=\"_top\">","</form>"));
// }}}

// {{{ Permet d'afficher uniquement les formations accessibles qu'aux visiteurs
$oProjet->oUtilisateur = NULL;
$oProjet->asInfosSession[SESSION_FORM] = 0;
// }}}
$sRepHttpPlateforme = dir_http_plateform();

if ($oProjet->initFormationsUtilisateur() > 0)
{
	$oBlocFormation->beginLoop();
	foreach ($oProjet->aoFormations as $oFormation)
	{
		$sUrl = "<a href='{$sRepHttpPlateforme}index2.php?idForm=".$oFormation->retId()."' target='_top'>".$oFormation->retNom()."</a>";
		$oBlocFormation->nextLoop();
		$oBlocFormation->remplacer("{formation->url}",$sUrl);
	}
	$oBlocFormation->afficher();
	$oBlocListeFormations->afficher();
}
else
{
	$oBlocListeFormations->effacer();     
}
$oBlocInfosPlateforme->afficher();

// ajouts des textes de présentation
$oBlocTexte = new TPL_Block("BLOCK_TEXTE",$oTpl);
$oBlocTexte->beginLoop();
$Texte = $oAccueil->getTexte($Visible=1);
if ($Texte) {
	$oBlocTexte->nextLoop();
	$oBlocTexte->remplacer("{texte->info}",convertBaliseMetaVersHtml($Texte));
	$oBlocTexte->afficher();
} 
else $oBlocTexte->effacer();


// ajout des breves
$titres = $oAccueil->getTitres();
$oTpl->remplacer('{breves->titre}',$titres['breves']);
$oBlocBreve = new TPL_Block("BLOCK_BREVE",$oTpl);
$oBlocBreve->beginLoop();
$breves = $oAccueil->getBreves($Visible=1, $Date=1);
if ($breves) {
	$numBreve = 0;
	foreach ($breves as $breve) {
		if (++$numBreve > MAX_BREVES) break;
		$oBlocBreve->nextLoop();
		$oBlocBreve->remplacer("{breve->info}",convertBaliseMetaVersHtml($breve->Texte));
	}
	$oBlocBreve->ajouter('<a href="javascript: void(0);" onclick="window.open('
	                     ."'login.php?breves=all','Toutes les breves','width=400,height=500,menubar=no,statusbar=no,resizable=yes,scrollbars=yes'"
	                     .')" class="breve-centered">Toutes les brèves...</a>');
	$oBlocBreve->afficher(); 
} else {
	$oBlocBreve->effacer();
}


// ajout des liens
$oTpl->remplacer('{liens->titre}',$titres['liens']);
$oBlocLien = new TPL_Block("BLOCK_LIEN",$oTpl);
$oBlocLien->beginLoop();
$liens = $oAccueil->getLiens($Visible=1);
if($liens){
	foreach ($liens as $lien) {
		$oBlocLien->nextLoop();
		if($lien->TypeLien!="inactif"){
			switch($lien->TypeLien){
				case "actuelle":
					$target=$lien->Lien.'"';
					break;
				case "nouvelle":
					$target=$lien->Lien.'" target="_blank"';
					break;
				case "popup":
					$target='javascript:void(0)" onClick="window.open(\''.$lien->Lien."','popup','width=500,height=500');return false;\"";
					break;
			}
			$sInfo = '<a href="'.$target.'>'.$lien->Texte."</a>"; 
		}
		else $sInfo = $lien->Texte;           
		$oBlocLien->remplacer("{lien->info}",$sInfo);
	}
	$oBlocLien->afficher();
}
else $oBlocLien->effacer();


$oTpl->afficher();

?>
