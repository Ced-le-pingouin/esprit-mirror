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
 * @file	IterateurComposite.php
 */

require_once(dirname(__FILE__).'/OO.php');
require_once(dirname(__FILE__).'/Iterateur.php');

/**
 * Interface dérivée de l'itérateur simple, qui permet en plus de savoir si un élément a des "enfants" (qui pourraient) 
 * donc eux aussi être "itérés"). Si un itérateur implémente cette interface, il pourra être passé par le décorateur 
 * IterateurRecursif, qui effectue une itération en descendant également dans les enfants de l'itérateur de base
 */
class IterateurComposite extends Iterateur 
{
	/**
	 * Indique si l'élément courant est susceptible d'avoir des éléments "enfants".
	 * 
	 * Par exemple, dans le cas d'un système de fichiers, on peut considérer que cette méthode retournerait \c false 
	 * pour un fichier, mais \c true pour un dossier (car il peut lui-même contenir des fichiers ou dossiers). 
	 * Idem pour un tableau : si l'un de ses éléments est lui-même un tableau, la méthode #aEnfants() de cet élément 
	 * retourne \c true. Etc, etc 
	 */
	function aEnfants()
	{
		OO::abstraite();
	}
	
	/**
	 * @return	le "sous-itérateur" qui permet de parcourir les enfants de l'élément courant
	 */
	function retIterateurEnfants()
	{
		OO::abstraite();
	}
}

OO::defInterface();

?>
