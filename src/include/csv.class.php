<?php

/*
** Fichier ................: csv.class.php
** Description ............:
** Date de création .......: 12/10/2005
** Dernière modification ..: 12/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

class CCSV
{
	function doubler_guillemets ($v_sTexte)
	{
		// Mettre un espace au début du message dans le cas où il y aurait un tiret :
		// c'est à cause de cet imbécil de Microsoft Excel
		if ("-" == substr($v_sTexte,0,1))
			$v_sTexte = " {$v_sTexte}";
		
		return str_replace("\"","\"\"",$v_sTexte);
	}
}

?>
