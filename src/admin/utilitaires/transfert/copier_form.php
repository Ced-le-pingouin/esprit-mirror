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
** Fichier .................: copier_form.php
** Description ............:
** Date de création .......: 24/08/2004
** Dernière modification ..: 26/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_lib("systeme_fichiers.lib.php",TRUE));
require_once("copier_form.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sNomBddSrc = $HTTP_GET_VARS["bddSrc"];
$url_sNomBddDst = $HTTP_GET_VARS["bddDst"];
$url_iIdFormSrc = $HTTP_GET_VARS["idFormSrc"];

$url_bCopierForums  = ($HTTP_GET_VARS["copierForums"] == "1");
$url_bCopierSujetsForums = ($HTTP_GET_VARS["copierSujetsForums"] == "1");
$url_bCopierChats        = ($HTTP_GET_VARS["copierChats"] == "1");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style(); ?>
<script type="text/javascript" language="javascript">
<!--
function init() { 
	top.autoriser_fermer_fenetre = true;
	document.getElementById("id_barre_de_progession").style.visibility = "hidden";
	document.getElementById("id_barre_de_progession").style.display = "none";
}
//-->
</script>
<style type="text/css">
<!--
.barre_de_progession
{
	background-color: rgb(255,255,255);
	display: block;
	position: absolute;
	top: 0;
	left:0;
	width: 100%;
	height: 100%;
	text-align: center;
	vertical-align: middle;
}
-->
</style>
</head>
<body onload="init()" style="background-image: none;">
<div id="id_barre_de_progession" class="barre_de_progession">
<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
<tr><td align="center" valign="middle"><img src="<?=dir_theme_commun('barre_de_progression.gif')?>" alt="barre_de_progression"><br><small>Un instant svp</small></td></tr>
<tr><td>&nbsp;</td></tr>
</table>
</div>
<table border="0" cellspacing="1" cellpadding="2" width="100%">
<tr><td class="cellule_sous_titre" width="99%">&nbsp;Copier&nbsp;</td><td class="cellule_sous_titre">&nbsp;Statut&nbsp;</td></tr>
<?php
// ---------------------
// Ouvrir une connexion avec le serveur de la base de données
// ---------------------
$hConnexion = mysql_connect($g_sNomServeurTransfert,$g_sNomProprietaireTransfert,$g_sMotDePasseTransfert);

// ---------------------
// Retourner le numéro d'ordre le plus haut
// de la base de données de destination
// ---------------------
$sRequeteSql = "SELECT MAX(OrdreForm), MAX(TypeForm) FROM {$url_sNomBddDst}.Formation";
$hResult = mysql_query($sRequeteSql,$hConnexion);
$aEnreg = mysql_fetch_row($hResult);
$iNumOrdreMax = $aEnreg[0]+1;
$iNumTypeMax = $aEnreg[1]+1;
unset($aEnreg);
mysql_free_result($hResult);

// ---------------------
// Rechercher la formation à copier
// ---------------------
$sRequeteSql = "SELECT * FROM {$url_sNomBddSrc}.Formation"
	." WHERE IdForm='{$url_iIdFormSrc}'"
	." LIMIT 1";
$hResult = mysql_query($sRequeteSql,$hConnexion);
$oFormSrc = mysql_fetch_object($hResult);
mysql_free_result($hResult);

// ---------------------
// Ajouter la formation
// ---------------------
afficher_col_copier(0,"Formation",$oFormSrc->NomForm);

$sRequeteSql = "INSERT INTO {$url_sNomBddDst}.Formation SET"
	." IdForm=NULL"
	.", NomForm='".MySQLEscapeString($oFormSrc->NomForm)." (transférée)'"
	.", DescrForm='".MySQLEscapeString($oFormSrc->DescrForm)."'"
	.", DateDebForm=NOW()"
	.", DateFinForm=NOW()"
	.", StatutForm='{$oFormSrc->StatutForm}'"
	.", TypeForm='{$iNumTypeMax}'"
	.", InscrSpontForm='{$oFormSrc->InscrSpontForm}'"
	.", InscrAutoModules='{$oFormSrc->InscrAutoModules}'"
	.", SuffixeTxt='{$oFormSrc->SuffixeTxt}'"
	.", InscrSpontEquipeF='{$oFormSrc->InscrSpontEquipeF}'"
	.", NbMaxDsEquipeF='{$oFormSrc->NbMaxDsEquipeF}'"
	.", VisiteurAutoriser='".(isset($oFormSrc->VisiteurAutoriser) ? $oFormSrc->VisiteurAutoriser : "0")."'"
	.", OrdreForm='{$iNumOrdreMax}'"
	.", IdPers='".(isset($oFormSrc->IdPers) ? $oFormSrc->IdPers : "0")."'";
mysql_query($sRequeteSql,$hConnexion);
$iIdFormNouv = mysql_insert_id($hConnexion);

$sRepFormSrc = dir_document_root("{$url_sNomBddSrc}/formation/f{$url_iIdFormSrc}/");
$sRepFormDst = dir_formation($iIdFormNouv);

mkdir($sRepFormDst);
mkdir("{$sRepFormDst}rubriques");

copier_repertoire("{$sRepFormSrc}rubriques","{$sRepFormDst}rubriques");

afficher_col_statut();

// ---------------------
// Modules
// ---------------------
$sRequeteSql = "SELECT * FROM {$url_sNomBddSrc}.Module"
	." WHERE IdForm='{$url_iIdFormSrc}'"
	." ORDER BY OrdreMod ASC";
$hResult = mysql_query($sRequeteSql,$hConnexion);
$aoModulesSrc = array();
while ($oEnreg = mysql_fetch_object($hResult))
	$aoModulesSrc[] = $oEnreg;
mysql_free_result($hResult);

foreach ($aoModulesSrc as $oModule)
{
	// Vérifier l'intitulé
	// -------------------
	if (isset($oModule->IdIntitule))
	{
		if ($oModule->IdIntitule > 0)
		{
			// Récupérer le nom de l'intitulé source
			$sRequeteSql = "SELECT * FROM {$url_sNomBddSrc}.Intitule"
				." WHERE IdIntitule='{$oModule->IdIntitule}'"
				." AND TypeIntitule='".TYPE_MODULE."'"
				." LIMIT 1";
			$hResult = mysql_query($sRequeteSql,$hConnexion);
			$oEnreg = mysql_fetch_object($hResult);
			mysql_free_result($hResult);
			
			$sNomIntitule = $oEnreg->NomIntitule;
			
			// Vérifier si il n'existe pas dans les intitulés de destination
			$sRequeteSql = "SELECT * FROM {$url_sNomBddDst}.Intitule"
				." WHERE NomIntitule='".MySQLEscapeString($sNomIntitule)."'"
				." AND TypeIntitule='".TYPE_MODULE."'"
				." LIMIT 1";
			$hResult = mysql_query($sRequeteSql,$hConnexion);
			
			if ($oEnreg = mysql_fetch_object($hResult))
			{
				$oModule->IdIntitule = $oEnreg->IdIntitule;
				mysql_free_result($hResult);
			}
			else
			{
				mysql_free_result($hResult);
				
				// Rajouter l'intitulé de destination
				$sRequeteSql = "INSERT INTO {$url_sNomBddDst}.Intitule SET"
					." IdIntitule=NULL"
					.", NomIntitule='".MySQLEscapeString($sNomIntitule)."'"
					.", TypeIntitule='".TYPE_MODULE."'";
				$hResult = mysql_query($sRequeteSql,$hConnexion);
				$oModule->IdIntitule = mysql_insert_id($hConnexion);
			}
		}
	}
	else
	{
		$oModule->IdIntitule = "1";
		$oModule->NumDepartIntitule = $oModule->OrdreMod;
	}
	
	// Copier le module
	// ----------------
	afficher_col_copier(1,"Module",$oModule->NomMod);
	$sRequeteSql = "INSERT INTO {$url_sNomBddDst}.Module SET"
		." IdMod=NULL"
		.", NomMod='".MySQLEscapeString($oModule->NomMod)."'"
		.", DescrMod='".MySQLEscapeString($oModule->DescrMod)."'"
		.", DateDebMod=NOW()"
		.", DateFinMod=NOW()"
		.", StatutMod='{$oModule->StatutMod}'"
		.", OrdreMod='{$oModule->OrdreMod}'"
		.", IdForm='{$iIdFormNouv}'"
		.", IdIntitule='{$oModule->IdIntitule}'"
		.", NumDepartIntitule='{$oModule->NumDepartIntitule}'";
	mysql_query($sRequeteSql,$hConnexion);
	$iIdModNouv = mysql_insert_id($hConnexion);
	afficher_col_statut();
	
	// ---------------------
	// Rubriques
	// ---------------------
	$sRequeteSql = "SELECT * FROM {$url_sNomBddSrc}.Module_Rubrique"
		." WHERE IdMod='{$oModule->IdMod}'"
		." ORDER BY OrdreRubrique ASC";
	$hResult = mysql_query($sRequeteSql,$hConnexion);
	$aoRubriquesSrc = array();
	while ($oEnreg = mysql_fetch_object($hResult))
		$aoRubriquesSrc[] = $oEnreg;
	mysql_free_result($hResult);
	
	foreach ($aoRubriquesSrc as $oRubrique)
	{
		$aaGaleries = array();
		
		// Vérifier l'intitulé
		// -------------------
		if (isset($oRubrique->IdIntitule))
		{
			if ($oRubrique->IdIntitule > 0)
			{
				// Récupérer le nom de l'intitulé source
				$sRequeteSql = "SELECT * FROM {$url_sNomBddSrc}.Intitule"
					." WHERE IdIntitule='{$oRubrique->IdIntitule}'"
					." AND TypeIntitule='".TYPE_RUBRIQUE."'"
					." LIMIT 1";
				$hResult = mysql_query($sRequeteSql,$hConnexion);
				$oEnreg = mysql_fetch_object($hResult);
				mysql_free_result($hResult);
				
				$sNomIntitule = $oEnreg->NomIntitule;
				
				// Vérifier si il n'existe pas dans les intitulés de destination
				$sRequeteSql = "SELECT * FROM {$url_sNomBddDst}.Intitule"
					." WHERE NomIntitule='".MySQLEscapeString($sNomIntitule)."'"
					." AND TypeIntitule='".TYPE_RUBRIQUE."'"
					." LIMIT 1";
				$hResult = mysql_query($sRequeteSql,$hConnexion);
				
				if ($oEnreg = mysql_fetch_object($hResult))
				{
					$oRubrique->IdIntitule = $oEnreg->IdIntitule;
					mysql_free_result($hResult);
				}
				else
				{
					mysql_free_result($hResult);
					
					// Rajouter l'intitulé de destination
					$sRequeteSql = "INSERT INTO {$url_sNomBddDst}.Intitule SET"
						." IdIntitule=NULL"
						.", NomIntitule='".MySQLEscapeString($sNomIntitule)."'"
						.", TypeIntitule='".TYPE_RUBRIQUE."'";
					$hResult = mysql_query($sRequeteSql,$hConnexion);
					$oRubrique->IdIntitule = mysql_insert_id($hConnexion);
				}
			}
		}
		else
		{
			$oRubrique->IdIntitule = "2";
			$oRubrique->NumDepartIntitule = $oRubrique->OrdreMod;
		}
		
		// Copier la rubrique/unité
		// ------------------------
		afficher_col_copier(2,"Rubrique/Unité",$oRubrique->NomRubrique);
		$sRequeteSql = "INSERT INTO {$url_sNomBddDst}.Module_Rubrique SET"
			." IdRubrique=NULL"
			.", NomRubrique='".MySQLEscapeString($oRubrique->NomRubrique)."'"
			.", DonneesRubrique='{$oRubrique->DonneesRubrique}'"
			.", TypeRubrique='{$oRubrique->TypeRubrique}'"
			.", StatutRubrique='{$oRubrique->StatutRubrique}'"
			.", TypeMenuUnite='0'"
			.", NumeroActivUnite='0'"
			.", OrdreRubrique='{$oRubrique->OrdreRubrique}'"
			.", IdMod='{$iIdModNouv}'"
			.", IdIntitule='".(isset($oRubrique->IdIntitule) ? $oRubrique->IdIntitule : "2")."'"
			.", NumDepartIntitule='".(isset($oRubrique->NumDepartIntitule) ? $oRubrique->NumDepartIntitule : $oRubrique->OrdreRubrique)."'"
			.", IdPers='0'";
		mysql_query($sRequeteSql,$hConnexion);
		$iIdRubNouv = mysql_insert_id($hConnexion);
		
		// Copier forum
		// ------------
		if ($oRubrique->TypeRubrique ==  LIEN_FORUM)
		{
			if ($url_bCopierForums)
			{
				$oForumSrc = rechercher_forum($oRubrique->IdRubrique,TYPE_RUBRIQUE);
				
				if (isset($oForumSrc))
				{
					$iIdForumDst = copier_forum($oForumSrc,$iIdRubNouv,TYPE_RUBRIQUE);
					
					if ($url_bCopierSujetsForums && $iIdForumDst > 0)
						copier_sujets_forum($oForumSrc,$iIdForumDst);
				}
			}
		}
		
		afficher_col_statut();
		
		// ---------------------
		// Activités
		// ---------------------
		$sRequeteSql = "SELECT * FROM {$url_sNomBddSrc}.Activ"
			." WHERE IdRubrique='{$oRubrique->IdRubrique}'"
			." ORDER BY OrdreActiv ASC";
		$hResult = mysql_query($sRequeteSql,$hConnexion);
		$aoActivsSrc = array();
		while ($oEnreg = mysql_fetch_object($hResult))
			$aoActivsSrc[] = $oEnreg;
		mysql_free_result($hResult);
		
		foreach ($aoActivsSrc as $oActiv)
		{
			// Copier l'activité
			// -----------------
			afficher_col_copier(3,"Activité",$oActiv->NomActiv);
			$sRequeteSql = "INSERT INTO {$url_sNomBddDst}.Activ SET"
				." IdActiv=NULL"
				.", NomActiv='".MySQLEscapeString($oActiv->NomActiv)."'"
				.", DescrActiv='".MySQLEscapeString($oActiv->DescrActiv)."'"
				.", DateDebActiv='NOW()'"
				.", DateFinActiv='NOW()'"
				.", ModaliteActiv='{$oActiv->ModaliteActiv}'"
				.", AfficherModaliteActiv='0'"
				.", StatutActiv='{$oActiv->StatutActiv}'"
				.", AfficherStatutActiv='0'"
				.", InscrSpontEquipeA='0'"
				.", NbMaxDsEquipeA='0'"
				.", OrdreActiv='{$oActiv->OrdreActiv}'"
				.", IdRubrique='{$iIdRubNouv}'"
				.", IdUnite='0'";
			mysql_query($sRequeteSql,$hConnexion);
			$iIdActivNouv = mysql_insert_id($hConnexion);
			
			$sRepActivSrc = "{$sRepFormSrc}activ_{$oActiv->IdActiv}";
			$sRepActivDst = dir_cours($iIdActivNouv,$iIdFormNouv);
			
			if (@mkdir($sRepActivDst))
			{
				copier_repertoire($sRepActivSrc,$sRepActivDst);
				
				// Effacer les documents des étudiants du collecticiel
				effacer_repertoire("{$sRepActivDst}ressources");
				
				// Effacer les archives de chat
				effacer_repertoire("{$sRepActivDst}chatlog");
			}
			
			afficher_col_statut();
			
			// ---------------------
			// Sous-activités
			// ---------------------
			$sRequeteSql = "SELECT * FROM {$url_sNomBddSrc}.SousActiv"
				." WHERE IdActiv='{$oActiv->IdActiv}'"
				." ORDER BY OrdreSousActiv ASC";
			$hResult = mysql_query($sRequeteSql,$hConnexion);
			$aoSousActivsSrc = array();
			while ($oEnreg = mysql_fetch_object($hResult))
				$aoSousActivsSrc[] = $oEnreg;
			mysql_free_result($hResult);
			
			foreach ($aoSousActivsSrc as $oSousActiv)
			{
				// Copier l'activité
				// -----------------
				afficher_col_copier(4,"Sous-activité",$oSousActiv->NomSousActiv);
				$sRequeteSql = "INSERT INTO {$url_sNomBddDst}.SousActiv SET"
					." IdSousActiv=NULL"
					.", NomSousActiv='".MySQLEscapeString($oSousActiv->NomSousActiv)."'"
					.", DescrSousActiv='".MySQLEscapeString($oSousActiv->DescrSousActiv)."'"
					.", DonneesSousActiv='{$oSousActiv->DonneesSousActiv}'"
					.", DateDebSousActiv=NOW()"
					.", DateFinSousActiv=NOW()"
					.", StatutSousActiv='{$oSousActiv->StatutSousActiv}'"
					.", VotesMinSousActiv='{$oSousActiv->VotesMinSousActiv}'"
					.", IdTypeSousActiv='{$oSousActiv->IdTypeSousActiv}'"
					.", PremierePageSousActiv='{$oSousActiv->PremierePageSousActiv}'"
					.", IdActiv='{$iIdActivNouv}'"
					.", OrdreSousActiv='{$oSousActiv->OrdreSousActiv}'"
					.", InfoBulleSousActiv='{$oSousActiv->InfoBulleSousActiv}'"
					.", ModaliteSousActiv='{$oSousActiv->ModaliteSousActiv}'"
					.", IdPers='0'";
				mysql_query($sRequeteSql,$hConnexion);
				
				$iIdSousActivNouv = mysql_insert_id($hConnexion);
				
				if ($oSousActiv->IdTypeSousActiv ==  LIEN_FORUM)
				{
					if ($url_bCopierForums)
					{
						$oForumSrc = rechercher_forum($oSousActiv->IdSousActiv,TYPE_SOUS_ACTIVITE);
						
						if (isset($oForumSrc))
						{
							$iIdForumDst = copier_forum($oForumSrc,$iIdSousActivNouv,TYPE_SOUS_ACTIVITE);
							
							if ($url_bCopierSujetsForums && $iIdForumDst > 0)
								copier_sujets_forum($oForumSrc,$iIdForumDst);
						}
					}
				}
				else if ($oSousActiv->IdTypeSousActiv == LIEN_COLLECTICIEL)
				{
					verifier_associer_galerie($oSousActiv->IdSousActiv,$iIdSousActivNouv,$aaGaleries);
				}
				else if ($oSousActiv->IdTypeSousActiv == LIEN_GALERIE)
				{
					associer_galerie_collecticiels($oSousActiv->IdSousActiv,$iIdSousActivNouv,$aaGaleries);
				}
				else if ($oSousActiv->IdTypeSousActiv == LIEN_CHAT)
				{
					if ($url_bCopierChats)
					{
						$aoChats = rechercher_chats($oSousActiv->IdSousActiv,TYPE_SOUS_ACTIVITE);
						
						copier_chats($aoChats,$iIdSousActivNouv);
					}
				}
				
				afficher_col_statut();
			}
		}
		
		ajouter_collecticiels_galeries($aaGaleries);
	}
}

// ---------------------
// Fermer la connexion avec la base de données
// ---------------------
mysql_close($hConnexion);

unset($hConnexion);
?>
<tr><td colspan="2" style="text-align: center;"><br>Fin de la copie</td></tr>
</table>
<p>&nbsp;</p>
</body>
</html>
