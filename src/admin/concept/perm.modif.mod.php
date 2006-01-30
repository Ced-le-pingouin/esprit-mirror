<?php

$bPeutModifier = $oProjet->verifModifierModule();
$g_bModifier  = $oProjet->verifPermission("PERM_MOD_COURS");
$g_bModifier &= $bPeutModifier;

$g_bModifierStatut  = $oProjet->verifPermission("PERM_MOD_STATUT_TOUS_COURS");
$g_bModifierStatut |= $oProjet->verifPermission("PERM_MOD_STATUT_COURS");
$g_bModifierStatut &= $bPeutModifier;

unset($bPeutModifier);
?>
