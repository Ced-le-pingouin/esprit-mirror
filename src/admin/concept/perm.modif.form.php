<?php
$bPeutModifier = $oProjet->verifModifierFormation();

$g_bModifier  = $oProjet->verifPermission("PERM_MOD_SESSION");
$g_bModifier &= $bPeutModifier;

$g_bModifierStatut  = $oProjet->verifPermission("PERM_MOD_STATUT_TOUTES_SESSIONS");
$g_bModifierStatut |= $oProjet->verifPermission("PERM_MOD_STATUT_SESSION");
$g_bModifierStatut &= $bPeutModifier;

unset($bPeutModifier);
?>
