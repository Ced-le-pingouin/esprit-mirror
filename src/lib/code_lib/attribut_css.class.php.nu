<?php

class CAttribut_CSS 
{
	var $m_iIdElement;
	
	// <TABLE>
	var $m_iBorder;
	var $m_iCellSpacing;
	var $m_iCellPadding;
	
	// <TD>
	var $m_iColSpan;
	var $m_iRowSpan;
	var $m_bNoWrap;
	
	// Attributs communs
	var $m_iHeight;
	var $m_iWidth;
	var $m_sAlign;
	var $m_sVAlign;
	

	/* ********************************************************************** */
	/* Constructeur                                                           */
	/* ********************************************************************** */

	function CAttribut_CSS ($v_iIdElement=NULL)
	{
		$this->m_iIdElement = $v_iIdElement;
	}
	

	/* ********************************************************************** */
	/* align                                                                  */
	/* ********************************************************************** */

	function Align ($v_sAlign=NULL,$v_bRetValeur=FALSE)
	{
		if ($v_sAlign == NULL)
		{
			$v = $this->m_sAlign;

			if ($v_bRetValeur || !isset ($v))
				return $v;
			else
				return "align=\"{$v}\"";
		}
		
		$this->m_sAlign = $v_sAlign;
	}


	/* ********************************************************************** */
	/* colspan                                                                */
	/* ********************************************************************** */

	function ColSpan ($v_iColSpan=NULL,$v_bRetValeur=FALSE)
	{
		if ($v_iColSpan == NULL)
		{
			$v = $this->m_iColSpan;
			
			if ($v_bRetValeur || !isset ($v))
				return $v;
			else
				return "colspan=\"{$v}\"";
		}
		
		$this->m_iColSpan = $v_iColSpan;
	}
	

	/* ********************************************************************** */
	/* height                                                                 */
	/* ********************************************************************** */

	function Height ($v_iHeight=NULL,$v_bRetValeur=FALSE)
	{
		if ($v_iHeight == NULL)
		{
			$v = $this->m_iHeight;

			if ($v_bRetValeur || !isset ($v))
				return $v;
			else
				return "height=\"{$v}\"";
		}
		
		$this->m_iHeight = $v_iHeight;
	}


	/* ********************************************************************** */
	/* inclure                                                                */
	/* ********************************************************************** */

	function inclure ()
	{
		$sAttribut = NULL;
		
		/*
		** 'rowspan' et 'colspan'
		**
		*/
		
		if ($this->m_iIdElement == ID_TD)
		{
			$cs = $this->m_iColSpan;
			$rs = $this->m_iRowSpan;

			$sAttr = ((isset ($cs)) ? $this->ColSpan () : NULL)
				." ".((isset ($rs)) ? $this->RowSpan () : NULL);
			
			$sAttribut .= trim ($sAttr)." ";
		}
				
		/*
		** 'align' et 'valign'
		**
		*/
		
		if ($this->m_iIdElement == ID_TD)
		{
			$a = $this->m_sAlign;
			$va = $this->m_sVAlign;

			$sAttr = ((isset ($a)) ? $this->Align () : NULL)
				." ".((isset ($va)) ? $this->VAlign () : NULL);
			
			$sAttribut .= trim ($sAttr)." ";
		}

		/*
		** 'nowrap'
		**
		*/

		if ($this->m_iIdElement == ID_TD)
		{
			// Inclure l'attribut: nowrap
			$sAttr = (($this->m_bNoWrap === TRUE) ? "nowrap" : NULL);
			
			$sAttribut .= trim ($sAttr)." ";			
		}
		
		/*
		** Afficher les attributs
		**
		*/

		echo trim ($sAttribut);
	}



	/* ********************************************************************** */
	/* nowrap                                                                 */
	/* ********************************************************************** */

	function NoWrap ($v_bNoWrap=NULL,$v_bRetValeur=FALSE)
	{
		if ($v_bNoWrap == NULL)
		{
			$v = $this->m_bNoWrap;

			if ($v_bRetValeur || !isset ($v))
				return $v;
			else
				return "nowrap=\"{$v}\"";
		}
		
		$this->m_bNoWrap = $v_bNoWrap;
	}


	/* ********************************************************************** */
	/* rowspan                                                                */
	/* ********************************************************************** */

	function RowSpan ($v_iRowSpan=NULL,$v_bRetValeur=FALSE)
	{
		if ($v_iRowSpan == NULL)
		{
			$v = $this->m_iRowSpan;
			
			if ($v_bRetValeur || !isset ($v))
				return $v;
			else
				return "rowspan=\"{$v}\"";
		}
		
		$this->m_iRowSpan = $v_iRowSpan;
	}


	/* ********************************************************************** */
	/* valign                                                                 */
	/* ********************************************************************** */

	function VAlign ($v_sVAlign=NULL,$v_bRetValeur=FALSE)
	{
		if ($v_sVAlign == NULL)
		{
			$v = $this->m_sVAlign;

			if ($v_bRetValeur || !isset ($v))
				return $v;
			else
				return "valign=\"{$v}\"";
		}
		
		$this->m_sVAlign = $v_sVAlign;
	}


	/* ********************************************************************** */
	/* width                                                                  */
	/* ********************************************************************** */

	function Width ($v_iWidth=NULL,$v_bRetValeur=FALSE)
	{
		if ($v_iWidth == NULL)
		{
			$v = $this->m_iWidth;

			if ($v_bRetValeur || !isset ($v))
				return $v;
			else
				return "width=\"{$v}\"";
		}
		
		$this->m_iWidth = $v_iWidth;
	}
}

?>
