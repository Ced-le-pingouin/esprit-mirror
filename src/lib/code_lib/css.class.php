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

// Ces constantes permettent de donner une identité au classe.
// Toutes les classes peuvent hériter d'une classe ou créer une instance d'une
// autre classe. Le problème, est de connaître le type de cette classe, 
// par exemple je suis une classe 'TABLE' par conséquent il reçoit comme
// indice ID_TABLE. Elle va permettre à la classe ATTRIBUT d'afficher les
// attributs pour le type TABLE.
define ("ID_BODY",0);

define ("ID_TABLE",1);
define ("ID_TR",2);
define ("ID_TD",3);

define ("ID_H1",4);
define ("ID_H2",5);
define ("ID_H3",6);
define ("ID_H4",7);
define ("ID_H5",8);
define ("ID_H6",9);

define ("ID_LINK",10);

// Textes
define ("ALIGNER_GAUCHE","left");
define ("ALIGNER_HCENTRE","center");
define ("ALIGNER_DROITE","right");

define ("ALIGNER_HAUT","top");
define ("ALIGNER_VCENTRE","middle");
define ("ALIGNER_BAS","bottom");

// Inclure des fichiers
require_once (dir_code_lib ("html_attributs.inc.php"));
require_once (dir_code_lib ("css_styles.inc.php"));

class CCss 
{
	var $m_sNom;		// Nom
	
	var $ID_ELEMENT;	// Donne une idée du type de l'élément
	
	// Attributs
	var $m_sAlignement;
	var $m_sVAlignement;
	var $m_sCouleurFond;
	
	// Définition des classes communs
	var $Fond;
	var $Couleur;
	var $Texte;	
	var $Bord;
			
	
	/* ********************************************************************** */
	/* Constructeur                                                           */
	/* ********************************************************************** */

	function CCss ($v_sNom=NULL)
	{
		$this->m_sNom = $v_sNom;
		
		$this->init ();		
	}
	
	function init ()
	{		
		if (!isset ($this->Fond))
			$this->Fond = new CFond_CSS ();
		
		if (!isset ($this->Couleur))
			$this->Couleur = new CCouleur_CSS ();
			
		if (!isset ($this->Texte))
			$this->Texte = new CTexte_CSS ();

		if (!isset ($this->Bord))
			$this->Bord = new CBord_CSS ();
	}
	
		
	/* ********************************************************************** */
	/* Attributs                                                              */
	/* ********************************************************************** */

	function Alignement ($v_sAlignement=NULL)
	{
		if ($v_sAlignement == NULL)
			return "align=\"{$this->m_sAlignement}\"";

		$this->m_sAlignement = $v_sAlignement;
	}
	

	function VAlignement ($v_sVAlignement=NULL)
	{
		if ($v_sVAlignement == NULL)
			return "valign=\"{$this->m_sVAlignement}\"";

		$this->m_sVAlignement = $v_sVAlignement;
	}
	
	function CouleurFond ($v_sCouleurFond=NULL)
	{
		if ($v_sCouleurFond === NULL)
			return "bgcolor=\"{$this->m_sCouleurFond}\"";
		
		$this->m_sCouleurFond = $v_sCouleurFond;
	}

			
	/* ********************************************************************** */
	/* inclure                                                                */
	/* ********************************************************************** */
	
	function inclure ()
	{
		$sStyle = "{$this->m_sNom}\n"
			."{\n"
			."\t".CouleurTexte ($this->Texte->Couleur->Couleur ())."\n"
			."\t".FamillePolice ($this->Texte->FamillePolices ())."\n"
			."\t".TaillePolice ($this->Texte->TaillePolice ())."\n"
			."}\n";
		
		echo str_replace ("\t\n",NULL,$sStyle);
	}		
}

?>
