<?php

/*
** Fichier ................: nvfrm_pg2.php
** Description ............: 
** Date de création .......: 04-06-2002
** Dernière modification ..: 17-04-2003
** Auteurs ................: Filippo Porco
** Emails .................: <ute@umh.ac.be>
**
*/
  
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"2\" width=\"100%\">\n"
	."<tr><td><h5>Etape 2&nbsp;: Choisissez la formation &agrave; copier</h5></td></tr>"
	."<tr>\n"
	."<td align=\"right\" valign=\"middle\">"
	."<b>Filtre&nbsp;:</b>&nbsp;"
	."<select name=\"filtre\" onchange=\"javascript: rechargerListe(this,'filtre');\">\n";

$asFiltre = array("Toutes les formations"
	,"Une formation par type"
	,"Année en cours (".date("01-01-Y")." au ".date("31-12-Y").")"
	,"Deux dernières années (".date("01-01-".(date("Y")-1))." au ".date("31-12-Y").")");

for ($i=0; $i<count($asFiltre); $i++)
	echo "<option name=\"CHOIX_FILTRE\" value=\"".($i+1)."\"".(($filtre == $i+1) ? " selected": NULL).">{$asFiltre[$i]}</option>\n";

$sParamsUrl = "";

echo "</select>\n"
	."</td>\n"
	."</tr>\n"
	."<tr><td>"
	."<iframe name=\"IFRAME_LISTE\" src=\"nvfrm_lst.php\" width=\"100%\" height=\"220px\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"0\" scrolling=\"yes\"></iframe>"
	."</td></tr>\n"
	."</table>\n";
?>
