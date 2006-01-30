<?php

/*
** Fichier .................: nouv_form.inc.php
** Description .............: 
** Date de création ........: 04/06/2002
** Dernière modification ...: 22/02/2005
** Auteurs .................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

$oFormation = new CFormation($oProjet->oBdd);

if ($oProjet->verifPermission("PERM_MOD_TOUTES_SESSIONS"))
	$iNbrFormations = $oFormation->initFormations();
else if (is_object($oProjet->oUtilisateur))
	$iNbrFormations = $oFormation->initFormationsPourCopie($oProjet->oUtilisateur->retId());
else
	$iNbrFormations = 0;

unset($oFormation);
?>
<h5>Etape 1&nbsp;: Choisissez l'option qui convient</h5>
<?php
$asTexte = array("Créer une nouvelle formation","Construire une formation à partir d'une formation existante");

if ($iNbrFormations == 0)
	unset($asTexte[1]);

for ($i=0; $i<count($asTexte); $i++)
	echo "<input type=\"radio\" name=\"TYPE_COPIE\""
		." onclick=\"changerType('$i')\""
		." onfocus=\"blur()\""
		.($i == $type ? " checked" : NULL)
		.">"
		."&nbsp;&nbsp;"
		.$asTexte[$i]
		."<br>\n";
?>
