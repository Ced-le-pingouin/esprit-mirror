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

if ($fonction == "valider")
{
	if (!is_object($oProjet->oUtilisateur))
		exit();
	
	$iIdPers = $oProjet->oUtilisateur->retId();
	
	if ($iIdPers > 0)
	{
		$url_sNomForm = mb_convert_encoding($url_sNomForm, 'UTF-8', 'HTML-ENTITIES');
		$url_sDescrForm = mb_convert_encoding($url_sDescrForm, 'UTF-8', 'HTML-ENTITIES');
		print "$url_sNomForm\n\n$url_sDescrForm\n";
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
		// Nous allons créer le répertoire de la formation
		mkdir(dir_formation($iIdForm,NULL,TRUE));
		
		// Se placer sur la nouvelle formation dans eConcept et puis on ferme
		// cette fenêtre
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
<table border="0" cellspacing="0" cellpadding="0" width="100%" style="font-size : 10pt;">
<tr><td colspan="2"><h5>Etape <?php echo $etape?>&nbsp;: Modalité d'inscription des étudiants aux cours</h5></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td><img src="<?php echo dir_images_communes('espacer.gif')?>" width="20" height="1" border="0"></td><td><input type="radio" name="InscrSpontForm" value="1"<?php echo ($url_iInscrSpontForm == 1 ? " checked" : NULL)?>>&nbsp;&nbsp;Tous les étudiants seront automatiquement inscrits à tous les cours de cette formation</td></tr>
<tr><td>&nbsp;</td><td><input type="radio" name="InscrSpontForm" value="0"<?php echo ($url_iInscrSpontForm == 1 ? NULL : " checked")?>>&nbsp;&nbsp;Certains étudiants seront inscrits à certains cours, d'autres pas</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">La première option vous dispense de devoir spécifier pour chaque étudiant les cours auxquels il est inscrit, ce qui peut s'avérer fastidieux.
 Choisissez donc cette option si vous vous trouvez dans un cas de figure où tous les étudiants doivent ou peuvent suivre tous les cours.
 Sinon, choisissez la seconde option.</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><b><u>Note</u></b>&nbsp;: Vous pourrez modifier cette option à tout moment à partir d'eConcept</td></tr>
</table>
