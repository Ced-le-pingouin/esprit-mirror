<?php

/*
** Fichier ................: debug.class.php
** Description ............: 
** Date de création .......: 27-02-2002
** Dernière modification ..: 27-02-2002
** Auteur .................: Fili//0: Porco
** Email ..................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/


class CDebug 
{
	var $m_sNomFichier;
	var $m_iHandle;
	var $m_sSeparateur="-";
	var $m_iNombreSeparateur=40;
	
	function CDebug ($v_sFichier=NULL,$v_sEffacerFichierSiExiste=FALSE)
	{
		if ($v_sFichier === NULL)
			$v_sFichier = "pdh";
		
		$this->m_sNomFichier = dir_root_plateform ("debug/{$v_sFichier}.debug");
		
		if ($v_sEffacerFichierSiExiste)
			$this->Effacer ();
		
		$this->Demarrer ();
	}
	
	function Effacer ()
	{
		$this->Stop ();
		
		@unlink ($this->m_sNomFichier);
	}
	
	function Demarrer ()
	{
		$this->m_iHandle = fopen ($this->m_sNomFichier,"a+");
	}
	
	function Ecrire ($v_sTexte)
	{
		if ($this->m_iHandle !== NULL)
			fwrite ($this->m_iHandle,trim ($v_sTexte)."\n");
	}
	
	function Stop ()
	{
		if ($this->m_iHandle !== NULL)
			fclose ($this->m_iHandle);
	}
	
	function Titre ($v_sTitre,$v_sSeparateur=NULL,$v_iNombre=NULL)
	{
		$sSeparateur = $this->Separateur (FALSE,$v_sSeparateur,$v_iNombre);
		
		$sTitre = substr ($sSeparateur,0,2);
		
		$sTitre .= "[ ".$v_sTitre." ]".substr ($sSeparateur,strlen ($v_sTitre)+6);
		
		$this->Ecrire (trim ($sTitre));
	}
	
	function defNombreSeparateur ($v_iNombreSeparateur)
	{
		$this->m_iNombreSeparateur = $v_iNombreSeparateur;
	}
	
	function defSeparateur ($v_sSeparateur)
	{
		$this->m_sSeparateur = $v_sSeparateur;
	}
	
	function Separateur ($v_bEcrire=TRUE)
	{
		for ($i=0; $i<$this->m_iNombreSeparateur; $i++)
			$sSeparateur .= $this->m_sSeparateur;
		
		if ($v_bEcrire)
			$this->Ecrire ($sSeparateur);
		else
			return $sSeparateur;
	}
}

?>
