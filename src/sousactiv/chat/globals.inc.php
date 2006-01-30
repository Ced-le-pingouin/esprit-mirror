<?php
require_once("../../globals.inc.php");

define("CID","delta_chat_");

function retIdUniqueChat ($v_iIdChat,$v_sNomEquipe=NULL) { return ":{$v_sNomEquipe}:".CID."{$v_iIdChat}"; }

function retHautStatut ($v_iStatutUtilisateur)
{
	if ($v_iStatutUtilisateur == STATUT_PERS_ADMIN ||
		$v_iStatutUtilisateur == STATUT_PERS_RESPONSABLE_POTENTIEL ||
		$v_iStatutUtilisateur == STATUT_PERS_RESPONSABLE ||
		$v_iStatutUtilisateur == STATUT_PERS_TUTEUR)
		return TRUE;
	else
		return FALSE;
}
?>
