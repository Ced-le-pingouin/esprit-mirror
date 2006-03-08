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
** Fichier ................: composer_galerie-index.php
** Description ............:
** Date de création .......: 12/09/2005
** Dernière modification ..: 06/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("galerie.lang"));

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdSA = (empty($HTTP_GET_VARS["idSA"]) ? NULL : $HTTP_GET_VARS["idSA"]);

$sParamsUrl = "?idSA={$url_iIdSA}";

// ---------------------
// Initialiser
// ---------------------
$sTitrePrincipal = TXT_COMPOSER_SA_GALERIE_TITRE;
$sSousTitre = $oProjet->oSousActivCourante->retNom();

// {{{ Insérer ces lignes dans l'en-tête de la page html
$sBlockHtmlHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function oPrincipale() { return top.frames["Principale"]; }
//-->
</script>
BLOCK_HTML_HEAD;
// }}}

// {{{ Frame principale
$sFramePrincipale = <<<BLOCK_FRAME_PRINCIPALE
<frameset rows="30px,*" frameborder="0" border="0">
<frame name="Filtre" src="composer_galerie-filtre.php{$sParamsUrl}" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
<frame name="Principale" src="composer_galerie.php{$sParamsUrl}" frameborder="0" marginwidth="10" marginheight="10" scrolling="auto" noresize="noresize">
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

$oTpl->remplacer("{titre_page_html}",htmlentities($sTitrePrincipal));
$oTpl->remplacer("{frame_src_haut}","composer_galerie-titre.php?tp=".rawurlencode($sTitrePrincipal)."&st=".rawurlencode($sSousTitre));
$oTpl->remplacer("{frame_principal}",$sFramePrincipale);
$oTpl->remplacer("{frame_src_bas}","composer_galerie-menu.php");

$oTpl->afficher();

$oProjet->terminer();

?>

