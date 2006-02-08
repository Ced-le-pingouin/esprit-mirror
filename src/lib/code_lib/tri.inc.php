<?php

// :a: Filippo PORCO
// :c: 18-03-2002
// :d: 06-03-2003

function redistNumsOrdre ($v_aoNumOrdre,$v_iAncienNumOrdre=NULL,$v_iNouveauNumOrdre=NULL)
{
	if ($v_iAncienNumOrdre == NULL && $v_iNouveauNumOrdre == NULL)
		for ($i=0; $i<count($v_aoNumOrdre); $i++)
			$v_aoNumOrdre[$i][1] = $i+1;
	else if ($v_iAncienNumOrdre<$v_iNouveauNumOrdre)
		for ($i=$v_iAncienNumOrdre; $i<$v_iNouveauNumOrdre; $i++)
			$v_aoNumOrdre[$i][1] = $i;
	else	
		for ($i=$v_iNouveauNumOrdre; $i<$v_iAncienNumOrdre; $i++)
			$v_aoNumOrdre[$i-1][1] = $i+1;
	
	return $v_aoNumOrdre;
}

?>
