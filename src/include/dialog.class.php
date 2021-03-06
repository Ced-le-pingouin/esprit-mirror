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

//@a: Fili//O: Porco
//@d: 14-12-2001
//@f: 02-07-2002
//@m: Filippo.Porco@umh.ac.be

class CDialog 
{
	var $m_sTitle;
	var $m_iNbrEspaces=2;
	
	function CDialog ($v_sTitle = "@")
	{
		if ($v_sTitle != "@")
			$this->Title ($v_sTitle);
	}
	
	function Title ($v_sTitle = "@")
	{
		if ($v_sTitle != "@")
			$this->m_sTitle = $v_sTitle;
		else
			$this->show ();		
	}

	function defNbrEspaces ($v_iNbrEspaces)
	{
		if ($v_iNbrEspaces<0 || $v_iNbrEspaces > 20)
			return;
		
		$this->m_iNbrEspaces = $v_iNbrEspaces;
	}	
	
	function show ()
	{
		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
		
		for ($i=0; $i<$this->m_iNbrEspaces; $i++)
			echo "<tr>\n<td >&nbsp;</td>\n</tr>\n";
			
		echo "</table>\n";
	
		echo "<div style=\"position: absolute; left:0; top: 0\">"
			."<img src=\"".dir_theme("bandeau.gif")."\">"
			."</div>\n";

		echo "<div class=\"Titre_3\" style=\"position: absolute; left: 20px; top: 7px;\">"
			.$this->m_sTitle.""
			."</div>\n";		
	}
}

class CTitrePrincipal 
{
	var $m_sTitre;
	var $m_sLargeur = "80%";
	
	function CTitrePrincipal ($v_sTitre)
	{
		$this->m_sTitre = emb_htmlentities($v_sTitre);
	}
	
	function defLargeur ($v_sLargeur)
	{
		$this->m_sLargeur = $v_sLargeur;
	}
	
	function afficher ()
	{
		echo "<!-- Afficher le titre principal -->\n"
			."<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"{$this->m_sLargeur}\">"
			."<tr>"
			."<td width=\"1%\" align=\"right\"><img src=\"".dir_theme ("barre_titre_g.gif")."\" border=\"0\"></td>"
			."<td valign=\"top\" class=\"Liste_Titre_Principal\">".$this->m_sTitre."</td>"
			."<td width=\"1%\"><img src=\"".dir_theme ("barre_titre_d.gif")."\"></td>"
			."</tr>"
			."<tr><td colspan=\"3\">&nbsp;</td></tr>"
			."</table>";
	}
}

?>
