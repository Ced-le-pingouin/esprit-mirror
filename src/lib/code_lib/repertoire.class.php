<?php

class CRepertoire 
{
	var $sRepertoire;
	
	function CRepertoire ($v_sRepertoire)
	{
		$this->sRepertoire = $v_sRepertoire;
	}
	
	function existe ()
	{
		return is_dir($this->sRepertoire);
	}
	
	function initFichiers ()
	{
	}
	
	function creer ()
	{
		$asReps = explode("/",$this->sRepertoire);
		$sRep = "/";
		
		for ($i=1; $i<count($asReps); $i++)
		{
			$sRep .= $asReps[$i]."/";
			
			if (is_dir($sRep))
				continue;
			@mkdir($sRep,0700);
		}
		
		return is_dir($this->sRepertoire);
	}
}

?>
