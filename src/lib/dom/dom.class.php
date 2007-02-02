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
 * @file	dom.class.php
 * 
 * Ce fichier décide quelle version des classes DOM il faut inclure, car ces dernières diffèrent entre PHP 4 (extension 
 * domxml) et PHP 5 (extension DOM). Nos classes DOM sont un ensemble minimum qui sont destinées dans un premier temps 
 * à réaliser une exportation SCORM
 */

// vérification de la présence des fonctions DOM PHP 5+
if (function_exists('dom_import_simplexml'))
	include_once(dirname(__FILE__).'/'.'dom-php5.class.php');
// sinon, est-ce que l'extension domxml PHP 4 est présente ?
else if (function_exists('domxml_new_doc'))
	include_once(dirname(__FILE__).'/'.'dom-php4.class.php');
// sinon, erreur
else
	die("ERREUR : Aucune bibliothèque DOM n'est présente dans l'installation de PHP sur ce serveur.");

?>