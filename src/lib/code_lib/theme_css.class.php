<?php

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
