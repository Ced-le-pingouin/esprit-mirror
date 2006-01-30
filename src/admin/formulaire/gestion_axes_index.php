<?php
//************************************************
//*       Récupération des variables             *
//************************************************

if (isset($HTTP_GET_VARS))
{

	$v_iIdFormulaire = $HTTP_GET_VARS['idformulaire'];
}
else if (isset($HTTP_POST_VARS))
{

	$v_iIdFormulaire = $HTTP_POST_VARS['idformulaire'];
}
else
{

	$v_iIdFormulaire = 0;
}
$sAdressePage= "gestion_axes_menu.php?idformulaire=".$v_iIdFormulaire;

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
   "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<title>Gestion des Axes</title>
</head>
</html>

<frameset rows="*,20" border="0" frameborder="0" framespacing="0">
<frame name="FORMFRAMEAXE" src="gestion_axes.php" marginwidth="0" marginheight="0" frameborder="0" scrolling="yes" noresize>
<frame name="FORMFRAMEAXEMENU" src="<?php echo "$sAdressePage"; ?>" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize>
</frameset>

