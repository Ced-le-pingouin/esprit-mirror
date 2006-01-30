<?php
require_once("globals.inc.php");
/*
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
*/
echo "<html>\n";

echo "<head>\n";

echo "<script type=\"text/javascript\">\n";
echo "<!--\n";
echo "\nfunction appliquer()\n"; 
echo "{ parent.frames['FORMFRAMEAXE'].document.forms['formaxe'].submit(); }\n";

echo "\nfunction fermer()\n"; 
echo "{parent.window.close ();}\n";
echo "//-->\n";
echo "</script>\n\n";

//CSS
echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">\n";
//FIN CSS
echo "</head>\n";


echo "<body class=\"menumodifbas\">\n";

echo "<table BGCOLOR=\"CAC3B1\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-top: 1px solid black; padding: 3px 0px 3px 0px;\"><TR width=\"100%\">";
		echo "<td align=\"left\">&nbsp";
			
			echo "<a href=\"javascript: appliquer();\">Valider</a>\n";
		echo "</td><td align=\"right\">";
			echo "<a href=\"javascript: fermer();\">Fermer</a>\n";
			echo "&nbsp</td>";
	echo"</TR></table>";


echo "</body>\n";
echo "</html>\n";

?>

