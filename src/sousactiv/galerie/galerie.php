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
** Fichier ................: galerie.php
** Description ............: 
** Date de création .......: 08/09/2005
** Dernière modification ..: 06/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

error_reporting(E_ALL);

require_once("globals.inc.php");
require_once(dir_locale("globals.lang"));
require_once(dir_database("galerie.tbl.php"));
require_once(dir_include("types_mime.inc.php"));

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Initialiser
// ---------------------
$oGalerie = new CGalerie($oProjet->oBdd,$oProjet->oSousActivCourante->retId());

$g_iIdPers = $oProjet->retIdUtilisateur();
$g_iNbRessources = 0;

$bFormationArchivee = FALSE;
// si la formation est archiv�e et que l'utilisateur n'a pas les droits de modification
if ($oProjet->oFormationCourante->retStatut()== STATUT_ARCHIVE)
{
	$bFormationArchivee = TRUE; 
}

// Rechercher les collecticiels ainsi que les ressources
$iNbCollecticiels = $oGalerie->initCollecticiels(TRUE);

// ---------------------
// Template global
// ---------------------
$oTplGlobal = new Template(dir_theme("globals.inc.tpl",FALSE,TRUE));

$asSetTplGlobal = array(NULL
	, "F" => $oTplGlobal->defVariable("SET_SEXE_FEMININ")
	, "M" => $oTplGlobal->defVariable("SET_SEXE_MASCULIN")
	, "EQUIPE" => $oTplGlobal->defVariable("SET_EQUIPE")
	, "ENVOI_COURRIEL" => $oTplGlobal->defVariable("SET_ENVOI_COURRIEL")
	, "ENVOI_COURRIEL_ICONE" => $oTplGlobal->defVariable("SET_ENVOI_COURRIEL_ICONE")
	// , "ENVOI_COURRIEL_ICONE_PASSIVE" => $oTplGlobal->defVariable("SET_ENVOI_COURRIEL_ICONE_PASSIVE")
	, "ENVOI_COURRIEL_TEXTE" => $oTplGlobal->defVariable("SET_ENVOI_COURRIEL_TEXTE")
);

unset($oTplGlobal);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("galerie.tpl");

// {{{ Barre des outils
$asRechBarreOutils = array(
	"{barre_outils.galerie}"
);

$oBlocBarreOutils = new TPL_Block("BLOCK_BARRE_OUTILS",$oTpl);

$sOutil = $oBlocBarreOutils->defVariable("SET_OUTIL_COMPOSER_GALERIE");

$asReplBarreOutils = array(
	(($oProjet->verifPermission("PERM_COMPOSER_GALERIE") && !$bFormationArchivee) ? $sOutil : NULL)
);

$oBlocBarreOutils->remplacer($asRechBarreOutils,$asReplBarreOutils);

$oBlocBarreOutils->afficher();

unset($sOutil);
// }}}

// {{{ Consigne
$oBlocConsigne = new TPL_Block("BLOCK_CONSIGNE",$oTpl);

$sConsigne = $oProjet->oSousActivCourante->retDescr();

if (empty($sConsigne))
	$oBlocConsigne->effacer();
else
{
	$oBlocConsigne->remplacer("{consigne}",convertBaliseMetaVersHtml($sConsigne));
	$oBlocConsigne->afficher();
}

unset($sConsigne, $oBlocConsigne);
// }}}

// {{{ Liste des documents
$oBlocGalerie       = new TPL_Block("BLOCK_GALERIE",$oTpl);
$oBlocAucunDocument = new TPL_Block("BLOCK_AUCUN_DOCUMENT",$oTpl);

