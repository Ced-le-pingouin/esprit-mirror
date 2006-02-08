<?php

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
