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
** Fichier ................: choix_courriel.php
** Description ............:
** Date de création .......: 17/01/2005
** Dernière modification ..: 12/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

$g_iIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

if ($g_iIdPers < 1)
	exit("<html><body></body></html>");

// ---------------------
// Inclure des fichiers
// ---------------------
include_once(dir_database("personnes.class.php"));

//----------------------
// Fonction affichant d'où est appelé le courriel
//----------------------
function verifierAppelCourriel($v_asTypeCourriel, $v_iNbPersonnes=2)
{
	global $g_Type;
	$g_Type = explode("-", $v_asTypeCourriel);
	switch($g_Type[1]){
		case "cours":
			$v_BlocModif =  "du ".$g_Type[1];
		break;
		case "module":
			$v_BlocModif = "du cours";
		break;
		case "formation":
			$v_BlocModif = "de la ".$g_Type[1];
		break;
		default:
			$v_BlocModif = "(".$g_Type[1].")";
		break;
	}
	return $v_BlocModif;
}

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_aiIdStatuts = (empty($_GET["idStatuts"])
	? NULL
	: explode("x",$_GET["idStatuts"]));

// Pour afficher toutes les équipes :
// > choix_courriel-liste.php?idEquipes=tous
// Pour afficher certaine équipe :
// > choix_courriel-liste.php?idEquipes=15&10&16&22
$url_aiIdEquipes = (empty($_GET["idEquipes"])
	? NULL
	: explode("x",$_GET["idEquipes"]));

$url_aiIdPers = (empty($_GET["idPers"])
	? NULL
	: explode("x",$_GET["idPers"]));

//$url_bSelectionnerPers = (empty($_GET["select"]) ? FALSE : $_GET["select"]);
$url_iPersonneId = (empty($_GET["idPers"]) ? NULL : $_GET["idPers"]);
$url_iEquipeId = (empty($_GET["selectEquipe"]) ? NULL : $_GET["selectEquipe"]);

$url_asTypeCourriel = (empty($_GET["typeCourriel"]) ? array("courriel-plateforme") : explode("@",$_GET["typeCourriel"]));

// si on se situe au niveau de la description de la formation
$url_iFormationId = (empty($_GET["idForm"]) ? NULL : $_GET["idForm"]);


// ---------------------
// Initialiser
// ---------------------
$iNbStatuts = (is_array($url_aiIdStatuts) ? count($url_aiIdStatuts) : 0);
$bStatutExiste = false;

// {{{ Rechercher les personnes inscrites dans des équipes
$iNbEquipes = 0;

if (is_array($url_aiIdEquipes))
{
	if ("tous" == $url_aiIdEquipes[0])
	{
		$iNbEquipes = $oProjet->initEquipes();
		$aoEquipes = $oProjet->aoEquipes;
	}
	else
	{
		$oEquipe = new CEquipe($oProjet->oBdd);
		$iNbEquipes = $oEquipe->initGraceIdEquipes($url_aiIdEquipes);
		$aoEquipes = $oEquipe->aoEquipes;
		unset($oEquipe);
	}
}
// }}}

// {{{ Rechercher les personnes inscrites à la plate-forme
$iNbPersonnes = 0;

if (is_array($url_aiIdPers))
{
	$oMembres = new CPersonnes($oProjet);
	$iNbPersonnes = $oMembres->initGraceIdPers($url_aiIdPers);
	$aoPersonnes = $oMembres->aoPersonnes;
	unset($oMembres);
}
// }}}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("choix_courriel-liste.tpl");

$sSetElementStatut   = $oTpl->defVariable("SET_ELEMENT_STATUT");
$sSetListeMembres    = $oTpl->defVariable("SET_LISTE_MEMBRES");
$sSetSeparateurTable = NULL; //$oTpl->defVariable("SET_SEPARATEUR_TABLE");

// ---------------------
// Liste des inscrits à la formation
// Si on se trouve dans la description de la formation,
// alors on affiche toutes les personnes inscrites à cette formation
// ---------------------
$oBlocListeStatutsFormation = new TPL_Block("BLOCK_LISTE_STATUTS_FORMATION",$oTpl);

