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
 * @file	dom-php5.class.php
 *
 * Contient les classes d'encapsulation du DOM à utiliser en PHP 5. Pour une explication détaillée du pourquoi de
 * l'existence de cette classe, voir le fichier \c dom-php4.class.php.
 *
 * Ces classes portent le même nom que les classes correspondantes en DOM PHP 5, précédé de la lettre C.
 *
 * En principe, dès qu'on a besoin d'une classe DOM dans le projet, on en crée l'équivalent pour PHP 4 dans le fichier
 * cité ci-dessus, on teste cette classe, et on vient rajouter son équivalent ici, qui en gros ne sera qu'un pointeur
 * simple vers la "vraie" classe du DOM PHP 5.
 */

class CDOMNode extends DOMNode {}
class CDOMElement extends DOMElement {}
class CDOMDocument extends DOMDocument {}
?>
