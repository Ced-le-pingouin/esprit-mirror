<?php

/*
** Fichier ................: recuperer_fichiers.php
** Description ............: 
** Date de cr�ation .......: 22-08-2002
** Derni�re modification ..: 10-10-2002
** Auteurs ................: Filippo Porco
** Emails .................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

if (!empty ($HTTP_GET_VARS))
{
	$IdForm  = $HTTP_GET_VARS["FORM"];
	$IdActiv = $HTTP_GET_VARS["ACTIV"];
}
else
	$IdForm = $IdActiv = NULL;

require_once ("globals.inc.php");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>

<title>R�cup�rer des fichiers relatifs � ce bloc</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<?=inserer_feuille_style()?>

<script type="text/javascript" language="javascript">
<!--

function init(v_iLargeur,v_iHauteur)
{
	if (v_iHauteur <= 0)
		if (document.all)
			v_iHauteur = document.body.scrollHeight+30;
		else
			v_iHauteur = document.body.offsetHeight+50;
		
	var iCentrerLargeur = ((screen.width-v_iLargeur)/2);
	var iCentrerHauteur = ((screen.height-v_iHauteur)/2);
	
	self.moveTo(iCentrerLargeur,iCentrerHauteur);
	self.resizeTo(v_iLargeur,v_iHauteur);
	self.focus();
}

//-->
</script>

</head>

<body>
<?php 

?>
<form name="FRM_RECUPERER_FICHIERS" action="<?php echo $HTTP_SERVER_VARS['PHP_SELF']; ?>" method="post">

<p>Choisissez dans la liste ci-dessous, le fichier que vous d�sirez r�cup�rer.</p>

<iframe src="recuperer_fichiers_liste.php<?php echo "?FORM=$IdForm&ACTIV=$IdActiv"; ?>" 
	name="FRAME_LISTE"
	frameborder="0"
	align="center"
	width="99%"
	height="350"
	frameborder="1"
	scrolling="yes"
	style="border: #999999 solid 1px;">
</iframe>
<div style="text-align: right;">
<hr>
<!--<input type="button" value="R�cup�rer" onclick="frames[0].document.forms[0].submit()">-->
<input type="button" value="Rafra&icirc;chir" onclick="top.frames['FRAME_LISTE'].location.reload(true)">
<input type="button" value="Fermer" onclick="self.close()">
</div>

</form>

</body>

</html>
