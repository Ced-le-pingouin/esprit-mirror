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
** Classe .................: CFond_CSS
** Description ............: Définit les quelques propriétés css pour les fonds.
** Date de création .......: 11-01-2002
** Dernière modification ..: 16-01-2002
** Auteur .................: Fili//0: Porco
** Email ..................: filippo.porco@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

class CFond_CSS 
{
	var $m_sCouleurFond;
	var $m_sImageFond;
	var $m_sRepetitionFond;
	var $m_sFixerFond;
	var $m_sPositionFond;
	
	// Constantes de 'background-attachment'
	var $DEFILER="scroll";
	var $FIXE="fixed";
	
	var $Couleur;
	
	
	/* ********************************************************************** */
	/* Constructeur                                                           */
	/* ********************************************************************** */

	function CFond_CSS ($v_sCouleur=NULL,$v_sImageFond=NULL,$v_sRepetition=NULL,$v_sFixer=NULL,$v_sPosition=NULL)
	{
		if (isset ($v_sCouleur))
			$this->Couleur ($v_sCouleur);
			
		if (isset ($v_sImageFond))
			$this->Image ($v_sImageFond);
			
		if (isset ($v_sRepetition))
			$this->Repetition ($v_sRepetition);
			
		if (isset ($v_sFixer))
			$this->Fixer ($v_sFixer);
			
		if (isset ($v_sPosition))
			$this->Position ($v_sPosition);
		
		if (!isset ($this->Couleur))
			$this->Couleur = new CCouleur_CSS ();
	}
	

	/* ********************************************************************** */
	/* Background                                                             */
	/* ********************************************************************** */

	function Fond ()
	{
		$sFond = ((isset ($this->m_sCouleurFond)) ? " {$this->m_sCouleurFond}" : NULL)
			.((isset ($this->m_sImageFond)) ? " url(\"{$this->m_sImageFond}\")" : NULL)
			.((isset ($this->m_sRepetitionFond)) ? " {$this->m_sRepetitionFond}" : NULL)
			.((isset ($this->m_sFixerFond)) ? " {$this->m_sFixerFond}" : NULL)
			.((isset ($this->m_sPositionFond)) ? " {$this->m_sPositionFond}" : NULL);
		
		return (Fond (trim($sFond)));
	}
	

	/* ********************************************************************** */
	/* Fond-color                                                             */
	/* ********************************************************************** */

	function Couleur ($v_sFondColor=NULL)
	{
		if ($v_sFondColor === NULL)
			return $this->m_sCouleurFond;
			
		$this->m_sCouleurFond = $v_sFondColor;
	}
	

	/* ********************************************************************** */
	/* Background-image                                                             */
	/* ********************************************************************** */

	function Image ($v_sImageFond=NULL)
	{
		if ($v_sImageFond === NULL)
			return $this->m_sImageFond;
			
		$this->m_sImageFond = trim ($v_sImageFond);
	}


	/* ********************************************************************** */
	/* background-repeat                                                      */
	/* ********************************************************************** */

	function Repetition ($v_sRepetitionFond=NULL)
	{
		if ($v_sRepetitionFond === NULL)
			return $this->m_sRepetitionFond;
			
		$this->m_sRepetitionFond = $v_sRepetitionFond;
	}


	/* ********************************************************************** */
	/* background-attachment                                                  */
	/* ********************************************************************** */

	function Fixer ($v_sFixerFond=NULL)
	{
		if ($v_sFixerFond === NULL)
			return $this->m_sFixerFond;

		$this->m_sFixerFond = $v_sFixerFond;
	}


	/* ********************************************************************** */
	/* background-position                                                    */
	/* ********************************************************************** */

	function Position ($v_sPositionFond=NULL)
	{
		if ($v_sPositionFond == NULL)
			return $this->m_sPositionFond;
			
		$this->m_sPositionFond = $v_sPositionFond;
	}
}

?>
