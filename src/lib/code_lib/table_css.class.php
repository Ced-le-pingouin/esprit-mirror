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
** Fichier ................: CTable_CSS
** Description ............: 
** Date de création .......: 11-01-2002
** Dernière modification ..: 16-01-2002
** Auteur .................: Fili//0: Porco
** Email ..................: filippo.porco@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

class CTable_CSS extends CCss
{	
	var $m_sBorder;
	var $m_sCellPadding;
	var $m_sCellSpacing;
	var $m_sWidth;
	var $m_sHeight;

	var $Cell;
	
	function CTable_CSS ($v_sNom="@",$v_sBorder="0",$v_sCellSpacing="0",$v_sCellPadding="0",$v_sWidth=NULL,$v_sHeight=NULL)
	{
		$this->ID_ELEMENT = ID_TABLE;
		
		$sNom = (($v_sNom == "@") ? "TABLE, TR, TD" : $v_sNom);
		
		$this->m_sBorder = $v_sBorder;
		$this->m_sCellSpacing = $v_sCellSpacing;
		$this->m_sCellPadding = $v_sCellPadding;
		$this->m_sWidth = $v_sWidth;
		$this->m_sHeight = $v_sHeight;
		
		if (!isset ($this->Fond))
			$this->Fond = new CFond_CSS ();
	}
	
	function ajouterCell ($v_sNomTd=NULL,$v_sAlign=NULL,$v_sVAlign=NULL,$v_sColSpan=NULL,$v_sRowSpan=NULL)
	{
		if ($this->Cell == NULL)
			$this->Cell = array ();
		
		if ($v_sNomTd === NULL)
			$iNbTd = count ($this->Cell);
		
		$this->Cell[$iNbTd] = new CCell_CSS (NULL,$v_sAlign,$v_sVAlign,$v_sColSpan,$v_sRowSpan);

		// Donner la couleur de fond de la table aux cellules
		$v = $this->Fond->Couleur ();

		if (!isset ($v))
			$v = $this->Fond->Couleur->Couleur ();

		$this->Cell[$iNbTd]->Fond->Couleur ($v);

		return $iNbTd;
	}

	function ajouterCells ($v_iNbCells)
	{
		if ($this->Cell === NULL)
			$this->Cell = array ();

		$iNbTd = count ($this->Cell);
			
		for ($i=$iNbTd; $i<$iNbTd+$v_iNbCells; $i++)
		{
			$this->Cell[$i] = new CCell_CSS ();
			
			$v = $this->Fond->Couleur ();
			
			if (!isset ($v))
				$v = $this->Fond->Couleur->Couleur ();
				
			$this->Cell[$i]->Fond->Couleur ($v);
		}
		
		return $iNbTd;
	}
			
	function Border ($v_sBorder=NULL)
	{
		if ($v_sBorder === NULL)
			return $this->m_sBorder;

		$this->m_sBorder = $v_sBorder;
	}

	function CellSpacing ($v_sCellSpacing=NULL)
	{
		if ($v_sCellSpacing === NULL)
			return $this->m_sCellSpacing;

		$this->m_sCellSpacing = $v_sCellSpacing;
	}

	function CellPadding ($v_sCellPadding=NULL)
	{
		if ($v_sCellPadding === NULL)
			return $this->m_sCellPadding;
		
		$this->m_sCellPadding = $v_sCellPadding;
	}

	function Attributs ()
	{
		// Afficher les attributs communs
		$sAttributs = aBordsTable ($this->m_sBorder)
			.aEspaceEntreCellules ($this->m_sCellSpacing)
			.aEspaceDansCellules ($this->m_sCellPadding)
			.aLargeur ($this->m_sWidth)
			.aHauteur ($this->m_sHeight);
		
		echo " ".trim ($sAttributs);
	}
	
	function Hauteur ($v_sHeight=NULL)
	{
		if ($v_sHeight === NULL)
			return $this->m_sHeight;

		$this->m_sHeight = $v_sHeight;
	}
	
	function Largeur ($v_sWidth=NULL)
	{
		if ($v_sWidth === NULL)
			return $this->m_sWidth;

		$this->m_sWidth = $v_sWidth;
	}
}

?>
