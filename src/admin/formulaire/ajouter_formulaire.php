<?php	
require_once("globals.inc.php");
$oProjet = new CProjet();

//************************************************
//*       Récupération des variables             *
//************************************************

if (isset($HTTP_GET_VARS))
{
	$v_iIdFormulaire = $HTTP_GET_VARS['idform'];
}
else if (isset($HTTP_POST_VARS))
{
	$v_iIdFormulaire = $HTTP_POST_VARS['idform'];
}
else
{
	echo "Erreur dans le passage des paramètres";
}

$iIdPers = $oProjet->oUtilisateur->retId();

$oFormulaire = new CFormulaire($oProjet->oBdd);
$v_iIdFormulaire = $oFormulaire->ajouter($iIdPers);


echo "<html>";
echo "<head>";
echo "<script src=\"selectionobj.js\" type=\"text/javascript\">";
echo "</script>";
echo "</head>";

//Le javascript permet de recharger les frames FORMFRAMELISTE et FORMFRAMEMENU sans intervention de l'utilisateur
echo "<body onLoad=\"rechargerliste(0,$v_iIdFormulaire)\" onunload=\"rechargermenugauche()\">";
echo "</body></html>";
?>
