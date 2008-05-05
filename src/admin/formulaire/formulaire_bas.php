<?php
require_once("globals.inc.php");

$oProjet = new CProjet();
$oTpl = new Template("formulaire_bas.tpl");

$oTpl->afficher();
?>
