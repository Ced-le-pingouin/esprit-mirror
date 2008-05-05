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

class CTable2CSV
{
	var $sNomFichier;
	var $sChamps;
	var $sDonnees;
	
	function CTable2CSV ($v_sNomFichier)
	{
		$this->sNomFichier = $v_sNomFichier;
		$this->sChamps = NULL;
		$this->sDonnees = NULL;
	}
	
	function defChamps ($v_asLigneChamps)
	{
		$this->sChamps = $this->ajouterLigne($v_asLigneChamps);
	}
	
	function defDonnees ($v_asLigneDonnees)
	{
		$this->sDonnees .= $this->ajouterLigne($v_asLigneDonnees);
	}
	
	function ajouterLigne ($v_asLigneDonnees)
	{
		$l = NULL;
		
		foreach ($v_asLigneDonnees as $sLigneDonnees)
			$l .= (isset($l) ? ";" : NULL)
				."\"".str_replace("\"","\\\"",$sLigneDonnees)."\"";
		
		return $l."\r\n";
	}
	
	function exporter ()
	{
		if (empty($this->sNomFichier))
			return FALSE;
		
		$document = $this->sChamps
			.$this->sDonnees;
		
		$fp = fopen($this->sNomFichier,"w");
		fwrite($fp,$document);
		fclose($fp);
	}
	
	function effacer () { @unlink($this->sNomFichier); }
}

?>