if ($iNbCollecticiels > 0)
{
	$asRechTpl = array(
		"{document.icone}"
		, "{document.nom}"
		, "{personne.sexe}"
		, "{personne.nom_complet}"
		, "{document.href}"
		, "{envoi_courriel}"
		, "{envoi_courriel.icone}"
		, "{envoi_courriel.texte}"
		, "{envoi_courriel.params}"
	);
	
	$oBlocGalerie->beginLoop();
	
	foreach ($oGalerie->aoCollecticiels as $oCollecticiel)
	{
		if (count($oCollecticiel->aoRessources) < 1)
			continue;
		
		$bCollecticielParEquipe = (MODALITE_PAR_EQUIPE == $oCollecticiel->retModalite(TRUE));
		
		$oBlocGalerie->nextLoop();
		
		$oBlocGalerie->remplacer("{collecticiel.nom}",emb_htmlentities($oCollecticiel->retNom()));
		
		$oBlocDocument = new TPL_Block("BLOCK_DOCUMENT",$oBlocGalerie);
		$oBlocDocument->beginLoop();
		
		$sRepCollecticiel = dir_collecticiel($oProjet->oFormationCourante->retId(),$oCollecticiel->retIdParent());
		
		foreach ($oCollecticiel->aoRessources as $oRessource)
		{
			$asExpediteurEquipe = array();
			
			if ($bCollecticielParEquipe)
			{
				$oRessource->initEquipe();
				$asExpediteurEquipe["icone"]    = $asSetTplGlobal["EQUIPE"];
				$asExpediteurEquipe["nom"]      = $oRessource->oEquipe->retNom();
				$asExpediteurEquipe["courriel"] = "idEquipes=".$oRessource->oEquipe->retId();
			}
			else
			{
				$oRessource->initExpediteur();
				$asExpediteurEquipe["icone"]    = ("F" == $oRessource->oExpediteur->retSexe() ? $asSetTplGlobal["F"] : $asSetTplGlobal["M"]);
				$asExpediteurEquipe["nom"]      = $oRessource->oExpediteur->retNom()." ".$oRessource->oExpediteur->retPrenom();
				$asExpediteurEquipe["courriel"] = "idPers=".$oRessource->oExpediteur->retId();
			}
			
			// {{{ Associer une icône à ce document
			$asIcones = explode(".",$oRessource->retUrl());
			$sExtension = $asIcones[count($asIcones)-1];
			$sIcone = $asTypesMIME[(array_key_exists($sExtension,$asTypesMIME) ? $sExtension : "inconnu")];
			// }}}
			
			$asReplTpl = array(
				$sIcone
				, emb_htmlentities($oRessource->retNom())
				, $asExpediteurEquipe["icone"]
				, emb_htmlentities($asExpediteurEquipe["nom"])
				, rawurlencode($sRepCollecticiel.$oRessource->retUrl())
				  // {{{ Envoi courriel
				, $asSetTplGlobal["ENVOI_COURRIEL"]
				, $asSetTplGlobal["ENVOI_COURRIEL_ICONE"]
				, $asSetTplGlobal["ENVOI_COURRIEL_TEXTE"]
				, "?typeCourriel=courriel-galerie&idStatuts=".STATUT_PERS_TUTEUR."&".$asExpediteurEquipe["courriel"]."&select=1"
				  // }}}
			);
			
			$oBlocDocument->nextLoop();
			$oBlocDocument->remplacer($asRechTpl,$asReplTpl);
			
			$g_iNbRessources++;
		}
		
		$oBlocDocument->afficher();
	}
}

if ($g_iNbRessources > 0)
{
	$oBlocGalerie->afficher();
	$oBlocAucunDocument->effacer();
}
else
{
	$oBlocGalerie->effacer();
	$oBlocAucunDocument->afficher();
}
// }}}

$oTpl->remplacer("{sousactiv.id}",$oProjet->oSousActivCourante->retId());

// {{{ Traduction des termes
$asRechTpl = array(
	"[TXT_CLIQUER_ICI_POUR_TELECHARGER_DOCUMENT]"
);

$asReplTpl = array(
	emb_htmlentities(TXT_CLIQUER_ICI_POUR_TELECHARGER_DOCUMENT)
);

$oTpl->remplacer($asRechTpl,$asReplTpl);
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

