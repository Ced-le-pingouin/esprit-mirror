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