if ($url_iFormationId > 0)
{
	$asListeStatuts = $oBlocListeStatutsFormation->defTableau("ARRAY_STATUTS_FORMATION");
	$v_aiIdStatutsForm = array(STATUT_PERS_TUTEUR, STATUT_PERS_ETUDIANT, STATUT_PERS_RESPONSABLE);
	
	$oBlocStatutFormation = new TPL_Block("BLOCK_STATUT_FORMATION",$oBlocListeStatutsFormation);
	$oBlocStatutFormation->remplacer("{liste_membres}",$sSetListeMembres);
	$sVarMembre = $oBlocStatutFormation->defVariable("VAR_MEMBRE");
	$oBlocStatutFormation->beginLoop();
	
	foreach ($v_aiIdStatutsForm as $iIdStatutForm)
	{
		$oMembres = new CPersonnes($oProjet);
		
		if (($iNbStatutsForm = $oMembres->initGraceIdFormation($url_iFormationId, $iIdStatutForm)) > 0)
		{
			$oBlocStatutFormation->nextLoop();
			$oBlocStatutFormation->remplacer("{statut.id}",$iIdStatutForm);
			$oBlocStatutFormation->remplacer("{statut.nom}",$asListeStatuts[$iIdStatutForm]);
			
			$oBlocMembre = new TPL_Block("BLOCK_MEMBRE",$oBlocStatutFormation);
			$oBlocMembre->beginLoop();
			
			foreach ($oMembres->aoPersonnes as $oPersonne)
			{
				$bValidCourriel = emailValide($oPersonne->retEmail());
				
				if ($bValidCourriel)
					$sMembre = $sVarMembre;
				else
					$sMembre = "<span class=\"sans_adresse_courrielle\">{$sVarMembre}</span>";
				
				if ($oPersonne->retId() == $g_iIdPers)
					$sMembre .= "&nbsp;<img src=\"theme://icones/etoile.gif\" width=\"13\" height=\"13\" alt=\"\" border=\"0\">";
				
				$oBlocMembre->nextLoop();
				$oBlocMembre->remplacer("{membre}",$sMembre);
				$oBlocMembre->remplacer("{membre.id}",$oPersonne->retId());
				$oBlocMembre->remplacer("{membre.nom}",emb_htmlentities($oPersonne->retNom()));
				$oBlocMembre->remplacer("{membre.prenom}",emb_htmlentities($oPersonne->retPrenom()));
				$oBlocMembre->remplacer("{membre.pseudo}",emb_htmlentities($oPersonne->retPseudo()));
				$oBlocMembre->remplacer("{membre.checkbox.disabled}",($bValidCourriel ? NULL : " disabled=\"disabled\""));
				if ($url_iPersonneId == $oPersonne->retId()) $url_bSelectionnerPers=true;
				else $url_bSelectionnerPers=False;
				$oBlocMembre->remplacer("{membre.checkbox.checked}",($bValidCourriel && $url_bSelectionnerPers ? " checked=\"checked\"" : NULL));
				$oBlocMembre->remplacer("{parent}",($bValidCourriel ? "idStatuts{$iIdStatutForm}" : NULL));				
			}
			
			$oBlocMembre->afficher();
		}
	}
	
	$oBlocStatutFormation->afficher();
}

if ($url_iFormationId > 0)
	$oBlocListeStatutsFormation->afficher();
else
	$oBlocListeStatutsFormation->effacer();
	
// ---------------------
// Liste des statuts
// ---------------------
$oBlocListeStatuts = new TPL_Block("BLOCK_LISTE_STATUTS",$oTpl);

