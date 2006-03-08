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
** Fichier ................: form.init.php
** Description ............:
** Date de création .......: 31/03/2005
** Dernière modification ..: 31/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

$oProjet->asInfosSession[SESSION_FORM]      = $g_iFormation;
$oProjet->asInfosSession[SESSION_MOD]       = $g_iModule;
$oProjet->asInfosSession[SESSION_UNITE]     = $g_iRubrique;
$oProjet->asInfosSession[SESSION_ACTIV]     = $g_iActiv;
$oProjet->asInfosSession[SESSION_SOUSACTIV] = $g_iSousActiv;
$oProjet->initSousActivCourante();

$oProjet->initStatutsUtilisateur();

?>

