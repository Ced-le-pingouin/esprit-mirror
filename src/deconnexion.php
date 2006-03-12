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

/*
** Fichier ................: deconnexion.php
** Description ............: Se déconnecter de la plate-forme.
** Date de création .......: 01/06/2001
** Dernière modification ..: 30/06/2004
** Auteurs ................: Filippo Porco
** Emails .................: ute@umh.ac.be
**
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
include_once("evenement.php");
$oProjet->effacerInfosSession();
$oProjet->terminer();

header("Location: {$oProjet->sUrlLogin}");
?>
