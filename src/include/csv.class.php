<?php

/*
** Fichier ................: csv.class.php
** Description ............:
** Date de cr�ation .......: 12/10/2005
** Derni�re modification ..: 12/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

class CCSV
{
	function doubler_guillemets ($v_sTexte)
	{
		// Mettre un espace au d�but du message dans le cas o� il y aurait un tiret :
		// c'est � cause de cet imb�cil de Microsoft Excel
		if ("-" == substr($v_sTexte,0,1))
			$v_sTexte = " {$v_sTexte}";
		
		return str_replace("\"","\"\"",$v_sTexte);
	}
}

?>
