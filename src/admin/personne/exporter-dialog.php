<?php

require_once("globals.inc.php");

$oTpl = new Template("exporter-dialog.tpl");

$oTpl->remplacer("{LISTE_IDPERS->value}",$HTTP_GET_VARS["LISTE_IDPERS"]);

$oBloc_onglet_champs        = new TPL_Block("BLOCK_ONGLET_CHAMPS",$oTpl);
$oBloc_onglet_type_fichiers = new TPL_Block("BLOCK_ONGLET_TYPE_FICHIERS",$oTpl);

$oTpl_onglet = new Template(dir_theme("onglet/onglet_tab.tpl",FALSE,TRUE));
$oSet_onglet_champs = $oTpl_onglet->defVariable("SET_ONGLET");

// Onglet "champs"
$oSet_champs = $oTpl->defVariable("SET_CHAMPS_EXPORTER");
$oBloc_onglet_champs->ajouter($oSet_onglet_champs);
$oBloc_onglet_champs->remplacer("{onglet->titre}",str_replace(" ","&nbsp;",htmlentities("Liste des champs")));
$oBloc_onglet_champs->remplacer("{onglet->texte}",$oSet_champs);

// Onglet "types de fichier"
$oSet_type_fichier = $oTpl->defVariable("SET_TYPE_FICHIER");
$oBloc_onglet_type_fichiers->ajouter($oSet_onglet_champs);
$oBloc_onglet_type_fichiers->remplacer("{onglet->titre}",str_replace(" ","&nbsp;",htmlentities("Types de fichier")));
$oBloc_onglet_type_fichiers->remplacer("{onglet->texte}",$oSet_type_fichier);

$oBloc_onglet_champs->afficher();
$oBloc_onglet_type_fichiers->afficher();

$oTpl->afficher();

?>

