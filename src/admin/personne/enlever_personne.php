<?php
require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Initialiser
// ---------------------
$iIdFormCourante = (isset($_GET["idform"]) ? $_GET["idform"] : 0);
$oTpl = new Template("enlever_personne.tpl");
$oFormation = new CFormation($oProjet->oBdd,$iIdFormCourante);
$sNomFormation = null;

$oBloc = new TPL_Block("BLOC_GENERAL",$oTpl);

if ($iIdFormCourante > 0)
{
	$sNomFormation = $oFormation->retNom();
}
$oBloc->remplacer("{formation->nom}",$sNomFormation);
//$oBloc->remplacer("{liste_personne}",$aListePersonne);
$oBloc->afficher();
$oTpl->afficher();
?>