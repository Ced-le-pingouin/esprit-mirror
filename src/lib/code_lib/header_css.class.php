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

class CH1_CSS extends CCss
{	
	function CH1_CSS ($v_sNom=NULL)
	{
		$this->ID_ELEMENT = ID_H1;
		
		$this->init ();

		$this->m_sNom = trim ($v_sNom);

		$this->m_sTaillePolice = ((defined ("H1_FONT_SIZE")) ? H1_FONT_SIZE : NULL);
		
		if (!isset ($this->Couleur))
			$this->Couleur = new CCouleur_CSS (((defined ("H1_Couleur")) ? H1_Couleur : NULL));
		
		return;
	}
	
	function init ($v_sNom=NULL)
	{		
		if (!isset ($this->Texte))
			$this->Texte = new CTexte_CSS ();

		if (!isset ($this->Bord))
			$this->Bord = new CBord_CSS ();
	}
	
	function Couleur ()
	{
		echo $this->Couleur->Couleur ();
		
		return $this->Couleur->Couleur ();
	}
	
	function inclure ()
	{
		$sStyleCss = "\n";
		
		$n = ($this->ID_ELEMENT-ID_H1)+1;
				
		if (strstr ($this->m_sNom,"#") || strstr ($this->m_sNom,"."))
			$sStyleCss .= "H{$n}{$this->m_sNom}";
		else if (strlen ($this->m_sNom))
			$sStyleCss .= "H{$n}.{$this->m_sNom}";
		else
			$sStyleCss .= "H{$n}";
		
		$sStyleCss .= "\n{\n"
			."\t".CouleurFond ($this->Texte->Fond->Couleur ())."\n"			// background-Couleur
			."\t".CouleurTexte ($this->Couleur ())."\n"					// Couleur
			."\t".LargeurPolice ($this->Texte->m_sFontWeight)."\n"				// font-weight
			."\t".TaillePolice ($this->m_sTaillePolice)."\n"						// font-size
			."\t".EspaceCaracteres ($this->Texte->EspaceEntreCaracteres ())."\n"			// letter-spacing
			."\t".AlignementTexte ($this->Texte->Alignement ())."\n"					// text-align
			."\t".$this->Bord->css ()."\n"										// border-style,border-width,border-
			."}\n";
		
		echo str_replace ("\t\n",NULL,$sStyleCss);
	}
}


class CH2_CSS extends CH1_CSS
{	
	function CH2_CSS ($v_sNom=NULL,$v_sId=NULL)
	{
		$this->ID_ELEMENT = ID_H2;
		
		$this->init ();
		
		$this->m_sNom = trim ($v_sNom);
		
		$this->m_sTaillePolice = ((defined ("H2_FONT_SIZE")) ? H2_FONT_SIZE : NULL);
		
		if (!isset ($this->Couleur))
			$this->Couleur = new CCouleur_CSS (((defined ("H2_Couleur")) ? H2_Couleur : NULL));		
	}	
}

class CH3_CSS extends CH1_CSS
{	
	function CH3_CSS ($v_sNom=NULL,$v_sId=NULL)
	{
		$this->ID_ELEMENT = ID_H3;
		
		$this->init ();
				
		$this->m_sNom = trim ($v_sNom);

		$this->m_sTaillePolice = ((defined ("H3_FONT_SIZE")) ? H3_FONT_SIZE : NULL);
		
		if (!isset ($this->Couleur))
			$this->Couleur = new CCouleur_CSS (((defined ("H3_Couleur")) ? H3_Couleur : NULL));		
	}	
}

class CH4_CSS extends CH1_CSS
{	
	function CH4_CSS ($v_sNom=NULL,$v_sId=NULL)
	{
		$this->ID_ELEMENT = ID_H4;
		
		$this->init ();
				
		$this->m_sNom = trim ($v_sNom);

		$this->m_sTaillePolice = ((defined ("H4_FONT_SIZE")) ? H4_FONT_SIZE : NULL);
		
		if (!isset ($this->Couleur))
			$this->Couleur = new CCouleur_CSS (((defined ("H4_Couleur")) ? H4_Couleur : NULL));
	}
}

class CH5_CSS extends CH1_CSS
{	
	function CH5_CSS ($v_sNom=NULL,$v_sId=NULL)
	{
		$this->ID_ELEMENT = ID_H5;
		
		$this->init ();
				
		$this->m_sNom = trim ($v_sNom);

		$this->m_sTaillePolice = ((defined ("H5_FONT_SIZE")) ? H5_FONT_SIZE : NULL);
		
		if (!isset ($this->Couleur))
			$this->Couleur = new CCouleur_CSS (((defined ("H5_Couleur")) ? H5_Couleur : NULL));
	}
}

class CH6_CSS extends CH1_CSS
{	
	function CH6_CSS ($v_sNom=NULL,$v_sId=NULL)
	{
		$this->ID_ELEMENT = ID_H6;
		
		$this->init ();
				
		$this->m_sNom = trim ($v_sNom);

		$this->m_sTaillePolice = ((defined ("H6_FONT_SIZE")) ? H6_FONT_SIZE : NULL);
		
		if (!isset ($this->Couleur))
			$this->Couleur = new CCouleur_CSS (((defined ("H6_Couleur")) ? H6_Couleur : NULL));
	}
}

?>
