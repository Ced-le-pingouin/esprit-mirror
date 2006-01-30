<?php

/*
** Fichier ................: connexion.php
** Description ............: 
** Date de cr�ation .......: 24/02/2003
** Derni�re modification ..: 03/09/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("evenement.tbl.php"));

$oProjet = new CProjet();
$oEvenement = new CEvenement($oProjet->oBdd);

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iTri     = (isset($HTTP_GET_VARS["tri"]) ? $HTTP_GET_VARS["tri"] : $oEvenement->TRI_DERNIERE_CONNEXION);
$url_bSensTri = (isset($HTTP_GET_VARS["sens"]) ? $HTTP_GET_VARS["sens"] : $oEvenement->TRI_DESCENDANT);
$url_iIdPers  = (empty($HTTP_GET_VARS["idPers"]) ? 0 : $HTTP_GET_VARS["idPers"]);

// ---------------------
// Initialiser
// ---------------------
$iStatut = $oProjet->retStatutUtilisateur();
$iIdForm = $oProjet->oFormationCourante->retId();

// ---------------------
// ---------------------
$sContenuTable = NULL;

$sFlecheVersHaut = "&nbsp;<img src=\"".dir_theme("sort-incr.gif")."\" border=\"0\">";
$sFlecheVersBas = "&nbsp;<img src=\"".dir_theme("sort-desc.gif")."\" border=\"0\">";

$oEvenement->defIdFormation($iIdForm);
$oEvenement->defModeTri($url_iTri);
$oEvenement->defTriAscendant($url_bSensTri);

$iNbrEven = $oEvenement->initEvenements();

// Nom/Trier ?/Identifiant du tri
$asTitres = array(
	array("#",FALSE,NULL),
	array("Nom",TRUE,$oEvenement->TRI_NOM),
	array("Pr�nom",TRUE,$oEvenement->TRI_PRENOM),
	array("Pseudo",TRUE,$oEvenement->TRI_PSEUDO),
	array("Nombre de connexions",TRUE,$oEvenement->TRI_NBR_CONNEXIONS),
	array("Derni�re connexion",TRUE,$oEvenement->TRI_DERNIERE_CONNEXION),
	array("D�connexion",TRUE,$oEvenement->TRI_DERNIERE_DECONNEXION),
	array("Dur�e",TRUE,$oEvenement->TRI_TEMPS_CONNEXIONS),
	array("-",FALSE,NULL));

$sContenuTable .= "<tr>";

for ($i=0; $i<count($asTitres); $i++)
{
	$sContenuTable .= "<td"
		." class=\"cellule_sous_titre\""
		.">";
	
	if ($asTitres[$i][1])
		$sContenuTable .= "<a href=\"javascript: self.location='connexion.php"
		."?tri={$asTitres[$i][2]}"
		."&sens=".($url_iTri == $asTitres[$i][2] && $url_bSensTri == $oEvenement->TRI_DESCENDANT ? 1 : 0).""
		."&idPers={$url_iIdPers}';\""
		." target=\"_self\">"
		.$asTitres[$i][0]
		."</a>"
		.($url_iTri == $asTitres[$i][2] ? ($url_bSensTri == $oEvenement->TRI_ASCENDANT ? $sFlecheVersHaut : $sFlecheVersBas) : NULL);
	else
		$sContenuTable .= $asTitres[$i][0];

	$sContenuTable .= "</td>";
}

$sContenuTable .= "</tr>";

$sNomClasseCss = NULL;

for ($iIdxEven=0; $iIdxEven<$iNbrEven; $iIdxEven++)
{
	if (!is_object($oEvenement->aoEvenements[$iIdxEven]->oConnecte) ||
		($url_iIdPers > 0 && $oEvenement->aoEvenements[$iIdxEven]->oConnecte->retId() != $url_iIdPers))
		continue;
		
	$sNomClasseCss = (isset($sNomClasseCss) ? NULL : " class=\"cellule_clair\"");
	$sNomClasseCssTri = (isset($sNomClasseCss) ? " class=\"cellule_clair_fonce\"" : " class=\"cellule_clair\"");
	
	$sContenuTable .= "<tr>"
		."<td class=\"numero_ligne\" align=\"right\">"
		.($iIdxEven+1)
		."</td>"
		."<td".($url_iTri == $oEvenement->TRI_NOM ? $sNomClasseCssTri : $sNomClasseCss)." nowrap=\"true\">"
		.$oEvenement->aoEvenements[$iIdxEven]->oConnecte->retNom()
		."</td>"
		."<td".($url_iTri == $oEvenement->TRI_PRENOM ? $sNomClasseCssTri : $sNomClasseCss)." align=\"center\" nowrap=\"true\">"
		.$oEvenement->aoEvenements[$iIdxEven]->oConnecte->retPrenom()
		."</td>"
		."<td".($url_iTri == $oEvenement->TRI_PSEUDO ? $sNomClasseCssTri : $sNomClasseCss)." width=\"1%\" align=\"center\" nowrap=\"true\">"
		.$oEvenement->aoEvenements[$iIdxEven]->oConnecte->retPseudo()
		."</td>"
		."<td".($url_iTri == $oEvenement->TRI_NBR_CONNEXIONS ? $sNomClasseCssTri : $sNomClasseCss)." width=\"1%\" align=\"center\">"
		.$oEvenement->aoEvenements[$iIdxEven]->retNbrConnexion()
		."</td>"
		."<td".($url_iTri == $oEvenement->TRI_DERNIERE_CONNEXION ? $sNomClasseCssTri : $sNomClasseCss)." width=\"1%\" align=\"center\" nowrap=\"true\">"
		.$oEvenement->aoEvenements[$iIdxEven]->retDateDerniereConnexion()
		."</td>"
		."<td".($url_iTri == $oEvenement->TRI_DERNIERE_DECONNEXION ? $sNomClasseCssTri : $sNomClasseCss)." width=\"1%\" align=\"center\" nowrap=\"true\">"
		.$oEvenement->aoEvenements[$iIdxEven]->retDateDerniereDeconnexion()
		."</td>"
		."<td".($url_iTri == $oEvenement->TRI_TEMPS_CONNEXIONS ? $sNomClasseCssTri : $sNomClasseCss)." width=\"1%\" align=\"center\">"
		.$oEvenement->aoEvenements[$iIdxEven]->retTempsConnexion(TRUE)
		."</td>"
		."<td{$sNomClasseCss} width=\"1%\" align=\"center\">&nbsp;"
		."<a href=\"javascript: details('".$oEvenement->aoEvenements[$iIdxEven]->oConnecte->retId()."');\""
		." title=\"Cliquez ici pour voir la liste compl�te des connexions\""
		." onfocus=\"blur()\">D&eacute;tails</a>"
		."&nbsp;</td>"
		."</tr>\n";
}

$oProjet->terminer();
?>
<html>
<head>
<?php inserer_feuille_style("trace.css"); ?>
<script type="text/javascript" language="javascript">
<!--

var winDetail = null;

function details(v_iIdPers)
{
	var sURL = "detail_connexion-index.php?idPers=" + v_iIdPers;
	var sNomFenetre = "WinDetail";
	var sCaracteristiques = "width=640,height=480,resizable=1";
	
	winDetail = open(sURL,sNomFenetre,sCaracteristiques);
	winDetail.focus();
}

function Recharger()
{
	self.location = "<?=$HTTP_SERVER_VARS['PHP_SELF']?>";
	top.oExporter().location = "<?=dir_admin("espion","exporter_connexion.php",FALSE)?>";
}

function fermerToutesFenetres()
{
	if (winDetail != null && !winDetail.closed)
		winDetail.close();
}

//-->
</script>
</head>
<body onunload="fermerToutesFenetres()">
<table border="0" cellpadding="2" cellspacing="1" width="100%">
<?=$sContenuTable?>
</table>
</body>
</html>

