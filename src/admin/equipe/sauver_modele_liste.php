<?php

/*
** Fichier ................: sauver_modele_liste.php
** Description ............: 
** Date de création .......: 14-01-2003
** Dernière modification ..: 10-02-2003
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

require_once("globals.inc.php");

// *************************************
//
// *************************************

$oProjet = new CProjet();

// *************************************
//
// *************************************

$amVariablesUrl = array(
	array("NIVEAU",0),array ("ID_NIVEAU",0),array("ACTION",NULL),array("NOM_FICHIER",NULL),array("DESCRIPTION",NULL)
	);

$aVarsURL = array();
	
foreach ($amVariablesUrl as $amVariableUrl)
{
	if (!empty($HTTP_POST_VARS[$amVariableUrl[0]]))
		$aVarsURL[$amVariableUrl[0]] = $HTTP_POST_VARS[$amVariableUrl[0]];
	else if (!empty($HTTP_GET_VARS[$amVariableUrl[0]]))
		$aVarsURL[$amVariableUrl[0]] = $HTTP_GET_VARS[$amVariableUrl[0]];
	else
		$aVarsURL[$amVariableUrl[0]] = $amVariableUrl[1];
}

// *************************************
// Rechercher les équipes ainsi que leurs membres
// *************************************

$oEquipe = new CEquipe($oProjet->oBdd);

$iNbrEquipes = $oEquipe->initEquipesNiveau($aVarsURL["NIVEAU"],$aVarsURL["ID_NIVEAU"],TRUE);

$sCorpHtml = "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">\n";

$test = new CModele($oProjet->oUtilisateur->retNomComplet(),date("j-m-Y"));

$test->sDescription = $aVarsURL["DESCRIPTION"];

for ($iIdxEquipe=0; $iIdxEquipe<$iNbrEquipes; $iIdxEquipe++)
{		
	$sCorpHtml .= "<tr>"
		."<td colspan=\"2\" class=\"cellule_sous_titre\">"
		."<b>".$oEquipe->aoEquipes[$iIdxEquipe]->retNom()."</b>"
		."</td></tr>\n";
	
	$aiIdPers = array();
	
	for ($iIdxMembre=0; $iIdxMembre<count($oEquipe->aoEquipes[$iIdxEquipe]->aoMembres); $iIdxMembre++)
	{
		$aiIdPers[] = $oEquipe->aoEquipes[$iIdxEquipe]->aoMembres[$iIdxMembre]->retId();
		
		$sCorpHtml .= "<tr>"
			."<td width=\"1%\" class=\"cellule_fonce\">&nbsp;&nbsp;</td>"
			."<td class=\"cellule_clair\">"
			.$oEquipe->aoEquipes[$iIdxEquipe]->aoMembres[$iIdxMembre]->retNomComplet()
			."</td></tr>\n";
	}

	$test->ajouterEquipe($oEquipe->aoEquipes[$iIdxEquipe]->retNom());
	$test->ajouterMembres($aiIdPers);

	if ($iIdxMembre <= 0)
		$sCorpHtml .= "<tr>"
			."<td class=\"cellule_clair\" colspan=\"2\" style=\"text-align: center;\">"
			."<span class=\"Attention\">Pas de membre</span>"
			."</td>"
			."</tr>\n";
}

$sCorpHtml .= "</table>\n";

// *************************************
//
// *************************************

if ($aVarsURL["ACTION"] == "enregistrer")
{
	$s = serialize($test);

	$sNomFichier = rawurldecode(dir_modeles("equipes",$aVarsURL["NOM_FICHIER"],TRUE));

	$fp = fopen($sNomFichier,"w");
	fwrite($fp,$s);
	fclose($fp);

	return;
}

// *************************************
//
// *************************************

$oProjet->terminer();

?>

<html>
<head><?php inserer_feuille_style("menu"); ?></head>
<body>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td style="background-color: #FFFFFF;">
<?php echo $sCorpHtml; ?>
</td></tr>
</table>
</body>
</html>