if ($iNbStatuts > 0)
{
	$asListeStatuts = $oBlocListeStatuts->defTableau("ARRAY_STATUTS");
	
	$oBlocStatut = new TPL_Block("BLOCK_STATUT",$oBlocListeStatuts);
	$oBlocStatut->remplacer("{liste_membres}",$sSetListeMembres);
	$oBlocStatut->remplacer("{type.activite}",verifierAppelCourriel($url_asTypeCourriel[0]));
	
	$sVarMembre = $oBlocStatut->defVariable("VAR_MEMBRE");
	$oBlocStatut->beginLoop();
	
	foreach ($url_aiIdStatuts as $iIdStatut)
	{
		$oMembres = new CPersonnes($oProjet);

		if (($iNbStatuts = $oMembres->initGraceIdStatut($iIdStatut)) > 0)
		{
			$oBlocStatut->nextLoop();
			$oBlocStatut->remplacer("{statut.id}",$iIdStatut);
			$oBlocStatut->remplacer("{statut.nom}",$asListeStatuts[$iIdStatut]);
			
			$oBlocMembre = new TPL_Block("BLOCK_MEMBRE",$oBlocStatut);
			$oBlocMembre->beginLoop();
			
			foreach ($oMembres->aoPersonnes as $oPersonne)
			{
				$bValidCourriel = emailValide($oPersonne->retEmail());
				
				if ($bValidCourriel)	
					$sMembre = $sVarMembre;
				else
					$sMembre = "<span class=\"sans_adresse_courrielle\">{$sVarMembre}</span>";
				
				if ($oPersonne->retId() == $g_iIdPers)
					$sMembre .= "&nbsp;<img src=\"theme://icones/etoile.gif\" width=\"13\" height=\"13\" alt=\"\" border=\"0\">";
				
				$oBlocMembre->nextLoop();
				$oBlocMembre->remplacer("{membre}",$sMembre);
				$oBlocMembre->remplacer("{membre.id}",$oPersonne->retId());
				$oBlocMembre->remplacer("{membre.nom}",emb_htmlentities($oPersonne->retNom()));
				$oBlocMembre->remplacer("{membre.prenom}",emb_htmlentities($oPersonne->retPrenom()));
				$oBlocMembre->remplacer("{membre.pseudo}",emb_htmlentities($oPersonne->retPseudo()));
				$oBlocMembre->remplacer("{membre.checkbox.disabled}",($bValidCourriel ? NULL : " disabled=\"disabled\""));
				if ($url_iPersonneId == $oPersonne->retId()) $url_bSelectionnerPers=true;
				else $url_bSelectionnerPers=False;
				$oBlocMembre->remplacer("{membre.checkbox.checked}",($bValidCourriel && $url_bSelectionnerPers ? " checked=\"checked\"" : NULL));
				$oBlocMembre->remplacer("{parent}",($bValidCourriel ? "idStatuts{$iIdStatut}" : NULL));
			}
			$bStatutExiste = true;
			$oBlocMembre->afficher();
		}
	}
	
	$oBlocStatut->afficher();
}

if ($bStatutExiste)
	$oBlocListeStatuts->afficher();
else
	$oBlocListeStatuts->effacer();

// ---------------------
// Liste des équipes
// ---------------------
$oBlocListeEquipes = new TPL_Block("BLOCK_LISTE_EQUIPES",$oTpl);

if ($iNbEquipes > 0)
{
	// si on est dans le forum -> toute les équipes 'du forum'
	$oBlocListeEquipes->remplacer("{type.activite}",verifierAppelCourriel($url_asTypeCourriel[0]));

	$oBlocEquipe = new TPL_Block("BLOCK_EQUIPE",$oBlocListeEquipes);
	$oBlocEquipe->remplacer("{liste_membres}",$sSetListeMembres);
	
	$sVarMembre = $oBlocEquipe->defVariable("VAR_MEMBRE");
	$oBlocEquipe->beginLoop();
	
	foreach ($aoEquipes as $oEquipe)
	{
		$iIdEquipe = $oEquipe->retId();
		
		$oBlocEquipe->nextLoop();
		
		$oBlocMembre = new TPL_Block("BLOCK_MEMBRE",$oBlocEquipe);
		$oMembres = new CPersonnes($oProjet);
		
		$iNbMembres = 0;
		$iNbMembresSelectionnes = 0;
		
		if (($iNbEquipes = $oMembres->initGraceIdEquipe($iIdEquipe)) > 0)
		{
			$oBlocMembre->beginLoop();
			
			foreach ($oMembres->aoPersonnes as $oPersonne)
			{
				$bValidCourriel = emailValide($oPersonne->retEmail());
				
				if ($bValidCourriel)
					$sMembre = $sVarMembre;
				else
					$sMembre = "<span class=\"sans_adresse_courrielle\">{$sVarMembre}</span>";
				
				if ($oPersonne->retId() == $g_iIdPers)
					$sMembre .= "&nbsp;<img src=\"theme://icones/etoile.gif\" width=\"13\" height=\"13\" alt=\"\" border=\"0\">";
				
				$oBlocMembre->nextLoop();
				$oBlocMembre->remplacer("{membre}",$sMembre);
				$oBlocMembre->remplacer("{membre.id}",$oPersonne->retId());
				$oBlocMembre->remplacer("{membre.nom}",emb_htmlentities($oPersonne->retNom()));
				$oBlocMembre->remplacer("{membre.prenom}",emb_htmlentities($oPersonne->retPrenom()));
				$oBlocMembre->remplacer("{membre.pseudo}",emb_htmlentities($oPersonne->retPseudo()));
				$oBlocMembre->remplacer("{membre.checkbox.disabled}",($bValidCourriel ? NULL : " disabled=\"disabled\""));
					(($url_iEquipeId == $iIdEquipe) || ($url_iPersonneId == $oPersonne->retId())) ? $url_bSelectionnerPers=true : $url_bSelectionnerPers=False;
				$oBlocMembre->remplacer("{membre.checkbox.checked}",($bValidCourriel && $url_bSelectionnerPers ? " checked=\"checked\"" : NULL));
				$oBlocMembre->remplacer("{parent}",($bValidCourriel ? "idEquipe" : NULL));
				
				$iNbMembres++;
				
				if ($bValidCourriel && $url_bSelectionnerPers)
					$iNbMembresSelectionnes++;
			}
			
			$oBlocMembre->afficher();
		}
		else
			$oBlocMembre->effacer();
		
		$oBlocEquipe->remplacer("{equipe.nom}",emb_htmlentities($oEquipe->retNom()));
		$oBlocEquipe->remplacer("{equipe.checked}",($iNbMembres == $iNbMembresSelectionnes ? " checked=\"checked\"" : NULL));
	}
	
	$oBlocEquipe->afficher();
	
	// Toutes les équipes
	$oBlocListeEquipes->remplacer("{equipe.nom}",$oBlocListeEquipes->defVariable("VAR_ELEMENT"));
	$oBlocListeEquipes->afficher();
}
else
{
	$oBlocListeEquipes->effacer();
}

