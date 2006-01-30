<?php

/*
** Fichier ................: admin_modif.inc.php
** Description ............: 
** Date de création .......: 04/06/2004
** Dernière modification ..: 07/06/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

/*function adminRetListeStatuts ($v_sNomListeStatuts,$v_aaListeStatuts)
{
	$sListeStatuts = NULL;
	
	foreach ($v_aaListeStatuts as $aStatut)
		$sListeStatuts .= "<option"
			." value=\"{$aStatut[0]}\""
			.($aStatut[2] ? " selected" : NULL)
			.">{$aStatut[1]}</option>";
	
	return "<select name=\"{$v_sNomListeStatuts}\">{$sListeStatuts}</select>";
}*/

function adminEntrerNom ($v_sNom,$v_mValeur)
{
	global $g_bModifier;
	
	echo "\n<!-- Nom -->\n\n"
		."<tr>\n"
		."<td><div class=\"intitule\">Nom&nbsp;:</div></td>\n"
		."<td>"
		."<input type=\"text\""
		." name=\"{$v_sNom}\""
		." size=\"53\""
		." value=\"{$v_mValeur}\""
		." style=\"width: 100%;\""
		.($g_bModifier ? NULL : " disabled")
		.">" // <input>
		."</td>\n"
		."</tr>\n\n";
}

function adminRetListeModalites ($v_sNomListeModalites,$v_aaListeModalites)
{
	global $g_bModifier;
	
	$sListeModalites = NULL;
	
	foreach ($v_aaListeModalites as $aModalite)
		$sListeModalites .= "<option"
			." value=\"".$aModalite[0]."\""
			.($aModalite[2] ? " selected" : NULL)
			.">".htmlentities($aModalite[1])."</option>";
	
	return "<select name=\"{$v_sNomListeModalites}\""
		.($g_bModifier ? NULL : " disabled")
		.">{$sListeModalites}</select>";
}

?>
