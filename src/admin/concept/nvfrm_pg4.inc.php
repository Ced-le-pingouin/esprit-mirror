<?php

if ($fonction == "valider")
{
	if (!is_object($oProjet->oUtilisateur))
		exit();
	
	$iIdPers = $oProjet->oUtilisateur->retId();
	
	if ($iIdPers > 0)
	{
		$url_sNomForm = html_entity_decode($url_sNomForm);
		$url_sDescrForm = html_entity_decode($url_sDescrForm);
		
		if ($type == NOUVELLE_FORMATION)
		{
			$oFormation = new CFormation($oProjet->oBdd);
			$iIdForm = $oFormation->ajouter($url_sNomForm,$url_sDescrForm,$url_iInscrSpontForm,$iIdPers);
			unset($oFormation);
		}
		else if ($type == COPIER_FORMATION)
		{
			// Copier cette formation
			$oFormation = new CFormation($oProjet->oBdd,$iIdForm);
			$iIdForm = $oFormation->copier();
			unset($oFormation);
			
			// Avec la nouvelle copie, faisons quelques modifications
			$oProjet->defFormationCourante($iIdForm);
			$oProjet->oFormationCourante->defNom($url_sNomForm);
			$oProjet->oFormationCourante->defDescr($url_sDescrForm);
			$oProjet->oFormationCourante->defInscrAutoModules($url_iInscrSpontForm);
			$oProjet->oFormationCourante->associerResponsable($iIdPers);
		}
	}
	
	if ($iIdForm > 0)
	{
		// Nous allons cr�er le r�pertoire de la formation
		mkdir(dir_formation($iIdForm,NULL,TRUE));
		
		// Se placer sur la nouvelle formation dans eConcept et puis on ferme
		// cette fen�tre
		echo "<script type=\"text/javascript\" language=\"javascript\"><!--\n"
			."top.opener.top.location.replace('"
			."econcept-index.php"
			."?type=".TYPE_FORMATION
			."&params={$iIdForm}:0:0:0:0:0"
			."&idForm={$iIdForm}" // Permet d'afficher cette formation dans eConcept
			."');\n"
			."top.close();\n"
			."//--></script>\n";
		exit();
	}
}

?>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td colspan="2"><h5>Etape <?=$etape?>&nbsp;: Modalit� d'inscription des �tudiants aux cours</h5></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td><img src="<?=dir_images_communes('espacer.gif')?>" width="20" height="1" border="0"></td><td><input type="radio" name="InscrSpontForm" value="1"<?=($url_iInscrSpontForm == 1 ? " checked" : NULL)?>>&nbsp;&nbsp;Tous les �tudiants seront automatiquement inscrits � tous les cours de cette formation</td></tr>
<tr><td>&nbsp;</td><td><input type="radio" name="InscrSpontForm" value="0"<?=($url_iInscrSpontForm == 1 ? NULL : " checked")?>>&nbsp;&nbsp;Certains �tudiants seront inscrits � certains cours, d'autres pas</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">La premi�re option vous dispense de devoir sp�cifier pour chaque �tudiant les cours auxquels il est inscrit, ce qui peut s'av�rer fastidieux.
 Choisissez donc cette option si vous vous trouvez dans un cas de figure o� tous les �tudiants doivent ou peuvent suivre tous les cours.
 Sinon, choisissez la seconde option.</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><b><u>Note</u></b>&nbsp;: Vous pourrez modifier cette option � tout moment � partir d'eConcept</td></tr>
</table>
