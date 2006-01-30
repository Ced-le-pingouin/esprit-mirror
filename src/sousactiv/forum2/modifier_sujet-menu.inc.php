<?php
if ($url_sModaliteFenetre == "ajouter")
{
	$aMenus = array(
		array("Déposer","top.oPrincipale().envoyer()")
		, array("Annuler","top.close()")
	);
}
else if ($url_sModaliteFenetre == "modifier")
{
	$aMenus = array(
		array("Valider","top.oPrincipale().envoyer()")
		, array("Annuler","top.close()")
	);
}
else if ($url_sModaliteFenetre == "supprimer")
{
	$aMenus = array(
		array("Oui","top.oPrincipale().envoyer()")
		, array("Non","top.close()")
	);
}
?>
