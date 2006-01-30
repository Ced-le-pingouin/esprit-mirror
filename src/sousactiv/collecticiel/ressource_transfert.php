<?php

/*
** Fichier ................: transferer_fichiers.php
** Description ............:
** Date de création .......: 27/11/2002
** Dernière modification ..: 05/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
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
$url_iIdPers   = (isset($HTTP_GET_VARS["idPers"]) ? $HTTP_GET_VARS["idPers"] : (isset($HTTP_POST_VARS["idPers"]) ? $HTTP_POST_VARS["idPers"] : 0));
$url_iModalite = (empty($HTTP_GET_VARS["idModalite"]) ? NULL : $HTTP_GET_VARS["idModalite"]);
$url_sTriCol   = (isset($HTTP_GET_VARS["TRICOL"]) ? $HTTP_GET_VARS["TRICOL"] : "titre");
$url_iTriDir   = (isset($HTTP_GET_VARS["TRIDIR"]) ? $HTTP_GET_VARS["TRIDIR"] : "1");

// ---------------------
// Initialisations
// ---------------------
$sTransfert = NULL;

// ---------------------------
// Construction de la liste des documents
// ---------------------------
if (!is_object($oProjet->oSousActivCourante))
{
	echo "<html>"
		."<head>".inserer_feuille_style()."</head>"
		."<body>"
		."<div align=\"center\">"
		."<p class=\"Texte_Negatif\"><b>Erreur</b>&nbsp;:</p>"
		."<pre>Veuillez fermer cette fenêtre</pre>"
		."</div>"
		."</body></html>\n";
	
	$oProjet->terminer();
	
	exit();
}

$iNbrRessources = $oProjet->oSousActivCourante->initRessources($url_sTriCol,$url_iTriDir,$url_iModalite,$url_iIdPers,TRANSFERT_FICHIERS,NULL);
$url_iTriDir = -$url_iTriDir;
$amEntetes = array(
	array("Titre","titre"),
	array("D&eacute;pos&eacute;&nbsp;par","depose"),
	array("&Eacute;tat","etat"),
	array("Transf&eacute;r&eacute;",NULL));

$sListeFichiersCollecticiel = "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"100%\">\n"
	."<tr>";

for ($i=0; $i<count($amEntetes); $i++)
{
	$sListeFichiersCollecticiel .= "<td class=\"cellule_sous_titre\">";

	if (isset($amEntetes[$i][1]))
		$sListeFichiersCollecticiel .= "&nbsp;<a class=\"Lien_Sous_Titre\" href=\"javascript: envoyer('{$amEntetes[$i][1]}','".($amEntetes[$i][1] == $url_sTriCol ? $url_iTriDir : 1)."');\""
			.">{$amEntetes[$i][0]}</a>&nbsp;"
			.($amEntetes[$i][1] == $url_sTriCol ? "&nbsp;<img src=\"".dir_theme(($url_iTriDir > 0 ? "sort-desc.gif" : "sort-incr.gif"))."\" border=\"0\">" : NULL);
	else
		$sListeFichiersCollecticiel .= "&nbsp;".$amEntetes[$i][0]."&nbsp;";

	$sListeFichiersCollecticiel .= "</td>";
}

$sListeFichiersCollecticiel .= "<td class=\"cellule_sous_titre\" width=\"1%\">"
	."<input type=\"checkbox\""
	." name=\"idResSA\""
	." onclick=\"select_deselect_checkbox(this)\""
	." onfocus=\"blur()\">"
	."</td>";

$sListeFichiersCollecticiel .= "</tr>\n";

$poRessources = &$oProjet->oSousActivCourante->aoRessources;

$sNomClassCss = NULL;

$iCptRes = 0;

for ($i=0; $i<$iNbrRessources; $i++)
{
	$poRessources[$i]->initExpediteur();
	
	$sNomClassCss = ($sNomClassCss == "cellule_clair" ? "cellule_fonce" : "cellule_clair");
	
	$bTransfere = $poRessources[$i]->retTransfere();
	
	/*if ($oProjet->oActivCourante->retModalite() == MODALITE_PAR_EQUIPE &&
		$poRessources[$i]->retStatut() <> STATUT_RES_TRANSFERE)
	{
		$poRessources[$i]->initEquipe();
		$sDeposePar = $poRessources[$i]->oEquipe->retNom()."&nbsp;<img src=\"".dir_theme("equipe.gif")."\" border=\"0\">";
	}
	else*/
	
	if ($poRessources[$i]->retStatut() == STATUT_RES_TRANSFERE && is_object($poRessources[$i]->oEquipe))
		$sDeposePar = $poRessources[$i]->oEquipe->retNom();
	else if (is_object($poRessources[$i]->oExpediteur))
		$sDeposePar = $poRessources[$i]->oExpediteur->retNomComplet();
	else
		$sDeposePar = "-";
	
 	$bFichierExiste = file_exists(dir_collecticiel($oProjet->oFormationCourante->retId(),$oProjet->oActivCourante->retId(),$poRessources[$i]->retUrl(),TRUE));

	$sListeFichiersCollecticiel .= "<tr>"
		."<td class=\"$sNomClassCss\">&nbsp;<b>".$poRessources[$i]->retNom()."</b></td>"
		."<td class=\"$sNomClassCss\" align=\"center\">{$sDeposePar}</td>"
		."<td class=\"$sNomClassCss\" align=\"center\">".$poRessources[$i]->retTexteStatut()."</td>";

	$sListeFichiersCollecticiel .= "<td class=\"$sNomClassCss\" align=\"center\" width=\"1%\">";
	
	if ($poRessources[$i]->retStatut() == STATUT_RES_TRANSFERE)
		// On ne peut pas vérifier qu'un fichier transféré
		// a été lui aussi transféré vers un autre collecticiel
		$sListeFichiersCollecticiel .= "-";
	else if ($bFichierExiste)
		$sListeFichiersCollecticiel .= ($bTransfere ? "oui" : "<span class=\"Texte_Negatif\">non</span>");
	else
		$sListeFichiersCollecticiel .= "Fichier&nbsp;absent du&nbsp;serveur";
		
	$sListeFichiersCollecticiel .= "</td>";

	$sListeFichiersCollecticiel .= "<td class=\"$sNomClassCss\" width=\"1%\" align=\"center\">";
	
	if ($bTransfere || !$bFichierExiste)
	{
		$sListeFichiersCollecticiel .= "-";
	}
	else
	{
		$sListeFichiersCollecticiel .= "<input type=\"checkbox\" name=\"idResSA[]\""
			." value=\"".$poRessources[$i]->retId()."\""
			." onclick=\"verif_checkbox_principal(this)\""
			." onfocus=\"blur()\">";
		
		$iCptRes++;
	}
	
	$sListeFichiersCollecticiel .= "</td>"
		."</tr>\n";
}

