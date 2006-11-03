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
** Fichier ................: tableau_bord-index.php
** Description ............:
** Date de création .......: 23/06/2005
** Dernière modification ..: 09/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("tableau_bord.lang"));

$oProjet = new CProjet();

$g_iIdStatutUtilisateur = $oProjet->retStatutUtilisateur();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForm     = (empty($_GET["form"]) ? $oProjet->oFormationCourante->retId() : $_GET["form"]);
$url_iIdModalite = (empty($_GET["idModal"]) ? NULL : $_GET["idModal"]); // !!! Laisser NULL car 0 = chat public et 1 = chat par équipe

$sParamsUrl = "?form={$url_iIdForm}";

foreach ($_GET as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialiser
// ---------------------
$oFormation = new CFormation($oProjet->oBdd,$url_iIdForm);

$sTitrePrincipal = TITRE;
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
$iHauteurFrameFiltre = (STATUT_PERS_ETUDIANT > $g_iIdStatutUtilisateur && empty($url_iIdModalite) ? 50 : 1);

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

$oTpl->remplacer("{titre_page_html}",mb_convert_encoding($sTitrePrincipal,"HTML-ENTITIES","UTF-8"));
$oTpl->remplacer("{frame_src_haut}","tableau_bord-titre.php?tp=".rawurlencode($sTitrePrincipal)."&st=".rawurlencode($sSousTitre));
$oTpl->remplacer("{frame_principal}",$sFramePrincipale);
$oTpl->remplacer("{frame_src_bas}","tableau_bord-menu.php");

$oTpl->afficher();

$oProjet->terminer();

?>

