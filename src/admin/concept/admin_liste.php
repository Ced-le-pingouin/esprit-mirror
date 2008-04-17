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
** Fichier ................: admin_liste.php
** Description ............: 
** Date de création .......: 01/02/2002
** Dernière modification ..: 31/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
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
if (isset($_GET))
{
	$url_iType   = $_GET["type"];
	$url_sParams = $_GET["params"];
	$sTempParams = $_GET["params"];
}
else if (isset($_POST))
{
	$url_iType   = $_POST["type"];
	$url_sParams = $_POST["params"];
	$sTempParams = $_POST["params"];
}

// ---------------------
// Initialiser
// ---------------------
list($g_iFormation,$g_iModule,$g_iRubrique,$g_iUnite,$g_iActiv,$g_iSousActiv) = explode(":",$url_sParams);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("admin_liste.tpl");

$oBlocFormation     = new TPL_Block("BLOCK_FORMATION",$oTpl);
$oBlocSansFormation = new TPL_Block("BLOCKELSE_FORMATION",$oTpl);

if ($g_iFormation > 0)
{
	$oProjet->defFormationCourante($g_iFormation);
	
	// {{{ Permissions au niveau de la formation
	include_once("perm.modif.form.php");
	// }}}
	
	$aaRechTpl                 = array();
	$aaRechTpl["formation"]    = array("{formation.type}","{formation.id}","{formation.nom}","{formation.nom:rawurlencode}","{id_cible}");
	$aaRechTpl["module"]       = array("{module.type}","{module.id}","{module.nom}","{module.nom:rawurlencode}","{module.intitule}","{id_cible}");
	$aaRechTpl["rubrique"]     = array("{rubrique.type}","{rubrique.id}","{rubrique.nom}","{rubrique.nom:rawurlencode}","{rubrique.intitule}","{rubrique.index}","{id_cible}");
	$aaRechTpl["activite"]     = array("{activite.type}","{activite.id}","{activite.index}","{activite.nom}","{activite.nom:rawurlencode}","{id_cible}");
	$aaRechTpl["sousactivite"] = array("{sousactivite.type}","{sousactivite.id}","{sousactivite.index}","{sousactivite.nom}","{sousactivite.nom:rawurlencode}","{id_cible}");
	
	// {{{ Modules
	$oBlocModule = new TPL_Block("BLOCK_MODULE",$oBlocFormation);
	
	$tIdMod = 0;
	
	if ($oProjet->oFormationCourante->initModules() > 0)
	{
		$oBlocModule->beginLoop();
		
		foreach ($oProjet->oFormationCourante->aoModules as $oModule)
		{
			$iIdMod = $oModule->retId();
			
			$oProjet->defModuleCourant($iIdMod,TRUE);
			
			// {{{ Permissions au niveau de la formation
			include("perm.modif.mod.php");
			// }}}
			
			if (!($g_bModifier | $g_bModifierStatut))
				continue;
			
			if ($tIdMod == 0 ||
				(TYPE_MODULE == $url_iType && $g_iModule == $iIdMod))
				$g_iModule = $tIdMod = $iIdMod;
			
			$oBlocModule->nextLoop();
			
			// {{{ Rubriques
			$oBlocRubrique = new TPL_Block("BLOCK_RUBRIQUE",$oBlocModule);
			
			if ($oModule->initRubriques() > 0)
			{
				$iIdxRubr = 1;
				
				$oBlocRubrique->beginLoop();
				
				foreach ($oModule->aoRubriques as $oRubrique)
				{
					$oBlocRubrique->nextLoop();
					
					// {{{ Activité
					$oBlocActiv = new TPL_Block("BLOCK_ACTIVITE",$oBlocRubrique);
					
					if ($oRubrique->initActivs() > 0)
					{
						$oBlocActiv->beginLoop();
						
						foreach ($oRubrique->aoActivs as $oActiv)
						{
							$oBlocActiv->nextLoop();
							
							// {{{ Sous-activité
							$oBlocSousActiv = new TPL_Block("BLOCK_SOUS_ACTIVITE",$oBlocActiv);
							
							if ($oActiv->initSousActivs() > 0)
							{
								$oBlocSousActiv->beginLoop();
								
								foreach ($oActiv->aoSousActivs as $oSousActiv)
								{
									$oBlocSousActiv->nextLoop();
									
									$iId  = $oSousActiv->retId();
									$sNom = $oSousActiv->retNom();
									
									$asReplTpl = array(TYPE_SOUS_ACTIVITE,$iId,$oSousActiv->retNumOrdre(),emb_htmlentities($sNom),rawurlencode($sNom),($url_iType == TYPE_SOUS_ACTIVITE && $g_iSousActiv == $iId ? " id=\"idCible\"" : NULL));
									
									$oBlocSousActiv->remplacer($aaRechTpl["sousactivite"],$asReplTpl);
								}
								
								$oBlocSousActiv->afficher();
							}
							else
								$oBlocSousActiv->effacer();
							// }}}
							
							$iId  = $oActiv->retId();
							$sNom = $oActiv->retNom();
							
							$asReplTpl = array(TYPE_ACTIVITE,$iId,$oActiv->retNumOrdre(),emb_htmlentities($sNom),rawurlencode($sNom),($url_iType == TYPE_ACTIVITE && $g_iActiv == $iId ? " id=\"idCible\"" : NULL));
							
							$oBlocActiv->remplacer($aaRechTpl["activite"],$asReplTpl);
						}
						
						$oBlocActiv->afficher();
					}
					else
						$oBlocActiv->effacer();
					// }}}
					
					$iId  = $oRubrique->retId();
					$sNom = $oRubrique->retNom();
					
					$sIntitule = "&nbsp;";
					
					if (LIEN_UNITE == $oRubrique->retType() &&
						$oRubrique->retNumDepart() > 0)
						$sIntitule = $oRubrique->oIntitule->retNom()
							."&nbsp;"
							.$oRubrique->retNumDepart()
							."&nbsp;:&nbsp;";
					
					$asReplTpl = array(TYPE_RUBRIQUE,$iId,emb_htmlentities($sNom),rawurlencode($sNom),$sIntitule,$iIdxRubr++,($url_iType == TYPE_RUBRIQUE && $g_iRubrique == $iId ? " id=\"idCible\"" : NULL));
					$oBlocRubrique->remplacer($aaRechTpl["rubrique"],$asReplTpl);
				}
				
				$oBlocRubrique->afficher();
			}
			else
				$oBlocRubrique->effacer();
			// }}}
			
			$iId  = $oModule->retId();
			$sNom = $oModule->retNom();
			
			$sIntitule = NULL;
			
			if ($oModule->retNumDepart() > 0)
				$sIntitule = emb_htmlentities($oModule->oIntitule->retNom())
					."&nbsp;"
					.$oModule->retNumDepart()
					."&nbsp;:&nbsp;";
			
			$asReplTpl = array(TYPE_MODULE,$iId,emb_htmlentities($sNom),rawurlencode($sNom),$sIntitule,($url_iType == TYPE_MODULE && $g_iModule == $iId ? " id=\"idCible\"" : NULL));
			$oBlocModule->remplacer($aaRechTpl["module"],$asReplTpl);
		}
		
		$oBlocModule->afficher();
	}
	else
		$oBlocModule->effacer();
	// }}}
	
	$sNom = $oProjet->oFormationCourante->retNom();
	
	$asReplTpl = array(TYPE_FORMATION,$g_iFormation,emb_htmlentities($sNom),rawurlencode($sNom),($url_iType == TYPE_FORMATION ? " id=\"idCible\"" : NULL));
	
	$oBlocFormation->remplacer($aaRechTpl["formation"],$asReplTpl);
	$oBlocFormation->afficher();
	
	$oBlocSansFormation->effacer();
	
	$url_sParams = "{$g_iFormation}:{$g_iModule}:{$g_iRubrique}:0:{$g_iActiv}:{$g_iSousActiv}";
}
else
{
	$url_iType   = 0;
	$url_sParams = "0:0:0:0:0:0";
	
	$oBlocFormation->effacer();
	$oBlocSansFormation->afficher();
}

$oTpl->remplacer("{url.params}","?type={$url_iType}&params={$sTempParams}");
$oTpl->remplacer("{params.indice}","{$sTempParams}");

$oTpl->afficher();

?>

