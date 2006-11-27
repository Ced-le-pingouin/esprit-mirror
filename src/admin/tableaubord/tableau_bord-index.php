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
 * @file	tableau_bord-index.php
 * 
 * @date	2006/11/27
 * 
 * @author	Filippo PORCO
 */

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdNiveau   = (empty($_GET["idNiveau"]) ? $oProjet->oFormationCourante->retId() : $_GET["idNiveau"]);
$url_iTypeNiveau = (empty($_GET["typeNiveau"]) ? TYPE_FORMATION : $_GET["typeNiveau"]);
$url_iIdType     = (empty($_GET["idType"]) ? 0 : $_GET["idType"]);
$url_iIdModalite = (empty($_GET["idModal"]) ? NULL : $_GET["idModal"]);

$sParamsUrl = NULL;

foreach ($_GET as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

if (empty($sParamsUrl))
	$sParamsUrl = "?idNiveau={$url_iIdNiveau}"
		."&typeNiveau={$url_iTypeNiveau}"
		."&idModal={$url_iIdModalite}";

// ---------------------
// Initialiser
// ---------------------
$g_iIdStatutUtilisateur = $oProjet->retStatutUtilisateur();

$oIds = new CIds($oProjet->oBdd,$url_iTypeNiveau,$url_iIdNiveau);

$oFormation = new CFormation($oProjet->oBdd,$oIds->retIdForm());

$sTitrePrincipal = "Tableau de bord";
$sSousTitre = $oFormation->retNom();

unset($oFormation);

// {{{ Insérer ces lignes dans l'en-tête de la page html
$sBlockHtmlHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function oFiltre() { return top.frames["Filtre"]; }
function oPrincipale() { return top.frames["Principale"]; }
function rafraichir() { oFiltre().document.forms[0].submit(); }
//-->
</script>
BLOCK_HTML_HEAD;
// }}}

// {{{ Frame principale
if (empty($url_iIdModalite)
	&& (STATUT_PERS_TUTEUR == $g_iIdStatutUtilisateur
		|| STATUT_PERS_CONCEPTEUR == $g_iIdStatutUtilisateur
		|| STATUT_PERS_RESPONSABLE == $g_iIdStatutUtilisateur
		|| STATUT_PERS_ADMIN == $g_iIdStatutUtilisateur))
	$iHauteurFrameFiltre = 50;
else
	$iHauteurFrameFiltre = 1;

$sFramePrincipale = <<<BLOCK_FRAME_PRINCIPALE
<frameset rows="{$iHauteurFrameFiltre}px,*" frameborder="0" border="0">
<frame name="Filtre" src="tableau_bord-filtre.php{$sParamsUrl}" frameborder="0" marginwidth="5" marginheight="5" scrolling="no" noresize="noresize">
<frame name="Principale" src="" frameborder="0" marginwidth="5" marginheight="5" scrolling="auto" noresize="noresize">
</frameset>
BLOCK_FRAME_PRINCIPALE;
// }}}

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("dialog-index.tpl",FALSE,TRUE));

$oBlockHtmlHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHtmlHead->ajouter($sBlockHtmlHead);
$oBlockHtmlHead->afficher();

$oTpl->remplacer("{titre_page_html}",emb_htmlentities($sTitrePrincipal));
$oTpl->remplacer("{frame_src_haut}","tableau_bord-titre.php?tp=".rawurlencode($sTitrePrincipal)."&st=".rawurlencode($sSousTitre));
$oTpl->remplacer("{frame_principal}",$sFramePrincipale);
$oTpl->remplacer("{frame_src_bas}","tableau_bord-menu.php");

$oTpl->afficher();

$oProjet->terminer();

?>

