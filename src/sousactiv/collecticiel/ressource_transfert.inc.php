<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

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
