<?php

if ($oRessourceSousActiv->verifDeposerDocuments($iTypeTransfert,$oProjet->aoEquipes,$g_iIdPersDest))
{
	$sNomFichierDst = retNomFichierUnique($oRessourceSousActiv->retUrl(TRUE,TRUE),$sRepDst);
	
	$oRessourceSousActiv->defUrl(basename($sNomFichierDst));
	
	if ($g_iIdPersDest > 0)
		$oRessourceSousActiv->defIdExped($g_iIdPersDest);
	
	// Vérifier que le répertoire existe
	if (!is_dir(dirname($sNomFichierDst)))
		creer_repertoire(dirname($sNomFichierDst));
	
	// Copier le fichier
	if (@copy($sRepSrc.$sNomFichierSrc,$sNomFichierDst))
	{
		if ($iErr <> TRANSFERT_REUSSI_SAUF)
			$iErr = TRANSFERT_REUSSI;
		
		$oRessourceSousActiv->ajouter($iIdSADest,STATUT_RES_TRANSFERE,$g_iIdResSA);
	}
	else
	{
		$iErr = TRANSFERT_REUSSI_SAUF;
		$aiIdResSA[] = $g_iIdResSA;
	}
}
else
{
	$iErr = TRANSFERT_REUSSI_SAUF;
	$aiIdResSA[] = $g_iIdResSA;
}

?>
