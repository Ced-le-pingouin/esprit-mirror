<?php
require_once("globals.inc.php");
$oProjet = new CProjet();
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
else
{
	$v_iIdObjForm = 0;
	$v_iIdFormulaire = 0;
}

//echo "<br>v_iIdTypeObj".$v_iIdTypeObj;
//echo "<br>v_iIdFormulaire : ".$v_iIdFormulaire;

echo "<html>\n";
echo "<head>\n";
echo "<script src=\"selectionobj.js\" type=\"text/javascript\">";
echo "</script>\n";

//CSS
echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">";
//FIN CSS
echo "</head>\n";

echo "<body class=\"modif\">";

if ($v_iIdObjForm > 0)
{
	$iIdNvObjForm = CopierUnObjetFormulaire($oProjet->oBdd, $v_iIdObjForm, $v_iIdFormulaire, "max");
	
	echo "<script>\n";
	echo "rechargerliste($iIdNvObjForm,$v_iIdFormulaire)\n";
	echo "</script>\n";

}
else
{
	echo "Erreur: Impossible de copier l'objet";
}
echo "</body>\n";
echo "</html>\n";
?>
