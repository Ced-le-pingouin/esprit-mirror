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

class CTexte_CSS 
{
	// Font
	var $m_sFamillePolices;
	var $m_sTaillePolice;
	var $m_sStylePolice;
	
	// Text
	var $m_sAlignementTexte;
	var $m_sEspaceEntreCaracteres;
	var $m_bRetourLigne;
	
	var $m_sGras;
	
	var $Couleur;
	var $Fond;
	var $Bord;
	
	// Déclarations des constantes
	var $ALIGNER_GAUCHE="left";
	var $CENTRER_HORIZONTALEMENT="center";
	var $ALIGNER_DROITE="right";
			
	/* ********************************************************************** */
	/* Constructeur                                                           */
	/* ********************************************************************** */

	function CTexte_CSS ()
	{
		$this->Couleur = new CCouleur_CSS ();
			
		$this->Fond = new CFond_CSS ();
		
		$this->Bord = new CBord_CSS ();
	}
	
	/* ********************************************************************** */
	/* text-align                                                             */
	/* ********************************************************************** */

	function Alignement ($v_sAlignementTexte=NULL)
	{
		if ($v_sAlignementTexte === NULL)
			return $this->m_sAlignementTexte;

		$this->m_sAlignementTexte = $v_sAlignementTexte;
	}
	
	/* ********************************************************************** */
	/* font-size                                                              */
	/* ********************************************************************** */

	function TaillePolice ($v_sTaillePolice=NULL)
	{
		if ($v_sTaillePolice === NULL)
			return $this->m_sTaillePolice;

		$this->m_sTaillePolice = $v_sTaillePolice;
	}
	

	/* ********************************************************************** */
	/* font-family                                                            */
	/* ********************************************************************** */

	function FamillePolices ($v_sFamillePolices=NULL)
	{
		if ($v_sFamillePolices === NULL)
			return $this->m_sFamillePolices;

		$this->m_sFamillePolices = $v_sFamillePolices;
	}


	/* ********************************************************************** */
	/* font-style                                                             */
	/* ********************************************************************** */

	function StylePolice ($v_sStylePolice=NULL)
	{
		if ($v_sStylePolice === NULL)
			return $this->m_sStylePolice;

		$this->m_sStylePolice = $v_sStylePolice;
	}
	
	function Italique ($v_sItalic=TRUE)
	{
		if ($v_sItalic === NULL)
			$this->StylePolice (NULL);
		else
			$this->StylePolice ((($v_sItalic) ? "Italic" : FALSE));
	}
		
	
	/* ********************************************************************** */
	/* font-weight                                                            */
	/* ********************************************************************** */
	
	function Gras ($v_sGras=TRUE)
	{
		$this->EpaisseurPolice ((($v_sGras === TRUE) ? "bold" : FALSE));
	}

	function EpaisseurPolice ($v_sEpaisseurPolice=NULL)
	{
		if ($v_sEpaisseurPolice === NULL)
			return $this->m_sEpaisseurPolice;

		$this->m_sEpaisseurPolice = $v_sEpaisseurPolice;
	}
		

	/* ********************************************************************** */
	/* letter-spacing                                                         */
	/* ********************************************************************** */

	function EspaceEntreCaracteres ($v_sEspaceEntreCaracteres=NULL)
	{
		if ($v_sEspaceEntreCaracteres === NULL)
			return $this->m_sEspaceEntreCaracteres;

		$this->m_sEspaceEntreCaracteres = $v_sEspaceEntreCaracteres;
	}
	

	/* ********************************************************************** */
	/* white-space                                                            */
	/* ********************************************************************** */

	function RetourLigne ($v_bRetourLigne=NULL)
	{
		if ($v_bRetourLigne === NULL)
			return $this->m_bRetourLigne;
		
		$this->m_bRetourLigne = $v_bRetourLigne;
	}


	/* ********************************************************************** */
	/* Style                                                                  */
	/* ********************************************************************** */

	function Style ()
	{
		$sStyle = CouleurFond ($this->Fond->Couleur->Couleur ())
			.sRetourLigne ($this->RetourLigne ())
			.CouleurTexte ($this->Couleur->Couleur ())
			.TaillePolice ($this->TaillePolice ())
			.StylePolice ($this->StylePolice ())
			.$this->Bord->css ()
			.LargeurPolice ($this->EpaisseurPolice ())
			.AlignementTexte ($this->Alignement ());
		
		if (strlen ($sStyle))
			echo " style=\"".trim ($sStyle)."\"";
	}	
}

?>
