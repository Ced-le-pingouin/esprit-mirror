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
** Fichier ................: dialog_base.class.php
** Description ............:
** Date de création .......: 17/12/2004
** Dernière modification ..: 21/12/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

class CDialogBase
{
	var $sPageIndex;
	var $sTitreFenetre;
	var $sEntetes = NULL;
	var $sSrcPrincipale;
	var $sSrcMenu;
	var $sBlocJavascript = NULL;
	
	function CDialogBase ($v_sTitreFenetre)
	{
		global $sPageIndex;
		
		$this->sPageIndex = $sPageIndex;
		$this->sTitreFenetre = mb_convert_encoding($v_sTitreFenetre,"HTML-ENTITIES","UTF-8");
	}
	
	function defSrcPrincipale ($v_sSrcPrincipale) { $this->sSrcPrincipale = $v_sSrcPrincipale; }
	function defSrcMenu ($v_sSrcMenu) { $this->sSrcMenu = $v_sSrcMenu; }
	
	function ajouterEntete ($v_sEntete) { $this->sEntetes .= $v_sEntete; }
	function insererDansBlocJavascript ($v_sLignesJavascript) { $this->sBlocJavascript .= $v_sLignesJavascript; }
	
	function retBlocJavascript ()
	{
		if (isset($this->sBlocJavascript))
			return "<script type=\"text/javascript\" language=\"javascript\">\n"
				."<!--\n"
				.$this->sBlocJavascript
				."\n//-->\n"
				."</script>\n";
				
		return NULL;
	}
	
	function afficher ()
	{
		$this->sEntetes .= $this->retBlocJavascript();
		
		$asElemsRechercher = array("{html_title}","{html_head}","{frame_src_principale}","{frame_src_menu}");
		$asElemsRemplacer = array($this->sTitreFenetre,$this->sEntetes,$this->sSrcPrincipale,$this->sSrcMenu);
		$this->sPageIndex = str_replace($asElemsRechercher,$asElemsRemplacer,$this->sPageIndex);
		
		$asElemsRechercher = array("racine://","theme://","javascript://","commun://");
		$asElemsRemplacer = array(dir_root_plateform(NULL,FALSE),dir_theme(),dir_javascript(),dir_theme_commun());
		$this->sPageIndex = str_replace($asElemsRechercher,$asElemsRemplacer,$this->sPageIndex);
		
		echo $this->sPageIndex;
	}
}

?>
