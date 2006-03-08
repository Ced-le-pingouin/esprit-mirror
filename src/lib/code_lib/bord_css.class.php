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

class CBord_CSS 
{
	var $m_sStyleBord;
	var $m_sEpaisseurBord;
	
	var $Couleur;
	
	// Déclarations des constantes
	var $SANS="none";
	var $POINTILLE="dotted";
	var $A_TIRET="dashed";
	var $DOUBLE="double";	
	var $SOLIDE="solid";
	
	// Epaisseur du trait
	var $FIN="thin";
	
	/* ********************************************************************** */
	/* Constructeur                                                           */
	/* ********************************************************************** */
	
	function CBord_CSS ()
	{		
		$this->Couleur = new CCouleur_CSS ();
	}
		
	function Epaisseur ($v_sEpaisseur=NULL)
	{
		if ($v_sEpaisseur === NULL)
			return ((strlen ($this->m_sEpaisseurBord)) ? "border-width: {$this->m_sEpaisseurBord};" : NULL);
		
		$this->m_sEpaisseurBord = $v_sEpaisseur;
	}
	
	
	/* ********************************************************************** */
	/* Couleur des bords                                                      */
	/* ********************************************************************** */
	
	function Couleur ()
	{
		$sCouleur = $this->Couleur->Couleur ();
		
		return ((strlen ($sCouleur)) ? "border-color: {$sCouleur}; ": NULL);
	}
	
	
	/* ********************************************************************** */
	/* Styles des bords                                                       */
	/* ********************************************************************** */
	
	function Style ($v_sStyleBord=NULL)
	{
		if ($v_sStyleBord === NULL)
			return ((strlen ($this->m_sStyleBord)) ? "border-style: {$this->m_sStyleBord};": NULL);
		
		$this->m_sStyleBord = $v_sStyleBord;
	}
			
	function css ()
	{
		$sStyle = $this->Style ()." ".$this->Epaisseur ()." ".$this->Couleur ();
			
		return trim ($sStyle)." ";
	}
}

?>
