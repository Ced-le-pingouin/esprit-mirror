<?php

/*
** Fichier ................: confirm_cp_form.inc.php
** Description ............: 
** Date de cr�ation .......: 04-06-2002
** Derni�re modification ..: 15-10-2002
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

// *************************************
// Changer la formation courante
// *************************************

$oProjet->defFormationCourante($iIdForm);

// *************************************
// Copier enti�rement la formation s�lectionn�e
// *************************************

if (isset($bConfirmation) && $bConfirmation)
{
	// Identifiant de l'auteur de la formation
	$iIdPers = $oProjet->oUtilisateur->retId();
	
	$url_sNom         = (empty($HTTP_POST_VARS["formation_nom"]) ? NULL : $HTTP_POST_VARS["formation_nom"]);
	$url_sDescription = (empty($HTTP_POST_VARS["formation_description"]) ? NULL : $HTTP_POST_VARS["formation_description"]);
	
	if (($iIdForm = $oProjet->oFormationCourante->copier()) > 0)
	{
		$oProjet->defFormationCourante($iIdForm);
		
		$oProjet->oFormationCourante->defNom($url_sNom);
		$oProjet->oFormationCourante->defDescr($url_sDescription);
		$oProjet->oFormationCourante->defIdPers($iIdPers);
	}
	
	// Lorsqu'on copie une formation on devient responsable de cette formation
	$sRequeteSql = "REPLACE INTO Formation_Resp SET"
		." IdForm='{$iIdForm}'"
		.", IdPers='{$iIdPers}'";
	$oProjet->oBdd->executerRequete($sRequeteSql);
	
	if ($iIdForm > 0)
	{
		echo "<script language=\"javascript\" type=\"text/javascript\">\n"
			."<!--\n\n"
			."\ttop.opener.parent.frames['ADMINFRAMELISTE'].location=\"admin_liste.php"
			."?type=".TYPE_FORMATION
			."&params={$iIdForm}:0:0:0:0:0\";\n"
			."\ttop.close();"
			."\n//-->\n"
			."</script>\n";
		exit();
	}
}

?>

<style type="text/css">
<!--

.titre
{
	font-weight: bold;
	text-align: left;
}

//-->
</style>

<h5>Etape 3&nbsp;: Veuillez donner un nom et &eacute;ventuellement une description &agrave; cette formation</h5>
<table border="0" cellspacing="0" cellpadding="3" width="100%">
<?php
echo "<tr>"
	."<td class=\"titre\" nowrap=\"1\">Nom</td>"
	."<td width=\"5\">:</td>"
	."<td><input"
	." type=\"text\""
	." name=\"formation_nom\""
	." size=\"50\""
	." value=\"".(empty($url_sNomForm)
		? htmlentities($oProjet->oFormationCourante->retNom())
		: $url_sNomForm)
	."\""
	." style=\"width: 370px;\""
	."></td>"
	."</tr>\n"
	."<tr>\n"
	."<td class=\"titre\" valign=\"top\" nowrap=\"nowrap\">Description</td>"
	."<td valign=\"top\" width=\"5\">:</td>"
	."<td>"
	."<textarea name=\"formation_description\" cols=\"50\" rows=\"4\" style=\"width: 370px; height: 70px;\">"
	.(empty($url_sDescrForm)
		? $oProjet->oFormationCourante->retDescr()
		: html_entity_decode($url_sDescrForm))
	."</textarea>"
	."&nbsp;&nbsp;[&nbsp;<a href=\"javascript: void(0);\" onclick=\"editeur('FRM_GENERAL','formation_description','editeur')\" onfocus=\"blur()\">Editeur</a>&nbsp;]</span>"
	."</td>\n"
	."</tr>\n"
	."<tr>\n"
	."<td class=\"titre\" nowrap=\"1\">Date de cr&eacute;ation</td>"
	."<td width=\"5\">:</td>"
	."<td nowrap=\"1\">"
	.$oProjet->oFormationCourante->retDateDeb()
	."</td>"
	."</tr>";
?>
</table>
<p>Indiquer le nom de votre formation et une description de celle-ci. Si vous indiquez une description, celle-ci appara�tra � l��cran &laquo;&nbsp;Menu&nbsp;&raquo; sous la rubrique &laquo;&nbsp;Description&nbsp;&raquo; de la formation.</p>
<p><b><u>Note</u></b>&nbsp;: Vous pouvez changer ces �l�ments � tout moment � partir d'eConcept.</p>
