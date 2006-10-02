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
 * @file	bdd.class.php
 * 
 * Contient la classe de gestion de la DB adaptée à Esprit
 * 
 * @date	2002/12/05
 * 
 * @author	Filippo PORCO
 */

require_once(dir_code_lib("bdd_mysql.class.php"));
require_once(dir_include("config.inc"));				// Information à propos de la base de données

/**
 * Gestion de la DB, adaptée à Esprit, utilisant des variables globales du projet (config) pour déterminer les 
 * paramètres de connexion
 */
class CBdd extends CBddMySql
{
	/**
	 * Constructeur. Effectue une connexion automatique à la DB grâce en utilisant les variables globales de la config 
	 * comme paramètres
	 */
	function CBdd()
	{
		global $g_sNomServeur, $g_sNomProprietaire, $g_sMotDePasse, $g_sNomBdd;
		
		$this->CBddMySql($g_sNomServeur, $g_sNomProprietaire, $g_sMotDePasse, $g_sNomBdd);
	}
}

?>
