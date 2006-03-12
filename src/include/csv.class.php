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
** Fichier ................: csv.class.php
** Description ............:
** Date de création .......: 12/10/2005
** Dernière modification ..: 12/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

class CCSV
{
	function doubler_guillemets ($v_sTexte)
	{
		// Mettre un espace au début du message dans le cas où il y aurait un tiret :
		// c'est à cause de cet imbécil de Microsoft Excel
		if ("-" == substr($v_sTexte,0,1))
			$v_sTexte = " {$v_sTexte}";
		
		return str_replace("\"","\"\"",$v_sTexte);
	}
}

?>
