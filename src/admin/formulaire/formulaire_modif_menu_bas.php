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

echo "<html>\n";

echo "<head>\n";

echo "<script type=\"text/javascript\">\n";
echo "<!--\n";

echo "\nfunction appliquer()\n";
echo "{if (parent.frames['FORMFRAMEMODIF'].document.forms.length > 0)"; //Teste si le formulaire existe si oui il execute le submit
echo "{ parent.frames['FORMFRAMEMODIF'].document.forms['formmodif'].submit(); } }\n";

echo "\nfunction annuler()\n"; 
echo "{if (parent.frames['FORMFRAMEMODIF'].document.forms.length > 0)"; //Teste si le formulaire existe si oui il execute le submit
echo "{ parent.frames['FORMFRAMEMODIF'].document.forms['formmodif'].reset(); } }\n";
echo "//-->\n";
echo "</script>\n\n";

//CSS
echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">\n";
//FIN CSS
echo "</head>\n";


echo "<body class=\"menumodifbas\">\n";
echo "<TABLE style=\"border-top:1px solid black;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\">\n";

echo "<tr><td style=\"text-align : left\">&nbsp\n";

echo "</td><td style=\"text-align : right\">";


echo "<a href=\"javascript: appliquer();\">Appliquer les changements</a>\n";
echo " | ";

echo "<a href=\"javascript: annuler();\">Annuler</a>\n";

echo "&nbsp</td></tr>\n";
echo "</TABLE>\n";
echo "</body>\n";
echo "</html>\n";
}//Verification de la permission d'utiliser le concepteur de formulaire
?>

