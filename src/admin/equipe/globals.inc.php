<?php

require_once ("../../globals.inc.php"); 

define("PERSONNE_INSCRITE",0);
define("PERSONNE_DANS_EQUIPE",1);
define("PERSONNE_SANS_EQUIPE",2);

function format_sous_titre ($v_sType,$v_sSousTitre)
{
	return "&nbsp;&nbsp;"
		."<b>".htmlentities($v_sType)."</b>"
		."&nbsp;"
		."<img src=\"".dir_theme("signet-2.gif")."\" border=\"0\">"
		."&nbsp;"
		.htmlentities($v_sSousTitre);
}

// *************************************
// Classe pour lire/enregistrer les modéles équipes
// *************************************

class CModele 
{
	var $sAuteur;
	var $sDateCreation;
	var $sDescription;
	var $aiIdPers;
	var $asNomEquipe;
	
	function CModele ($v_sAuteur=NULL,$v_sDateCreation=NULL)
	{
		$this->sAuteur = $v_sAuteur;
		$this->sDateCreation = $v_sDateCreation;
		$this->asNomEquipe = array();
		$this->aiIdPers = array();
	}
		
	function ajouterEquipe ($v_sNomEquipe)
	{
		$this->asNomEquipe[] = $v_sNomEquipe;
	}
	
	function ajouterMembres ($v_aiIdPers)
	{
		$this->aiIdPers[] = $v_aiIdPers;
	}
}

?>