// ---------------------
// Liste des personnes inscrites à la plate-forme
// ---------------------
$oBlocListePersonnes = new TPL_Block("BLOCK_LISTE_PERSONNES",$oTpl);

if ($iNbPersonnes > 0)
{
	$oBlocPersonne = new TPL_Block("BLOCK_PERSONNE",$oBlocListePersonnes);
	$oBlocPersonne->remplacer("{liste_membres}",$sSetListeMembres);
	($iNbPersonnes > 1) ? $oBlocPersonne->remplacer("{personne.nombre}","Toutes les personnes") : $oBlocPersonne->remplacer("{personne.nombre}","La personne");
	if ($iNbPersonnes < 2) $oBlocPersonne->remplacer("{type.activite}", "");
	else $oBlocPersonne->remplacer("{type.activite}",verifierAppelCourriel($url_asTypeCourriel[0]));
	
	$oBlocMembre = new TPL_Block("BLOCK_MEMBRE",$oBlocPersonne);
	$sVarMembre = $oBlocPersonne->defVariable("VAR_MEMBRE");
	$oBlocMembre->beginLoop();
	
	foreach ($aoPersonnes as $oPersonne)
	{
		$bValidCourriel = emailValide($oPersonne->retEmail());
		
		if ($bValidCourriel)
			$sMembre = $sVarMembre;
		else
			$sMembre = "<span class=\"sans_adresse_courrielle\">{$sVarMembre}</span>";
		
		if ($oPersonne->retId() == $g_iIdPers)
			$sMembre .= "&nbsp;<img src=\"theme://icones/etoile.gif\" width=\"13\" height=\"13\" alt=\"\" border=\"0\">";
		
		$oBlocMembre->nextLoop();
		$oBlocMembre->remplacer("{membre}",$sMembre);
		$oBlocMembre->remplacer("{membre.id}",$oPersonne->retId());
		$oBlocMembre->remplacer("{membre.nom}",emb_htmlentities($oPersonne->retNom()));
		$oBlocMembre->remplacer("{membre.prenom}",emb_htmlentities($oPersonne->retPrenom()));
		$oBlocMembre->remplacer("{membre.pseudo}",emb_htmlentities($oPersonne->retPseudo()));
		$oBlocMembre->remplacer("{membre.checkbox.disabled}",($bValidCourriel ? NULL : " disabled=\"disabled\""));
				($url_iPersonneId == $oPersonne->retId()) ? $url_bSelectionnerPers=true : $url_bSelectionnerPers=False;
		$oBlocMembre->remplacer("{membre.checkbox.checked}",($bValidCourriel && $url_bSelectionnerPers ? " checked=\"checked\"" : NULL));
		$oBlocMembre->remplacer("{parent}",($bValidCourriel ? "idPers" : NULL));
	}
	
	$oBlocMembre->afficher();
	$oBlocPersonne->afficher();
	$oBlocListePersonnes->afficher();
}
else
{
	$oBlocListePersonnes->effacer();
}

$oBlocAucunInscrit = new TPL_Block("BLOCK_AUCUN_INSCRIT",$oTpl);

if ($iNbPersonnes > 0 || $iNbEquipes > 0 || $bStatutExiste || $url_iFormationId >0)
	$oBlocAucunInscrit->effacer();
else
	$oBlocAucunInscrit->afficher();

// {{{ Formulaire
$oTpl->remplacer(array("{form}","{/form}"),array("<form>","</form>"));
// }}}

$oTpl->afficher();

?>

