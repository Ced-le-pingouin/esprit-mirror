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

require_once(dirname (__FILE__)."/fichiers_permis.inc.php");

define ("UPLOAD_OK",0);
define ("UPLOAD_ERREUR",1);
define ("UPLOAD_EXTENSION_INTERDITE",2);

function retTexteErreurChargement ($v_iCodeErreur)
{
	switch ($v_iCodeErreur)
	{
		case UPLOAD_OK :
			return "Chargement du fichier vers le serveur a réussi";
			break;
			
		case UPLOAD_ERREUR :
			return "Une erreur est survenue lors du chargement du fichier vers le serveur";
			break;
			
		case UPLOAD_EXTENSION_INTERDITE :
			return "L'extension de ce fichier n'est pas autorisé";
			break;
	}
	
	return NULL;
}

/*
** Fonction .....: retNomFichierUnique ()
** Description ..: Cette fonction vérifie si un fichier du même nom existe, dans ce cas,
**                 il lui attribue un numéro de quatre chiffre incrémenté de 1.
** Variables d'entrée ........: $v_sNomFichier - Donner le nom du fichier à uploader (Ex.: FileXYZ.EXT)
** Return .......: $newFile - Retourne un nom de fichier unique (Ex.: FileXYZ-0023.EXT)
**
*/

function retNomFichierUnique ($v_sNomFichier,$v_sNomRepertoire=NULL,$v_bAjouterZero=TRUE)
{ // v1.01
	$i = 0;
	
	$pos  = strrpos($v_sNomFichier,".");
	
	if ($pos>0)
	{
		$sNomFichier = substr($v_sNomFichier,0,$pos);
		$ext = substr($v_sNomFichier,$pos,(strlen($v_sNomFichier)-$pos));
	}
	else
	{
		$sNomFichier = $v_sNomFichier;
		$ext = "";
	}
	
	while (TRUE)
	{
		$nbZero = "";
		
		if ($v_bAjouterZero)
		{
			if ($i<10)
				$nbZero = "000";
			else if ($i<100)
				$nbZero = "00";
			else if ($i<1000)
				$nbZero = "0";
		}
		
		$r_sNomFichier = $v_sNomRepertoire.$sNomFichier."-".$nbZero.$i.$ext;
		
		if (!file_exists($r_sNomFichier))
			break;
		
		$i++;
	}
	
	return ($r_sNomFichier);
}

/*
** Fonction ............: chargerFichier ()
** Description .........: Cette fonction copie le fichier qui se trouve dans le répertoire temporaire du serveur
**                        vers le sa destination du côté serveur.
** Variables d'entrée ..: - $v_sNomFichierTmpCharger, nom du fichier temporaire;
**                        - $v_sNomFichierCharger, nom du fichier + le répertoire de destination.
** Variable de retour ..: chargerFichier () renvoie UPLOAD_OK en cas de succès, UPLOAD_FAIL sinon.
**
** Exemple .............: chargerFichier ("FH102JHG","ressources/documents/doc-0010.doc");
**
*/

function chargerFichier ($v_sNomFichierTmpCharger,$_sNomFichierCharger,$v_sExtAutorisee=NULL)
{
	if (!validerFichier ($_sNomFichierCharger,$v_sExtAutorisee))
		return UPLOAD_EXTENSION_INTERDITE;
	
	if (!@copy($v_sNomFichierTmpCharger,$_sNomFichierCharger))
		return UPLOAD_ERREUR;
	
	chmod($_sNomFichierCharger,0644);
	
	return UPLOAD_OK;
}

function erreurChargement ($v_sNomFichier,$v_iResult)
{
	echo "<br>Erreur de chargement du fichier '{$v_sNomFichier}' vers le serveur (ERREUR No $v_iResult)<br>";
}

?>
