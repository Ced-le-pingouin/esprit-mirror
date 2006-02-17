<?php
require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForm   = (empty($HTTP_GET_VARS["ID_FORM"]) ? 0 : $HTTP_GET_VARS["ID_FORM"]);
$url_iIdStatut = (empty($HTTP_GET_VARS["STATUT_PERS"]) ? 0 : $HTTP_GET_VARS["STATUT_PERS"]);

// ---------------------
// Initialisations
// ---------------------
switch ($url_iIdStatut)
{
	case STATUT_PERS_CONCEPTEUR:
		$sTitreOngletPersonnes = _("Liste&nbsp;des&nbsp;concepteurs");
		break;
	case STATUT_PERS_TUTEUR:
		$sTitreOngletPersonnes = _("Liste&nbsp;des&nbsp;tuteurs");
		break;
	case STATUT_PERS_ETUDIANT:
		$sTitreOngletPersonnes = _("Liste&nbsp;des&nbsp;&eacute;tudiants");
		break;
		
	default:
		$sTitreOngletPersonnes = "&nbsp;";
}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("ass_multiple.tpl");

$oTpl->remplacer("{formation->id}",$url_iIdForm);
$oTpl->remplacer("{statut->id}",$url_iIdStatut);

$oTpl->remplacer("{titre->onglet->personnes}",$sTitreOngletPersonnes);

$oTpl->afficher();

?>

