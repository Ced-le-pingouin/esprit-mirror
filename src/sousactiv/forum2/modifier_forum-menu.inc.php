<?php
if ($url_sModaliteFenetre == "ajouter")
	$aMenus = array(array("Déposer","top.modifier_forum()"), array("Annuler","top.close()"));
else if ($url_sModaliteFenetre == "modifier")
	$aMenus = array(array("Valider","top.modifier_forum()"), array("Annuler","top.close()"));
else if ($url_sModaliteFenetre == "supprimer")
	$aMenus = array(array("Oui","top.modifier_forum()"), array("Non","top.close()"));
?>
