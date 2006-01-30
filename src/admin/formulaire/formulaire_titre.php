<?php

require_once("globals.inc.php");

$sLienCSS = lien_feuille_style("concept.css");

$oTpl = new Template(dir_theme("dialog-titre-2.tpl",FALSE,TRUE));

$oBlock_Head = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlock_Head->ajouter($sLienCSS);
$oBlock_Head->afficher();

$oTpl->remplacer("{titre_principal}","Formulaires");
$oTpl->remplacer("{sous_titre}","");
$oTpl->afficher();

?>

