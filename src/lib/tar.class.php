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
 * @file	tar.class.php
 * 
 * Contient la classe de gestion d'archives GNU TAR
 * 
 * @date	2006/06/12
 * 
 * @author	Cédric FLOQUET
 */

require_once dir_lib('pcltar/pcltar.lib.php', TRUE);

/**
 * Gestion d'archives GNU TAR, utilisant les fonctions de la bibliothèque PclTar
 * 
 * @todo	Cette classe est incomplète, elle ne gère pas la décompression des archives, uniquement la compression.
 * 			De plus, seules des archives gzippées sont créées
 */
class CTar
{
	var $sCheminArchive = NULL;	///< Chaîne de caractères contenant le chemin de l'archive à créer
	var $asEntrees = NULL;		///< Tableau de chaînes représentant les chemins des entrées (fichier/dossier) à inclure dans l'archive
	var $sCheminAAjouter = "";	///< Ce chemin sera ajouté devant la racine de chaque fichier inclus à l'archive
	var $sCheminAEnlever = "";	///< Les fichiers inclus dans l'archive et dont le chemin commence par cette chaîne, auront cette chaîne enlevée du chemin
	var $sCompression = 'tgz';	///< Type de compression à utiliser. Dépendante des fonctions de PclTar
	
	var $iCodeRetour = 1;		///< Lorsqu'on demande la création d'archive directement dans le constructeur, on ne peut pas retourner de code d'erreur, il est donc stocké dans une propriété de l'objet
	
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
	function CTar($v_sCheminArchive, $v_asEntrees = NULL, $v_bCreer = FALSE)
	{
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
			$this->asEntrees = NULL;
		
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
	 * @return	le code d'erreur de la fonction \c PclTarCreate() (biblio externe), qui est 1 si l'opération est correcte
	 */
	function creerArchive()
	{
		return PclTarCreate($this->sCheminArchive, $this->asEntrees, $this->sCompression, "", $this->sCheminAEnlever);
	}
	
	/**
	 * Ajoute des fichiers à l'archive, écrasant les versions plus anciennes éventuelles
	 * 
	 * @return	le code d'erreur de la fonction \c PclTarUpdate() (biblio externe), qui est 1 si l'opération est correcte
	 */
	function majArchive($v_asEntrees)
	{
		$this->ajouterEntreesDsListe($v_asEntrees, TRUE);
		
		return PclTarUpdate($this->sCheminArchive, $this->asEntrees, $this->sCompression, "", $this->sCheminAEnlever);
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
