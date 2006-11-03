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
** Fichier ................: outils.php
** Description ............:
** Date de création .......: 24/04/2004
** Dernière modification ..: 28/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Ludovic FLAMME
**                           Cédric FLOQUET <cedric.floquet@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("outils.lang"));

$oProjet = new CProjet();
$oProjet->initStatutsUtilisateur(FALSE);
$oProjet->verifPeutUtiliserOutils();

// ---------------------
// Initialiser
// ---------------------
$iIdForm = (isset($oProjet->oFormationCourante) && is_object($oProjet->oFormationCourante) ? $oProjet->oFormationCourante->retId() : 0);

$sBlocJavascript = <<<BLOCK_JAVASCRIPT
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
BLOCK_JAVASCRIPT;

// {{{ Liste des outils
$aaOutils = array(
	array($oProjet->retNom(),"logo-".mb_strtolower($oProjet->retNom(),"UTF-8").".gif","informations()",TXT_PLATEFORME_DESCRIPTION,TRUE)
	, array(TXT_ECONCEPT_TITRE,"conception.gif","eConcept()",TXT_ECONCEPT_DESCRIPTION,$oProjet->verifPermission("PERM_OUTIL_ECONCEPT"))
	, array(TXT_FORMULAIRE_TITRE,"formulaire.gif","formulaire()",TXT_FORMULAIRE_DESCRIPTION,$oProjet->verifPermission("PERM_OUTIL_FORMULAIRE"))
	, array(TXT_TRANSFERT_FORMATIONS_TITRE,"transfert_form.gif","transfert_form()",TXT_TRANSFERT_FORMATIONS_DESCRIPTION,$oProjet->verifPermission("PERM_OUTIL_CORBEILLE"))
	, array(TXT_CORBEILLE_TITRE,"corbeille.gif","corbeille()",TXT_CORBEILLE_DESCRIPTION,$oProjet->verifPermission("PERM_OUTIL_CORBEILLE"))
	, array(TXT_CONSOLE_TITRE,"console.gif","console()",TXT_CONSOLE_DESCRIPTION,$oProjet->verifPermission("PERM_OUTIL_CONSOLE"))
	, array(TXT_PERMISSIONS_TITRE,"permission.gif","permissions()",TXT_PERMISSIONS_DESCRIPTION,$oProjet->verifPermission("PERM_OUTIL_PERMISSION"))
	, array(TXT_INSCRIPTION_TITRE,"inscription.gif","gestion_utilisateur('{$iIdForm}')",TXT_INSCRIPTION_DESCRIPTION,$oProjet->verifPermission("PERM_OUTIL_INSCRIPTION"))
	, array(TXT_EQUIPES_TITRE,"equipe.gif","equipes()",TXT_EQUIPES_DESCRIPTION,$oProjet->verifPermission("PERM_OUTIL_EQUIPE"))
	, array(TXT_STATUTS_TITRE,"fichier_statuts.gif","ouvrir_fich_statut()",TXT_STATUTS_DESCRIPTION,$oProjet->verifPermission("PERM_OUTIL_STATUT"))
	, array(TXT_EXPORTER_INSCRITS_TITRE,"export_liste_personnes.gif","exporter_liste_personnes()",TXT_STATUTS_DESCRIPTION,$oProjet->verifPermission("PERM_OUTIL_EXPORT_TABLE_PERSONNE"))
	, array(TXT_ENVOI_COURRIEL_TITRE,"courriel_envoye.gif","choix_courriel('?idPers=tous')",TXT_ENVOI_COURRIEL_DESCRIPTION,$oProjet->verifPermission("PERM_OUTIL_ENVOI_COURRIEL"))
	, array(TXT_DOSSIERS_TITRE,"dossier_formations.gif","dossiers()",TXT_DOSSIERS_DESCRIPTION,$oProjet->verifPermission("PERM_CLASSER_FORMATIONS"))
	, array(TXT_AVERTISSEMENT_LOGIN_TITRE,"avertissement_login.gif","avertissement_login()",TXT_AVERTISSEMENT_LOGIN_DESCRIPTION,(STATUT_PERS_ADMIN == $oProjet->retStatutUtilisateur()))
);
// }}}

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("outils.tpl",FALSE,TRUE));

$oBlocJavascript = new TPL_Block("BLOCK_JAVASCRIPT",$oTpl);

$oBlocJavascript->ajouter($sBlocJavascript);
$oBlocJavascript->afficher();

$oBlocOutil = new TPL_Block("BLOCK_OUTIL",$oTpl);

$oBlocOutil->beginLoop();

$oBloc_style = NULL;

for ($i=0; $i<count($aaOutils); $i++)
{
	if (!$aaOutils[$i][4])
		continue;
	
	$oBloc_style = ("outil_fond_clair" == $oBloc_style
		? "outil_fond_fonce"
		: "outil_fond_clair");
	
	$oBlocOutil->nextLoop();
	
	$oBlocOutil->remplacer("{outil.style}",$oBloc_style);
	$oBlocOutil->remplacer("{outil.nom}",mb_convert_encoding($aaOutils[$i][0],"HTML-ENTITIES","UTF-8"));
	$oBlocOutil->remplacer("{outil.icone}",$aaOutils[$i][1]);
	$oBlocOutil->remplacer("{outil.lien}",$aaOutils[$i][2]);
	$oBlocOutil->remplacer("{outil.description}",mb_convert_encoding($aaOutils[$i][3],"HTML-ENTITIES","UTF-8"));
}

$oBlocOutil->afficher();

$oTpl->afficher();

$oProjet->terminer();

?>
