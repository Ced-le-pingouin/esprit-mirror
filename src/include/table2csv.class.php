<?php

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
