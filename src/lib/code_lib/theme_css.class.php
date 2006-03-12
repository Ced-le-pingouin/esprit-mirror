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
** Classe .................: CTheme
** Description ............: 
** Date de création .......: 11-01-2002
** Dernière modification ..: 16-01-2002
** Auteur .................: Fili//0: Porco
** Email ..................: filippo.porco@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

// Fichiers communs
require_once (dir_code_lib ("css.class.php"));
require_once (dir_code_lib ("couleur_css.class.php"));
require_once (dir_code_lib ("fond_css.class.php"));
require_once (dir_code_lib ("texte_css.class.php"));
require_once (dir_code_lib ("body_css.class.php"));
require_once (dir_code_lib ("header_css.class.php"));
require_once (dir_code_lib ("table_css.class.php"));
require_once (dir_code_lib ("cell_css.class.php"));
require_once (dir_code_lib ("lien_css.class.php"));
require_once (dir_code_lib ("bord_css.class.php"));


class CTheme 
{
	var $body;
	var $h1;
	var $h2;
	
	var $link;
		
	function CTheme ()
	{
		$this->body = new CBody_CSS ();
		
		$this->h1 = new CH1_CSS ();
		$this->h2 = new CH2_CSS ();
		
		$this->link = new CLink_CSS ();
	}
	
	function inclureTous ()
	{
		$this->body->inclure ();
		
		$this->h1->inclure ();
		$this->h2->inclure ();
		
		echo "\n";
		
		$this->link->inclure ();
		
		echo "\n";
	}
}

?>
