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
** Fichier ................: ressource_evaluation.php
** Description ............:
** Date de création .......: 01/03/2001
** Dernière modification ..: 29/11/2005
** Auteurs ................: Cédric FLOQUET <cedric.floquet@umh.ac.be>
**                           Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdPers = (isset($_GET["idPers"]) ? $_GET["idPers"] : 0);

// ---------------------
// Initialiser
// ---------------------
$g_iIdPers = $oProjet->retIdUtilisateur();

$g_bModeEdition = ($url_iIdPers == $g_iIdPers) && $oProjet->verifPermission("PERM_EVALUER_COLLECTICIEL");

// Récupérer le répertoire de destination
$g_sRepDest = dir_collecticiel($oProjet->oFormationCourante->retId()
	,$oProjet->oActivCourante->retId()
	,NULL
	,TRUE);

$g_sRepDestRelatif = dir_collecticiel($oProjet->oFormationCourante->retId()
	,$oProjet->oActivCourante->retId()
	,NULL
	,FALSE);

// ---------------------
// Déclaration des fonctions locales
// ---------------------
function afficherEvaluation ($v_oEval=NULL,$v_bEditable=FALSE)
{
	global $oProjet;
	global $oResSA, $sSousTitre, $g_bModeEdition;
	global $g_sRepDestRelatif;
	
	if (empty($v_oEval))
		$v_bEditable = $g_bModeEdition;
	
	echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"5\" width=\"100%\">\n";
	
	// ---------------------
	// Etat du document
	// ---------------------
	echo "<tr>"
		."<td align=\"right\">"
		."<span class=\"intitule\">&Eacute;tat&nbsp;du&nbsp;document&nbsp;:</span>"
		."</td>";
	
	if ($v_bEditable)
	{
		// Donner le statut "Accepté" par défaut
		if (STATUT_RES_SOUMISE == ($iStatutActuelDocument = $oResSA->retStatut()))
			$iStatutActuelDocument = STATUT_RES_ACCEPTEE;
		
		echo "<td width=\"99%\">"
			."<select name=\"statutEval\">";
		
		for ($iIndexStatut=STATUT_RES_APPROF; $iIndexStatut<=STATUT_RES_ACCEPTEE; $iIndexStatut++)
			echo "<option"
				." value=\"{$iIndexStatut}\""
				.($iStatutActuelDocument == $iIndexStatut ? " selected=\"selected\"" : NULL)
				.">".$oResSA->retTexteStatut($iIndexStatut)."</option>\n";
		
		echo "</select>"
			."</td>";
	}
	else
		echo "<td width=\"99%\">".str_replace(" ","&nbsp;",$oResSA->retTexteStatut())."</td>";
	
	echo "</tr>\n";
	
	if ($v_bEditable)
		echo "<input type=\"hidden\" name=\"idResSA\" value=\"".$oResSA->retId()."\">\n";
	
	echo "<tr>"
		."<td>&nbsp;</td>"
		."<td class=\"cellule_clair\">\n";
	
	if (isset($v_oEval))
	{
		list($tmp) = explode(" ",$v_oEval->retDate());
		
		$date = ereg_replace("([0-9]{4})-([0-9]{2})-([0-9]{2})","\\3-\\2-\\1",$tmp);
		
		echo "Evaluation du document par "
			.$v_oEval->oEvaluateur->retLienEmail()
			."&nbsp;({$date})";
	}
	else
		echo "Nouvelle &eacute;valuation de &laquo;&nbsp;<b>".$oResSA->retNom()."</b>&nbsp;&raquo;";
	
	echo "</td></tr>\n";
	echo "<tr>\n";
	echo "<td align=\"right\" width=\"1%\"><span class=\"intitule\">Appr&eacute;ciation&nbsp;:</span></TD>\n";
	echo "<td>";
	
	if ($v_bEditable)
		echo "<input type=\"text\" size=\"45\" name=\"appreciationEval\""
			." value=\"".(isset($v_oEval) ? htmlentities($v_oEval->retAppreciation(),ENT_COMPAT,"UTF-8") : NULL)."\""
			." style=\"width: 100%;\""
			.">";
	else
		echo "<p class=\"appreciation\">"
			.htmlentities($v_oEval->retAppreciation(),ENT_COMPAT,"UTF-8")
			."</p>";
	
	echo "</td>\n";
	echo "</tr>\n";
	
	// {{{ Commentaire
	echo "<tr>\n";
	echo "<td class=\"intitule\" align=\"right\" valign=\"top\">Commentaire&nbsp;:</TD>\n";
	echo "<td valign=\"top\">";
	
	if ($v_bEditable)
		echo "<textarea id=\"commentaire\" cols=\"45\" rows=\"12\" name=\"commentaireEval\" style=\"width: 100%;\">"
			.(isset($v_oEval) ? htmlentities($v_oEval->retCommentaire(),ENT_COMPAT,"UTF-8") : NULL)
			."</textarea>"
			."<br>\n"
			."<div style=\"color: rgb(127,157,185); text-align: right;\">[&nbsp;"
			."<a href=\"javascript: void(0);\""
			." onclick=\"editeur('formEval','commentaireEval','evaluation'); return false;\""
			." onfocus=\"blur()\""
			.">Editeur</a>"
			."&nbsp;]"
			."</div>";
	else
	{
		// {{{ Tableau de bord
		$asRechTpl = array("racine://","{tableaudebord.niveau.id}","{tableaudebord.niveau.type}");
		$amReplTpl = array(dir_root_plateform(NULL,FALSE),$oProjet->oRubriqueCourante->retId(),$oProjet->oRubriqueCourante->retTypeNiveau());
		// }}}
		
		echo "<p class=\"appreciation\">"
			.str_replace($asRechTpl,$amReplTpl,convertBaliseMetaVersHtml($v_oEval->retCommentaire()))
			."</p>";
	}
	
	echo "</td>\n";
	echo "</tr>\n";
	// }}}
	
	// {{{ Attacher un document
	$sLienRessource = NULL;
	
	if (is_object($oResSA->oRessourceAttache))
		$sLienRessource = "<a"
			." href=\"".dir_lib("download.php?f=".rawurlencode($g_sRepDestRelatif.$oResSA->oRessourceAttache->retUrl()))."&amp;fn=1\""
			.">"
			.$oResSA->oRessourceAttache->retUrl()
			."</a>";
		
	if ($v_bEditable)
		echo "<tr>"
			."<td>&nbsp;</td>"
			."<td>"
			."<fieldset>"
			."<legend>"
			."&nbsp;<span class=\"intitule\">"
			.htmlentities("Attacher un document",ENT_COMPAT,"UTF-8")."&nbsp;:"
			."</span>&nbsp;"
			."</legend>"
			."<br>"
			."<input type=\"file\" name=\"evaluation_fichier\" size=\"50\" style=\"width: 100%;\">"
			."<br>"
			.(isset($sLienRessource) ? "<small>Fichier&nbsp;actuel&nbsp;: </small>{$sLienRessource}" : "<small>Pas de document trouvé</small>")
			.(isset($sLienRessource) && strlen($oResSA->oRessourceAttache->retUrl()) > 0 ? "<div style=\"color: rgb(127,157,185); text-align: right;\">[&nbsp;<a href=\"javascript: effacer_ressource(); void(0);\">Effacer</a>&nbsp;]</div>" : NULL)
			."</fieldset>"
			."</td>"
			."</tr>\n";
	else if (isset($sLienRessource))
		echo "<tr>"
			."<td>"
			."<span class=\"intitule\">Document&nbsp;attach&eacute;&nbsp;:</span>"
			."</td>"
			."<td>{$sLienRessource}</td>"
			."</tr>\n";
	// }}}
	
	echo "</table>\n";
	
	if ($v_bEditable)
		echo "<script type=\"text/javascript\" language=\"javascript\"><!--\n"
			."changerSousTitre('{$sSousTitre}');"
			."top.frames['Bas'].location = 'ressource_evaluation-menu.php?eval=1';"
			."\n//--></script>";
}

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style("evaluation_ressource.css"); ?>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('globals.js.php')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('window.js')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('dom.window.js')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('dom.element.js')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('outils_admin.js')?>"></script>
<script type="text/javascript" language="javascript">
<!--

