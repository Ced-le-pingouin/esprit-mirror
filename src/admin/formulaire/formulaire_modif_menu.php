<?php
//Ceci est ajouté uniquement pour pouvoir effectuer un contrôle de l'utilisateur
require_once("globals.inc.php");
$oProjet = new CProjet();
if ($oProjet->verifPermission('PERM_MOD_FORMULAIRES'))
{

//************************************************
//*       Récupération des variables             *
//************************************************

if (isset($HTTP_GET_VARS))
{
	$v_iIdObjForm = $HTTP_GET_VARS['idobj'];
	$v_iIdFormulaire = $HTTP_GET_VARS['idformulaire'];
}
else if (isset($HTTP_POST_VARS))
{
	$v_iIdObjForm = $HTTP_POST_VARS['idobj'];
	$v_iIdFormulaire = $HTTP_POST_VARS['idformulaire'];
}
?>

<html>
<head>
<link type="text/css" rel="stylesheet" href="<?=dir_theme("formulaire/formulaire.css");?>">
</head>

<?php
echo "<body class=\"menumodif\">\n";
echo "<TABLE style=\"border-top:1px solid black; border-bottom:1px solid black\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\">\n";

// affichage du n° d'ordre et du type de l'élément courant
echo "<tr><td style=\"text-align : left\">&nbsp\n";
if ($v_iIdObjForm > 0)
{
	$oObjForm = new CObjetFormulaire($oProjet->oBdd, $v_iIdObjForm);
	$oTypeObj = new CTypeObjetForm($oProjet->oBdd, $oObjForm->retIdType());
	
	echo "<b>Elément";
	echo " ".$oObjForm->retOrdre();
	echo " (".$oTypeObj->retDescCourte().")";
	echo "</b>";
}
else
{
	echo "<b>Options du formulaire</b>";
}
echo "</td>";

echo "</tr>\n";
echo "</TABLE>\n";
echo "</body>\n";
}//Verification de la permission d'utiliser le concepteur de formulaire
?>

