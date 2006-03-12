<?php
if ($url_sModaliteFenetre == "ajouter")	$aMenus = array(array("Déposer","top.oPrincipale().valider()"),array("Annuler","top.oPrincipale().Annuler()"));
else if ($url_sModaliteFenetre == "modifier") $aMenus = array(array("Valider","top.oPrincipale().valider()"),array("Annuler","top.oPrincipale().Annuler()"));
else if ($url_sModaliteFenetre == "supprimer") $aMenus = array(array("Oui","top.oPrincipale().supprimer()"),array("Non","top.oPrincipale().Annuler()"));
?>

