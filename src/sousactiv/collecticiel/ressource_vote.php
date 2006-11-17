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
** Fichier .................: ressource_vote.php
** Description .............: 
** Date de création ........: 01/03/2001
** Dernière modification ...: 25/04/2005
** Auteurs .................: Cédric FLOQUET <cedric.floquet@umh.ac.be>
**                            Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

if (!is_object($oProjet->oSousActivCourante) ||
	!is_object($oProjet->oActivCourante))
{
	$oProjet->terminer();
	
	erreurFatale("Le numéro de l'activité/sous-activité est inférieur à 1");
}

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_aiIdResSAVotes = (empty($_GET["idResSAVotes"]) ? NULL : $_GET["idResSAVotes"]);
$url_bVoter         = (empty($_GET["voter"]) ? FALSE : TRUE);

// ---------------------
// Initialiser
// ---------------------
$g_iIdPers   = $oProjet->retIdUtilisateur();
$g_iModalite = $oProjet->oSousActivCourante->retModalite(TRUE);

// Rechercher la modalité de l'activité (individuel/par équipe)
// Rechercher les membres de cette équipe car elle sera utilisée lors des votes
// des étudiants
if (MODALITE_PAR_EQUIPE == $g_iModalite)
{
	$oProjet->initEquipe(TRUE);
	$g_iIdEquipe = $oProjet->oEquipe->retId();
	$oProjet->oSousActivCourante->oEquipe = &$oProjet->oEquipe;
}
else
{
	$url_bVoter  = ($g_iIdPers > 0);
	$g_iIdEquipe = 0;
}

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style(); ?>
<script type="text/javascript" language="javascript">
<!--

function voter() { top.voter = true; document.formVoter.submit(); }

function annuler() {
	top.opener.nettoyer();
	top.close();
}

//-->
</script>
</head>
<body>
<?php

// {{{ L'édutiant a soumis son document
if ($url_bVoter && isset($url_aiIdResSAVotes))
{
	// Transformer en tableau
	settype($url_aiIdResSAVotes,"array");
	
	// {{{ Enregistrer le vote de l'étudiant dans la base de données
	$oResSA = new CRessourceSousActiv($oProjet->oBdd,$url_aiIdResSAVotes[0]);
	$bResSASoumis = $oProjet->oSousActivCourante->voterPourRessource($oResSA->retId(),$g_iIdPers);
	unset($oResSA);
	// }}}
	
	if ($bResSASoumis)
	{
		echo "<p>&nbsp;</p>"
			."<p align=\"center\">";
		
		if ($g_iIdEquipe > 0)
			echo emb_htmlentities("Vous venez de voter pour ce document.")
				."<br>"
				.htmlentities("Celui-ci a obtenu le nombre de votes requis."
				." Il a donc été soumis au tuteur pour évaluation.",ENT_COMPAT,"UTF-8");
		else
			echo emb_htmlentities("Votre document a été soumis au tuteur pour évaluation");
		
		echo "</p>\n"
			."<script type=\"text/javascript\" language=\"javascript\"><!--\n"
			."top.window.opener.location = top.window.opener.location;\n"
			."top.frames['Bas'].location = 'ressource_vote-menu.php';"
			."\n//--></script>"
			."</body></html>";
		
		$oProjet->terminer();
		
		exit();
	}
}
// }}}

