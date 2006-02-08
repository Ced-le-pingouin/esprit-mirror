<?php

// Ces constantes permettent de donner une identit� au classe.
// Toutes les classes peuvent h�riter d'une classe ou cr�er une instance d'une
// autre classe. Le probl�me, est de conna�tre le type de cette classe, 
// par exemple je suis une classe 'TABLE' par cons�quent il re�oit comme
// indice ID_TABLE. Elle va permettre � la classe ATTRIBUT d'afficher les
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
	
	var $ID_ELEMENT;	// Donne une id�e du type de l'�l�ment
	
	// Attributs
	var $m_sAlignement;
	var $m_sVAlignement;
	var $m_sCouleurFond;
	
	// D�finition des classes communs
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
