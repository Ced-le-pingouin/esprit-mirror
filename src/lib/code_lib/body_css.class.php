<?php

/*
** Classe .................: CBody_CSS
** Description ............: Classe de la balise <BODY>
** Date de création .......: 11-01-2002
** Dernière modification ..: 11-01-2002
** Auteur .................: Fili//0: Porco
** Email ..................: filippo.porco@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

class CBody_CSS extends CCss
{
	function CBody_CSS ()
	{
		$this->CCss ("BODY, DIV, TABLE, TH, TR, TD, H1, H2, H3, H4, H5, H6, P, A, SPAN");
		
		// Police de caractères
		$this->Texte->FamillePolices (((defined ("BODY_FONT_FAMILY")) ? BODY_FONT_FAMILY : NULL ));
		$this->Texte->TaillePolice (((defined ("BODY_FONT_SIZE")) ? BODY_FONT_SIZE : NULL ));

		// Fond de la page
		$this->Texte->Couleur->Couleur (((defined ("BODY_COLOR")) ? BODY_COLOR : NULL));
		$this->Fond->Couleur (((defined ("BODY_BACKGROUND_COLOR")) ? BODY_BACKGROUND_COLOR : NULL));
		$this->Fond->Image (((defined ("BODY_BACKGROUND_IMAGE")) ? BODY_BACKGROUND_IMAGE : NULL));
		$this->Fond->Fixer (((defined ("BODY_BACKGROUND_ATTACHMENT")) ? BODY_BACKGROUND_ATTACHMENT : NULL));
		$this->Fond->Repetition (((defined ("BODY_BACKGROUND_REPEAT")) ? BODY_BACKGROUND_REPEAT : NULL));
	}
	
	function inclure ()
	{
		$sStyle = "BODY\n"
			."{\n"
			."\t".$this->Fond->Fond ()."\n"
			."\t".CouleurTexte ($this->Couleur->Couleur ())."\n"
			."}\n\n";

		echo str_replace ("\t\n",NULL,$sStyle);
		
		parent::inclure ();	
	}
}

?>
