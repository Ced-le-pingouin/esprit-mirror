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
 * @file	dom-php4.class.php
 * 
 * Contient les classes d'encapsulation du DOM à utiliser en PHP 4, mais avec les noms de méthodes des classes DOM PHP
 * 5.
 * 
 * Ces classes portent le même nom que les classes correspondantes en DOM PHP 5, précédé de la lettre C.
 * 
 * Dans la mesure du possible, elles reproduisent les méthodes du DOM PHP 5 (nom, nombre de paramètres) en utilisant
 * en interne la bibliothèque DOM XML de PHP 4. Cependant, certains paramètres sont ignorés si leur comportement 
 * ne peut être aisément reproduit en PHP 4, ou tout simplement si l'utilisation du DOM XML dans le projet Esprit ne les
 * requiert pas; cela permet de ne pas récrire entièrement une version PHP 4 du DOM PHP 5.
 * 
 * Les noms des paramètres ne respectent pas les conventions de noms utilisées habituellement dans nos projets, ils sont
 * repris à l'identique de la documentation de DOM pour PHP 5.
 */

/**
 * Classe reproduisant en PHP 4 le comportement de la classe \c DOMNode de la bibliothèque DOM PHP 5
 */
class CDOMNode // extends DomNode
{
	var $_oDOMNode = NULL;	///< objet de type DomNode (PHP 4) qui est utilisé en interne pour reproduire le comportement de DOMNode (PHP 5)
	
	/**
	 * Constructeur. Crée un nouveau noeud DOM
	 * 
	 * @param	name	le nom du noeud à créer
	 */
	function CDOMNode($name)
	{
		$this->_oDOMNode = new DomNode($name);
	}
	
	/**
	 * Ajoute un nouveau noeud enfant, à la fin des enfants existants
	 * 
	 * @param	newnode		le noeud à ajouter, sous forme d'objet CDOMNode (ou d'une classe descendante)
	 * 
	 * @return	le noeud ajouté (INCOMPLET)
	 * 
	 * @todo	La valeur de retour diffère entre le DOM XML PHP 4 et le DOM PHP 5, il faudra tester et uniformiser, 
	 * 			même si la valeur de retour a peu d'intérêt dans cette méthode (en tout cas dans le type d'utilisation  
	 * 			que j'en fais)
	 */
	function appendChild($newnode)
	{
		return $this->_oDOMNode->append_child($newnode->_oDOMNode);
	}
}

/**
 * Classe reproduisant en PHP 4 le comportement de la classe \c DOMElement de la bibliothèque DOM PHP 5
 */
class CDOMElement extends CDOMNode
{
	/**
	 * Constructeur. Crée un élément DOM
	 * 
	 * @param	name			le nom de l'élément à créer
	 * @param	value			la valeur à attribuer à l'élément (A TESTER)
	 * @param	namespaceURI	l'URI d'un espace de nom pour créer l'élément dans un espace de nom spécifique (INCOMPLET)
	 * 
	 * @todo	L'attribution immédiate d'une valeur utilise la méthode \c setContent() des objets DOM PHP4, cela semble 
	 * 			fonctionner pour créer des éléments XML (balises) avec un contenu texte, mais je ne sais pas si d'autres 
	 * 			utilisations sont possibles
	 * 
	 * @todo	Pour l'instant la création dans un espace de nom (3è paramètre) n'est pas implémentée
	 */
	function CDOMElement($name, $value = NULL, $namespaceURI = NULL)
	{
		$this->_oDOMNode = new DomElement($name);
		if (isset($value))
			$this->_oDOMNode->set_content($value);
	}
	
