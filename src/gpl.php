<?php
require_once("globals.inc.php");

$oProjet = new CProjet();
$sListeDestinataireEmail = "esprit.contact@gmail.com";
$sSujetEmail = "Esprit - Contact - Logiciel Libre";
$sMessageEmail = "Un nouvel utilisateur d'Esprit a laissé ses coordonnées.\n\n";

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("login/gpl.tpl",FALSE,TRUE));
$oBlocFormulaire = new TPL_Block("BLOCK_FORMULAIRE",$oTpl);
$oBlocMerci = new TPL_Block("BLOCK_MERCI",$oTpl);

if( isset($_POST["organisme"]) || isset($_POST["raison"]) || isset($_POST["email"]) || isset($_POST["usage"]) || isset($_POST["evolution"]))
{
	if( !empty($_POST["organisme"]) && !empty($_POST["usage"]) )
	{
		$sMessageEmail	.= "Organisme: ".$_POST["organisme"]
						."\nRaison sociale: ".$_POST["raison"]
						."\nEmail: ".$_POST["email"]
						."\nUsage: ".$_POST["usage"]
						."\nEvolution: ".$_POST["evolution"];
		mail($sListeDestinataireEmail,$sSujetEmail,$sMessageEmail);
		$oBlocFormulaire->effacer();
		$oBlocMerci->afficher();
		$oTpl->remplacer("[FORMAT_TEXTE]","");
	}
	else
	{
		$oBlocFormulaire->afficher();
		$oBlocMerci->effacer();
		$oTpl->remplacer("[FORMAT_TEXTE]","color: rgb(225,55,55);");
		$oTpl->remplacer("[ORG]",$_POST["organisme"]);
		$oTpl->remplacer("[RAISON]",$_POST["raison"]);
		$oTpl->remplacer("[EMAIL]",$_POST["email"]);
		$oTpl->remplacer("[USAGE]",$_POST["usage"]);
		$oTpl->remplacer("[EVO]",$_POST["evolution"]);
	}
}
else
{
	$oBlocFormulaire->afficher();
	$oBlocMerci->effacer();
		$oTpl->remplacer("[FORMAT_TEXTE]","");
	$oTpl->remplacer("[ORG]","");
	$oTpl->remplacer("[RAISON]","");
	$oTpl->remplacer("[EMAIL]","");
	$oTpl->remplacer("[USAGE]","");
	$oTpl->remplacer("[EVO]","");
}

$oTpl->afficher();
$oProjet->terminer();
?>
