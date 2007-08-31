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

/**
 * @file	zip.class.php
 */

require_once(dirname(__FILE__).'/pclzip/pclzip.lib.php');

/**
 * Gestion d'archives PKZIP, utilisant les fonctions de la bibliothèque PclZip
 * 
 * @todo	Cette classe est incomplète, elle ne gère pas la décompression des archives, uniquement la compression.
 * 			De plus, il faudrait "synchroniser" cette classe avec CTar (objet de base pour la (dé)compression puis 
 * 			héritage de chacune des deux classes ?), notamment au niveau des valeurs de retour
 */
class CZip
{
	var $oArchive;				///< Objet PclZip interne qui représente l'archive

	var $sCheminArchive = NULL;	///< Chaîne de caractères contenant le chemin de l'archive à créer
	var $asEntrees = array();	///< Tableau de chaînes représentant les chemins des entrées (fichier/dossier) à inclure dans l'archive
	var $sCheminAAjouter = "";	///< Ce chemin sera ajouté devant la racine de chaque fichier inclus à l'archive
	var $sCheminAEnlever = "";	///< Les fichiers inclus dans l'archive et dont le chemin commence par cette chaîne, auront cette chaîne enlevée du chemin
	
	var $iCodeRetour = 0;		///< Lorsqu'on demande la création d'archive directement dans le constructeur, on ne peut pas retourner de code d'erreur, il est donc stocké dans une propriété de l'objet
	
	/**
	 * Constructeur
	 * 
	 * @param	v_sCheminArchive	le chemin complet de l'archive qu'on désire créer
	 * @param	v_asEntrees			un tableau contenant les chemins de fichiers ou dossiers à compresser.
	 * 								Si \c null (par défaut), il faudra ajouter des entrées plus tard à l'aide de la 
	 * 								fonction #ajouterEntreesDsListe(), et le paramètre \p v_bCreer ne sera donc pas 
	 * 								pris en compte (il vaudra \c false)
	 * @param	v_bCreer			si \c true, crée immédiatement l'archive. Le code de retour de cette opération est 
	 * 								stocké dans la propriété \c iCodeRetour
	 * 
	 * @todo	Vérifier que le chemin/nom de l'archive à créer est valide
	 */
	function CZip($v_sCheminArchive, $v_asEntrees = array(), $v_bCreer = FALSE)
	{
		$this->oArchive = new PclZip($v_sCheminArchive);
		
		$this->sCheminArchive = $v_sCheminArchive;
		
		if (!empty($v_asEntrees))
		{
			$this->ajouterEntreesDsListe($v_asEntrees);
			
			if ($v_bCreer)
				$this->iCodeRetour = $this->$this->creerArchive();
		}
	}
	
	/**
	 * Ajoute une entrée (fichier ou dossier) à la liste des entrées qui constitueront le fichier compressé
	 * 
	 * @param	v_asEntrees	la liste des entrées à inclure à l'archive, sous forme de tableau de chaînes de caractères, 
	 * 						chacune représentant le chemin d'un fichier/dossier à inclure
	 * @param	v_bCreer	si \c true, l'archive est créée immédiatement, et le code de retour de cette opération est 
	 * 						retourné
	 * 
	 * @return	si \p v_bCreer est à \c true, le code de retour de l'opération de création de l'archive est retourné. 
	 * 			Sinon, la valeur retournée est \c 1
	 */
	function ajouterEntreesDsListe($v_asEntrees, $v_bEffacerAnciennes = FALSE, $v_bCreer = FALSE)
	{
		if ($v_bEffacerAnciennes)
			$this->asEntrees = array();
		
		if (!is_array($v_asEntrees))
			$this->asEntrees = array_merge($this->asEntrees, array($v_asEntrees));
		else
			$this->asEntrees = array_merge($this->asEntrees, $v_asEntrees);
		
		if (!$v_bCreer)
			return 1;
		else
			return $this->creerArchive();
	}
	
	/**
	 * Crée l'archive
	 * 
	 * @return	la valeur de retour de la fonction \c PclZip::create() (biblio externe), qui est une liste de propriétés 
	 * 			de fichiers si l'opération s'est bien déroulée, sinon \c 0
	 */
	function creerArchive()
	{
		return $this->oArchive->create($this->asEntrees, 
		                               PCLZIP_OPT_ADD_PATH   , "", 
		                               PCLZIP_OPT_REMOVE_PATH, $this->sCheminAEnlever);
	}
	
	function desarchiver($v_sDossierDest = NULL, $v_bEcraserExistant = FALSE)
	{
		if (empty($v_sDossierDest))
			$v_sDossierDest = dirname(realpath($this->sCheminArchive));
		
		return $this->oArchive->extract(PCLZIP_OPT_PATH, $v_sDossierDest, PCLZIP_OPT_REPLACE_NEWER);
	}
	
	/** @name Fonctions de lecture des champs de l'objet */
	//@{
	function retCheminArchive() { return $this->sCheminArchive; }
	//@}
	
	/** @name Fonctions de définition des champs de l'objet */
	//@{
	function defModifsChemins($v_sCheminAAjouter = "", $v_sCheminAEnlever = "")
	{
		$this->sCheminAAjouter = $v_sCheminAAjouter;
		$this->sCheminAEnlever = $v_sCheminAEnlever;
	}
	//@}
}

?>