function init()
{
	redimensionner();
}

function changerSousTitre(v_sSousTitre)
{
	var obj = top.frames["Haut"];
	
	if (obj && obj.changerSousTitre)
		obj.changerSousTitre(v_sSousTitre);
	else
		setTimeout("changerSousTitre('" + v_sSousTitre + "')",1000);
}

function effacer_ressource()
{
	if (confirm("Etes-vous certain de vouloir effacer ce documment attaché ?"))
	{
		top.oMenu().location = "ressource_evaluation-menu.php";
		document.forms[0].elements["ressource_attache_effacer"].value = "1";
		document.forms[0].submit();
	}
}

function editeur_callback(v_sForm,v_sElem,v_sTexte)
{
	document.forms[v_sForm].elements[v_sElem].value = v_sTexte;
}

function valider() { document.forms[0].submit(); }

function redimensionner()
{
	if (document.getElementById && document.getElementById("commentaire"))
	{
		var win = new DOMWindow(self);
		var commentaire = new DOMElement("commentaire");
		commentaire.setHeight(parseInt(win.innerHeight())-220);
		setTimeout("redimensionner()",1000);
	}
}
//-->
</script>
</head>
<body onload="init()">
<?php
$sSousTitre = "&nbsp;";

if (isset($_GET["idResSA"]))
{
	echo "<form"
		." name=\"formEval\""
		." action=\"".$_SERVER["PHP_SELF"]."\""
		." method=\"post\""
		." enctype=\"multipart/form-data\""
		.">\n";
	
	$oResSA = new CRessourceSousActiv($oProjet->oBdd,$_GET["idResSA"]);
	$oResSA->initEvaluations();
	$oResSA->initRessourceAttache();
	
	$sSousTitre = rawurlencode($oResSA->retNom());
	
	$bDejaUneEval = FALSE;
	
	$bAfficherEval = FALSE;
		
	for ($iIndexEval=0; $iIndexEval<count($oResSA->aoEvaluations); $iIndexEval++)
	{
		$oEval = &$oResSA->aoEvaluations[$iIndexEval];
		$oEval->initEvaluateur();
		
		if ($url_iIdPers == $oEval->oEvaluateur->retId())
		{
			$bAfficherEval = TRUE;
			break;
		}
	}
	
	if ($bAfficherEval)
	{
		$bEditable = ($g_bModeEdition && $g_iIdPers == $oEval->oEvaluateur->retId());
		
		if ($bEditable)
			$bDejaUneEval = TRUE;
		
		afficherEvaluation($oEval,$bEditable);
	}
	else if ($g_iIdPers == $url_iIdPers && $g_bModeEdition && $oProjet->verifTuteur())
		afficherEvaluation(NULL,TRUE);
	else
	{
		echo "<p>&nbsp;</p>"
			."<div align=\"center\">"
			."<div class=\"pas_encore_evaluer\">"
			.htmlentities("Ce tuteur n'a pas encore évalué ce document",ENT_COMPAT,"UTF-8")
			."</div>\n"
			."</div>\n";
		echo "<script type=\"text/javascript\" language=\"javascript\"><!--\n"
			."changerSousTitre('{$sSousTitre}');\n"
			."top.frames['Bas'].location = 'ressource_evaluation-menu.php';\n"
			."//--></script>\n";
	}
	
	echo "<input type=\"hidden\" name=\"ressource_attache_effacer\" value=\"0\">\n"
		."</form>\n";
}
else if ($_POST["ressource_attache_effacer"] == "1")
{
	// ---------------------
	// Effacer un document attaché
	// ---------------------
	$oResSA = new CRessourceSousActiv($oProjet->oBdd,$_POST["idResSA"]);
	$oResSA->effacerRessourceAttache($g_sRepDest);
	unset($oResSA);
	
	echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">"
		."<tr><td>&nbsp;</td></tr>"
		."<tr><td align=\"center\">"
		."<h3>".htmlentities("Le document attaché a bien été effacé",ENT_COMPAT,"UTF-8")."</h3>"
		."</td></tr>"
		."</table>\n";
}
else if (isset($_POST["idResSA"]))
{
	// ---------------------
	// Uploader le document attaché à une évaluation
	// ---------------------
	$sNomFichier = trim(stripslashes($_FILES["evaluation_fichier"]["name"]));
	
	if (strlen($sNomFichier) > 0)
	{
		include_once(dir_code_lib("upload.inc.php"));
		
		// Copier le fichier du répertoire temporaire vers sa destination
		$sNomFichierNouv = retNomFichierUnique($sNomFichier,$g_sRepDest);
		
		if (@move_uploaded_file($_FILES["evaluation_fichier"]["tmp_name"],$sNomFichierNouv))
		{
			$oResSA = new CRessourceSousActiv($oProjet->oBdd,$_POST["idResSA"]);
			$oResSA->initRessourceAttache();
			
			if ($oResSA->oRessourceAttache == NULL)
			{
				// Ajouter dans la table Ressource
				$oResAttache = new CRessource($oProjet->oBdd);
				$oResAttache->defNom($sNomFichier);
				$oResAttache->defUrl(basename($sNomFichierNouv));
				$oResAttache->defIdExped($g_iIdPers);
				
				// Attacher cette ressource à cette évaluation
				if (($iIdRes = $oResAttache->ajouter()) > 0)
					$oResSA->ajouterRessourceAttache($iIdRes);
			}
			else
			{
				// Effacer l'ancien fichier attaché à cette évaluation
				@unlink($g_sRepDest.$oResSA->oRessourceAttache->retUrl());
				
				$oResSA->oRessourceAttache->defNom($sNomFichier);
				$oResSA->oRessourceAttache->defUrl(basename($sNomFichierNouv));
				$oResSA->oRessourceAttache->enregistrer();
			}
		}
	}
	
	// ---------------------
	// Enregistrer l'évaluation
	// ---------------------
	$oProjet->oSousActivCourante->enregistrerEvaluation($_POST["idResSA"],
		$g_iIdPers,
		$_POST["appreciationEval"],
		$_POST["commentaireEval"],
		$_POST["statutEval"]);
	
	echo "<script type=\"text/javascript\" language=\"javascript\"><!--\n";
	echo "top.frames['Bas'].location = 'ressource_evaluation-menu.php';\n";
	echo "top.opener.recharger();\n";
	echo "setTimeout('top.close()',1000);\n";
	echo "\n//--></script>\n";
	
	echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">"
		."<tr><td>&nbsp;</td></tr>"
		."<tr><td align=\"center\">"
		."<h3>".htmlentities("L'évaluation a été enregistrée",ENT_COMPAT,"UTF-8")."</h3>"
		."</td></tr>"
		."</table>\n";
}
else
{
	echo "<br><h3>ERREUR : LA RESSOURCE N'A PAS ETE SPECIFIEE !!!</h3>";
}
?>
</body>
</html>
<?php $oProjet->terminer() ?>
