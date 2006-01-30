<?php

/*
** Classe .................: CBalisesCFJ
** Description
** Date de création .......: 28/06/2004
** Dernière modification ..: 09/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

class CBalisesCFJ
{
	var $sTexte;
	
	function CBalisesCFJ ($v_sTexte)
	{
		$this->sTexte = nl2br(htmlentities(trim(stripslashes($v_sTexte))));
		$this->analyser();
	}
	
	function analyser ()
	{
		$this->gras();
	}
	
	function gras ()
	{
		$this->sTexte = str_replace("[b]","<b>",$this->sTexte);
		$this->sTexte = str_replace("[/b]","</b>",$this->sTexte);
	}
	
	function italique ()
	{
		$this->sTexte = str_replace("[i]","<i>",$this->sTexte);
		$this->sTexte = str_replace("[/i]","</i>",$this->sTexte);
	}
	
	function souligne ()
	{
		$this->sTexte = str_replace("[u]","<u>",$this->sTexte);
		$this->sTexte = str_replace("[/u]","</u>",$this->sTexte);
	}
	
	/**
	 * [email "ute@umh.ac.be"]Ecrivez-moi à cette adresse[/email]
	 * <a href="mailto:ute@umh.ac.be">Ecrivez-moi à cette adresse</a>
	 */
	function email ()
	{
	}
	
	/**
	 * [site "ute.umh.ac.be" "_blank"]Unité de Technologie de l'Education[/site]
	 * <a href="http://ute.umh.ac.be" target="_blank">Unité de Technologie de l'Education</a>
	 */
	function site ()
	{
	}
	
	function afficher () { echo $this->sTexte; }
	function retourner () { return $this->sTexte; }
}

?>