if ($g_iIdEquipe > 0)
{
	$oProjet->oSousActivCourante->initRessources("date",TRI_DECROISSANT,MODALITE_PAR_EQUIPE,$g_iIdEquipe);
	
	$iPourcentVotes = $oProjet->oSousActivCourante->retVotesMin();
	$iNbVotesRequis = $oProjet->oSousActivCourante->retVotesMinReels();
	
	echo "<div align=\"center\">"
		."<p>"
		."Vous pouvez voir ci-dessous la liste des documents &laquo;&nbsp;en&nbsp;cours&nbsp;&raquo;."
		."<br>"
		."Vous ne pouvez voter que pour un seul document mais vous pouvez changer d'avis."
		."</p>"
		."</div>\n";
	
	echo "<form name=\"formVoter\" action=\"".$_SERVER["PHP_SELF"]."\" method=\"get\">\n";
	
	echo "<TABLE border=\"0\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">\n";
	echo "<TR>\n";
	echo "<TD class=\"cellule_sous_titre\" align=\"center\" height=\"20\">Titre</TD>\n";
	echo "<TD class=\"cellule_sous_titre\" align=\"center\" height=\"20\">Total votes</TD>\n";
	echo "<TD class=\"cellule_sous_titre\" align=\"center\" height=\"20\">Qui a voté ?</TD>\n";
	echo "<TD class=\"cellule_sous_titre\" align=\"center\" height=\"20\">Je vote</TD>\n";
	echo "</TR>\n";
	
	$CssStyle = NULL;
	
	foreach ($oProjet->oSousActivCourante->aoRessources as $oResSA)
	{
		if (STATUT_RES_EN_COURS != $oResSA->retStatut())
			continue;
		
		$oResSA->initVotants();
		
		$CssStyle = (($CssStyle == "cellule_clair") ? "cellule_fonce":"cellule_clair");
		
		echo "<TR>\n";
		
		echo "<TD class=\"{$CssStyle}\" align=\"center\" width=\"150px\"><B>";
		echo htmlentities($oResSA->retNom());
		echo "</B>";
		//echo "<BR>\n";
		//echo "déposé par ".$oResSA->oExpediteur->retLienEmail();
		echo "</TD>\n";
		
		$iNbVotes = count($oResSA->aoVotants);
		echo "<td class=\"{$CssStyle}\" align=\"center\">{$iNbVotes}/{$iNbVotesRequis}</td>\n";
		
		echo "<td class=\"{$CssStyle}\" align=\"center\">";
		
		$bAVotePourCeDoc = FALSE;
		
		if ($iNbVotes)
		{
			for ($iIdxVotant=0; $iIdxVotant<$iNbVotes; $iIdxVotant++)
			{
				if ($oResSA->aoVotants[$iIdxVotant]->retId() == $oProjet->oUtilisateur->retId())
				{
					$bAVotePourCeDoc = TRUE;
					echo "<b>".$oResSA->aoVotants[$iIdxVotant]->retLienEmail()."</b><br>";
				}
				else
					echo $oResSA->aoVotants[$iIdxVotant]->retLienEmail()."<br>";
			}
		}
		else
			echo "-";
		
		echo "</TD>\n";
		
		echo "<td class=\"{$CssStyle}\" align=\"center\">"
			."<input"
			." type=\"radio\""
			." name=\"idResSAVotes[]\""
			." value=\"".$oResSA->retId()."\""
			." onfocus=\"blur()\""
			.($bAVotePourCeDoc ? " checked" : NULL)
			.">"
			."</td>\n";
		
		echo "</tr>\n";
	}
	
	// Afficher le bouton pour pouvoir voter
	echo "<tr>"
		."<td colspan=\"4\" align=\"right\">"
		."<a href=\"javascript: voter();\">"
			.emb_htmlentities("Je vote pour ma sélection")
		."</a>"
		."</td>"
		."</tr>\n";
	
	echo "</table>\n";
	
	echo "<input type=\"hidden\" name=\"voter\" value=\"1\">\n";
	
	echo "</form>\n";
	
	echo "<p>&nbsp;</p>"
		."<div align=\"center\">"
		."<p>"
		."Un document doit obtenir <b>{$iPourcentVotes}%</b> des votes pour être soumis au tuteur."
		."<br>"
		."Dans le cas de votre équipe, cela représente <b>{$iNbVotesRequis} vote".($iNbVotesRequis > 1 ? "s" : NULL)."</b>."
		."</p>"
		."</div>\n";
}
else if ($g_iIdPers > 0)
{
	$oProjet->oSousActivCourante->initRessources("date",TRI_DECROISSANT,MODALITE_INDIVIDUEL,$g_iIdPers,STATUT_RES_EN_COURS);
	
	// ...si on n'a pas encore confirmé la soumission de son document (mais qu'il a
	// été sélectionné)
	if (isset($aiIdResSA))
	{
		echo "<form"
			." name=\"formVoter\""
			." action=\"".$_SERVER["PHP_SELF"]."\""
			." method=\"post\""
			.">"; // <form>
		
		echo "<p align=\"center\">Voulez-vous soumettre ce fichier"
			." au(x) tuteur(s) pour évaluation&nbsp;?</p>\n";
		
		echo "<table width=\"70%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\" align=\"center\"><tr><td>\n";
		echo "<table width=\"100%\" height=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><td align=\"center\">\n";
		
		foreach ($aiIdResSA as $iIdResSA)
		{
			$oRes = new CRessourceSousActiv($oProjet->oBdd,$iIdResSA);
			echo emb_htmlentities($oRes->retNom());
			echo "<input type=\"hidden\" name=\"resVotes[]\" value=\"{$iIdResSA}\">";
			unset($oRes);
		}
		
		echo "</td></tr></table>\n";
		echo "</td></tr></table>\n";
		
		echo "</form>";
		
		echo "<script type=\"text/javascript\" language=\"javascript\"><!--\n"
			."top.frames['Bas'].location = 'ressource_vote-menu.php?voter=1';"
			."\n//--></script>";
	}
	else
	{
		// ...si pas de vote, ni de document à soumettre sélectionné sur la page précédente => Erreur
		echo "<p>&nbsp;</p><p align=\"center\">Aucun document n'a été sélectionné&nbsp;!</p>\n";
		echo "<script type=\"text/javascript\" language=\"javascript\"><!--\n"
			."top.frames['Bas'].location = 'ressource_vote-menu.php';"
			."\n//--></script>";
	}
}

?>
</body>
</html>
<?php $oProjet->terminer(); ?>