if ($iNbrRessources < 1)
	$sListeFichiersCollecticiel .= "<tr><td class=\"cellule_clair\" colspan=\"5\"><div style=\"text-align: center;\"><small>Aucun document à transférer</small></div></td></tr>\n";
		
$sListeFichiersCollecticiel .= "</table>\n";

// ---------------------------
// Construction de la liste des collecticiels
// ---------------------------

$iNbrCollecticiels = $oProjet->oRubriqueCourante->initCollecticiels(NULL,$url_iIdPers);

$sListeCollecticielEquipe = "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";

$bChecked = TRUE;

$iCptCollect = 0;

for ($i=0; $i<$iNbrCollecticiels; $i++)
{
	// Ne pas afficher le collecticiel de cette sous-activité
	if ($oProjet->oRubriqueCourante->aoCollecticiels[$i]->retId() == $oProjet->oSousActivCourante->retId())
		continue;
	
	$iCptCollect++;
	
	$sListeCollecticielEquipe .= "<tr>"
		."<td>&nbsp;&nbsp;</td>"
		."<td>"
		."<input type=\"radio\""
		." name=\"idSADest\""
		." value=\"".$oProjet->oRubriqueCourante->aoCollecticiels[$i]->retId()."\""
		." onfocus=\"blur()\""
		.($bChecked ? " checked" : NULL)
		.">"
		."</td>"
		."<td>".$oProjet->oRubriqueCourante->aoCollecticiels[$i]->retNom()."</td>"
		."</tr>\n";

	$bChecked = FALSE;
}

// ---------------------------
// Afficher ou non le lien qui permettra d'envoyer les fichiers
// ---------------------------

$sLienTransfere = NULL;

if ($iCptRes > 0 && $iCptCollect > 0)
	$sLienTransfere = "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\">"
		."<tr>"
		."<td>&nbsp;&nbsp;</td>"
		."<td>"
		."<a href=\"javascript: document.forms[0].submit();\" onfocus=\"blur()\">"
		."Cliquez ici, pour transférer les fichiers sélectionnés"
		."</a>"
		."</td>"
		."</tr>"
		."</table>\n";

$sListeCollecticielEquipe .= "</table>\n";

unset($iNbrRessources,$iNbrCollecticiels);

$oProjet->terminer();

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php inserer_feuille_style(); ?>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript('globals.js'); ?>"></script>
<script type="text/javascript" language="javascript">
<!--

function envoyer(v_sTri,v_iTriDirection)
{
	document.location = "ressource_transfert.php?idPers=<?=$url_iIdPers?>&idModalite=<?=$url_iModalite?>&TRICOL=" + v_sTri + "&TRIDIR=" + v_iTriDirection;
}

function init()
{
<?php
if (isset($HTTP_POST_VARS["TRANSFERT"]))
{
	$sParamsUrl = NULL;
	
	if (isset($HTTP_POST_VARS["idResSA"]))
		$sParamsUrl = "?idSADest=".$HTTP_POST_VARS["idSADest"]."&idResSA=".implode("x",$HTTP_POST_VARS["idResSA"]);
	
	echo "\tvar w=self.open('ressource_transfert_result-index.php{$sParamsUrl}','TransfertResult','width=600,height=400,toolbar=0,resizable=1');\n";
		//."\tw.focus();\n";
}
?>
	return;
}

//-->
</script>
</head>
<body onload="init()">
<?php if (isset($sTransfert)) echo $sTransfert; ?>
<form action="ressource_transfert.php" method="post" target="_self">
<h4>1.&nbsp;&nbsp;Choisissez les fichiers que vous voulez transférer vers un autre collecticiel</h4>
<?php echo $sListeFichiersCollecticiel; ?>
<h4>2.&nbsp;&nbsp;Choisissez un collecticiel de destination</h4>
<?php echo $sListeCollecticielEquipe; ?>
<h4>3.&nbsp;&nbsp;Commencez le transfert</h4>
<?php echo $sLienTransfere; ?>
<input type="hidden" name="idPers" value="<?php echo $url_iIdPers; ?>">
<input type="hidden" name="idModalite" value="<?php echo $url_iModalite; ?>">
<input type="hidden" name="TRANSFERT" value="1">

</form>
</body>
</html>
