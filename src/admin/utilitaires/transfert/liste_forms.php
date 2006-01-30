<?php

/*
** Fichier .................: liste_formations.php
** Description ............:
** Date de création .......: 23/08/2004
** Dernière modification ..: 23/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sNomBdd    = $HTTP_GET_VARS["bdd"];
$url_iIdFormSrc = $HTTP_GET_VARS["idFormSrc"];

// ---------------------
// Ouvrir une connexion avec la base de données
// ---------------------
$oBdd = new CBddMySql($g_sNomServeurTransfert,$g_sNomProprietaireTransfert,$g_sMotDePasseTransfert,$url_sNomBdd);

// ---------------------
// Rechercher toutes les formations appartenant à cette base de données
// ---------------------
$sRequeteSql = "SELECT * FROM Formation"
	." WHERE StatutForm<>'".STATUT_EFFACE."'"
	." ORDER BY NomForm ASC";
$hResult = $oBdd->executerRequete($sRequeteSql);

$sListeFormation = NULL;

while ($oEnreg = $oBdd->retEnregSuiv($hResult))
{
	$sListeFormation .= "<tr>"
		."<td style=\"background-color: rgb(233,230,213);\">"
		."<input"
		." type=\"radio\""
		." name=\"id_form_select\""
		." value=\"{$oEnreg->IdForm}\""
		." onclick=\"top.changer_id_form(this.value)\""
		." onfocus=\"blur()\""
		.($oEnreg->IdForm == $url_iIdFormSrc ? " checked" : NULL)
		."></td>"
		."<td style=\"border: rgb(174,165,138) none 1px; border-bottom-style: dashed; font-weight: normal;\">".stripslashes($oEnreg->NomForm)."</td>"
		."<tr>\n";
}

$oBdd->libererResult($hResult);

$oBdd->terminer();
?>
<html>
<head>
<?php inserer_feuille_style(); ?>
</head>
<body style="background-image: none; background-color: rgb(251,249,238);">
<table border="0" cellspacing="0" cellpadding="3" width="100%">
<?php echo $sListeFormation; ?>
</table>
</body>
</html>