	/**
	 * Assigne une valeur à un attribut de l'élément. Si l'attribut n'existe pas, il est créé
	 * 
	 * @param	name	le nom de l'attribut auquel on veut assigner une valeur
	 * @param	value	la valeur à assigner à l'attribut
	 * 
	 * @return	l'ancien objet DomAttribute si l'attribut avait déjà une valeur, sinon le nouvel objet (INCOMPATIBLE)
	 * 
	 * @todo	La valeur de retour (un objet DomAttribute) n'est pas la même que pour le "vrai" 
	 * 			DOM PHP 5 qui, d'après la doc PHP, retourne TRUE ou FALSE. A tester 
	 */
	function setAttribute($name, $value)
	{
		return $this->_oDOMNode->set_attribute($name, $value);
	}
}


/**
 * Classe reproduisant en PHP 4 le comportement de la classe \c DOMDocument de la bibliothèque DOM PHP 5
 */
class CDOMDocument extends CDOMNode
{
	var $xmlEncoding = NULL;	///< Variable interne pour stocker l'encodage demandé à l'instanciation (et encoding dans PHP 5, qu'est-ce que c'est ?)
	var $formatOutput = FALSE;  ///< Indique si on veut que la sortie du document soit mise en forme (sauts de ligne et tabulations)   
	
	/**
	 * Constructeur
	 * 
	 * @param	version		le numéro de version du document en tant que partie de la déclaration XML 
	 * @param	encoding	l'encodage du document en tant que partie de la déclaration XML
	 * 
	 * @todo	Quelles sont les valeurs par défaut correctes des paramètres ?
	 */
	function CDOMDocument($version = '1.0', $encoding = 'UTF-8')
	{
		$this->_oDOMNode = domxml_new_doc($version);
		$this->xmlEncoding = $encoding;
	}
	
	/**
	 * Crée une nouvelle instance de la classe CDOMElement. Ce noeud ne sera pas affiché dans le document, à moins qu'il 
	 * ne soit inséré avec CDOMNode#appendChild()
	 * 
	 * @param	name	le nom de l'élément à créer
	 * @param	value	la valeur à donner à l'élément (le texte entre les balises)
	 * 
	 * @return	l'élément créé (INCOMPLET)
	 * 
	 * @todo	Normalement, la valeur de retour doit être FALSE si un problème est survenu à la création
	 */
	function createElement($name, $value = NULL)
	{
		return new CDOMElement($name, $value);
	}
	
	/**
	 * Sauvegarde l'arborescence XML dans un fichier
	 * 
	 * @param	filename	le chemin du fichier dans lequel on veut sauver le XML
	 * @param	options		les options supplémentaires (voir la doc de PHP) (INCOMPLET)
	 * 
	 * @return	le nombre d'octets écrits, ou \c FALSE si
	 * 
	 * @todo	le paramètre \p options n'est pas implémenté ici (en PHP 5, il existe à partir de la version 5.1.0)
	 */
	function save($filename, $options = 0)
	{
		// en DOM XML PHP 4, il existe la fonction \c dump_file() pour réaliser cette opération, mais elle pose  
		// apparemment problème avec le chemin du fichier
		//return $this->_oDOMNode->dump_file($filename, FALSE, $this->formatOutput);
		
		$f = @fopen($filename, 'wb');
		if ($f !== FALSE)
		{
			$r = fwrite($f, $this->saveXML(NULL, $options));
			fclose($f);
			
			return $r;
		}
		
		return FALSE;
	}
	
	/**
	 * Retourne l'arbre XML interne sous forme de chaîne de caractères
	 * 
	 * @param	node	le noeud à retourner sous forme de chaîne, ou NULL si on désire tout le document. Dans le cas
	 * 					d'un noeud spécifique, il n'y aura pas de déclaration XML au début de la chaîne retournée 
	 * 					(INCOMPLET)
	 * @param	options	les options supplémentaires (INCOMPLET)
	 * 
	 * @todo	Pour l'instant, seul le document complet peut être retourné sous forme de chaîne, pas une sous-partie
	 * 
	 * @todo	Pour l'instant, aucune options supplémetnaire n'est prise en compte 
	 */
	function saveXML($node = NULL, $options = 0)
	{
		return $this->_oDOMNode->dump_mem($this->formatOutput, $this->xmlEncoding);
	}
}

?>