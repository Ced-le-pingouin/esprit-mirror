<?php

/*
** Fichier ................: sauver_modele.php
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

$oProjet = new CProjet();

$sNom = $oProjet->oUtilisateur->retNomComplet();

$oProjet->terminer();

?>
<html>
<head>
<?php inserer_feuille_style(); ?>

<script type="text/javascript" language="javascript">
<!--

function Annuler()
{
	document.forms[0].elements["ID_FORM"].value = "0";
	document.forms[0].elements["ACTION"].value = "annuler";
	document.forms[0].submit();
}

function Enregistrer()
{
	var sNomFichier = document.forms[0].elements["NOM_FICHIER"].value;
		
	if (sNomFichier.length <= 0)
		return false;
		
	document.forms[0].elements["ACTION"].value = "enregistrer";
	document.forms[0].submit();
	
	return true;
}

//-->
</script>

</head>
<body>
<p>Nous vous conseillons d'entrer un nom simple: sans espaces et sans lettres accentuées.</p>
<form action="sauver_modele_liste.php" target="liste" method="post">
<p><b>Nom du fichier du modèle d'équipe&nbsp;:</b><br><input type="text" name="NOM_FICHIER" size="50"></p>
<p><b>Description&nbsp;:</b><br><textarea name="DESCRIPTION" rows="10" cols="50"></textarea></p>
<input type="hidden" name="NIVEAU" value="<?=$HTTP_GET_VARS['NIVEAU']?>">
<input type="hidden" name="ID_NIVEAU" value="<?=$HTTP_GET_VARS['ID_NIVEAU']?>">
<input type="hidden" name="ACTION" value="annuler">
</form>
</body>
</html>
