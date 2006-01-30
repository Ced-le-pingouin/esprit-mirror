<?php

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
