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

class CTable2HTML
{
	var $sNomFichier;
	var $sChamps;
	var $sDonnees;
	
	function CTable2HTML ($v_sNomFichier)
	{
		$this->sNomFichier = $v_sNomFichier;
		$this->sChamps = NULL;
		$this->sDonnees = NULL;
	}
	
	function defChamps ($v_asLigneChamps)
	{
		$this->sChamps = $this->ajouterLigne($v_asLigneChamps,"th");
	}
	
	function defDonnees ($v_asLigneDonnees)
	{
		$this->sDonnees .= $this->ajouterLigne($v_asLigneDonnees);
	}
	
	function ajouterLigne ($v_asLignesDonnees,$v_sStypeCol="td")
	{
		$l = "<tr>\n";
		
		foreach ($v_asLignesDonnees as $sLigneDonnees)
			$l .= "<{$v_sStypeCol}>"
				.(empty($sLigneDonnees) || $sLigneDonnees == "NULL" ? "<div align=\"center\">&#8212;</div>" : $sLigneDonnees)
				."</{$v_sStypeCol}>";
		
		return $l."</tr>\n";
	}
	
	function exporter ()
	{
		if (empty($this->sNomFichier))
			return FALSE;
		
		$document = "<html>\n"
			."<head>\n"
		        ."<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n"
			."<style type=\"text/css\">\n"
			."<!--\n"
			."body, a, p, th, td { font-family: Arial, Helvetica, sans-serif; font-size: 8pt; }\n"
			."th { background-color: rgb(0,0,0); color: rgb(255,255,255); font-weight: bold; }\n"
			."td { border: rgb(230,230,230) none 1px; border-bottom-style: dotted; }\n"
			."-->\n"
			."</style>"
			."</head>\n"
			."<body>\n"
			."<table border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
			.$this->sChamps
			.$this->sDonnees
			."</table>\n"
			."</body>\n"
			."</html>\n";
		
		$fp = fopen($this->sNomFichier,"w");
		fwrite($fp,$document);
		fclose($fp);
	}
}

?>
